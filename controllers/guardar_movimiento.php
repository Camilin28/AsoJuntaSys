<?php
require '../config/conexion.php';

$descripcion = $_POST['descripcion'];
$tipo = $_POST['tipo_movimiento'];
$monto = $_POST['monto'];
$fecha = $_POST['fecha'];
$responsable = $_POST['responsable'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;

$sql = "INSERT INTO movimientos_financieros (descripcion, tipo_movimiento, monto, fecha, responsable, observaciones)
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisss", $descripcion, $tipo, $monto, $fecha, $responsable, $observaciones);

if ($stmt->execute()) {
    header("Location: ../views/dashboard_tesorero.php");
} else {
    echo "Error al guardar el movimiento: " . $conn->error;
}
