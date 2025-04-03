<?php
require '../config/db.php';

$sql = "SELECT * FROM usuarios";
$stmt = $pdo->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
</head>
<body>
    <h2>Lista de Usuarios</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($usuarios as $usuario) : ?>
        <tr>
            <td><?= $usuario['id'] ?></td>
            <td><?= $usuario['nombre'] ?></td>
            <td><?= $usuario['email'] ?></td>
            <td>
                <a href="actualizar_usuario.php?id=<?= $usuario['id'] ?>">Editar</a> |
                <a href="eliminar_usuario.php?id=<?= $usuario['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
