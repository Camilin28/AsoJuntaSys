<?php
require_once '../includes/auth.php';
require_once '../includes/auditoria.php';
require '../config/db.php';

// RF-007/RF-008: eliminación lógica de JAC (activar/desactivar, sin DELETE real,
// para preservar el histórico de actas, documentos y movimientos asociados).

requireRole(['Presidente General']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/gestionar_jac.php");
    exit();
}

validarTokenCSRF($_POST['csrf_token'] ?? '');

$id = $_POST['id'] ?? null;

if (!$id) {
    header("Location: ../views/gestionar_jac.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT nombre, estado FROM juntas WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $jac = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$jac) {
        header("Location: ../views/gestionar_jac.php");
        exit();
    }

    $nuevoEstado = ($jac['estado'] === 'Activa') ? 'Inactiva' : 'Activa';

    $update = $pdo->prepare("UPDATE juntas SET estado = :estado WHERE id = :id");
    $update->execute([':estado' => $nuevoEstado, ':id' => $id]);

    $accion = ($nuevoEstado === 'Inactiva') ? 'eliminar' : 'editar';
    $detalle = ($nuevoEstado === 'Inactiva')
        ? "JAC '{$jac['nombre']}' desactivada (eliminación lógica)"
        : "JAC '{$jac['nombre']}' reactivada";

    registrarAuditoria($pdo, $accion, 'jac', (int) $id, $detalle);

    header("Location: ../views/gestionar_jac.php?success=1");
    exit();

} catch (PDOException $e) {
    error_log('toggle_estado_jac.php: ' . $e->getMessage());
    header("Location: ../views/gestionar_jac.php?error=1");
    exit();
}