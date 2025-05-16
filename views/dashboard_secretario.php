<?php
session_start();

// Verificar que el usuario ha iniciado sesión y tiene el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Secretaria de JAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --fondo-color: #000000;
        }

        body {
            background-color: var(--fondo-color);
        }

        .navbar {
            background: linear-gradient(135deg, #6f42c1, #4b0082) !important;
        }

        .custom-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 4px 4px 10px #a020f0, -4px -4px 10px #8a2be2;
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

        .card-actas .card-header {
            background: linear-gradient(135deg, #6f42c1, #5a189a);
        }

        .card-agenda .card-header {
            background: linear-gradient(135deg, #20c997, #198754);
        }

        .card-documentos .card-header {
            background: linear-gradient(135deg, #fd7e14, #e8590c);
        }

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
                    <span class="nav-link text-white">Bienvenida, <?php echo htmlspecialchars($nombre); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../public/logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4 text-white">Panel de la Secretaria de la JAC</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card custom-card card-actas mb-4">
                <div class="card-header">Actas de Reuniones</div>
                <div class="card-body">
                    <p class="card-text">Consultar y registrar actas oficiales de reuniones.</p>
                    <a href="actas.php" class="btn btn-primary btn-custom">Ver Actas</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card card-agenda mb-4">
                <div class="card-header">Agenda</div>
                <div class="card-body">
                    <p class="card-text">Organizar el calendario de reuniones y eventos.</p>
                    <a href="agenda.php" class="btn btn-success btn-custom">Ver Agenda</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card card-documentos mb-4">
                <div class="card-header">Documentos Oficiales</div>
                <div class="card-body">
                    <p class="card-text">Gestionar cartas, certificaciones y otros documentos.</p>
                    <a href="documentos.php" class="btn btn-warning btn-custom text-white">Ver Documentos</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
