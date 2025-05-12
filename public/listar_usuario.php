<?php
require '../config/db.php';

$sql = "SELECT * FROM usuarios";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
</head>
<body>
    <h2>Usuarios Registrados</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Fecha de Registro</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td><?= $usuario['id'] ?></td>
                <td><?= $usuario['nombre'] ?></td>
                <td><?= $usuario['email'] ?></td>
                <td><?= $usuario['fecha_registro'] ?></td>
                <td>
                    <a href="actualizar_usuario.php?id=<?= $usuario['id'] ?>">✏️ Editar</a> |
                    <a href="eliminar_usuario.php?id=<?= $usuario['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">🗑️ Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
