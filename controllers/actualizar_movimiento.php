<?php
require_once '../includes/auth.php';

requireRole([
    'Tesorería',
    'Presidente General'
]);

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/dashboard_tesoreria.php");
    exit();
}

$id            = $_POST['id'] ?? null;
$descripcion   = $_POST['descripcion'] ?? '';
$tipo          = $_POST['tipo_movimiento'] ?? '';
$monto         = $_POST['monto'] ?? 0;
$fecha         = $_POST['fecha'] ?? null;
$responsable   = $_POST['responsable'] ?? null;
$observaciones = $_POST['observaciones'] ?? null;

if (!$id) {
    die("❌ Error: falta el ID del movimiento.");
}

try {
    $sql = "UPDATE recursos_financieros
            SET descripcion = :descripcion,
                tipo_movimiento = :tipo_movimiento,
                monto = :monto,
                fecha = :fecha,
                responsable = :responsable,
                observaciones = :observaciones
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':descripcion'     => $descripcion,
        ':tipo_movimiento' => $tipo,
        ':monto'           => $monto,
        ':fecha'           => $fecha,
        ':responsable'     => $responsable,
        ':observaciones'   => $observaciones,
        ':id'              => $id,
    ]);

    header("Location: ../views/dashboard_tesoreria.php?success=edit");
    exit();
} catch (PDOException $e) {
    error_log('actualizar_movimiento.php: ' . $e->getMessage());
    die("❌ Error al actualizar el movimiento.");
}