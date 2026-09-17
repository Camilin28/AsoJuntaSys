<?php
require_once '../includes/auth.php';
require '../config/db.php';

requireRole(['Presidente General']);

$sql = "SELECT u.*, j.nombre AS jac_nombre 
        FROM usuarios u 
        LEFT JOIN juntas j ON u.jac_id = j.id 
        ORDER BY u.fecha_registro DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
$csrfToken = generarTokenCSRF();
$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_AsojuntaSys.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Usuarios Registrados - AsoJuntaSys</title>
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
.card-usuarios {
  background: #fff;
  padding: 20px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  margin-top: 20px;
}
.badge-rol { background-color: #81C784; color: #1b5e20; font-weight: 600; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="../views/dashboard_presidente.php">Junta de Acción Comunal</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><span class="nav-link text-white">Bienvenido, <?= htmlspecialchars($nombre) ?></span></li>
        <li class="nav-item"><a class="nav-link text-white" href="logout.php">Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
    <a href="../views/dashboard_presidente.php" class="btn btn-custom mb-3">⬅ Volver al Dashboard</a>
    <h2 class="mb-4">👥 Usuarios Registrados</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            ✅ Operación realizada correctamente.
        </div>
    <?php endif; ?>

    <div class="card-usuarios">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>JAC</th>
                        <th>Fecha de Registro</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($usuarios): ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?= htmlspecialchars($usuario['id']) ?></td>
                            <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                            <td><?= htmlspecialchars($usuario['email']) ?></td>
                            <td><span class="badge badge-rol"><?= htmlspecialchars($usuario['rol']) ?></span></td>
                            <td><?= htmlspecialchars($usuario['jac_nombre'] ?? 'Sin JAC') ?></td>
                            <td><?= htmlspecialchars($usuario['fecha_registro']) ?></td>
                            <td class="text-center">
                                <a href="actualizar_usuario.php?id=<?= $usuario['id'] ?>" class="btn btn-warning btn-sm">✏️ Editar</a>
                                <form action="eliminar_usuario.php" method="POST" style="display:inline;"
                                      onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?')">
                                    <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">🗑️ Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted">No hay usuarios registrados.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>