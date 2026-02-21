<?php
require '../config/db.php';
require '../vendor/autoload.php';

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$stmt = $pdo->query("SELECT id, titulo, descripcion, fecha, hora, color FROM agenda ORDER BY fecha ASC");
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<h2 style="text-align:center;color:#2E7D32;">Agenda Institucional - AsoJuntaSys</h2>
<table border="1" cellspacing="0" cellpadding="6" width="100%">
<thead style="background-color:#2E7D32;color:#fff;">
<tr>
<th>ID</th><th>Título</th><th>Descripción</th><th>Fecha</th><th>Hora</th><th>Color</th>
</tr></thead><tbody>';

foreach ($eventos as $e) {
    $html .= "<tr>
        <td>{$e['id']}</td>
        <td>{$e['titulo']}</td>
        <td>{$e['descripcion']}</td>
        <td>{$e['fecha']}</td>
        <td>{$e['hora']}</td>
        <td><span style='background-color:{$e['color']};color:#fff;padding:2px 8px;border-radius:4px;'>{$e['color']}</span></td>
    </tr>";
}
$html .= '</tbody></table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("reporte_financiero.pdf", ["Attachment" => false]);

