<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color:rgb(0, 0, 0);
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
            background-color:rgb(0, 0, 0);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 2px 2px 15px #00ff00, -2px -2px 15px #00ff00; /* Neumorphic shadow */
            width: 250px;
            height: auto;
        }

        .sidebar h2 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #ffff;
        }

        .sidebar a {
            display: block;
            color: #ffff;
            text-decoration: none;
            padding: 10px 0;
            font-size: 16px;
            margin: 5px 0;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color:rgb(0, 255, 76);
            color: white;
            border-radius: 5px;
        }

        .content {
            flex: 1;
            padding: 20px;
            background-color: #0000;
            border-radius: 15px;
            box-shadow: 2px 2px 15px #a3b1c6, -2px -2px 15px #00ff00; /* Neumorphic shadow */
            margin-left: 20px;
        }

        .content h2 {
            font-size: 24px;
            color: #ffff;
            margin-bottom: 20px;
        }

        .card {
            background-color: #ffffff;
            padding: 20px;
            margin: 10px 0;
            border-radius: 15px;
            
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
            background: linear-gradient(to right, #00ff00, #00bfff);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 4px 4px 10px #a3b1c6, -4px -4px 10px #ffffff; /* Neumorphic button effect */
            margin-top: 10px;
        }


        .card button:active {
            box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
        }

        .logout-btn {
            margin-top: 10px;
            
            border-radius: 5px;
            
        }

        .logout-btn:hover {
            background-color:rgb(60, 237, 44);
        }

        .logout-btn:active {
            background-color:rgb(57, 239, 12);
        }

        .admin-actions {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .admin-actions .card {
            width: 45%;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Sidebar -->
        <div class="sidebar">
            <h2>Bienvenido, <?php echo $_SESSION['nombre']; ?></h2>
            <a href="dashboard_admin.php">Panel de Control</a>
            <a href="usuarios.php">Gestión de Usuarios</a>
            <a href="estadisticas.php">Estadísticas</a>
            <a href="configuracion.php">Configuración</a>
            <a href="../public/logout.php" class="logout-btn">Cerrar sesión</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h2>Panel de Control - Admin</h2>

            <!-- Admin Actions -->
            <div class="admin-actions">
                <div class="card">
                    <h3>Usuarios Registrados</h3>
                    <p>Visualiza y gestiona los usuarios registrados en el sistema.</p>
                    <button onclick="location.href='../public/listar_usuario.php'">Ver Usuarios</button>
                </div>

                <div class="card">
                    <h3>Estadísticas Generales</h3>
                    <p>Revisa las estadísticas sobre el uso de la plataforma.</p>
                    <button onclick="location.href='estadisticas.php'">Ver Estadísticas</button>
                </div>

                <div class="card">
                    <h3>Gestión de Contenido</h3>
                    <p>Añadir, editar o eliminar contenido en el sistema.</p>
                    <button onclick="location.href='contenido.php'">Gestionar Contenido</button>
                </div>

                <div class="card">
                    <h3>Configuración del Sistema</h3>
                    <p>Ajusta la configuración del sistema y preferencias.</p>
                    <button onclick="location.href='configuracion.php'">Ver Configuración</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
