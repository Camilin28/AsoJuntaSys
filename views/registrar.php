<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
</head>
<body>
    <h2>Registro de Usuario</h2>

    <!-- Mensajes de error o éxito -->
    <?php
    if (isset($_SESSION['mensaje'])) {
        echo "<p style='color: green;'>" . $_SESSION['mensaje'] . "</p>";
        unset($_SESSION['mensaje']); 
    }
    if (isset($_SESSION['error'])) {
        echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
        unset($_SESSION['error']);
    }
    ?>

    <form action="../public/procesar_registro.php" method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" required><br>

        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" required><br>

        <label for="contraseña">Contraseña:</label>
        <input type="password" name="contraseña" required><br>

        <label for="rol">Rol:</label>
    <select name="rol" required>
        <option value="Presidente General">Presidente General</option>
        <option value="Presidentes de JAC">Presidentes de JAC</option>
        <option value="Secretaría">Secretaría</option>
        <option value="Tesorería">Tesorería</option>
    </select><br>
    
        <button type="submit">Registrarse</button>
    </form>

    <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
</body>
</html>
