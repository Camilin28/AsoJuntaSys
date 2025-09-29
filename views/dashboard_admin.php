<?php
session_start();

// Verifica que el usuario esté autenticado y tenga el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Presidente General') {
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
    <title>Dashboard Admin</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5; /* Fondo general claro */
            margin: 0;
            padding: 0;
        }

        .dashboard-container {
            display: flex;
            justify-content: space-between;
            height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            background-color: #2E7D32; /* Verde institucional */
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
            color: #fff;
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
            background-color: #FBC02D; /* Amarillo */
            color: #333;
        }

        .logout-btn {
            margin-top: auto;
            background-color: #F44336; /* Rojo para destacar logout */
            text-align: center;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
            color: #fff;
        }

        /* Contenido */
        .content {
            flex: 1;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 15px 0 0 15px;
            margin: 20px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }

        .content h2 {
            font-size: 24px;
            color: #2E7D32;
            margin-bottom: 25px;
        }

        /* Cards */
        .admin-actions {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .admin-actions .card {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
            width: 45%;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .admin-actions .card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 6px 14px rgba(0,0,0,0.15);
        }

        .card h3 {
            font-size: 20px;
            color: #2E7D32;
            margin-bottom: 10px;
        }

        .card p {
            font-size: 15px;
            color: #555;
        }

        .card button {
            padding: 10px 16px;
            background-color: #2E7D32;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            cursor: pointer;
            margin-top: 12px;
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
            <a href="dashboard_admin.php">Panel de Control</a>
            <a href="usuarios.php">Gestión de Usuarios</a>
            <a href="estadisticas.php">Estadísticas</a>
            <a href="configuracion.php">Configuración</a>
            <a href="../public/logout.php" class="logout-btn">Cerrar sesión</a>
        </div>

        <!-- Main Content -->
        <div class="content">
            <h2>Panel de Control - Presidente General Asojuntas</h2>

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
