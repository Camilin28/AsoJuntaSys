<?php
session_start();

// Verificar que el usuario ha iniciado sesión y tiene el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: ../views/login.php");
    exit();
}

require('../config/db.php');

try {
    // Total ingresos
    $sql_ingresos = "SELECT SUM(monto) AS total_ingresos FROM recursos_financieros WHERE tipo_movimiento = 'Ingreso'";
    $stmt = $pdo->prepare($sql_ingresos);
    $stmt->execute();
    $totalIngresos = $stmt->fetchColumn();

    // Total egresos
    $sql_egresos = "SELECT SUM(monto) AS total_egresos FROM recursos_financieros WHERE tipo_movimiento = 'Gasto'";
    $stmt = $pdo->prepare($sql_egresos);
    $stmt->execute();
    $totalEgresos = $stmt->fetchColumn();

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Dashboard Tesorería - Asojuntas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff9c4; /* Fondo claro */
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #2e7d32;
            color: white;
            padding: 20px;
            border-radius: 0 15px 15px 0;
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }

        .sidebar a:hover {
            background: #1b5e20;
        }

        .logout-btn {
            background: #e53935;
            text-align: center;
            display: block;
            margin-top: 20px;
            padding: 10px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
        }

        .logout-btn:hover {
            background: #c62828;
        }

        .content {
            flex: 1;
            padding: 30px;
            background: linear-gradient(135deg, #e8f5e9, #fff9c4);
            border-radius: 15px 0 0 15px;
            overflow-y: auto;
        }

        .content h2 {
            margin-bottom: 30px;
            color: #2e7d32;
        }

        .admin-actions {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .card {
            flex: 1;
            min-width: 250px;
            margin: 10px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            padding: 20px;
        }

        .card h3 {
            margin-bottom: 15px;
        }

        .card button {
            margin-top: 10px;
            padding: 10px 20px;
            background: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .card button:hover {
            background: #1b5e20;
        }

        /* Card especial para Balance */
        .balance-card {
            background: linear-gradient(135deg, #4caf50, #fdd835);
            color: white;
        }

        .balance-card h3, 
        .balance-card p {
            color: white !important;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Bienvenido, <?= htmlspecialchars($nombre) ?></h2>
            <a href="dashboard_tesoreria.php">Panel Financiero</a>
            <a href="ingresos.php">Gestión de Ingresos</a>
            <a href="egresos.php">Gestión de Egresos</a>
            <a href="reportes.php">Reportes Financieros</a>
            <a href="../public/logout.php" class="logout-btn">Cerrar sesión</a>
        </div>

        <!-- Main content -->
        <div class="content">
            <h2>Panel de Control - Tesorero Asojuntas</h2>
            <div class="admin-actions">
                <!-- Ingresos -->
                <div class="card">
                    <h3>💰 Ingresos Totales</h3>
                    <p>$ <?= number_format($totalIngresos ?? 0, 0, ',', '.') ?></p>
                    <button onclick="location.href='ingresos.php'">Ver Detalle</button>
                </div>

                <!-- Egresos -->
                <div class="card">
                    <h3>📉 Egresos Totales</h3>
                    <p>$ <?= number_format($totalEgresos ?? 0, 0, ',', '.') ?></p>
                    <button onclick="location.href='egresos.php'">Ver Detalle</button>
                </div>

                <!-- Balance -->
                <div class="card balance-card">
                    <h3>📊 Balance Actual</h3>
                    <p>
                        $ <?= number_format(($totalIngresos ?? 0) - ($totalEgresos ?? 0), 0, ',', '.') ?>
                    </p>
                </div>
            </div>

            <!-- Reportes -->
            <div class="card" style="width:100%; margin-top:20px;">
                <h3>📑 Reportes</h3>
                <p>Genera informes detallados sobre las finanzas de la JAC.</p>
                <button onclick="location.href='reportes.php'">Ver Reportes</button>
            </div>
        </div>
    </div>
</body>
</html>
