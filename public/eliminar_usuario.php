<?php
require_once '../includes/auth.php';
require_once '../includes/auditoria.php';
require '../config/db.php';

requireRole(['Presidente General']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: listar_usuario.php");
    exit();
}

validarTokenCSRF($_POST['csrf_token'] ?? '');

$id = $_POST['id'] ?? null;

if ($id) {
    $stmtNombre = $pdo->prepare("SELECT nombre FROM usuarios WHERE id = :id");
    $stmtNombre->execute(['id' => $id]);
    $usuarioEliminado = $stmtNombre->fetch(PDO::FETCH_ASSOC);

    $sql = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    registrarAuditoria($pdo, 'eliminar', 'usuario', (int) $id, $usuarioEliminado['nombre'] ?? null);

    header("Location: listar_usuario.php");
    exit();
}

header("Location: listar_usuario.php");
exit();