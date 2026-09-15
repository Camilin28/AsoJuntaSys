<?php
// ARCHIVO TEMPORAL DE DIAGNÓSTICO - BORRAR DESPUÉS DE USAR
// Prueba aislada de conexión SMTP con Brevo, sin pasar por sesión ni base de datos.

require_once __DIR__ . '/config/mail.php';

echo "<h2>Diagnóstico Brevo</h2>";

echo "<p>Variables de entorno detectadas:</p><ul>";
echo "<li>BREVO_SMTP_USER: " . (getenv('BREVO_SMTP_USER') ? '✅ presente (' . getenv('BREVO_SMTP_USER') . ')' : '❌ FALTA') . "</li>";
echo "<li>BREVO_SMTP_PASS: " . (getenv('BREVO_SMTP_PASS') ? '✅ presente (longitud: ' . strlen(getenv('BREVO_SMTP_PASS')) . ' caracteres)' : '❌ FALTA') . "</li>";
echo "<li>BREVO_FROM_EMAIL: " . (getenv('BREVO_FROM_EMAIL') ? '✅ presente (' . getenv('BREVO_FROM_EMAIL') . ')' : '❌ FALTA') . "</li>";
echo "</ul>";

$mail = crearMailer();

if (!$mail) {
    echo "<p style='color:red;'>❌ crearMailer() devolvió null — revisa las variables de arriba, alguna está vacía.</p>";
    exit();
}

echo "<p>✅ PHPMailer se configuró correctamente. Intentando enviar correo de prueba...</p>";

try {
    $mail->addAddress('berbeocamilo@gmail.com', 'Prueba');
    $mail->Subject = 'Prueba de conexión Brevo - AsoJuntaSys';
    $mail->isHTML(true);
    $mail->Body = '<p>Este es un correo de prueba para diagnosticar la conexión con Brevo.</p>';
    $mail->SMTPDebug = 2; // Muestra el diálogo SMTP completo
    $mail->Debugoutput = function($str, $level) {
        echo "<pre style='background:#eee;padding:5px;'>" . htmlspecialchars($str) . "</pre>";
    };

    $mail->send();
    echo "<p style='color:green;font-weight:bold;'>✅ ¡Correo enviado exitosamente! Revisa tu bandeja (y spam).</p>";
} catch (Exception $e) {
    echo "<p style='color:red;font-weight:bold;'>❌ Error al enviar: " . htmlspecialchars($mail->ErrorInfo) . "</p>";
}