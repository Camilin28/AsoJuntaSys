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
    <style>
        /* Estilo general */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #81c784;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }

        h2 {
            text-align: center;
            color: #ffffff;
        }

        table {
            width: 80%;
            margin-top: 20px;
            border-collapse: collapse;
            text-align: left;
            background-color: #fff9c4;
            border-radius: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            padding: 12px 15px;
            border-bottom: 1px solid #81c784;
        }

        table th {
            background: linear-gradient(135deg, #2E7D32 10%, #FBC02D 100%);
            color: white;
            text-align: center;
            
        }

        table td {
            text-align: center;
        }

        table a {
            text-decoration: none;
            color: #2E7D32;
            font-weight: bold;
        }

        table a:hover {
            color: #FBC02D;
        }

        /* Botón de regreso al dashboard */
        .regresar-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background: linear-gradient(135deg, #2E7D32 10%, #FBC02D 100%);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .regresar-btn:hover {
            background-color:rgb(12, 42, 237);
        }
    </style>
</head>
<body>

    <h2>Usuarios Registrados</h2>
    
    <table>
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
                    <a href="../public/actualizar_usuario.php?id=<?= $usuario['id'] ?>">✏️ Editar</a> |
                    <a href="../public/eliminar_usuario.php?id=<?= $usuario['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">🗑️ Eliminar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Botón de regreso al dashboard -->
    <a href="../views/dashboard_presidente.php" class="regresar-btn">Regresar</a>

</body>
</html>
