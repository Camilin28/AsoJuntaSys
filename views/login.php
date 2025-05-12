<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión</title>
</head>
<body>
    <h2>Inicio de Sesión</h2>

    <!-- Mostrar mensajes de error o éxito -->
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

    <form action="../public/procesar_login.php" method="POST">
        <label for="email">Correo Electrónico:</label>
        <input type="email" name="email" id="email" required><br><br>

        <label for="contraseña">Contraseña:</label>
        <input type="contraseña" name="contraseña" id="contraseña" required><br><br>

        <button type="submit">Iniciar Sesión</button>
    </form>

    <p>¿No tienes una cuenta? <a href="registrar.php">Registrarse</a></p>
</body>
</html>
