<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0e5ec;
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            display: flex;
            justify-content: space-between;
            padding: 20px;
            height: 100vh;
            flex-wrap: wrap;
        }

        .sidebar {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff; /* Neumorphic shadow */
            width: 250px;
            height: auto;
        }

        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #333;
        }

        .sidebar a {
            display: block;
            color: #555;
            text-decoration: none;
            padding: 10px 0;
            font-size: 16px;
            margin: 5px 0;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #a3b1c6;
            color: white;
            border-radius: 5px;
        }

        .content {
            flex: 1;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff; /* Neumorphic shadow */
            margin-left: 20px;
        }

        .content h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            margin: 10px 0;
            border-radius: 15px;
            box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff; /* Neumorphic shadow */
        }

        .card h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 16px;
            color: #555;
        }

        .card button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 4px 4px 10px #a3b1c6, -4px -4px 10px #ffffff; /* Neumorphic button effect */
            margin-top: 10px;
        }

        .card button:hover {
            background-color: #45a049;
        }

        .card button:active {
            box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
        }

        .logout-btn {
            margin-top: 20px;
            background-color: #f44336;
        }

        .logout-btn:hover {
            background-color: #e53935;
        }

        .logout-btn:active {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Bienvenido, <?php echo $_SESSION['usuario']; ?></h2>
            <a href="dashboard.php">Inicio</a>
            <a href="perfil.php">Mi Perfil</a>
            <a href="ajustes.php">Ajustes</a>
            <a href="../public/logout.php" class="logout-btn">Cerrar sesión</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h2>Panel de Control</h2>

            <!-- Cards / Panels -->
            <div class="card">
                <h3>Estadísticas</h3>
                <p>Datos generales sobre el uso del sistema.</p>
                <button>Ver detalles</button>
            </div>

            <div class="card">
                <h3>Usuarios Registrados</h3>
                <p>Información sobre los usuarios registrados.</p>
                <button>Ver usuarios</button>
            </div>

            <div class="card">
                <h3>Notificaciones</h3>
                <p>Últimas notificaciones importantes.</p>
                <button>Ver notificaciones</button>
            </div>
        </div>
    </div>
</body>
</html>
