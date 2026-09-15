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

    try {
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        registrarAuditoria($pdo, 'eliminar', 'usuario', (int) $id, $usuarioEliminado['nombre'] ?? null);

        header("Location: listar_usuario.php?success=delete");
        exit();
    } catch (PDOException $e) {
        error_log('eliminar_usuario.php: ' . $e->getMessage());

        if ($e->getCode() === '23000') {
            $_SESSION['error'] = "No se pudo eliminar el usuario porque tiene documentos, actas u otros registros asociados en el sistema. Reasigna o elimina esos registros primero.";
        } else {
            $_SESSION['error'] = "Ocurrió un error al eliminar el usuario.";
        }

        header("Location: listar_usuario.php");
        exit();
    }
}

header("Location: listar_usuario.php");
exit();