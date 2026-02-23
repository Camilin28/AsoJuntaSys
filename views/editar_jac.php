<?php
session_start();
require('../config/db.php');

if ($_SESSION['usuario_rol'] !== 'Presidente General') {
    header("Location: ../views/login.php");
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM juntas WHERE id = ?");
$stmt->execute([$id]);
$jac = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];

    $stmt = $pdo->prepare("
        UPDATE juntas
        SET nombre = ?, direccion = ?, telefono = ?
        WHERE id = ?
    ");

    $stmt->execute([$nombre, $direccion, $telefono, $id]);

    header("Location: gestionar_jac.php");
    exit();
}
?>

<h2>Editar JAC</h2>

<form method="POST">
    <input type="text" name="nombre" value="<?= $jac['nombre'] ?>" required><br><br>
    <input type="text" name="direccion" value="<?= $jac['direccion'] ?>" required><br><br>
    <input type="text" name="telefono" value="<?= $jac['telefono'] ?>"><br><br>

    <button type="submit">Actualizar</button>
</form>