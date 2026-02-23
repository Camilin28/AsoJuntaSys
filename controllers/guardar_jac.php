<?php
session_start();
require('../config/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/gestionar_jac.php");
    exit();
}

$nombre = trim($_POST['nombre_jac']);
$presidente_id = $_POST['presidente_id'];
$secretario_id = $_POST['secretario_id'];
$tesorero_id = $_POST['tesorero_id'];

if (empty($nombre) || empty($presidente_id) || empty($secretario_id) || empty($tesorero_id)) {
    die("Todos los cargos son obligatorios.");
}

// Evitar que se repita la misma persona
if ($presidente_id == $secretario_id || 
    $presidente_id == $tesorero_id || 
    $secretario_id == $tesorero_id) {
    die("Una misma persona no puede ocupar dos cargos.");
}

try {

    $pdo->beginTransaction();

    // 1️⃣ Crear JAC
    $stmt = $pdo->prepare("
        INSERT INTO juntas (nombre) 
        VALUES (?)
    ");
    $stmt->execute([$nombre]);

    $jac_id = $pdo->lastInsertId();

    // 2️⃣ Asignar jac_id a los usuarios
    $stmt = $pdo->prepare("
        UPDATE usuarios 
        SET jac_id = ? 
        WHERE id = ?
    ");

    $stmt->execute([$jac_id, $presidente_id]);
    $stmt->execute([$jac_id, $secretario_id]);
    $stmt->execute([$jac_id, $tesorero_id]);

    $pdo->commit();

    header("Location: ../views/gestionar_jac.php?success=1");

} catch (Exception $e) {

    $pdo->rollBack();
    die("Error al crear la JAC: " . $e->getMessage());
}