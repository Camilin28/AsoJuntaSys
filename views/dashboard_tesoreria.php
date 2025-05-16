<?php
session_start();

// Verificar que el usuario ha iniciado sesión y tiene el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Tesorero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --fondo-color: #2c2f33; /* Gris oscuro */
            --navbar-gradient: linear-gradient(135deg, #6a0dad,rgb(21, 90, 219)); /* Morado a azul */
            --ingresos-gradient: linear-gradient(135deg, #004080, #0059b3); /* Azul oscuro */
            --egresos-gradient: linear-gradient(135deg, #990000, #cc0000); /* Rojo oscuro */
            --reportes-gradient: linear-gradient(135deg, #cc6600, #ff9933); /* Naranja oscuro */
        }

        body {
            background-color: var(--fondo-color);
        }

        .navbar {
            background: var(--navbar-gradient) !important;
            box-shadow: 4px 4px 50px #A203FE;
        }

        .custom-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 4px 4px 15px #a020f0, -4px -4px 15px #0000ff;   
            transition: transform 0.2s ease-in-out;
            border: none;
        }

        .custom-card:hover {
            transform: scale(1.03);
        }

        .custom-card .card-header {
            color: white;
            font-weight: bold;
            padding: 10px 15px;
        }

        /* Colores para las tarjetas */
        .card-ingresos .card-header {
            background: var(--ingresos-gradient);
        }

        .card-egresos .card-header {
            background: var(--egresos-gradient);
        }

        .card-reportes .card-header {
            background: var(--reportes-gradient);
        }

        /* Fondo blanco para el contenido de las tarjetas */
        .custom-card .card-body {
            background-color: white;
            color: #333;
            padding: 20px;
        }

        .btn-custom {
            border-radius: 10px;
            box-shadow: 2px 2px 5px rgba(9, 9, 9, 0.92);
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
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
    <h2 class="mb-4 text-white">Panel del Tesorero</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card custom-card card-ingresos border-0 mb-4">
                <div class="card-header">Ingresos</div>
                <div class="card-body">
                    <p class="card-text">Consultar y gestionar los ingresos de la comunidad.</p>
                    <a href="ingresos.php" class="btn btn-primary btn-custom">Ver Ingresos</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card card-egresos border-0 mb-4">
                <div class="card-header">Egresos</div>
                <div class="card-body">
                    <p class="card-text">Revisar y controlar los egresos comunitarios.</p>
                    <a href="egresos.php" class="btn btn-danger btn-custom">Ver Egresos</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card card-reportes border-0 mb-4">
                <div class="card-header">Reportes</div>
                <div class="card-body">
                    <p class="card-text">Generar y consultar reportes financieros.</p>
                    <a href="reportes.php" class="btn btn-warning btn-custom text-white">Ver Reportes</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
