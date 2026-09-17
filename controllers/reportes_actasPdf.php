<?php
ob_start(); // 🔹 Evita espacios antes del PDF
require_once '../includes/auth.php';
require '../config/db.php';
require '../vendor/autoload.php';

requireLogin();

use Dompdf\Dompdf;
use Dompdf\Options;

// 🔹 Configurar Dompdf para UTF-8 y mejor calidad
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

if (!empty($_SESSION['jac_id'])) {
    $stmt = $pdo->prepare("SELECT id, titulo, fecha_reunion, hora_reunion, lugar FROM actas WHERE jac_id = :jac_id ORDER BY fecha_reunion DESC");
    $stmt->execute([':jac_id' => $_SESSION['jac_id']]);
} else {
    $stmt = $pdo->query("SELECT id, titulo, fecha_reunion, hora_reunion, lugar FROM actas ORDER BY fecha_reunion DESC");
}
$actas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 🔹 HTML limpio y compatible
$html = '
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #333; }
h2 { text-align: center; color: #2E7D32; }
table { border-collapse: collapse; width: 100%; margin-top: 15px; }
th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; }
th { background-color: #2E7D32; color: white; }
tr:nth-child(even) { background-color: #f9f9f9; }
</style>
</head>
<body>
<h2>Reporte de Actas - AsoJuntaSys</h2>
<table>
<thead>
<tr>
<th>ID</th><th>Título</th><th>Fecha Reunión</th><th>Lugar</th>
</tr>
</thead>
<tbody>';

if (count($actas) === 0) {
    $html .= '<tr><td colspan="4" style="text-align:center;">No hay actas registradas</td></tr>';
} else {
    foreach ($actas as $a) {
        $html .= "<tr>
            <td>{$a['id']}</td>
            <td>" . htmlspecialchars($a['titulo']) . "</td>
            <td>{$a['fecha_reunion']}</td>
            <td>" . htmlspecialchars($a['lugar']) . "</td>
        </tr>";
    }
}

$html .= '
</tbody>
</table>
</body>
</html>';

// 🔹 Cargar HTML y renderizar
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// 🔹 Mostrar en el navegador (NO descargar automáticamente)
$dompdf->stream("reporte_actas.pdf", ["Attachment" => false]);
exit;
