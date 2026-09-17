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

$id = intval($_GET['id']);

/* ===========================
   Obtener datos de la JAC
=========================== */

$stmt = $pdo->prepare("
    SELECT *
    FROM juntas
    WHERE id = ?
");

$stmt->execute([$id]);

$jac = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$jac) {
    die("JAC no encontrada.");
}

/* ===========================
   Obtener directivos
=========================== */

$stmt = $pdo->prepare("
    SELECT nombre, cargo
    FROM usuarios
    WHERE jac_id = ?
");

$stmt->execute([$id]);

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$presidente = 'No asignado';
$secretario = 'No asignado';
$tesorero   = 'No asignado';

foreach ($usuarios as $usuario) {

    if ($usuario['cargo'] === 'Presidente') {
        $presidente = $usuario['nombre'];
    }

    if ($usuario['cargo'] === 'Secretario') {
        $secretario = $usuario['nombre'];
    }

    if ($usuario['cargo'] === 'Tesorero') {
        $tesorero = $usuario['nombre'];
    }
}

/* ===========================
   Estadísticas
=========================== */

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM usuarios
    WHERE jac_id = ?
");
$stmt->execute([$id]);
$totalUsuarios = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM actas
    WHERE jac_id = ?
");
$stmt->execute([$id]);
$totalActas = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM agenda
    WHERE jac_id = ?
");
$stmt->execute([$id]);
$totalAgenda = $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COUNT(*)
    FROM recursos_financieros
    WHERE jac_id = ?
");
$stmt->execute([$id]);
$totalFinanzas = $stmt->fetchColumn();

?>

<!DOCTYPE html>
<html lang="es">

<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_AsojuntaSys.png">

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Detalle JAC - AsoJuntaSys</title>

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
    padding:25px;
    border-radius:20px;
    margin-bottom:25px;
    box-shadow:0 4px 15px rgba(0,0,0,.15);
}

.info-card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}

.stat-number{
    font-size:2rem;
    font-weight:bold;
    color:var(--verde);
}

</style>

</head>

<body>

<div class="container py-4">

    <div class="header-card">

        <h2>
            🏘️ <?= htmlspecialchars($jac['nombre']) ?>
        </h2>

        <p class="mb-0">
            Información institucional de la Junta de Acción Comunal
        </p>

    </div>

    <div class="row">

        <div class="col-lg-6 mb-4">

            <div class="card info-card h-100">

                <div class="card-body">

                    <h5>Información General</h5>

                    <hr>

                    <p>
                        <strong>Dirección:</strong><br>
                        <?= htmlspecialchars($jac['direccion']) ?>
                    </p>

                    <p>
                        <strong>Teléfono:</strong><br>
                        <?= htmlspecialchars($jac['telefono']) ?>
                    </p>

                    <p>
                        <strong>Estado:</strong><br>

                        <?php if($jac['estado'] == 'Activa'): ?>
                            <span class="badge bg-success">Activa</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactiva</span>
                        <?php endif; ?>

                    </p>

                    <p>
                        <strong>Fecha creación:</strong><br>
                        <?= $jac['fecha_creacion'] ?>
                    </p>

                </div>

            </div>

        </div>

        <div class="col-lg-6 mb-4">

            <div class="card info-card h-100">

                <div class="card-body">

                    <h5>Directivos Asignados</h5>

                    <hr>

                    <p>
                        👤 <strong>Presidente:</strong><br>
                        <?= htmlspecialchars($presidente) ?>
                    </p>

                    <p>
                        📝 <strong>Secretario:</strong><br>
                        <?= htmlspecialchars($secretario) ?>
                    </p>

                    <p>
                        💰 <strong>Tesorero:</strong><br>
                        <?= htmlspecialchars($tesorero) ?>
                    </p>

                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col-md-3 mb-3">

            <div class="card info-card text-center">

                <div class="card-body">

                    <div class="stat-number">
                        <?= $totalUsuarios ?>
                    </div>

                    <div>Usuarios</div>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card info-card text-center">

                <div class="card-body">

                    <div class="stat-number">
                        <?= $totalActas ?>
                    </div>

                    <div>Actas</div>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card info-card text-center">

                <div class="card-body">

                    <div class="stat-number">
                        <?= $totalAgenda ?>
                    </div>

                    <div>Eventos</div>

                </div>

            </div>

        </div>

        <div class="col-md-3 mb-3">

            <div class="card info-card text-center">

                <div class="card-body">

                    <div class="stat-number">
                        <?= $totalFinanzas ?>
                    </div>

                    <div>Movimientos</div>

                </div>

            </div>

        </div>

    </div>

    <div class="mt-4 d-flex gap-2">

        <a href="usuarios_jac.php?id=<?= $jac['id'] ?>"
           class="btn btn-success">
            👥 Gestionar Usuarios
        </a>

        <a href="editar_jac.php?id=<?= $jac['id'] ?>"
           class="btn btn-warning">
            ✏️ Editar JAC
        </a>

        <a href="gestionar_jac.php"
           class="btn btn-secondary">
            ← Volver
        </a>

    </div>

</div>

</body>
</html>