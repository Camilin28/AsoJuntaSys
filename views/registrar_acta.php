<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];

// 📂 Obtener lista de documentos disponibles
$sqlDocs = $pdo->query("SELECT id, titulo FROM documentos ORDER BY fecha_subida DESC");
$documentos = $sqlDocs->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registrar Acta</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background-color: #fff9c4;
}
.container {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
    margin-top: 30px;
    max-width: 900px;
}
h2 {
    color: #2E7D32;
    margin-bottom: 20px;
}
label { font-weight: bold; }
.btn-custom {
    background-color: #2E7D32;
    color: #fff;
    border-radius: 8px;
}
.btn-custom:hover {
    background-color: #FBC02D;
    color: #333;
}
</style>
</head>
<body>
<div class="container">
    <h2>📝 Registrar Nueva Acta</h2>
    <form action="../controllers/guardar_acta.php" method="POST">
        <div class="mb-3">
            <label for="titulo">Título del Acta</label>
            <input type="text" class="form-control" name="titulo" required>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="fecha_reunion">Fecha de la Reunión</label>
                <input type="date" class="form-control" name="fecha_reunion" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="hora_reunion">Hora</label>
                <input type="time" class="form-control" name="hora_reunion">
            </div>
        </div>

        <div class="mb-3">
            <label for="lugar">Lugar</label>
            <input type="text" class="form-control" name="lugar" required>
        </div>

        <div class="mb-3">
            <label for="asistentes">Asistentes</label>
            <textarea class="form-control" name="asistentes" rows="2" placeholder="Nombres de los asistentes"></textarea>
        </div>

        <div class="mb-3">
            <label for="orden_dia">Orden del Día</label>
            <textarea class="form-control" name="orden_dia" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="acuerdos">Acuerdos y Compromisos</label>
            <textarea class="form-control" name="acuerdos" rows="3"></textarea>
        </div>

        <div class="mb-3">
            <label for="observaciones">Observaciones</label>
            <textarea class="form-control" name="observaciones" rows="2"></textarea>
        </div>

        <div class="mb-3">
            <label for="documento_id">Documento Asociado (opcional)</label>
            <select class="form-select" name="documento_id">
                <option value="">-- Ninguno --</option>
                <?php foreach ($documentos as $doc): ?>
                    <option value="<?= $doc['id'] ?>"><?= htmlspecialchars($doc['titulo']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-custom">Guardar Acta</button>
        <a href="actas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>
