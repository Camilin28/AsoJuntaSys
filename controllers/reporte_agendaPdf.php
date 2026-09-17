<?php
require_once '../includes/auth.php';
require '../config/db.php';
require '../vendor/autoload.php';

requireLogin();

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$sqlBaseAgenda = "SELECT ag.id, ag.titulo, ag.descripcion, ag.fecha, ag.hora, ag.color, j.nombre AS jac_nombre
                  FROM agenda ag
                  LEFT JOIN juntas j ON ag.jac_id = j.id";

if ($_SESSION['usuario_rol'] !== 'Presidente General' && !empty($_SESSION['jac_id'])) {
    $stmt = $pdo->prepare($sqlBaseAgenda . " WHERE ag.jac_id = :jac_id ORDER BY ag.fecha ASC");
    $stmt->execute([':jac_id' => $_SESSION['jac_id']]);
} else {
    $stmt = $pdo->query($sqlBaseAgenda . " ORDER BY j.nombre ASC, ag.fecha ASC");
}
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<h2 style="text-align:center;color:#2E7D32;">Agenda Institucional - AsoJuntaSys</h2>
<table border="1" cellspacing="0" cellpadding="6" width="100%">
<thead style="background-color:#2E7D32;color:#fff;">
<tr>
<th>ID</th><th>JAC</th><th>Título</th><th>Descripción</th><th>Fecha</th><th>Hora</th><th>Color</th>
</tr></thead><tbody>';

foreach ($eventos as $e) {
    $html .= "<tr>
        <td>{$e['id']}</td>
        <td><strong>" . htmlspecialchars($e['jac_nombre'] ?? 'Sin JAC') . "</strong></td>
        <td>" . htmlspecialchars($e['titulo']) . "</td>
        <td>" . htmlspecialchars($e['descripcion'] ?? '') . "</td>
        <td>{$e['fecha']}</td>
        <td>{$e['hora']}</td>
        <td><span style='background-color:{$e['color']};color:#fff;padding:2px 8px;border-radius:4px;'>{$e['color']}</span></td>
    </tr>";
}
$html .= '</tbody></table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("reporte_agenda.pdf", ["Attachment" => false]);
