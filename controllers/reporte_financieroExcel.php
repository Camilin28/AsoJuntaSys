<?php
require_once '../includes/auth.php';
require '../config/db.php';
require '../vendor/autoload.php'; // Asegúrate de tener PhpSpreadsheet instalado

requireRole(['Tesorería', 'Presidente General']);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Reporte Financiero');

// Encabezados
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', 'Descripción');
$sheet->setCellValue('C1', 'Tipo de Movimiento');
$sheet->setCellValue('D1', 'Clasificación');
$sheet->setCellValue('E1', 'Monto');
$sheet->setCellValue('F1', 'Fecha');
$sheet->setCellValue('G1', 'Responsable');

$stmt = $pdo->query("SELECT * FROM recursos_financieros ORDER BY fecha DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rowIndex = 2;
foreach ($rows as $row) {
    $sheet->setCellValue("A$rowIndex", $row['id']);
    $sheet->setCellValue("B$rowIndex", $row['descripcion']);
    $sheet->setCellValue("C$rowIndex", $row['tipo_movimiento']);
    $sheet->setCellValue("D$rowIndex", $row['clasificacion']);
    $sheet->setCellValue("E$rowIndex", $row['monto']);
    $sheet->setCellValue("F$rowIndex", $row['fecha']);
    $sheet->setCellValue("G$rowIndex", $row['responsable']);
    $rowIndex++;
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_financiero.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;