<?php
session_start();

// Verificar rol
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];

// 🔹 Ejemplo de conteos (cuando tengas BD lo ajustamos)
$totalActas = 12;
$totalAgenda = 5;
$totalDocumentos = 20;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Secretaría - JAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff9c4; /* Fondo claro en vez de negro */
        }

        .navbar {
            background: linear-gradient(135deg, #2E7D32, #1b5e20) !important; /* Verde institucional */
        }
        .navbar .nav-link:hover {
            color: #FBC02D !important;
        }

        .stats-row .card {
            border-radius: 12px;
            text-align: center;
            padding: 15px;
            font-weight: bold;
            color: white;
        }

        /* Paleta oficial */
        .stat-actas { background: linear-gradient(135deg, #2E7D32, #1b5e20); } /* Verde */
        .stat-agenda { background: linear-gradient(135deg, #FBC02D, #f57f17); color: #000; } /* Amarillo */
        .stat-docs { background: linear-gradient(135deg, #4caf50, #2e7d32); } /* Verde claro complementario */

        .custom-card {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
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

        /* Headers con la paleta */
        .card-actas .card-header {
            background: linear-gradient(135deg, #2E7D32, #1b5e20); /* Verde */
        }

        .card-agenda .card-header {
            background: linear-gradient(135deg, #FBC02D, #f57f17); /* Amarillo */
            color: #000; /* Contraste */
        }

        .card-documentos .card-header {
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

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard_secretario.php">Junta de Acción Comunal</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Bienvenida, <?= htmlspecialchars($nombre) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../public/logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2 class="mb-4 text-dark">📋 Panel de la Secretaría de la JAC</h2>

    <!-- 🔹 Mini estadísticas -->
    <div class="row stats-row mb-4">
        <div class="col-md-4">
            <div class="card stat-actas">
                <h5>📄 Actas</h5>
                <p><?= $totalActas ?> registradas</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-agenda">
                <h5>🗓️ Agenda</h5>
                <p><?= $totalAgenda ?> eventos</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card stat-docs">
                <h5>🗂️ Documentos</h5>
                <p><?= $totalDocumentos ?> almacenados</p>
            </div>
        </div>
    </div>

    <!-- 🔹 Cards principales -->
    <div class="row">
        <div class="col-md-4">
            <div class="card custom-card card-actas mb-4">
                <div class="card-header">📄 Actas de Reuniones</div>
                <div class="card-body">
                    <p class="card-text">Consultar y registrar actas oficiales de reuniones.</p>
                    <a href="actas.php" class="btn btn-success btn-custom">Ver Actas</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card card-agenda mb-4">
                <div class="card-header">🗓️ Agenda</div>
                <div class="card-body">
                    <p class="card-text">Organizar el calendario de reuniones y eventos.</p>
                    <a href="agenda.php" class="btn btn-warning btn-custom text-dark">Ver Agenda</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card custom-card card-documentos mb-4">
                <div class="card-header">🗂️ Documentos Oficiales</div>
                <div class="card-body">
                    <p class="card-text">Gestionar cartas, certificaciones y otros documentos.</p>
                    <a href="documentos.php" class="btn btn-success btn-custom">Ver Documentos</a>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
