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
            background-color: #e0e5ec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .register-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 8px 8px 15px #a3b1c6, -8px -8px 15px #ffffff; /* Neumorphic shadow */
            width: 350px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #555;
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
            background: #e0e5ec;
            box-shadow: inset 4px 4px 8px #c8d0e7, inset -4px -4px 8px #ffffff; /* Neumorphic input effect */
            color: #333;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            box-shadow: 0 0 5px 2px #a3b1c6;
        }

        button {
            width: 100%;
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

        .error-message,
        .success-message {
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
    <div class="register-container">
        <h2>Registro de Usuario</h2>

        <!-- Mensajes de error o éxito -->
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
