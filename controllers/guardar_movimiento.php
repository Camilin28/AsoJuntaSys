<?php

require_once '../includes/auth.php';
require_once '../includes/auditoria.php';

requireRole([
    'Tesorería',
    'Presidente General'
]);

require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descripcion = $_POST['descripcion'];
    $tipo_movimiento = $_POST['tipo_movimiento'];
    $monto = $_POST['monto'];
    $fecha = $_POST['fecha'];
    $responsable = $_POST['responsable'];
    $observaciones = $_POST['observaciones'];

    $clasificacion = null;
    if ($tipo_movimiento === "Otro" && !empty($_POST['otro_clasificacion'])) {
        $clasificacion = $_POST['otro_clasificacion'];
    }

    $sql = "INSERT INTO recursos_financieros 
                (descripcion, tipo_movimiento, clasificacion, monto, fecha, responsable, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$descripcion, $tipo_movimiento, $clasificacion, $monto, $fecha, $responsable, $observaciones]);

    registrarAuditoria($pdo, 'crear', 'movimiento_financiero', (int) $pdo->lastInsertId(), "{$tipo_movimiento}: \${$monto}");

    header("Location: ../views/dashboard_tesoreria.php");
    exit();
}
?>
