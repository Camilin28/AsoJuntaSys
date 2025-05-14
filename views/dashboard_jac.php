<?php
session_start();

// Verificar que el usuario ha iniciado sesión y tiene el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Presidentes de JAC') {
    header("Location: login.php");
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
        .custom-card {
            background: linear-gradient(145deg,#ffff);
            border-radius: 15px;
            box-shadow: 4px 4px 10px #00ff00, -4px -4px 10px #0000ff;
            transition: transform 0.2s ease-in-out;
        }

        .custom-card:hover {
            transform: scale(1.03);
        }

        .custom-card .card-header {
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            font-weight: bold;
        }

        .btn-custom {
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(9, 9, 9, 0.92);
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Junta de Acción Comunal</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Bienvenido, <?php echo htmlspecialchars($nombre); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../public/logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4">Panel del Presidente de la JAC</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card custom-card border-0 mb-4">
                <div class="card-header bg-primary text-white">Reuniones</div>
                <div class="card-body">
                    <p class="card-text">Consultar y gestionar reuniones comunitarias.</p>
                    <a href="reuniones.php" class="btn btn-primary btn-custom">Ver Reuniones</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card border-0 mb-4">
                <div class="card-header bg-success text-white">Proyectos</div>
                <div class="card-body">
                    <p class="card-text">Revisar proyectos activos y propuestos en la comunidad.</p>
                    <a href="proyectos.php" class="btn btn-success btn-custom">Ver Proyectos</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card border-0 mb-4">
                <div class="card-header bg-warning text-white">Informes</div>
                <div class="card-body">
                    <p class="card-text">Consultar informes de gestión y avances.</p>
                    <a href="informes.php" class="btn btn-warning btn-custom text-white">Ver Informes</a>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
