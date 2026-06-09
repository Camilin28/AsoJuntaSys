<?php
session_start();
require('../config/db.php');

if (
    !isset($_SESSION['usuario_id']) ||
    !isset($_SESSION['usuario_rol']) ||
    $_SESSION['usuario_rol'] !== 'Presidente General'
) {
    header("Location: login.php");
    exit();
}

/* ===========================
   Obtener usuarios disponibles
   =========================== */

$stmt = $pdo->prepare("
    SELECT id, nombre
    FROM usuarios
    WHERE rol = 'Presidentes de JAC'
    AND jac_id IS NULL
    ORDER BY nombre
");
$stmt->execute();
$presidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT id, nombre
    FROM usuarios
    WHERE rol = 'Secretaría'
    AND jac_id IS NULL
    ORDER BY nombre
");
$stmt->execute();
$secretarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT id, nombre
    FROM usuarios
    WHERE rol = 'Tesorería'
    AND jac_id IS NULL
    ORDER BY nombre
");
$stmt->execute();
$tesoreros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Crear JAC - AsoJuntaSys</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

:root{
    --verde-principal:#2E7D32;
    --verde-secundario:#81C784;
    --amarillo:#FBC02D;
    --amarillo-suave:#FFF9C4;
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

.section-title{
    color:var(--verde-principal);
    font-weight:bold;
    margin-bottom:15px;
}

</style>

</head>

<body>

<div class="container py-4">

    <div class="header-card">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h2>🏘️ Crear Nueva JAC</h2>
                <p class="mb-0">
                    Registre una nueva Junta de Acción Comunal y asigne sus responsables.
                </p>
            </div>

            <a href="gestionar_jac.php" class="btn btn-volver">
                ← Volver
            </a>

        </div>

    </div>

    <div class="card form-card">

        <div class="card-header-custom">
            <h5 class="mb-0">
                Información de la Junta
            </h5>
        </div>

        <div class="card-body p-4">

            <form action="../controllers/guardar_jac.php" method="POST">

                <h5 class="section-title">
                    Datos Generales
                </h5>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Nombre de la JAC
                        </label>

                        <input
                            type="text"
                            name="nombre_jac"
                            class="form-control"
                            required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">
                            Teléfono
                        </label>

                        <input
                            type="text"
                            name="telefono"
                            class="form-control">
                    </div>

                </div>

                <div class="mb-4">
                    <label class="form-label">
                        Dirección
                    </label>

                    <input
                        type="text"
                        name="direccion"
                        class="form-control"
                        required>
                </div>

                <hr>

                <h5 class="section-title">
                    Asignación de Responsables
                </h5>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Presidente
                        </label>

                        <select
                            name="presidente_id"
                            class="form-select"
                            >

                            <option value="">No asignar por ahora</option>

                            <?php foreach($presidentes as $p): ?>

                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nombre']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Secretaría
                        </label>

                        <select
                            name="secretario_id"
                            class="form-select"
                            >

                            <option value="">No asignar por ahora</option>

                            <?php foreach($secretarios as $s): ?>

                                <option value="<?= $s['id'] ?>">
                                    <?= htmlspecialchars($s['nombre']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="form-label">
                            Tesorería
                        </label>

                        <select
                            name="tesorero_id"
                            class="form-select"
                            >

                            <option value="">No asignar por ahora</option>

                            <?php foreach($tesoreros as $t): ?>

                                <option value="<?= $t['id'] ?>">
                                    <?= htmlspecialchars($t['nombre']) ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

                <div class="text-end mt-4">

                    <button
                        type="submit"
                        class="btn btn-guardar btn-lg text-white">

                        💾 Crear JAC

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

</body>
</html>