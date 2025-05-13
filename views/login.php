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
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #000;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            display: flex;
            width: 800px;
            height: 450px;
            border: 2px solid #00ff00;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 20px #0000FF;
        }

        .left-panel {
            width: 50%;
            background-color: #111;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .left-panel h2 {
            color: white;
            margin-bottom: 30px;
            font-size: 28px;
            align-self: auto;
        }

        label {
            color: #ccc;
            font-size: 14px;
            margin-bottom: 5px;
        }

        input[type="email"],
        input[type="password"] {
            padding: 10px;
            margin-bottom: 20px;
            border: none;
            border-bottom: 2px solid #555;
            background: transparent;
            color: white;
            font-size: 16px;
        }

        input:focus {
            outline: none;
            border-bottom-color: #00ffff;
        }

        button {
            background: linear-gradient(to right, #00ff00, #00bfff);
            border: none;
            color: white;
            padding: 12px;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            opacity: 0.9;
        }

        .signup-link {
            margin-top: 20px;
            font-size: 14px;
            color: white;
        }

        .signup-link a {
            color: #00ffff;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .right-panel {
            width: 50%;
            background: linear-gradient(to right, #00ff00, #00bfff);
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            font-size: 20px;
            padding: 20px;
            text-align: center;
        }

        .right-panel h1 {
            margin-bottom: 10px;
        }

        .error-message {
            color: #ff4444;
            font-size: 14px;
            margin-bottom: 15px;
        }

        footer {
            position: absolute;
            bottom: 15px;
            font-size: 14px;
            color: white;
            text-align: center;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <h2>LOGIN</h2>

            <?php
            if (isset($_SESSION['error'])) {
                echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']);
            }
            ?>

            <form action="../public/procesar_login.php" method="POST">
                <label for="email">Usuario</label>
                <input type="email" name="email" required placeholder="Correo electrónico">

                <label for="password">Contraseña</label>
                <input type="password" name="password" required placeholder="Introduce tu contraseña">

                <button type="submit">Iniciar Sesión</button>
            </form>

            <p class="signup-link">¿No tienes cuenta? <a href="../views/registrar.php">Por favor, Regístrate</a></p>
        </div>
        <div class="right-panel">
            <h1>BIENVENIDO</h1>
            
        </div>
    </div>

   
</body>
</html>
