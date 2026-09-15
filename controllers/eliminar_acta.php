<?php
require_once('../includes/auth.php');
require_once('../includes/auditoria.php');
require('../config/db.php');

requireRole(['Secretaría']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/actas.php");
    exit();
}

validarTokenCSRF($_POST['csrf_token'] ?? '');

$id = $_POST['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM actas WHERE id = :id");
    $stmt->execute([':id' => $id]);

    registrarAuditoria($pdo, 'eliminar', 'acta', (int) $id);
}

header("Location: ../views/actas.php?success=delete");
exit();