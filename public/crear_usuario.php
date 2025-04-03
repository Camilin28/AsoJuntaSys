<?php
require '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); //

    $sql = "INSERT INTO usuarios (nombre, email, contraseña) VALUES (:nombre, :email, :password)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nombre' => $nombre,
        'email' => $email,
        'password' => $password
    ]);

    echo "✅ Usuario registrado con éxito.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Usuario</title>
</head>
<body>
    <h2>Registrar Usuario</h2>
    <form method="POST">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>
        <br>
        <label>Email:</label>
        <input type="email" name="email" required>
        <br>
        <label>Contraseña:</label>
        <input type="contraseña" name="contraseña" required>
        <br>
        <button type="submit">Registrar</button>
    </form>
</body>
</html>
