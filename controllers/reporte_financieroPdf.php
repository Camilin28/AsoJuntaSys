<?php
require_once '../includes/auth.php';
require '../config/db.php';
require '../vendor/autoload.php'; // Debes tener Dompdf instalado

requireRole(['Tesorería', 'Presidente General']);

use Dompdf\Dompdf;

$dompdf = new Dompdf();

$sqlBase = "SELECT rf.*, j.nombre AS jac_nombre 
            FROM recursos_financieros rf
            LEFT JOIN juntas j ON rf.jac_id = j.id";

if ($_SESSION['usuario_rol'] !== 'Presidente General' && !empty($_SESSION['jac_id'])) {
    $stmt = $pdo->prepare($sqlBase . " WHERE rf.jac_id = :jac_id ORDER BY rf.fecha DESC");
    $stmt->execute([':jac_id' => $_SESSION['jac_id']]);
} else {
    $stmt = $pdo->query($sqlBase . " ORDER BY j.nombre ASC, rf.fecha DESC");
}
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$html = '
<h2 style="text-align:center;color:#2E7D32;">Reporte Financiero - AsoJuntaSys</h2>
<table border="1" cellspacing="0" cellpadding="6" width="100%">
<thead style="background-color:#2E7D32;color:#fff;">
<tr>
<th>ID</th><th>JAC</th><th>Descripción</th><th>Tipo</th><th>Clasificación</th><th>Monto</th><th>Fecha</th><th>Responsable</th>
</tr>
</thead><tbody>';

$jacAnterior = null;
foreach ($data as $row) {
    $jacActual = $row['jac_nombre'] ?? 'Sin JAC';
    // Resalta el inicio de cada nueva JAC (solo relevante cuando hay varias mezcladas)
    $filaNuevaJac = ($jacActual !== $jacAnterior);
    $estiloFila = $filaNuevaJac ? ' style="border-top:2px solid #2E7D32;"' : '';
    $jacAnterior = $jacActual;

    $html .= "<tr{$estiloFila}>
        <td>{$row['id']}</td>
        <td><strong>" . htmlspecialchars($jacActual) . "</strong></td>
        <td>" . htmlspecialchars($row['descripcion']) . "</td>
        <td>{$row['tipo_movimiento']}</td>
        <td>" . htmlspecialchars($row['clasificacion'] ?? '') . "</td>
        <td>\$ {$row['monto']}</td>
        <td>{$row['fecha']}</td>
        <td>" . htmlspecialchars($row['responsable']) . "</td>
    </tr>";
}
$html .= '</tbody></table>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream("reporte_financiero.pdf", ["Attachment" => false]);
