<?php
session_start();
require('../config/db.php'); // 🔹 Para traer datos reales

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Presidente General') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];

// 🔹 Totales rápidos
$totalUsuarios = $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$totalActas = $pdo->query("SELECT COUNT(*) FROM actas")->fetchColumn();
$totalDocumentos = $pdo->query("SELECT COUNT(*) FROM documentos")->fetchColumn();
$totalEventos = $pdo->query("SELECT COUNT(*) FROM agenda")->fetchColumn();

// 🔹 Últimos registros
$ultimosDocumentos = $pdo->query("SELECT titulo, estado, fecha_subida FROM documentos ORDER BY fecha_subida DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$ultimasActas = $pdo->query("SELECT titulo, fecha_reunion, lugar FROM actas ORDER BY fecha_reunion DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard Presidente</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
.dashboard-container { display: flex; height: 100vh; }
.sidebar { background-color: #2E7D32; padding: 20px; width: 250px; color: #fff; display: flex; flex-direction: column; border-radius: 0 15px 15px 0; }
.sidebar a { color: #fff; padding: 10px; text-decoration: none; border-radius: 6px; }
.sidebar a:hover { background-color: #FBC02D; color: #333; }
.content { flex: 1; padding: 30px; background-color: #fff; border-radius: 15px 0 0 15px; margin: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow-y: auto; }
h2 { color: #2E7D32; }
.card-resumen { background-color: #E8F5E9; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 3px 8px rgba(0,0,0,0.1); }
.card-resumen h3 { color: #2E7D32; font-size: 24px; }
.card-resumen p { color: #333; font-weight: bold; font-size: 18px; }
.section-title { border-left: 5px solid #2E7D32; padding-left: 10px; margin-top: 30px; color: #1b5e20; }
.table th { background-color: #2E7D32; color: #fff; }
</style>
</head>
<body>
<div class="dashboard-container">
    <div class="sidebar">
        <h4>👔 Presidente: <?= htmlspecialchars($nombre) ?></h4>
        <a href="dashboard_presidente.php">Panel General</a>
        <a href="../public/listar_usuario.php">Gestión de Usuarios</a>
        <a href="estadisticas.php">Estadísticas</a>
        <a href="configuracion.php">Configuración</a>
        <a href="../public/logout.php" class="mt-auto btn btn-danger text-white">Cerrar sesión</a>
    </div>

    <div class="content">
        <h2>Panel de Control - Presidente General</h2>

        <!-- 🔹 Resumen General -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card-resumen">
                    <h3><?= $totalUsuarios ?></h3>
                    <p>Usuarios</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-resumen">
                    <h3><?= $totalActas ?></h3>
                    <p>Actas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-resumen">
                    <h3><?= $totalDocumentos ?></h3>
                    <p>Documentos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-resumen">
                    <h3><?= $totalEventos ?></h3>
                    <p>Eventos</p>
                </div>
            </div>
        </div>

        <!-- 🔹 Últimos documentos -->
        <h4 class="section-title mt-4">📄 Últimos Documentos</h4>
        <table class="table table-bordered table-hover">
            <thead>
                <tr><th>Título</th><th>Estado</th><th>Fecha</th></tr>
            </thead>
            <tbody>
                <?php foreach ($ultimosDocumentos as $doc): ?>
                    <tr>
                        <td><?= htmlspecialchars($doc['titulo']) ?></td>
                        <td><?= htmlspecialchars($doc['estado']) ?></td>
                        <td><?= htmlspecialchars($doc['fecha_subida']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- 🔹 Últimas actas -->
        <h4 class="section-title">📝 Últimas Actas</h4>
        <table class="table table-bordered table-hover">
            <thead>
                <tr><th>Título</th><th>Fecha</th><th>Lugar</th></tr>
            </thead>
            <tbody>
                <?php foreach ($ultimasActas as $acta): ?>
                    <tr>
                        <td><?= htmlspecialchars($acta['titulo']) ?></td>
                        <td><?= htmlspecialchars($acta['fecha_reunion']) ?></td>
                        <td><?= htmlspecialchars($acta['lugar']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
