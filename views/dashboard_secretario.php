<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];

// 🔹 KPIs dinámicos
$totalActas = $pdo->query("SELECT COUNT(*) AS total FROM actas")->fetch(PDO::FETCH_ASSOC)['total'];
$totalDocumentos = $pdo->query("SELECT COUNT(*) AS total FROM documentos")->fetch(PDO::FETCH_ASSOC)['total'];
$totalAgenda = $pdo->query("SELECT COUNT(*) AS total FROM correspondencia")->fetch(PDO::FETCH_ASSOC)['total'];

// 🔹 Últimos registros
$ultimosDocs = $pdo->query("SELECT d.titulo, c.nombre AS categoria, d.fecha_subida 
    FROM documentos d
    JOIN categorias_documentos c ON d.categoria_id = c.id
    ORDER BY d.fecha_subida DESC LIMIT 5");

$ultimasActas = $pdo->query("SELECT d.titulo, a.fecha_reunion, a.lugar
    FROM actas a
    LEFT JOIN documentos d ON a.documento_id = d.id
    ORDER BY a.fecha_reunion DESC LIMIT 5");

$ultimasCorr = $pdo->query("SELECT tipo, asunto, fecha, estado
    FROM correspondencia
    ORDER BY fecha DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Secretaría - JAC</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #fff9c4; /* Fondo claro */
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.navbar {
    background: linear-gradient(135deg, #2E7D32, #1b5e20) !important;
}
.navbar .nav-link:hover { color: #FBC02D !important; }

.stats-row .card {
    border-radius: 12px;
    text-align: center;
    padding: 15px;
    font-weight: bold;
    color: white;
}

.stat-actas { background: linear-gradient(135deg, #2E7D32, #1b5e20); } 
.stat-agenda { background: linear-gradient(135deg, #FBC02D, #f57f17); color: #000; } 
.stat-docs { background: linear-gradient(135deg, #4caf50, #2e7d32); } 

.custom-card {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 2px 2px 10px rgba(0,0,0,0.2);
    transition: transform 0.2s ease-in-out;
    border: none;
}
.custom-card:hover { transform: scale(1.03); }

.custom-card .card-header { color: white; font-weight: bold; padding: 12px; }
.card-actas .card-header { background: linear-gradient(135deg, #2E7D32, #1b5e20); }
.card-agenda .card-header { background: linear-gradient(135deg, #FBC02D, #f57f17); color: #000; }
.card-documentos .card-header { background: linear-gradient(135deg, #4caf50, #2e7d32); }

.custom-card .card-body { background-color: white; color: #333; padding: 20px; }
.btn-custom { border-radius: 10px; box-shadow: 2px 2px 5px rgba(0,0,0,0.2); }
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

<!-- Mini estadísticas dinámicas -->
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

<!-- Cards principales -->
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

<!-- Últimos registros -->
<div class="row">
    <div class="col-md-4">
        <h4>Últimas Actas</h4>
        <ul class="list-group">
            <?php while($acta = $ultimasActas->fetch(PDO::FETCH_ASSOC)): ?>
                <li class="list-group-item"><?= htmlspecialchars($acta['titulo'] ?? "Sin título") ?> - <?= $acta['fecha_reunion'] ?> (<?= $acta['lugar'] ?>)</li>
            <?php endwhile; ?>
        </ul>
    </div>
    <div class="col-md-4">
        <h4>Últimos Documentos</h4>
                <ul class="list-group">
            <?php while($doc = $ultimosDocs->fetch(PDO::FETCH_ASSOC)): ?>
                <li class="list-group-item"><?= htmlspecialchars($doc['titulo']) ?> (<?= $doc['categoria'] ?>) - <?= $doc['fecha_subida'] ?></li>
            <?php endwhile; ?>
        </ul>
    </div>
    <div class="col-md-4">
        <h4>Últimas Correspondencias</h4>
        <ul class="list-group">
            <?php while($corr = $ultimasCorr->fetch(PDO::FETCH_ASSOC)): ?>
                <li class="list-group-item"><?= $corr['tipo'] ?>: <?= $corr['asunto'] ?> (<?= $corr['estado'] ?>)</li>
            <?php endwhile; ?>
        </ul>
    </div>
</div>

</div>
</body>
</html>
