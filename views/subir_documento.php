<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];

// Categorías disponibles
$sql = "SELECT id, nombre FROM categorias_documentos ORDER BY nombre ASC";
$stmt = $pdo->query($sql);
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Subir Documento</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #fff9c4; }
.container {
  background: #fff;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
  margin-top: 30px;
  max-width: 700px;
}
h2 { color: #2E7D32; margin-bottom: 20px; }
.btn-custom { background-color: #2E7D32; color: #fff; border-radius: 8px; }
.btn-custom:hover { background-color: #FBC02D; color: #333; }
</style>
</head>
<body>
<div class="container">
  <h2>📤 Subir Nuevo Documento</h2>
  <form action="../controllers/guardar_documento.php" method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label for="titulo" class="form-label">Título</label>
      <input type="text" class="form-control" name="titulo" required>
    </div>
    <div class="mb-3">
      <label for="descripcion" class="form-label">Descripción</label>
      <textarea class="form-control" name="descripcion" rows="3"></textarea>
    </div>
    <div class="mb-3">
      <label for="categoria" class="form-label">Categoría</label>
      <select name="categoria_id" class="form-select" required>
        <option value="">Seleccionar categoría</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="mb-3">
      <label for="archivo" class="form-label">Archivo</label>
      <input type="file" class="form-control" name="archivo" accept=".pdf,.docx,.jpg,.png" required>
    </div>
    <button type="submit" class="btn btn-custom">Guardar</button>
    <a href="documentos.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
