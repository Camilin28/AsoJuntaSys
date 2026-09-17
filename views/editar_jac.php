<?php
session_start();
require('../config/db.php');
require_once('../includes/auth.php');

/* ===========================
   Seguridad
   =========================== */

if (
    !isset($_SESSION['usuario_id']) ||
    !isset($_SESSION['usuario_rol']) ||
    $_SESSION['usuario_rol'] !== 'Presidente General'
) {
    header("Location: login.php");
    exit();
}

$csrfToken = generarTokenCSRF();

/* ===========================
   Validar ID
   =========================== */

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: gestionar_jac.php");
    exit();
}

$id = intval($_GET['id']);

/* ===========================
   Obtener JAC
   =========================== */

$stmt = $pdo->prepare("
    SELECT *
    FROM juntas
    WHERE id = ?
");

$stmt->execute([$id]);

$jac = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jac) {
    header("Location: gestionar_jac.php");
    exit();
}

/* ===========================
   Total usuarios
   =========================== */

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM usuarios
    WHERE jac_id = ?
");

$stmt->execute([$id]);

$totalUsuarios = $stmt->fetchColumn();

/* ===========================
   Actualizar datos
   =========================== */

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    validarTokenCSRF($_POST['csrf_token'] ?? '');

    $nombre = trim($_POST['nombre']);
    $direccion = trim($_POST['direccion']);
    $telefono = trim($_POST['telefono']);
    $estado = $_POST['estado'];

    if (!empty($nombre) && !empty($direccion)) {

        $stmt = $pdo->prepare("
            UPDATE juntas
            SET nombre = ?,
                direccion = ?,
                telefono = ?,
                estado = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $nombre,
            $direccion,
            !empty($telefono) ? $telefono : null,
            $estado,
            $id
        ]);

        header("Location: gestionar_jac.php?updated=1");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_web.png">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Editar JAC - AsoJuntaSys</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

:root{
    --verde-principal:#2E7D32;
    --verde-secundario:#81C784;
    --amarillo:#FBC02D;
    --gris:#424242;
}

body{
    background:#f5f7fa;
}

.header-card{
    background:linear-gradient(
        135deg,
        var(--verde-principal),
        var(--verde-secundario)
    );
    color:white;
    border-radius:20px;
    padding:25px;
    margin-bottom:25px;
    box-shadow:0 4px 20px rgba(0,0,0,.15);
}

.form-card{
    border:none;
    border-radius:20px;
    overflow:hidden;
    box-shadow:0 3px 15px rgba(0,0,0,.08);
}

.card-header-custom{
    background:var(--verde-principal);
    color:white;
    padding:18px;
}

.form-label{
    font-weight:600;
    color:var(--gris);
}

.btn-guardar{
    background:var(--verde-principal);
    border:none;
    font-weight:bold;
}

.btn-guardar:hover{
    background:#1f5d24;
}

.btn-volver{
    background:var(--amarillo);
    color:black;
    border:none;
    font-weight:bold;
}

.btn-volver:hover{
    background:#e5af00;
    color:black;
}

.info-box{
    background:white;
    border-left:5px solid var(--amarillo);
    padding:15px;
    border-radius:10px;
    margin-bottom:15px;
    box-shadow:0 2px 8px rgba(0,0,0,.05);
}

.input-group-text{
    background:var(--verde-secundario);
    border:none;
    color:#000;
    font-weight:600;
}

.stat-card{
    border:none;
    border-radius:15px;
    box-shadow:0 3px 12px rgba(0,0,0,.08);
}

.stat-number{
    font-size:2rem;
    font-weight:bold;
    color:var(--verde-principal);
}

</style>

</head>

<body>

<div class="container py-4">

    <div class="header-card">

        <div class="d-flex justify-content-between align-items-center flex-wrap">

            <div>
                <h2 class="mb-2">
                    ✏️ Editar Junta de Acción Comunal
                </h2>

                <p class="mb-0">
                    Actualiza la información general de la Junta.
                </p>
            </div>

            <a href="gestionar_jac.php" class="btn btn-volver">
                ← Volver
            </a>

        </div>

    </div>

    <div class="row mb-4">

        <div class="col-md-4 mb-3">

            <div class="card stat-card">

                <div class="card-body text-center">

                    <div class="stat-number">
                        <?= $totalUsuarios ?>
                    </div>

                    <div>
                        Usuarios Asociados
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-8">

            <div class="info-box">
                <strong>ID:</strong>
                <?= $jac['id'] ?>
            </div>

            <div class="info-box">
                <strong>Fecha de creación:</strong>

                <?= !empty($jac['fecha_creacion'])
                    ? date('d/m/Y', strtotime($jac['fecha_creacion']))
                    : 'No registrada'; ?>
            </div>

        </div>

    </div>

    <div class="card form-card">

        <div class="card-header-custom">
            <h5 class="mb-0">
                Información General
            </h5>
        </div>

        <div class="card-body p-4">

            <form method="POST">

                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div class="mb-4">

                    <label class="form-label">
                        Nombre de la Junta
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            🏘️
                        </span>

                        <input
                            type="text"
                            name="nombre"
                            class="form-control"
                            value="<?= htmlspecialchars($jac['nombre']) ?>"
                            required>

                    </div>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Dirección
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            📍
                        </span>

                        <input
                            type="text"
                            name="direccion"
                            class="form-control"
                            value="<?= htmlspecialchars($jac['direccion']) ?>"
                            required>

                    </div>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Teléfono
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            📞
                        </span>

                        <input
                            type="text"
                            name="telefono"
                            class="form-control"
                            value="<?= htmlspecialchars($jac['telefono']) ?>">

                    </div>

                </div>

                <div class="mb-4">

                    <label class="form-label">
                        Estado
                    </label>

                    <select
                        name="estado"
                        class="form-select">

                        <option value="Activa"
                            <?= $jac['estado'] == 'Activa' ? 'selected' : '' ?>>
                            Activa
                        </option>

                        <option value="Inactiva"
                            <?= $jac['estado'] == 'Inactiva' ? 'selected' : '' ?>>
                            Inactiva
                        </option>

                    </select>

                </div>

                <div class="d-flex justify-content-between flex-wrap gap-2">

                    <a
                        href="usuarios_jac.php?id=<?= $id ?>"
                        class="btn btn-outline-success">

                        👥 Ver Usuarios

                    </a>

                    <div>

                        <a
                            href="gestionar_jac.php"
                            class="btn btn-secondary">

                            Cancelar

                        </a>

                        <button
                            type="submit"
                            class="btn btn-guardar text-white">

                            💾 Actualizar JAC

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>