<?php
require_once('../includes/auth.php');
require('../config/db.php');

requireLogin();

$q = trim($_GET['q'] ?? '');

$filtroJac = ($_SESSION['usuario_rol'] !== 'Presidente General' && !empty($_SESSION['jac_id']))
    ? " AND d.jac_id = :jac_id"
    : "";

if ($q === '') {
    // Si no hay búsqueda, devolver todos los documentos
    $sql = "SELECT d.id, d.titulo, d.descripcion, d.archivo, d.estado, d.fecha_subida, 
                   c.nombre AS categoria, u.nombre AS usuario
            FROM documentos d
            JOIN categorias_documentos c ON d.categoria_id = c.id
            JOIN usuarios u ON d.usuario_id = u.id
            WHERE 1=1{$filtroJac}
            ORDER BY d.fecha_subida DESC";
    $stmt = $pdo->prepare($sql);
    $params = [];
    if ($filtroJac) {
        $params[':jac_id'] = $_SESSION['jac_id'];
    }
    $stmt->execute($params);
} else {
    // Buscar por título, descripción, categoría o usuario
    $sql = "SELECT d.id, d.titulo, d.descripcion, d.archivo, d.estado, d.fecha_subida, 
                   c.nombre AS categoria, u.nombre AS usuario
            FROM documentos d
            JOIN categorias_documentos c ON d.categoria_id = c.id
            JOIN usuarios u ON d.usuario_id = u.id
            WHERE (d.titulo LIKE :q1
               OR d.descripcion LIKE :q2
               OR c.nombre LIKE :q3
               OR u.nombre LIKE :q4){$filtroJac}
            ORDER BY d.fecha_subida DESC";
    $stmt = $pdo->prepare($sql);
    $like = "%$q%";
    $params = [':q1' => $like, ':q2' => $like, ':q3' => $like, ':q4' => $like];
    if ($filtroJac) {
        $params[':jac_id'] = $_SESSION['jac_id'];
    }
    $stmt->execute($params);
}

$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

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
    <?php if ($documentos): ?>
      <?php foreach ($documentos as $doc): ?>
        <tr>
          <td><?= htmlspecialchars($doc['titulo']) ?></td>
          <td><?= htmlspecialchars($doc['categoria']) ?></td>
          <td><?= htmlspecialchars($doc['descripcion'] ?? '-') ?></td>
          <td><?= htmlspecialchars($doc['estado']) ?></td>
          <td><?= htmlspecialchars($doc['usuario']) ?></td>
          <td><?= $doc['fecha_subida'] ?></td>
          <td class="text-center">
            <a href="../uploads/documentos/<?= $doc['archivo'] ?>" target="_blank" class="btn btn-info btn-sm">📄 Ver</a>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php else: ?>
      <tr><td colspan="7" class="text-center text-muted">No se encontraron resultados.</td></tr>
    <?php endif; ?>
  </tbody>
</table>