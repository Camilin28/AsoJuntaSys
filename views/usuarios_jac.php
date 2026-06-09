<?php
session_start();
require('../config/db.php');

if (
    !isset($_SESSION['usuario_id']) ||
    $_SESSION['usuario_rol'] !== 'Presidente General'
) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: gestionar_jac.php");
    exit();
}

$jac_id = intval($_GET['id']);

$stmt = $pdo->prepare("
    SELECT *
    FROM juntas
    WHERE id = ?
");
$stmt->execute([$jac_id]);

$jac = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jac) {
    die("JAC no encontrada.");
}

/*
|--------------------------------------------------------------------------
| Usuarios pertenecientes a la JAC
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        id,
        nombre,
        email,
        rol,
        fecha_registro
    FROM usuarios
    WHERE jac_id = ?
    ORDER BY rol, nombre
");

$stmt->execute([$jac_id]);

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Estadísticas
|--------------------------------------------------------------------------
*/

$totalUsuarios = count($usuarios);

$presidentes = 0;
$secretarios = 0;
$tesoreros = 0;

foreach ($usuarios as $usuario) {

    if ($usuario['rol'] === 'Presidentes de JAC') {
        $presidentes++;
    }

    if ($usuario['rol'] === 'Secretaría') {
        $secretarios++;
    }

    if ($usuario['rol'] === 'Tesorería') {
        $tesoreros++;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Usuarios de la JAC</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

:root{
    --verde:#2E7D32;
    --verde-claro:#81C784;
    --amarillo:#FBC02D;
}

body{
    background:#f5f7fa;
}

.header-card{
    background:linear-gradient(135deg,var(--verde),var(--verde-claro));
    color:white;
    border-radius:20px;
    padding:25px;
    margin-bottom:25px;
}

.card-custom{
    border:none;
    border-radius:18px;
    box-shadow:0 3px 12px rgba(0,0,0,.08);
}

.stat-number{
    font-size:2rem;
    font-weight:bold;
    color:var(--verde);
}

.table-card{
    border:none;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 3px 12px rgba(0,0,0,.08);
}

.table thead{
    background:var(--verde);
    color:white;
}

.badge-presidente{
    background:#198754;
}

.badge-secretario{
    background:#0dcaf0;
}

.badge-tesorero{
    background:#ffc107;
    color:black;
}

</style>

</head>
<body>

<div class="container py-4">

    <div class="header-card">

        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h2 class="mb-1">
                    👥 Usuarios de la JAC
                </h2>

                <h5>
                    <?= htmlspecialchars($jac['nombre']) ?>
                </h5>
            </div>

            <div>
                <a href="ver_jac.php?id=<?= $jac_id ?>"
                   class="btn btn-light">
                    ← Volver
                </a>
            </div>

        </div>

    </div>

    <div class="row mb-4">

        <div class="col-md-4 mb-3">

            <div class="card card-custom">

                <div class="card-body text-center">

                    <div class="stat-number">
                        <?= $totalUsuarios ?>
                    </div>

                    <div>Total Usuarios</div>

                </div>

            </div>

        </div>

        <div class="col-md-8">

            <div class="card card-custom">

                <div class="card-body">

                    <div class="row text-center">

                        <div class="col">
                            <h4><?= $presidentes ?></h4>
                            <small>Presidentes</small>
                        </div>

                        <div class="col">
                            <h4><?= $secretarios ?></h4>
                            <small>Secretarios</small>
                        </div>

                        <div class="col">
                            <h4><?= $tesoreros ?></h4>
                            <small>Tesoreros</small>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card table-card">

        <div class="card-header bg-white">

            <h5 class="mb-0">
                Listado de usuarios
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Cargo</th>
                            <th>Registro</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if(empty($usuarios)): ?>

                        <tr>
                            <td colspan="4" class="text-center">
                                No hay usuarios asociados.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach($usuarios as $usuario): ?>

                        <tr>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($usuario['nombre']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($usuario['email']) ?>
                            </td>

                            <td>

                                <?php

                                $clase = 'bg-secondary';

                                if($usuario['rol'] == 'Presidentes de JAC'){
                                    $clase = 'badge-presidente';
                                }

                                if($usuario['rol'] == 'Secretaría'){
                                    $clase = 'badge-secretario';
                                }

                                if($usuario['rol'] == 'Tesorería'){
                                    $clase = 'badge-tesorero';
                                }

                                ?>

                                <span class="badge <?= $clase ?>">
                                    <?= htmlspecialchars($usuario['rol']) ?>
                                </span>

                            </td>

                            <td>
                                <?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?>
                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>
</html>