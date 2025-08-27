<?php
require '../config/conexion.php';

$id = $_POST['id'];
$descripcion = $_POST['descripcion'];
$tipo = $_POST['tipo_movimiento'];
$monto = $_POST['monto'];
$fecha = $_POST['fecha'];
$responsable = $_POST['responsable'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;

$sql = "UPDATE movimientos_financieros SET descripcion = ?, tipo_movimiento = ?, monto = ?, fecha = ?, responsable = ?, observaciones = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisssi", $descripcion, $tipo, $monto, $fecha, $responsable, $observaciones, $id);

if ($stmt->execute()) {
    header("Location: ../views/dashboard_tesorero.php");
} else {
    echo "Error al actualizar: " . $conn->error;
}
