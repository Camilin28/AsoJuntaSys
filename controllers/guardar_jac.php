<?php
session_start();
require('../config/db.php');

// Seguridad: solo Presidente General puede crear JAC
if (
    !isset($_SESSION['usuario_id']) ||
    !isset($_SESSION['usuario_rol']) ||
    $_SESSION['usuario_rol'] !== 'Presidente General'
) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/gestionar_jac.php");
    exit();
}

/* ===========================
   Obtener datos del formulario
   =========================== */

$nombre        = trim($_POST['nombre_jac']);
$direccion     = trim($_POST['direccion']);
$telefono      = trim($_POST['telefono']);
$presidente_id = $_POST['presidente_id'];
$secretario_id = $_POST['secretario_id'];
$tesorero_id   = $_POST['tesorero_id'];

/* ===========================
   Validaciones
   =========================== */

if (
    empty($nombre) ||
    empty($direccion) ||
    empty($presidente_id) ||
    empty($secretario_id) ||
    empty($tesorero_id)
) {
    die("Todos los campos obligatorios deben completarse.");
}

// Evitar que una misma persona tenga dos cargos
if (
    $presidente_id == $secretario_id ||
    $presidente_id == $tesorero_id ||
    $secretario_id == $tesorero_id
) {
    die("Una misma persona no puede ocupar más de un cargo.");
}

try {

    $pdo->beginTransaction();

    /* ===========================
       1️⃣ Crear JAC
       =========================== */

    $stmt = $pdo->prepare("
        INSERT INTO juntas (nombre, direccion, telefono) 
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $nombre,
        $direccion,
        !empty($telefono) ? $telefono : null
    ]);

    $jac_id = $pdo->lastInsertId();

    /* ===========================
       2️⃣ Asignar jac_id a usuarios
       =========================== */

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
    exit();

} catch (Exception $e) {

    $pdo->rollBack();
    die("Error al crear la JAC: " . $e->getMessage());
}