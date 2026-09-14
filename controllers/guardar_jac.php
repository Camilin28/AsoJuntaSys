<?php
require_once('../includes/auth.php');
require('../config/db.php');

requireRole(['Presidente General']);

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/gestionar_jac.php");
    exit();
}

/* ===========================
   Datos del formulario
=========================== */

$nombre        = trim($_POST['nombre_jac'] ?? '');
$direccion     = trim($_POST['direccion'] ?? '');
$telefono      = trim($_POST['telefono'] ?? '');

$presidente_id = !empty($_POST['presidente_id']) ? intval($_POST['presidente_id']) : null;
$secretario_id = !empty($_POST['secretario_id']) ? intval($_POST['secretario_id']) : null;
$tesorero_id   = !empty($_POST['tesorero_id']) ? intval($_POST['tesorero_id']) : null;

/* ===========================
   Validaciones
=========================== */

if (empty($nombre) || empty($direccion)) {
    $_SESSION['error'] = "El nombre y la dirección son obligatorios.";
    header("Location: ../views/crear_jac.php");
    exit();
}

/* ===========================
   Evitar cargos duplicados
=========================== */

$cargosSeleccionados = array_filter([
    $presidente_id,
    $secretario_id,
    $tesorero_id
]);

if (count($cargosSeleccionados) !== count(array_unique($cargosSeleccionados))) {

    $_SESSION['error'] =
        "Una misma persona no puede ocupar más de un cargo.";

    header("Location: ../views/crear_jac.php");
    exit();
}

try {

    $pdo->beginTransaction();

    /* ===========================
       Crear JAC
    =========================== */

    $stmt = $pdo->prepare("
        INSERT INTO juntas (
            nombre,
            direccion,
            telefono,
            estado,
            fecha_creacion
        )
        VALUES (
            ?, ?, ?, 'Activa', CURDATE()
        )
    ");

    $stmt->execute([
        $nombre,
        $direccion,
        !empty($telefono) ? $telefono : null
    ]);

    $jac_id = $pdo->lastInsertId();

    /* ===========================
       Asignar Presidente
    =========================== */

    if ($presidente_id) {

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET
                jac_id = ?,
                cargo = 'Presidente'
            WHERE id = ?
        ");

        $stmt->execute([
            $jac_id,
            $presidente_id
        ]);
    }

    /* ===========================
       Asignar Secretario
    =========================== */

    if ($secretario_id) {

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET
                jac_id = ?,
                cargo = 'Secretario'
            WHERE id = ?
        ");

        $stmt->execute([
            $jac_id,
            $secretario_id
        ]);
    }

    /* ===========================
       Asignar Tesorero
    =========================== */

    if ($tesorero_id) {

        $stmt = $pdo->prepare("
            UPDATE usuarios
            SET
                jac_id = ?,
                cargo = 'Tesorero'
            WHERE id = ?
        ");

        $stmt->execute([
            $jac_id,
            $tesorero_id
        ]);
    }

    $pdo->commit();

    $_SESSION['mensaje'] =
        "La Junta de Acción Comunal fue creada correctamente.";

    header("Location: ../views/gestionar_jac.php");
    exit();

} catch (Exception $e) {

    $pdo->rollBack();

    $_SESSION['error'] =
        "Error al crear la JAC: " . $e->getMessage();

    header("Location: ../views/crear_jac.php");
    exit();
}