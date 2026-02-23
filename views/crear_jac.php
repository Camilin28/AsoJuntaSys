<?php
require('../config/db.php');

// Presidentes disponibles
$stmt = $pdo->prepare("
    SELECT id, nombre 
    FROM usuarios 
    WHERE rol = 'presidentes de jac' 
    AND jac_id IS NULL
");
$stmt->execute();
$presidentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Secretarios disponibles
$stmt = $pdo->prepare("
    SELECT id, nombre 
    FROM usuarios 
    WHERE rol = 'secretaria' 
    AND jac_id IS NULL
");
$stmt->execute();
$secretarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tesoreros disponibles
$stmt = $pdo->prepare("
    SELECT id, nombre 
    FROM usuarios 
    WHERE rol = 'tesorero' 
    AND jac_id IS NULL
");
$stmt->execute();
$tesoreros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form action="../controllers/guardar_jac.php" method="POST">

    <label>Nombre de la JAC</label>
    <input type="text" name="nombre_jac" required class="form-control">

    <label class="mt-3">Presidente</label>
    <select name="presidente_id" required class="form-control">
        <option value="">Seleccionar</option>
        <?php foreach ($presidentes as $p): ?>
            <option value="<?= $p['id'] ?>">
                <?= htmlspecialchars($p['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label class="mt-3">Secretario</label>
    <select name="secretario_id" required class="form-control">
        <option value="">Seleccionar</option>
        <?php foreach ($secretarios as $s): ?>
            <option value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label class="mt-3">Tesorero</label>
    <select name="tesorero_id" required class="form-control">
        <option value="">Seleccionar</option>
        <?php foreach ($tesoreros as $t): ?>
            <option value="<?= $t['id'] ?>">
                <?= htmlspecialchars($t['nombre']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit" class="btn btn-success mt-3">
        Crear JAC
    </button>

</form>