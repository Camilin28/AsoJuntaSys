<?php
require_once '../includes/auth.php';
require '../config/db.php';
require '../vendor/autoload.php';

requireRole(['Tesorería', 'Presidente General']);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

// Paleta institucional AsoJuntaSys
const COLOR_VERDE_PRINCIPAL = '2E7D32';
const COLOR_AMARILLO_PRINCIPAL = 'FBC02D';
const COLOR_AMARILLO_SUAVE = 'FFF9C4';
const COLOR_GRIS_OSCURO = '424242';

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Reporte Financiero');

/* ===========================
   Encabezado / título del reporte
=========================== */

$sheet->mergeCells('A1:G1');
$sheet->setCellValue('A1', 'AsoJuntaSys — Reporte de Movimientos Financieros');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setRGB('FFFFFF');
$sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(COLOR_VERDE_PRINCIPAL);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getRowDimension(1)->setRowHeight(28);

$sheet->mergeCells('A2:G2');
$sheet->setCellValue('A2', 'Generado el ' . date('d/m/Y H:i'));
$sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(9)->getColor()->setRGB(COLOR_GRIS_OSCURO);
$sheet->getStyle('A2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(COLOR_AMARILLO_SUAVE);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

/* ===========================
   Encabezados de columnas (fila 4)
=========================== */

$filaEncabezado = 4;
$columnas = ['A' => 'ID', 'B' => 'Descripción', 'C' => 'Tipo de Movimiento', 'D' => 'Clasificación', 'E' => 'Monto', 'F' => 'Fecha', 'G' => 'Responsable'];

foreach ($columnas as $col => $titulo) {
    $sheet->setCellValue("{$col}{$filaEncabezado}", $titulo);
}

$rangoEncabezado = "A{$filaEncabezado}:G{$filaEncabezado}";
$sheet->getStyle($rangoEncabezado)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle($rangoEncabezado)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(COLOR_VERDE_PRINCIPAL);
$sheet->getStyle($rangoEncabezado)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
$sheet->getRowDimension($filaEncabezado)->setRowHeight(20);

/* ===========================
   Datos
=========================== */

$stmt = $pdo->query("SELECT * FROM recursos_financieros ORDER BY fecha DESC");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$rowIndex = $filaEncabezado + 1;
$totalIngresos = 0;
$totalGastos = 0;

foreach ($rows as $row) {
    $sheet->setCellValue("A$rowIndex", $row['id']);
    $sheet->setCellValue("B$rowIndex", $row['descripcion']);
    $sheet->setCellValue("C$rowIndex", $row['tipo_movimiento']);
    $sheet->setCellValue("D$rowIndex", $row['clasificacion'] ?: '—');
    $sheet->setCellValue("E$rowIndex", (float) $row['monto']);
    $sheet->setCellValue("F$rowIndex", $row['fecha']);
    $sheet->setCellValue("G$rowIndex", $row['responsable']);

    // Resalta en rojo los gastos, en verde los ingresos (columna Monto)
    $colorMonto = ($row['tipo_movimiento'] === 'Gasto') ? 'C62828' : COLOR_VERDE_PRINCIPAL;
    $sheet->getStyle("E$rowIndex")->getFont()->getColor()->setRGB($colorMonto);

    if ($row['tipo_movimiento'] === 'Ingreso') {
        $totalIngresos += (float) $row['monto'];
    } elseif ($row['tipo_movimiento'] === 'Gasto') {
        $totalGastos += (float) $row['monto'];
    }

    // Franjas alternas para facilitar la lectura
    if ($rowIndex % 2 === 0) {
        $sheet->getStyle("A{$rowIndex}:G{$rowIndex}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F5F5F5');
    }

    $rowIndex++;
}

$filaUltimoDato = $rowIndex - 1;

/* ===========================
   Totales
=========================== */

$rowIndex++; // deja una fila en blanco
$sheet->setCellValue("D{$rowIndex}", 'Total Ingresos:');
$sheet->setCellValue("E{$rowIndex}", $totalIngresos);
$sheet->getStyle("D{$rowIndex}:E{$rowIndex}")->getFont()->setBold(true)->getColor()->setRGB(COLOR_VERDE_PRINCIPAL);
$rowIndex++;

$sheet->setCellValue("D{$rowIndex}", 'Total Gastos:');
$sheet->setCellValue("E{$rowIndex}", $totalGastos);
$sheet->getStyle("D{$rowIndex}:E{$rowIndex}")->getFont()->setBold(true)->getColor()->setRGB('C62828');
$rowIndex++;

$sheet->setCellValue("D{$rowIndex}", 'Saldo:');
$sheet->setCellValue("E{$rowIndex}", $totalIngresos - $totalGastos);
$sheet->getStyle("D{$rowIndex}:E{$rowIndex}")->getFont()->setBold(true)->setSize(12)->getColor()->setRGB(COLOR_GRIS_OSCURO);
$sheet->getStyle("D{$rowIndex}:E{$rowIndex}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB(COLOR_AMARILLO_PRINCIPAL);

/* ===========================
   Formato de columnas
=========================== */

// Formato de moneda para la columna Monto (incluye la fila de totales)
$sheet->getStyle("E" . ($filaEncabezado + 1) . ":E{$rowIndex}")
    ->getNumberFormat()->setFormatCode('$#,##0');

// Formato de fecha
if ($filaUltimoDato >= $filaEncabezado + 1) {
    $sheet->getStyle("F" . ($filaEncabezado + 1) . ":F{$filaUltimoDato}")
        ->getNumberFormat()->setFormatCode('dd/mm/yyyy');

    // Bordes en la tabla de datos
    $sheet->getStyle("A{$filaEncabezado}:G{$filaUltimoDato}")
        ->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('DDDDDD');
}

// Anchos de columna
$anchos = ['A' => 6, 'B' => 32, 'C' => 16, 'D' => 16, 'E' => 14, 'F' => 12, 'G' => 18];
foreach ($anchos as $col => $ancho) {
    $sheet->getColumnDimension($col)->setWidth($ancho);
}

// Fija la fila de encabezado al hacer scroll
$sheet->freezePane("A" . ($filaEncabezado + 1));

/* ===========================
   Descarga
=========================== */

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="reporte_financiero.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;