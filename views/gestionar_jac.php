<?php
session_start();
require('../config/db.php');

// 🔐 Solo Presidente General
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Presidente General') {
    header("Location: ../views/login.php");
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM juntas ORDER BY id DESC");
    $juntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $juntas = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de JAC</title>
</head>
<body>

<h2>Gestión de Juntas de Acción Comunal</h2>

<a href="crear_jac.php" class="btn btn-primary">+ Crear Nueva JAC</a>

<table border="1" cellpadding="10">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($juntas as $jac): ?>
            <tr>
                <td><?= $jac['id'] ?></td>
                <td><?= htmlspecialchars($jac['nombre']) ?></td>
                <td><?= htmlspecialchars($jac['direccion']) ?></td>
                <td><?= htmlspecialchars($jac['telefono']) ?></td>
                <td>
                    <a href="editar_jac.php?id=<?= $jac['id'] ?>">Editar</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>