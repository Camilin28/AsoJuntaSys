<?php
require_once '../includes/auth.php';

requireRole([
    'Tesorería',
    'Presidente General'
]);

require_once '../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    die("❌ ID no proporcionado.");
}

try {
    $stmt = $pdo->prepare("DELETE FROM recursos_financieros WHERE id = :id");
    $stmt->execute([':id' => $id]);

    header("Location: ../views/dashboard_tesoreria.php?success=delete");
    exit();
} catch (PDOException $e) {
    error_log('eliminar_movimiento.php: ' . $e->getMessage());
    die("❌ Error al eliminar el movimiento.");
}