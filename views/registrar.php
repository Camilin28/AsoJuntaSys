<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';

requireRole(['Presidente General']);

// Cargar JAC disponibles para asignar al usuario
$jacs = $pdo->query("SELECT id, nombre FROM juntas ORDER BY nombre ASC")->fetchAll();

$error   = $_SESSION['error']   ?? null;
$mensaje = $_SESSION['mensaje'] ?? null;
unset($_SESSION['error'], $_SESSION['mensaje']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Usuario — AsoJuntaSys</title>
    <!-- Usa los mismos estilos que el resto de tus vistas -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container py-4" style="max-width: 600px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">Crear nuevo usuario</h4>
        <a href="dashboard_presidente.php" class="btn btn-sm btn-outline-secondary">← Volver</a>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($mensaje): ?>
        <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="../public/procesar_registro.php">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nombre completo</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Contraseña temporal</label>
                    <input type="password" name="password" class="form-control" 
                           minlength="8" required>
                    <div class="form-text">Mínimo 8 caracteres. El usuario podrá cambiarla después.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Rol</label>
                    <select name="rol" class="form-select" required>
                        <option value="">— Selecciona un rol —</option>
                        <option value="Presidentes de JAC">Presidente de JAC</option>
                        <option value="Secretaría">Secretaría</option>
                        <option value="Tesorería">Tesorería</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Junta de Acción Comunal</label>
                    <select name="jac_id" class="form-select">
                        <option value="">— Sin asignar por ahora —</option>
                        <?php foreach ($jacs as $jac): ?>
                            <option value="<?= $jac['id'] ?>">
                                <?= htmlspecialchars($jac['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Puedes asignar la JAC después desde Gestionar JAC.</div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Crear usuario</button>
                </div>

            </form>
        </div>
    </div>

</div>
</body>
</html>