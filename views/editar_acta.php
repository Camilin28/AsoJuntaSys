<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die("❌ ID de acta no proporcionado.");
}

// Obtener acta
$stmt = $pdo->prepare("SELECT * FROM actas WHERE id = :id");
$stmt->execute([':id' => $id]);
$acta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$acta) {
    die("❌ Acta no encontrada.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Acta</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>✏️ Editar Acta</h2>
    <form action="../controllers/actualizar_acta.php" method="POST">
        <input type="hidden" name="id" value="<?= $acta['id'] ?>">

        <div class="mb-3">
            <label for="titulo">Título</label>
            <input type="text" class="form-control" name="titulo" value="<?= htmlspecialchars($acta['titulo']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="fecha_reunion">Fecha</label>
            <input type="date" class="form-control" name="fecha_reunion" value="<?= $acta['fecha_reunion'] ?>" required>
        </div>

        <div class="mb-3">
            <label for="lugar">Lugar</label>
            <input type="text" class="form-control" name="lugar" value="<?= htmlspecialchars($acta['lugar']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="asistentes">Asistentes</label>
            <textarea class="form-control" name="asistentes"><?= htmlspecialchars($acta['asistentes']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="acuerdos">Acuerdos</label>
            <textarea class="form-control" name="acuerdos"><?= htmlspecialchars($acta['acuerdos']) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="observaciones">Observaciones</label>
            <textarea class="form-control" name="observaciones"><?= htmlspecialchars($acta['observaciones']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-success">💾 Guardar Cambios</button>
        <a href="actas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
