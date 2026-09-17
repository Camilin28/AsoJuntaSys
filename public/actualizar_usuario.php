<?php
require_once '../includes/auth.php';
require_once '../includes/auditoria.php';
require '../config/db.php';

requireRole(['Presidente General']);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header("Location: listar_usuario.php");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (
        empty($_SESSION['csrf_token']) ||
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        die("❌ Solicitud inválida o expirada. Vuelve a intentarlo desde la página original.");
    }

    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];

    $sql = "UPDATE usuarios SET nombre = :nombre, email = :email WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nombre' => $nombre, 'email' => $email, 'id' => $id]);

    registrarAuditoria($pdo, 'editar', 'usuario', (int) $id, $nombre);

    header("Location: listar_usuario.php?success=1");
    exit();
}

$csrfToken = generarTokenCSRF();
$nombreSesion = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_web.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Usuario - AsoJuntaSys</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
body { background-color: #fff9c4; }
.navbar { background: linear-gradient(135deg, #2E7D32, #1b5e20) !important; }
.navbar .nav-link:hover { color: #FBC02D !important; }
.btn-custom { background-color: #2E7D32; color: #fff; border-radius: 8px; }
.btn-custom:hover { background-color: #FBC02D; color: #000; }
.card-form {
  background: #fff;
  padding: 30px;
  border-radius: 10px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  max-width: 480px;
  margin: 30px auto;
}
.card-form label { font-weight: 600; color: #424242; }
.card-form input:focus { border-color: #2E7D32; box-shadow: 0 0 0 0.2rem rgba(46,125,50,0.2); }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="../views/dashboard_presidente.php">Junta de Acción Comunal</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><span class="nav-link text-white">Bienvenido, <?= htmlspecialchars($nombreSesion) ?></span></li>
        <li class="nav-item"><a class="nav-link text-white" href="logout.php">Cerrar sesión</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container">
    <div class="card-form">
        <h3 class="mb-4 text-center" style="color:#2E7D32;">✏️ Editar Usuario</h3>

        <form method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($usuario['id']) ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="mb-3">
                <label class="form-label">Nombre</label>
                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" required>
            </div>

            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-custom flex-fill">Actualizar</button>
                <a href="listar_usuario.php" class="btn btn-secondary flex-fill">Regresar</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>