<?php
require_once '../includes/auth.php';
require_once '../config/db.php';

requireRole(['Presidente General']);

// Filtro simple por acción (opcional)
$filtroAccion = $_GET['accion'] ?? '';

if ($filtroAccion !== '') {
    $stmt = $pdo->prepare("SELECT * FROM auditoria WHERE accion = :accion ORDER BY fecha DESC LIMIT 200");
    $stmt->execute([':accion' => $filtroAccion]);
} else {
    $stmt = $pdo->query("SELECT * FROM auditoria ORDER BY fecha DESC LIMIT 200");
}

$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_AsojuntaSys.png">
<meta charset="UTF-8">
<title>Auditoría - AsoJuntaSys</title>
<style>
  body { font-family: Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 20px; }
  h2 { color: #2E7D32; }
  table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
  th, td { border: 1px solid #ddd; padding: 8px 10px; text-align: left; font-size: 14px; }
  th { background: #2E7D32; color: #fff; }
  tr:nth-child(even) { background: #f9f9f9; }
  .badge { padding: 2px 8px; border-radius: 4px; color: #fff; font-size: 12px; }
  .badge-login_exitoso { background: #2E7D32; }
  .badge-login_fallido { background: #c62828; }
  .badge-logout { background: #757575; }
  .badge-crear { background: #1565C0; }
  .badge-editar { background: #EF6C00; }
  .badge-eliminar { background: #B71C1C; }
  .filtros { margin-bottom: 15px; }
  .filtros a { margin-right: 10px; text-decoration: none; color: #2E7D32; font-weight: bold; }
  .volver { display: inline-block; margin-bottom: 15px; }
</style>
</head>
<body>
  <a class="volver" href="dashboard_presidente.php">← Volver al panel</a>
  <h2>Auditoría y registro de actividad</h2>

  <div class="filtros">
    Filtrar por acción:
    <a href="auditoria.php">Todas</a>
    <a href="auditoria.php?accion=login_exitoso">Login exitoso</a>
    <a href="auditoria.php?accion=login_fallido">Login fallido</a>
    <a href="auditoria.php?accion=crear">Creaciones</a>
    <a href="auditoria.php?accion=editar">Ediciones</a>
    <a href="auditoria.php?accion=eliminar">Eliminaciones</a>
  </div>

  <table>
    <thead>
      <tr>
        <th>Fecha</th>
        <th>Usuario</th>
        <th>Acción</th>
        <th>Entidad</th>
        <th>Detalles</th>
        <th>IP</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($registros): ?>
        <?php foreach ($registros as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['fecha']) ?></td>
            <td><?= htmlspecialchars($r['usuario_nombre'] ?? '—') ?></td>
            <td><span class="badge badge-<?= htmlspecialchars($r['accion']) ?>"><?= htmlspecialchars($r['accion']) ?></span></td>
            <td><?= htmlspecialchars($r['entidad'] ?? '—') ?><?= $r['entidad_id'] ? ' #' . $r['entidad_id'] : '' ?></td>
            <td><?= htmlspecialchars($r['detalles'] ?? '') ?></td>
            <td><?= htmlspecialchars($r['ip'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6" style="text-align:center;color:#888;">No hay registros de auditoría aún.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
