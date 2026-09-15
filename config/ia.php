<?php
/**
 * Cliente de la API de Gemini (Google AI Studio) para el Asistente Virtual (RF-028).
 *
 * RNF-015: si el servicio de IA falla o se demora, se degrada de forma
 * controlada (mensaje de error claro, nunca un error fatal).
 * RNF-016: solo se envía el contexto mínimo necesario para responder la
 * consulta; nunca datos personales identificables innecesarios.
 */

define('GEMINI_MODELO', 'gemini-2.5-flash');
define('GEMINI_TIMEOUT_SEGUNDOS', 15);

/**
 * Envía la pregunta del usuario + el contexto institucional a Gemini
 * y devuelve la respuesta en texto plano.
 *
 * @return array{ok: bool, respuesta: string}
 */
function consultarGemini(string $instruccionSistema, string $pregunta): array {

    $apiKey = getenv('GEMINI_API_KEY');

    if (!$apiKey) {
        error_log('config/ia.php: falta la variable de entorno GEMINI_API_KEY');
        return ['ok' => false, 'respuesta' => 'El asistente virtual no está disponible en este momento.'];
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . GEMINI_MODELO . ":generateContent";

    $payload = [
        'system_instruction' => [
            'parts' => [['text' => $instruccionSistema]],
        ],
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => $pregunta]]],
        ],
        'generationConfig' => [
            'temperature' => 0.3,
            'maxOutputTokens' => 400,
        ],
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'x-goog-api-key: ' . $apiKey,
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, GEMINI_TIMEOUT_SEGUNDOS);

    $respuestaCruda = curl_exec($ch);
    $codigoHttp = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $errorCurl = curl_error($ch);
    curl_close($ch);

    if ($errorCurl) {
        error_log('config/ia.php: error de cURL al llamar a Gemini: ' . $errorCurl);
        return ['ok' => false, 'respuesta' => 'El asistente virtual tardó demasiado en responder. Intenta de nuevo en un momento.'];
    }

    if ($codigoHttp < 200 || $codigoHttp >= 300) {
        error_log('config/ia.php: Gemini respondió HTTP ' . $codigoHttp . ': ' . $respuestaCruda);
        return ['ok' => false, 'respuesta' => 'El asistente virtual no pudo procesar tu consulta en este momento.'];
    }

    $data = json_decode($respuestaCruda, true);
    $texto = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

    if (!$texto) {
        error_log('config/ia.php: respuesta de Gemini sin texto utilizable: ' . $respuestaCruda);
        return ['ok' => false, 'respuesta' => 'No se pudo generar una respuesta. Intenta reformular tu pregunta.'];
    }

    return ['ok' => true, 'respuesta' => trim($texto)];
}