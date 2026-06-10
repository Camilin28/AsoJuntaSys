<?php
require '../config/db.php';
require '../vendor/autoload.php'; // Debes tener Dompdf instalado
echo "autoload OK";

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$stmt = $pdo->query("SELECT * FROM recursos_financieros ORDER BY fecha DESC");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<h2 style="text-align:center;color:#2E7D32;">Reporte Financiero - AsoJuntaSys</h2>
<table border="1" cellspacing="0" cellpadding="6" width="100%">
<thead style="background-color:#2E7D32;color:#fff;">
<tr>
<th>ID</th><th>Descripción</th><th>Tipo</th><th>Clasificación</th><th>Monto</th><th>Fecha</th><th>Responsable</th>
</tr>
</thead><tbody>';

foreach ($data as $row) {
    $html .= "<tr>
        <td>{$row['id']}</td>
        <td>{$row['descripcion']}</td>
        <td>{$row['tipo_movimiento']}</td>
        <td>{$row['clasificacion']}</td>
        <td>$ {$row['monto']}</td>
        <td>{$row['fecha']}</td>
        <td>{$row['responsable']}</td>
    </tr>";
}
$html .= '</tbody></table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("reporte_financiero.pdf", ["Attachment" => false]);

