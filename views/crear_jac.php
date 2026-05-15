<?php
session_start();
require('../config/db.php');

// Seguridad: solo Presidente General puede entrar
if (
    !isset($_SESSION['usuario_id']) ||
    !isset($_SESSION['usuario_rol']) ||
    $_SESSION['usuario_rol'] !== 'Presidente General'
) {
    header("Location: login.php");
    exit();
}

/* ===========================
   Obtener usuarios disponibles
   =========================== */

// Presidentes disponibles
$stmt = $pdo->prepare("
    SELECT id, nombre 
    FROM usuarios 
    WHERE rol = 'Presidentes de JAC' 
    AND jac_id IS NULL
");
$stmt->execute();
$presidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Secretarios disponibles
$stmt = $pdo->prepare("
    SELECT id, nombre 
    FROM usuarios 
    WHERE rol = 'Secretaría' 
    AND jac_id IS NULL
");
$stmt->execute();
$secretarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tesoreros disponibles
$stmt = $pdo->prepare("
    SELECT id, nombre 
    FROM usuarios 
    WHERE rol = 'Tesorería' 
    AND jac_id IS NULL
");
$stmt->execute();
$tesoreros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear JAC</title>
</head>
<body>

<h2>Crear Nueva JAC</h2>

<form action="../controllers/guardar_jac.php" method="POST">

    <!-- Nombre -->
    <label>Nombre de la JAC</label>
    <input type="text" name="nombre_jac" required class="form-control">

    <!-- Ubicación -->
    <label class="mt-3">Direccion</label>
    <input type="text" name="direccion" required class="form-control">

    <!-- Teléfono -->
    <label class="mt-3">Teléfono (Opcional)</label>
    <input type="text" name="telefono" class="form-control">

    <hr>

    <!-- Presidente -->
    <label class="mt-3">Presidente</label>
    <select name="presidente_id" required class="form-control">
        <option value="">Seleccionar</option>
        <?php foreach ($presidentes as $p): ?>
            <option value="<?= $p['id'] ?>">
                <?= htmlspecialchars($p['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Secretaría -->
    <label class="mt-3">Secretaría</label>
    <select name="secretario_id" required class="form-control">
        <option value="">Seleccionar</option>
        <?php foreach ($secretarios as $s): ?>
            <option value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- Tesorería -->
    <label class="mt-3">Tesorería</label>
    <select name="tesorero_id" required class="form-control">
        <option value="">Seleccionar</option>
        <?php foreach ($tesoreros as $t): ?>
            <option value="<?= $t['id'] ?>">
                <?= htmlspecialchars($t['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <br><br>

    <button type="submit" class="btn btn-success">
        Crear JAC
    </button>

</form>

</body>
</html>