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
    SELECT j.nombre AS nombre_jac,
           u.*
    FROM usuarios u
    INNER JOIN juntas j
        ON j.id = u.jac_id
    WHERE u.jac_id = ?
    ORDER BY u.rol
");

$stmt->execute([$jac_id]);

$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nombreJac = $usuarios[0]['nombre_jac'] ?? 'JAC';
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Usuarios JAC</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

:root{
    --verde:#2E7D32;
    --verde-claro:#81C784;
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
}

.table-card{
    border:none;
    border-radius:15px;
    overflow:hidden;
    box-shadow:0 4px 15px rgba(0,0,0,.08);
}

</style>

</head>

<body>

<div class="container py-4">

<div class="header-card">

    <h2>👥 Usuarios de <?= htmlspecialchars($nombreJac) ?></h2>

    <p class="mb-0">
        Gestión de integrantes de la Junta de Acción Comunal
    </p>

</div>

<div class="card table-card">

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover align-middle">

                <thead class="table-success">

                    <tr>

                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>ID Usuario</th>

                    </tr>

                </thead>

                <tbody>

                <?php foreach($usuarios as $usuario): ?>

                    <tr>

                        <td>
                            <?= htmlspecialchars($usuario['nombre']) ?>
                        </td>

                        <td>
                            <?= htmlspecialchars($usuario['email']) ?>
                        </td>

                        <td>

                            <?php

                            if($usuario['rol']=='Presidentes de JAC'){
                                echo '<span class="badge bg-primary">Presidente</span>';
                            }
                            elseif($usuario['rol']=='Secretaría'){
                                echo '<span class="badge bg-warning text-dark">Secretaría</span>';
                            }
                            elseif($usuario['rol']=='Tesorería'){
                                echo '<span class="badge bg-success">Tesorería</span>';
                            }
                            else{
                                echo $usuario['rol'];
                            }

                            ?>

                        </td>

                        <td>
                            <?= $usuario['id'] ?>
                        </td>

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