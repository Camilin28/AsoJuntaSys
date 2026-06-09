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

$stmt = $pdo->prepare("
    SELECT *
    FROM usuarios
    WHERE jac_id = ?
");
$stmt->execute([$id]);

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalUsuarios = count($usuarios);
?>

<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Detalle JAC</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

:root{
    --verde:#2E7D32;
    --verde-claro:#81C784;
    --amarillo:#FBC02D;
}

body{
    background:#f4f6f9;
}

.header-card{
    background:linear-gradient(135deg,var(--verde),var(--verde-claro));
    color:white;
    padding:25px;
    border-radius:20px;
    margin-bottom:25px;
}

.info-card{
    border:none;
    border-radius:15px;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<div class="header-card">

    <h2>🏘️ <?= htmlspecialchars($jac['nombre']) ?></h2>

    <p class="mb-0">
        Información completa de la Junta de Acción Comunal
    </p>

</div>

<div class="row">

    <div class="col-lg-6">

        <div class="card info-card mb-4">

            <div class="card-body">

                <h5>Información General</h5>

                <hr>

                <p>
                    <strong>ID:</strong>
                    <?= $jac['id'] ?>
                </p>

                <p>
                    <strong>Dirección:</strong>
                    <?= htmlspecialchars($jac['direccion']) ?>
                </p>

                <p>
                    <strong>Teléfono:</strong>
                    <?= htmlspecialchars($jac['telefono']) ?>
                </p>

                <p>
                    <strong>Estado:</strong>

                    <?php if($jac['estado']=='Activa'): ?>
                        <span class="badge bg-success">Activa</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Inactiva</span>
                    <?php endif; ?>

                </p>

                <p>
                    <strong>Fecha creación:</strong>
                    <?= $jac['fecha_creacion'] ?>
                </p>

            </div>

        </div>

    </div>

    <div class="col-lg-6">

        <div class="card info-card">

            <div class="card-body text-center">

                <h5>Total Usuarios</h5>

                <div style="font-size:55px;font-weight:bold;color:#2E7D32;">
                    <?= $totalUsuarios ?>
                </div>

            </div>

        </div>

    </div>

</div>

<div class="card info-card">

    <div class="card-body">

        <h5>Miembros de la JAC</h5>

        <div class="table-responsive mt-3">

            <table class="table table-hover">

                <thead class="table-success">

                    <tr>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach($usuarios as $u): ?>

                    <tr>

                        <td><?= htmlspecialchars($u['nombre']) ?></td>

                        <td><?= htmlspecialchars($u['email']) ?></td>

                        <td><?= htmlspecialchars($u['rol']) ?></td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<div class="mt-4">

    <a href="gestionar_jac.php" class="btn btn-secondary">
        ← Volver
    </a>

</div>

</div>

</body>
</html>