<?php
require_once('../includes/auth.php');
require('../config/db.php');

requireRole(['Secretaría']);

$id = $_GET['id'] ?? null;
if ($id) {
    $stmt = $pdo->prepare("DELETE FROM actas WHERE id = :id");
    $stmt->execute([':id' => $id]);
}

header("Location: ../views/actas.php?success=delete");
exit();