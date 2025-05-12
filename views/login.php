<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e0e5ec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff; /* Neumorphic shadow */
            width: 300px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #44;
        }

        input[type="email"],
        input[type="password"] {
            width: 90%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            background: #e0e5ec;
            box-shadow: inset 4px 4px 8px #c8d0e7, inset -4px -4px 8px #ffffff; /* Neumorphic input effect */
            color: #333;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            box-shadow: 0 0 5px 2px #a3b1c6;
        }

        button {
            width: 50%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 4px 4px 10px #a3b1c6, -4px -4px 10px #ffffff; /* Neumorphic button effect */
        }

        button:hover {
            background-color: #45a049;
        }

        button:active {
            box-shadow: inset 4px 4px 8px #a3b1c6, inset -4px -4px 8px #ffffff;
        }

        .error-message {
            color: red;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .signup-link {
            margin-top: 10px;
            font-size: 14px;
        }

        .signup-link a {
            text-decoration: none;
            color: #4CAF50;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <!-- Mensajes de error -->
        <?php
        if (isset($_SESSION['error'])) {
            echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
            unset($_SESSION['error']);
        }
        ?>

        <form action="../public/procesar_login.php" method="POST">
            <label for="email">Correo Electrónico:</label>
            <input type="email" name="email" required placeholder="Introduce tu correo electrónico">

            <label for="password">Contraseña:</label>
            <input type="password" name="password" required placeholder="Introduce tu contraseña">

            <button type="submit">Iniciar Sesión</button>
        </form>

        <p class="signup-link">¿No tienes cuenta? <a href="../views/registrar.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
