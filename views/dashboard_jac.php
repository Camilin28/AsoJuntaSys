<?php
session_start();

// Verificar que el usuario ha iniciado sesión y tiene el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Presidentes de JAC') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Presidente de JAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5; /* Fondo claro */
        }

        .navbar {
            background: linear-gradient(135deg, #2E7D32, #1b5e20) !important; /* Verde institucional */
            box-shadow: 0px 4px 15px rgba(0,0,0,0.3);
        }
        .navbar .nav-link:hover {
            color: #FBC02D !important;
        }

        .custom-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.2);
            transition: transform 0.2s ease-in-out;
            border: none;
        }

        .custom-card:hover {
            transform: scale(1.03);
        }

        .custom-card .card-header {
            color: white;
            font-weight: bold;
            padding: 12px;
        }

        /* Colores institucionales en cards */
        .card-reuniones .card-header {
            background: linear-gradient(135deg, #2E7D32, #1b5e20); /* Verde institucional */
        }

        .card-proyectos .card-header {
            background: linear-gradient(135deg, #FBC02D, #f57f17); /* Amarillo */
            color: #000;
        }

        .card-informes .card-header {
            background: linear-gradient(135deg, #4caf50, #2e7d32); /* Verde claro */
        }

        .custom-card .card-body {
            background-color: white;
            color: #333;
            padding: 20px;
        }

        .btn-custom {
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard_jac.php">Junta de Acción Comunal</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Bienvenido, <?= htmlspecialchars($nombre) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../public/logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4 text-dark">📌 Panel del Presidente de la JAC</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card custom-card card-reuniones mb-4">
                <div class="card-header">📅 Reuniones</div>
                <div class="card-body">
                    <p class="card-text">Consultar y gestionar reuniones comunitarias.</p>
                    <a href="reuniones.php" class="btn btn-success btn-custom">Ver Reuniones</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card card-proyectos mb-4">
                <div class="card-header">📂 Proyectos</div>
                <div class="card-body">
                    <p class="card-text">Revisar proyectos activos y propuestos en la comunidad.</p>
                    <a href="proyectos.php" class="btn btn-warning btn-custom text-dark">Ver Proyectos</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card card-informes mb-4">
                <div class="card-header">📑 Informes</div>
                <div class="card-body">
                    <p class="card-text">Consultar informes de gestión y avances.</p>
                    <a href="informes.php" class="btn btn-success btn-custom">Ver Informes</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
