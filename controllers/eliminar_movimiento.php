<?php
require '../config/conexion.php';

if (!isset($_GET['id'])) {
    echo "ID no proporcionado";
    exit();
}

$id = $_GET['id'];
$sql = "DELETE FROM movimientos_financieros WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    header("Location: ../views/dashboard_tesorero.php");
} else {
    echo "Error al eliminar: " . $conn->error;
}
