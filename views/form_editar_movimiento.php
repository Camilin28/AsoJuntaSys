<?php
require '../config/db.php';
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$sql = "SELECT * FROM movimientos_financieros WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$mov = $stmt->get_result()->fetch_assoc();

if (!$mov) {
    echo "Movimiento no encontrado";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Movimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Editar Movimiento</h2>
    <form action="../controllers/actualizar_movimiento.php" method="POST">
        <input type="hidden" name="id" value="<?= $mov['id'] ?>">
        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <input type="text" class="form-control" name="descripcion" value="<?= $mov['descripcion'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Tipo de Movimiento</label>
            <select class="form-select" name="tipo_movimiento" required>
                <option value="Ingreso" <?= $mov['tipo_movimiento'] === 'Ingreso' ? 'selected' : '' ?>>Ingreso</option>
                <option value="Gasto" <?= $mov['tipo_movimiento'] === 'Gasto' ? 'selected' : '' ?>>Gasto</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Monto</label>
            <input type="number" class="form-control" name="monto" value="<?= $mov['monto'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Fecha</label>
            <input type="date" class="form-control" name="fecha" value="<?= $mov['fecha'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Responsable</label>
            <input type="text" class="form-control" name="responsable" value="<?= $mov['responsable'] ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Observaciones</label>
            <textarea class="form-control" name="observaciones" rows="3"><?= $mov['observaciones'] ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Movimiento</button>
        <a href="dashboard_tesorero.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
