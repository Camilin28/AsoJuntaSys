<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Movimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Agregar Movimiento Financiero</h2>
    <form action="../controllers/guardar_movimiento.php" method="POST">
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <input type="text" class="form-control" name="descripcion" required>
        </div>
        <div class="mb-3">
            <label for="tipo_movimiento" class="form-label">Tipo de Movimiento</label>
            <select class="form-select" name="tipo_movimiento" required>
                <option value="">Seleccione...</option>
                <option value="Ingreso">Ingreso</option>
                <option value="Gasto">Gasto</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="monto" class="form-label">Monto</label>
            <input type="number" class="form-control" name="monto" required>
        </div>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" name="fecha" required>
        </div>
        <div class="mb-3">
            <label for="responsable" class="form-label">Responsable</label>
            <input type="text" class="form-control" name="responsable">
        </div>
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea class="form-control" name="observaciones" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Movimiento</button>
        <a href="dashboard_tesorero.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
