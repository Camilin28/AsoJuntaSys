<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: login.php");
    exit();
}
require('../config/db.php');

$sql_ingresos = "SELECT SUM(monto) FROM recursos_financieros 
                 WHERE tipo_movimiento IN ('Ingreso','Donacion','Subsidio') 
                    OR (tipo_movimiento = 'Otro' AND clasificacion = 'Ingreso')";
$totalIngresos = $pdo->query($sql_ingresos)->fetchColumn() ?? 0;

$sql_egresos = "SELECT SUM(monto) FROM recursos_financieros 
                WHERE tipo_movimiento IN ('Gasto','Transferencia') 
                   OR (tipo_movimiento = 'Otro' AND clasificacion = 'Egreso')";
$totalEgresos = $pdo->query($sql_egresos)->fetchColumn() ?? 0;

$balance = $totalIngresos - $totalEgresos;
$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Tesorería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fdfde3;
        }

        .sidebar {
            background-color: #2e7d32;
            color: white;
            min-height: 100vh;
            padding: 20px;
            font-size: 1.1rem;
            width: 280px; /* Sidebar más amplia */
        }

        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 12px 0;
            padding: 10px 15px;
            border-radius: 6px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #fbc02d; /* Amarillo de la paleta */
            color: #2e7d32; /* Verde oscuro para contraste */
        }

        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 8px rgba(0,0,0,0.1);
        }

        .card-header {
            font-weight: bold;
            font-size: 1.25rem;
        }

        .card-body h4 {
            font-size: 1.6rem;
            font-weight: bold;
        }

        h2 {
            font-size: 2rem;
            font-weight: bold;
        }

        .btn-sm {
            font-size: 1rem;
            padding: 8px 14px;
        }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar">
        <h5>Bienvenido, <?= htmlspecialchars($nombre) ?></h5>
        <hr>
        <a href="dashboard_tesoreria.php">📊 Panel Financiero</a>
        <a href="ingresos.php">💰 Gestión de Ingresos</a>
        <a href="egresos.php">📉 Gestión de Egresos</a>
        <a href="reportes.php">📑 Reportes Financieros</a>
        <a href="../public/logout.php" class="btn btn-danger mt-3 w-100">Cerrar sesión</a>
    </div>

    <!-- Contenido -->
    <div class="container-fluid p-4">
        <h2 class="mb-4 text-success">Panel de Control - Tesorero Asojuntas</h2>

        <!-- Fila de 4 cards -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-header bg-success text-white">💰 Ingresos Totales</div>
                    <div class="card-body">
                        <h4>$ <?= number_format($totalIngresos, 0, ',', '.') ?></h4>
                        <a href="ingresos.php" class="btn btn-success btn-sm">Ver Detalle</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-header bg-danger text-white">📉 Egresos Totales</div>
                    <div class="card-body">
                        <h4>$ <?= number_format($totalEgresos, 0, ',', '.') ?></h4>
                        <a href="egresos.php" class="btn btn-danger btn-sm">Ver Detalle</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-header bg-warning text-dark">📊 Balance Actual</div>
                    <div class="card-body">
                        <h4>$ <?= number_format($balance, 0, ',', '.') ?></h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-header bg-warning text-dark">✏️ Registrar Movimiento</div>
                    <div class="card-body">
                        <p class="mb-2" style="font-size: 1.05rem;">Agrega ingresos, egresos u otros movimientos.</p>
                        <a href="form_movimiento.php" class="btn btn-success btn-sm">➕ Nuevo</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card de Reportes debajo -->
        <div class="card mt-4">
            <div class="card text-center">
                <div class="card-header bg-success text-white">📑 Reportes</div>
                    <div class="card-body" style="font-size: 1.1rem;">
                        <p>Genera informes detallados sobre las finanzas de la JAC.</p>
                        <a href="reportes.php" class="btn btn-success">Ver Reportes</a>
                    </div>
                </div>
            </div>
         </div>
    </div>

</body>
</html>
