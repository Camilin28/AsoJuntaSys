<?php
// ARCHIVO TEMPORAL DE DIAGNÓSTICO - BORRAR DESPUÉS DE USAR

require_once __DIR__ . '/config/mail.php';

echo "<h2>Diagnóstico Brevo (API HTTP)</h2>";

echo "<p>Variables de entorno detectadas:</p><ul>";
echo "<li>BREVO_API_KEY: " . (getenv('BREVO_API_KEY') ? '✅ presente (longitud: ' . strlen(getenv('BREVO_API_KEY')) . ' caracteres)' : '❌ FALTA') . "</li>";
echo "<li>BREVO_FROM_EMAIL: " . (getenv('BREVO_FROM_EMAIL') ? '✅ presente (' . getenv('BREVO_FROM_EMAIL') . ')' : '❌ FALTA') . "</li>";
echo "</ul>";

echo "<p>¿Extensión curl de PHP disponible? " . (function_exists('curl_init') ? '✅ sí' : '❌ NO') . "</p>";

echo "<p>Intentando enviar correo de prueba vía API...</p>";

$resultado = enviarCorreoBrevo(
    'berbeocamilo@gmail.com',
    'Prueba',
    'Prueba API Brevo - AsoJuntaSys',
    '<p>Este es un correo de prueba usando la API HTTP de Brevo.</p>'
);

if ($resultado) {
    echo "<p style='color:green;font-weight:bold;'>✅ Brevo aceptó el correo. Revisa tu bandeja (y spam) en un minuto.</p>";
} else {
    echo "<p style='color:red;font-weight:bold;'>❌ Falló. Revisa los logs de Railway (Deployments → Logs) para ver el detalle exacto que quedó registrado con error_log.</p>";
}