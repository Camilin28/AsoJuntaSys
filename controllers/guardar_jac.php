<?php

session_start();
require('../config/db.php');

if (
    !isset($_SESSION['usuario_id']) ||
    !isset($_SESSION['usuario_rol']) ||
    $_SESSION['usuario_rol'] !== 'Presidente General'
) {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/gestionar_jac.php");
    exit();
}

$nombre        = trim($_POST['nombre_jac']);
$direccion     = trim($_POST['direccion']);
$telefono      = trim($_POST['telefono']);
$presidente_id = intval($_POST['presidente_id']);
$secretario_id = intval($_POST['secretario_id']);
$tesorero_id   = intval($_POST['tesorero_id']);

if (
    empty($nombre) ||
    empty($direccion) ||
    empty($presidente_id) ||
    empty($secretario_id) ||
    empty($tesorero_id)
) {
    die("Todos los campos obligatorios deben completarse.");
}

if (
    $presidente_id == $secretario_id ||
    $presidente_id == $tesorero_id ||
    $secretario_id == $tesorero_id
) {
    die("No se puede asignar la misma persona a múltiples cargos.");
}

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO juntas
        (
            nombre,
            direccion,
            telefono,
            estado,
            fecha_creacion
        )
        VALUES
        (
            ?,
            ?,
            ?,
            'Activa',
            CURDATE()
        )
    ");

    $stmt->execute([
        $nombre,
        $direccion,
        !empty($telefono) ? $telefono : null
    ]);

    $jac_id = $pdo->lastInsertId();

    $stmtUsuario = $pdo->prepare("
        UPDATE usuarios
        SET jac_id = ?
        WHERE id = ?
    ");

    $stmtUsuario->execute([$jac_id, $presidente_id]);
    $stmtUsuario->execute([$jac_id, $secretario_id]);
    $stmtUsuario->execute([$jac_id, $tesorero_id]);

    $pdo->commit();

    header("Location: ../views/gestionar_jac.php?success=1");
    exit();

} catch (Exception $e) {

    $pdo->rollBack();

    die(
        "Error al crear la JAC: "
        . $e->getMessage()
    );
}