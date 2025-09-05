<?php
// egresos.php
session_start();

// Verificar que el usuario ha iniciado sesión y tiene el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: ../views/login.php");
    exit();
}

require('../config/db.php'); // Ajusta la ruta si es necesario

try {
    // Consulta para obtener los egresos (tipo_movimiento = 'Gasto')
    $sql = "SELECT * FROM recursos_financieros WHERE tipo_movimiento = 'Gasto'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $egresos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Egresos - Junta de Acción Comunal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard_tesorero.php">Junta de Acción Comunal</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Bienvenido, <?= htmlspecialchars($nombre) ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="../public/logout.php">Cerrar sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h2>Lista de Egresos</h2>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Tipo de Movimiento</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($egresos) > 0): ?>
                <?php foreach ($egresos as $egreso): ?>
                <tr>
                    <td><?= htmlspecialchars($egreso['id']) ?></td>
                    <td><?= htmlspecialchars($egreso['descripcion']) ?></td>
                    <td><?= htmlspecialchars($egreso['tipo_movimiento']) ?></td>
                    <td><?= number_format($egreso['monto'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($egreso['fecha']) ?></td>
                    <td><?= htmlspecialchars($egreso['responsable'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($egreso['observaciones'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hay egresos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="../views/dashboard_tesoreria.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
</div>

</body>
</html>
