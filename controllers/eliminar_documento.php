<?php
require_once('../includes/auth.php');
require_once('../includes/auditoria.php');
require('../config/db.php');

requireRole(['Secretaría', 'Presidente General']);

if (!isset($_GET['id'])) {
    header("Location: ../views/documentos.php");
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT archivo, titulo FROM documentos WHERE id = :id");
$stmt->execute([':id' => $id]);
$doc = $stmt->fetch(PDO::FETCH_ASSOC);

if ($doc) {
    $rutaArchivo = "../uploads/documentos/" . $doc['archivo'];
    if (file_exists($rutaArchivo)) {
        unlink($rutaArchivo);
    }
    $delete = $pdo->prepare("DELETE FROM documentos WHERE id = :id");
    $delete->execute([':id' => $id]);

    registrarAuditoria($pdo, 'eliminar', 'documento', (int) $id, $doc['titulo'] ?? null);
}

header("Location: ../views/documentos.php?success=2");
exit();
