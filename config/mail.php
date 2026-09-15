<?php
/**
 * Envío de correo vía la API HTTP de Brevo (RF-005).
 *
 * Se usa la API por HTTPS en vez de SMTP porque Railway bloquea las
 * conexiones salientes por puertos SMTP (25/465/587), común en
 * plataformas cloud para prevenir spam. La API HTTPS (puerto 443)
 * no tiene ese problema.
 *
 * Todas las credenciales vienen de variables de entorno de Railway,
 * nunca hardcodeadas en el código.
 */

/**
 * Envía un correo a través de la API de Brevo.
 *
 * @return bool true si Brevo aceptó el correo, false si hubo un error
 *              (el detalle queda registrado con error_log, nunca se
 *              expone al usuario final).
 */
function enviarCorreoBrevo(string $paraEmail, string $paraNombre, string $asunto, string $cuerpoHtml): bool {

    $apiKey = getenv('BREVO_API_KEY');
    $fromEmail = getenv('BREVO_FROM_EMAIL');

    if (!$apiKey || !$fromEmail) {
        error_log('config/mail.php: faltan variables de entorno de Brevo (BREVO_API_KEY, BREVO_FROM_EMAIL)');
        return false;
    }

    $payload = [
        'sender'      => ['name' => 'AsoJuntaSys', 'email' => $fromEmail],
        'to'          => [['email' => $paraEmail, 'name' => $paraNombre]],
        'subject'     => $asunto,
        'htmlContent' => $cuerpoHtml,
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'api-key: ' . $apiKey,
        'content-type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    $respuesta = curl_exec($ch);
    $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errorCurl = curl_error($ch);
    curl_close($ch);

    if ($errorCurl) {
        error_log('config/mail.php: error de cURL al llamar a Brevo: ' . $errorCurl);
        return false;
    }

    if ($codigoHttp < 200 || $codigoHttp >= 300) {
        error_log('config/mail.php: Brevo respondió HTTP ' . $codigoHttp . ': ' . $respuesta);
        return false;
    }

    return true;
}