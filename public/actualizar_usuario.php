<?php
require '../config/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nombre' => $nombre, 'email' => $email, 'id' => $id]);

    echo "✅ Usuario actualizado con éxito.";
    header("Location: listar_usuario.php");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
</head>
<body>
    <h2>Editar Usuario</h2>
    <form method="POST">
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
        <label>Nombre:</label>
        <input type="text" name="nombre" value="<?= $usuario['nombre'] ?>" required>
        <br>
        <label>Email:</label>
        <input type="email" name="email" value="<?= $usuario['email'] ?>" required>
        <br>
        <button type="submit">Actualizar</button>
    </form>
</body>
</html>
