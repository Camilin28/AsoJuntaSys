<?php
/**
 * Cliente de la API de Gemini (Google AI Studio) para el Asistente Virtual (RF-028).
 *
 * Usa "function calling": el modelo decide por sí mismo qué herramientas
 * llamar (y con qué parámetros) según la pregunta, en vez de recibir todo
 * el contexto precargado de antemano.
 *
 * RNF-015: si el servicio de IA falla o se demora, se degrada de forma
 * controlada (mensaje de error claro, nunca un error fatal).
 * RNF-016: cada herramienta, al ejecutarse en PHP, sigue aplicando las
 * mismas restricciones de rol que el resto del sistema — el modelo puede
 * PEDIR cualquier cosa, pero solo recibe datos que el usuario ya podía ver.
 */

define('GEMINI_MODELO', 'gemini-3.6-flash');
define('GEMINI_TIMEOUT_SEGUNDOS', 20);
define('GEMINI_MAX_ITERACIONES_TOOLS', 5);

/**
 * Hace una única llamada HTTP a generateContent.
 * @return array|null El array decodificado de la respuesta de Gemini, o null si falló.
 */
function _gemini_llamar_raw(string $instruccionSistema, array $contents, array $tools): ?array {

    $apiKey = getenv('GEMINI_API_KEY');
    if (!$apiKey) {
        error_log('config/ia.php: falta la variable de entorno GEMINI_API_KEY');
        return null;
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . GEMINI_MODELO . ":generateContent";

    $payload = [
        'system_instruction' => ['parts' => [['text' => $instruccionSistema]]],
        'contents' => $contents,
        'generationConfig' => [
            'temperature' => 0.4,
            'maxOutputTokens' => 1024,
            'thinkingConfig' => ['thinkingLevel' => 'low'],
        ],
    ];
    if ($tools) {
        $payload['tools'] = [['functionDeclarations' => $tools]];
    }

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
        return null;
    }
    if ($codigoHttp < 200 || $codigoHttp >= 300) {
        error_log('config/ia.php: Gemini respondió HTTP ' . $codigoHttp . ': ' . $respuestaCruda);
        return null;
    }

    return json_decode($respuestaCruda, true);
}

/**
 * Conversa con Gemini dándole acceso a "herramientas" (funciones PHP reales).
 * El modelo decide, según la pregunta, si necesita llamar alguna herramienta
 * antes de responder; si lo hace, se ejecuta $ejecutorHerramienta() y el
 * resultado se le devuelve al modelo para que complete su respuesta.
 *
 * @param string   $instruccionSistema Reglas de comportamiento (sin contexto precargado).
 * @param array    $contents Historial de conversación + la pregunta nueva del usuario.
 * @param array    $tools Declaraciones de funciones disponibles (formato Gemini functionDeclarations).
 * @param callable $ejecutorHerramienta function(string $nombre, array $args): array — ejecuta la herramienta y devuelve el resultado.
 * @return array{ok: bool, respuesta: string, contents: array} contents incluye los turnos de herramientas usados, para guardarlos en el historial si se desea.
 */
function consultarGeminiConHerramientas(string $instruccionSistema, array $contents, array $tools, callable $ejecutorHerramienta): array {

    for ($i = 0; $i < GEMINI_MAX_ITERACIONES_TOOLS; $i++) {

        $data = _gemini_llamar_raw($instruccionSistema, $contents, $tools);

        if ($data === null) {
            return ['ok' => false, 'respuesta' => 'El asistente virtual no pudo procesar tu consulta en este momento.', 'contents' => $contents];
        }

        $parts = $data['candidates'][0]['content']['parts'] ?? [];

        if (!$parts) {
            error_log('config/ia.php: respuesta de Gemini sin parts: ' . json_encode($data));
            return ['ok' => false, 'respuesta' => 'No se pudo generar una respuesta. Intenta reformular tu pregunta.', 'contents' => $contents];
        }

        // ¿El modelo pidió llamar una o más herramientas?
        $llamadasFuncion = array_filter($parts, fn($p) => isset($p['functionCall']));

               if ($llamadasFuncion) {
            // PHP decodifica un {} vacío de Gemini como [] (lista), pero al
            // reenviarlo Gemini exige que 'args' sea un objeto ({}), no una
            // lista. Normalizamos cualquier 'args' vacío a un objeto real.
            $llamadasNormalizadas = array_map(function ($p) {
                if (isset($p['functionCall']['args']) && is_array($p['functionCall']['args']) && empty($p['functionCall']['args'])) {
                    $p['functionCall']['args'] = new stdClass();
                }
                return $p;
            }, array_values($llamadasFuncion));

            // Registramos el turno del modelo pidiendo las herramientas
            $contents[] = ['role' => 'model', 'parts' => $llamadasNormalizadas];

            $respuestasFuncion = [];
            foreach ($llamadasFuncion as $llamada) {
                $nombre = $llamada['functionCall']['name'] ?? '';
                $args = $llamada['functionCall']['args'] ?? [];
                $resultado = $ejecutorHerramienta($nombre, $args);
                $respuestasFuncion[] = [
                    'functionResponse' => ['name' => $nombre, 'response' => $resultado],
                ];
            }
            $contents[] = ['role' => 'user', 'parts' => $respuestasFuncion];

            continue; // volvemos a llamar a Gemini con el resultado de la herramienta
        }

        // Respuesta de texto normal
        $texto = $parts[0]['text'] ?? null;
        if ($texto) {
            $contents[] = ['role' => 'model', 'parts' => [['text' => $texto]]];
            return ['ok' => true, 'respuesta' => trim($texto), 'contents' => $contents];
        }

        break;
    }

    error_log('config/ia.php: se agotaron las iteraciones de herramientas sin respuesta final');
    return ['ok' => false, 'respuesta' => 'La consulta se volvió demasiado compleja. Intenta preguntar algo más específico.', 'contents' => $contents];
}