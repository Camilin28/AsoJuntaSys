<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];

// 🔹 Filtros
$categoriaSeleccionada = $_GET['categoria'] ?? '';
$estadoSeleccionado = $_GET['estado'] ?? '';

// 🔹 Categorías
$sqlCategorias = "SELECT id, nombre FROM categorias_documentos ORDER BY nombre ASC";
$stmtCat = $pdo->query($sqlCategorias);
$categorias = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// 🔹 Consulta principal
$sql = "SELECT d.id, d.titulo, d.descripcion, d.archivo, d.estado, d.fecha_subida, 
               c.nombre AS categoria, u.nombre AS usuario
        FROM documentos d
        JOIN categorias_documentos c ON d.categoria_id = c.id
        JOIN usuarios u ON d.usuario_id = u.id
        WHERE 1=1";
$params = [];
if ($categoriaSeleccionada) {
    $sql .= " AND c.id = :categoria";
    $params[':categoria'] = $categoriaSeleccionada;
}
if ($estadoSeleccionado) {
    $sql .= " AND d.estado = :estado";
    $params[':estado'] = $estadoSeleccionado;
}
$sql .= " ORDER BY d.fecha_subida DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Documentos - Secretaría</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
body { background-color: #fff9c4; }
.navbar { background: linear-gradient(135deg, #2E7D32, #1b5e20) !important; }
.navbar .nav-link:hover { color: #FBC02D !important; }
.table thead { background-color: #2E7D32; color: #fff; }
.table tbody tr:hover { background-color: #E8F5E9; }
.btn-custom { background-color: #2E7D32; color: #fff; border-radius: 8px; }
.btn-custom:hover { background-color: #FBC02D; color: #000; }
.btn-secundary { background-color: #707070ff; color: #fff; border-radius: 8px; }
.btn-secundary:hover { background-color: #FBC02D; color: #000; }
.alert { transition: opacity 0.8s ease-out, transform 0.8s ease-out; }
.filter-card {
  background: #fff;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  margin-bottom: 20px;
}
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
<h2 class="mb-4">📑 Gestión de Documentos</h2>

<div id="ajaxMessage" class="alert d-none"></div>

<!-- 🔹 Filtros -->
<div class="filter-card">
  <form method="GET" class="row g-3 align-items-center">
    <div class="col-md-3">
      <label class="form-label fw-bold text-success">Categoría:</label>
      <select name="categoria" class="form-select">
        <option value="">Todas</option>
        <?php foreach ($categorias as $cat): ?>
          <option value="<?= $cat['id'] ?>" <?= ($categoriaSeleccionada == $cat['id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['nombre']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold text-success">Estado:</label>
      <select name="estado" class="form-select">
        <option value="">Todos</option>
        <option value="Pendiente" <?= ($estadoSeleccionado == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
        <option value="Revisado" <?= ($estadoSeleccionado == 'Revisado') ? 'selected' : '' ?>>Revisado</option>
        <option value="Aprobado" <?= ($estadoSeleccionado == 'Aprobado') ? 'selected' : '' ?>>Aprobado</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label fw-bold text-success">Buscar título:</label>
      <input type="text" id="buscador" class="form-control" placeholder="Escribe para buscar...">
    </div>
    <div class="col-md-3 d-flex align-items-end">
      <button type="submit" class="btn btn-success me-2">🔍 Aplicar</button>
      <a href="documentos.php" class="btn btn-secondary">Limpiar</a>
    </div>
  </form>
</div>

<a href="subir_documento.php" class="btn btn-custom mb-3">➕ Subir Documento</a>
<a href="categorias_documentos.php" class="btn btn-success mb-3">📁 Gestionar Categorías</a>
<a href="dashboard_secretario.php" class="btn btn-secundary mb-3 ">⬅ Volver al Dashboard</a>

<!-- 🔹 Tabla -->
<div id="tablaDocumentos">
<table class="table table-bordered table-hover align-middle">
  <thead>
    <tr class="text-center">
      <th>Título</th>
      <th>Categoría</th>
      <th>Descripción</th>
      <th>Estado</th>
      <th>Usuario</th>
      <th>Fecha</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($documentos as $doc): ?>
      <tr>
        <td><?= htmlspecialchars($doc['titulo']) ?></td>
        <td><?= htmlspecialchars($doc['categoria']) ?></td>
        <td><?= htmlspecialchars($doc['descripcion'] ?? '-') ?></td>
        <td class="text-center">
          <select class="form-select form-select-sm estado-select" data-id="<?= $doc['id'] ?>">
            <option value="Pendiente" <?= ($doc['estado'] == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
            <option value="Revisado" <?= ($doc['estado'] == 'Revisado') ? 'selected' : '' ?>>Revisado</option>
            <option value="Aprobado" <?= ($doc['estado'] == 'Aprobado') ? 'selected' : '' ?>>Aprobado</option>
          </select>
        </td>
        <td><?= htmlspecialchars($doc['usuario']) ?></td>
        <td><?= $doc['fecha_subida'] ?></td>
        <td class="text-center">
          <a href="../uploads/documentos/<?= $doc['archivo'] ?>" target="_blank" class="btn btn-info btn-sm">📄 Ver</a>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>
</div>

<script>
// 🔹 Actualizar estado AJAX
document.querySelectorAll('.estado-select').forEach(select => {
  select.addEventListener('change', async function() {
    const id = this.dataset.id;
    const estado = this.value;
    const response = await fetch('../controllers/actualizar_estado_documento.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: `id=${id}&estado=${estado}`
    });
    const data = await response.json();
    const msg = document.getElementById("ajaxMessage");
    msg.textContent = data.message;
    msg.className = `alert ${data.success ? 'alert-success' : 'alert-danger'}`;
    msg.classList.remove("d-none");
    setTimeout(() => msg.classList.add("d-none"), 2000);
  });
});

// 🔹 Buscador en tiempo real
document.getElementById('buscador').addEventListener('keyup', async function() {
  const query = this.value;
  const response = await fetch(`../controllers/buscar_documento.php?q=${encodeURIComponent(query)}`);
  const html = await response.text();
  document.getElementById('tablaDocumentos').innerHTML = html;
});
</script>
</body>
</html>
