<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM actas WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header("Location: ../views/actas.php?success=delete");
exit();
