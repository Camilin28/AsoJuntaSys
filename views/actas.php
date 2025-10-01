<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$nombre = $_SESSION['usuario_nombre'];

// Consultar todas las actas
$sql = "SELECT a.id, d.titulo AS documento, a.fecha_reunion, a.lugar, a.asistentes, a.acuerdos, a.observaciones
        FROM actas a
        LEFT JOIN documentos d ON a.documento_id = d.id
        ORDER BY a.fecha_reunion DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$actas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Actas - Secretaría</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #fff9c4; }
.navbar { background: linear-gradient(135deg, #2E7D32, #1b5e20) !important; }
.navbar .nav-link:hover { color: #FBC02D !important; }
.table thead { background-color: #2E7D32; color: #fff; }
.table tbody tr:hover { background-color: #E8F5E9; }
.btn-custom { background-color: #2E7D32; color: #fff; }
.btn-custom:hover { background-color: #FBC02D; color: #000; }

/* Animación del mensaje */
.alert {
    transition: opacity 0.8s ease-out, transform 0.8s ease-out;
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
    <h2 class="mb-4">📄 Actas de Reuniones</h2>

    <!-- Mensaje de éxito -->
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <div id="successMessage" class="alert alert-success">✅ El acta fue registrada exitosamente.</div>
    <?php endif; ?>

    <a href="registrar_acta.php" class="btn btn-custom mb-3">➕ Registrar Nueva Acta</a>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Documento Asociado</th>
                <th>Fecha</th>
                <th>Lugar</th>
                <th>Asistentes</th>
                <th>Acuerdos</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($actas) > 0): ?>
                <?php foreach ($actas as $acta): ?>
                    <tr>
                        <td><?= $acta['id'] ?></td>
                        <td><?= $acta['documento'] ?? '-' ?></td>
                        <td><?= $acta['fecha_reunion'] ?></td>
                        <td><?= $acta['lugar'] ?></td>
                        <td><?= $acta['asistentes'] ?></td>
                        <td><?= $acta['acuerdos'] ?></td>
                        <td><?= $acta['observaciones'] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted">No hay actas registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Script para ocultar mensaje -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    let msg = document.getElementById("successMessage");
    if (msg) {
        setTimeout(() => {
            msg.style.opacity = "0";
            msg.style.transform = "translateY(-10px)";
            setTimeout(() => msg.remove(), 800);
        }, 3000); // ⏳ Desaparece después de 3 segundos
    }
});
</script>

</body>
</html>
