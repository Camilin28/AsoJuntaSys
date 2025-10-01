<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        
        body {
            font-family: Arial, sans-serif;
            background-color: #fff9c4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            /* Usamos Blanco #FFFFFF para la base de la caja */
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 8px 8px 15px #fbc02d, -8px -8px 15px #2e7d32;
            width: 350px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            color: #2e7d32; /* Gris Oscuro para el título */
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #2e7d32;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            background: #fff9c4;
            
            color: #424242; /* Gris Oscuro para el texto de entrada */
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 5px 2px #2e7d32;
        }

        button {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #2E7D32 5%, #FBC02D 70%); 
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 6px 6px 12px #fbc02d, -4px -4px 10px #ffffff;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        button:hover {
            background-color: #45a049; /* Un poco más claro que el principal para el hover */
        }

        button:active {
            box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
            background-color: #2E7D32; /* Vuelve al color principal al ser presionado */
        }

        .error-message {
            color: red; /* Mantenemos el rojo para mensajes de error */
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .success-message {
            color: #FBC02D; /* 🟡 Amarillo Principal para mensajes de éxito */
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .signup-link {
            margin-top: 10px;
            font-size: 14px;
            color: #424242; /* Gris Oscuro para el texto del link */
        }

        .signup-link a {
            text-decoration: none;
            color: #2E7D32; /* 🟢 Verde Principal para el link */
            font-weight: bold;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>Registro de Usuario</h2>

        <?php
        if (isset($_SESSION['mensaje'])) {
            echo "<p class='success-message'>" . $_SESSION['mensaje'] . "</p>";
            unset($_SESSION['mensaje']); 
        }
        if (isset($_SESSION['error'])) {
            echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        ?>

        <form action="../public/procesar_registro.php" method="POST">
            <label for="nombre">Nombre:</label>
            <input type="text" name="nombre" required placeholder="Introduce tu nombre">

            <label for="email">Correo Electrónico:</label>
            <input type="email" name="email" required placeholder="Introduce tu correo electrónico">

            <label for="contraseña">Contraseña:</label>
            <input type="password" name="contraseña" required placeholder="Introduce tu contraseña">

            <label for="rol">Rol:</label>
            <select name="rol" required>
                <option value="Presidente General">Presidente General</option>
                <option value="Presidentes de JAC">Presidentes de JAC</option>
                <option value="Secretaría">Secretaría</option>
                <option value="Tesorería">Tesorería</option>
            </select>

            <button type="submit">Registrarse</button>
        </form>

        <p class="signup-link">¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
    </div>
</body>
</html>