<?php
require_once '../includes/auth.php';
require '../config/db.php';

requireRole(['Presidente General']);

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM usuarios WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);

    echo "✅ Usuario eliminado con éxito.";
    header("Location: listar_usuario.php");
}
?>