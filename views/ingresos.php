<?php
// ingresos.php
session_start();

// Verificar que el usuario ha iniciado sesión y tiene el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: ../views/login.php");
    exit();
}

require('../config/db.php'); // Ajusta la ruta si es necesario

try {
    // Consulta para obtener los ingresos
    $sql = "SELECT * FROM recursos_financieros WHERE tipo_movimiento = 'Ingreso'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $ingresos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Ingresos - Junta de Acción Comunal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
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
    <h2>Lista de Ingresos</h2>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Tipo de Movimiento</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Responsable</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($ingresos) > 0): ?>
                <?php foreach ($ingresos as $ingreso): ?>
                <tr>
                    <td><?= htmlspecialchars($ingreso['id']) ?></td>
                    <td><?= htmlspecialchars($ingreso['descripcion']) ?></td>
                    <td><?= htmlspecialchars($ingreso['tipo_movimiento']) ?></td>
                    <td><?= number_format($ingreso['monto'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($ingreso['fecha']) ?></td>
                    <td><?= htmlspecialchars($ingreso['responsable'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($ingreso['observaciones'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hay ingresos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <a href="dashboard_tesoreria.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
</div>

</body>
</html>

