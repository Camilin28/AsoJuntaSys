<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];

// ✅ Agregar categoría
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['nombre'])) {
    $nombreCategoria = trim($_POST['nombre']);
    $stmt = $pdo->prepare("INSERT INTO categorias_documentos (nombre) VALUES (:nombre)");
    $stmt->execute([':nombre' => $nombreCategoria]);
    header("Location: categorias_documentos.php?success=1");
    exit();
}

// ✅ Eliminar categoría
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $pdo->prepare("DELETE FROM categorias_documentos WHERE id = ?")->execute([$id]);
    header("Location: categorias_documentos.php?deleted=1");
    exit();
}

// ✅ Obtener todas las categorías
$stmt = $pdo->query("SELECT * FROM categorias_documentos ORDER BY nombre ASC");
$categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_AsojuntaSys.png">
<meta charset="UTF-8">
<title>Categorías de Documentos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #fff9c4; }
.navbar { background: linear-gradient(135deg, #2E7D32, #1b5e20); }
.navbar .nav-link:hover { color: #FBC02D !important; }
.table thead { background-color: #2E7D32; color: #fff; }
.btn-custom { background-color: #2E7D32; color: #fff; border-radius: 8px; }
.btn-custom:hover { background-color: #FBC02D; color: #000; }
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard_secretario.php">Junta de Acción Comunal</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><span class="nav-link text-white">Bienvenida, <?= htmlspecialchars($nombre) ?></span></li>
        <li class="nav-item"><a class="nav-link text-white" href="../public/logout.php">Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
<h2 class="mb-4">📂 Gestión de Categorías de Documentos</h2>

<?php if (isset($_GET['success'])): ?>
  <div class="alert alert-success">✅ Categoría agregada correctamente.</div>
<?php elseif (isset($_GET['deleted'])): ?>
  <div class="alert alert-danger">🗑️ Categoría eliminada correctamente.</div>
<?php endif; ?>

<form method="POST" class="row g-3 mb-4">
  <div class="col-md-8">
    <input type="text" name="nombre" class="form-control" placeholder="Nombre de la nueva categoría" required>
  </div>
  <div class="col-md-4">
    <button type="submit" class="btn btn-custom w-100">Agregar Categoría</button>
    
  </div>
</form>

<table class="table table-bordered table-hover">
  <thead>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php if (count($categorias) > 0): ?>
      <?php foreach ($categorias as $cat): ?>
      <tr>
        <td><?= $cat['id'] ?></td>
        <td><?= htmlspecialchars($cat['nombre']) ?></td>
        <td>
          <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger"
             onclick="return confirm('¿Eliminar esta categoría?')">Eliminar</a>
        </td>
      </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="3" class="text-center text-muted">No hay categorías registradas.</td></tr>
    <?php endif; ?>
  </tbody>
</table>
<a href="dashboard_secretario.php" class="btn btn-secondary mb-3">⬅ Volver al Dashboard</a>
</div>

</body>
</html>

