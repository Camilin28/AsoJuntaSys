<?php
session_start();

// Verifica que el usuario esté autenticado y tenga el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre']; // nombre del usuario desde sesión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Tesorero</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff9c4;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            display: flex;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background-color: #2E7D32;
            padding: 20px;
            width: 250px;
            color: #fff;
            display: flex;
            flex-direction: column;
            border-radius: 0 15px 15px 0;
            box-shadow: 2px 0px 10px rgba(0,0,0,0.2);
        }

        .sidebar h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .sidebar a {
            display: block;
            color: #fff;
            text-decoration: none;
            padding: 12px 10px;
            font-size: 16px;
            margin: 6px 0;
            border-radius: 6px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #FBC02D;
            color: #333;
        }

        .logout-btn {
            margin-top: auto;
            background-color: #F44336;
            text-align: center;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        /* Contenido */
        .content {
            flex: 2;
            padding: 30px;
            background: linear-gradient(135deg, #2E7D32 40%, #FBC02D 100%);
            border-radius: 15px 0 0 15px;
            margin: 40px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }

        .content h2 {
            font-size: 24px;
            color: #ffffffff;
            margin-bottom: 25px;
        }

        /* Cards */
        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: #ffffff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            flex: 1;
            min-width: 250px;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 6px 14px rgba(0,0,0,0.15);
        }

        .card h3 {
            color: #2E7D32;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .highlight {
            color: #FBC02D;
            font-size: 22px;
        }

        .card button {
            margin-top: 12px;
            padding: 10px 16px;
            background-color: #2E7D32;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .card button:hover {
            background-color: #FBC02D;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Bienvenido, <?php echo htmlspecialchars($nombre); ?></h2>
            <a href="dashboard_tesorero.php">Panel Financiero</a>
            <a href="ingresos.php">Gestión de Ingresos</a>
            <a href="egresos.php">Gestión de Egresos</a>
            <a href="reportes.php">Reportes Financieros</a>
            <a href="../public/logout.php" class="logout-btn">Cerrar sesión</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h2>Panel de Control - Tesorero Asojuntas</h2>

            <div class="cards">
                <div class="card">
                    <h3>💰 Ingresos Totales</h3>
                    <p class="highlight">$</p>
                    <button onclick="location.href='ingresos.php'">Ver Detalle</button>
                </div>

                <div class="card">
                    <h3>📉 Egresos Totales</h3>
                    <p class="highlight">$</p>
                    <button onclick="location.href='egresos.php'">Ver Detalle</button>
                </div>

                <div class="card">
                    <h3>📈 Balance Actual</h3>
                    <p class="highlight">$</p>
                    <button onclick="location.href='reportes.php'">Generar Reporte</button>
                </div>

                <div class="card">
                    <h3>📊 Reportes</h3>
                    <p>Genera informes detallados sobre las finanzas de la JAC.</p>
                    <button onclick="location.href='reportes.php'">Ver Reportes</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
