<?php
// reportes.php
session_start();

// Verificar que el usuario ha iniciado sesión y tiene el rol correcto
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: ../views/login.php");
    exit();
}

require('../config/db.php');

try {
    // Obtener total ingresos
    $sql_ingresos = "SELECT SUM(monto) AS total_ingresos FROM recursos_financieros WHERE tipo_movimiento = 'Ingreso'";
    $stmt = $pdo->prepare($sql_ingresos);
    $stmt->execute();
    $totalIngresos = $stmt->fetchColumn();

    // Obtener total egresos (Gasto)
    $sql_egresos = "SELECT SUM(monto) AS total_egresos FROM recursos_financieros WHERE tipo_movimiento = 'Gasto'";
    $stmt = $pdo->prepare($sql_egresos);
    $stmt->execute();
    $totalEgresos = $stmt->fetchColumn();

    // Obtener movimientos agrupados por tipo para mostrar detalle
    $sql_movimientos = "SELECT * FROM recursos_financieros ORDER BY fecha DESC";
    $stmt = $pdo->prepare($sql_movimientos);
    $stmt->execute();
    $movimientos = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Reportes Financieros - Junta de Acción Comunal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(135deg, #cc6600, #ff9933);">
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
    <h2>Reportes Financieros</h2>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Ingresos</div>
                <div class="card-body">
                    <h3>$ <?= number_format($totalIngresos ?? 0, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Total Egresos</div>
                <div class="card-body">
                    <h3>$ <?= number_format($totalEgresos ?? 0, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>
    </div>

    <h3>Movimientos recientes</h3>
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Monto</th>
                <th>Fecha</th>
                <th>Responsable</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($movimientos) > 0): ?>
                <?php foreach ($movimientos as $mov): ?>
                <tr>
                    <td><?= htmlspecialchars($mov['id']) ?></td>
                    <td><?= htmlspecialchars($mov['descripcion']) ?></td>
                    <td><?= htmlspecialchars($mov['tipo_movimiento']) ?></td>
                    <td><?= number_format($mov['monto'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($mov['fecha']) ?></td>
                    <td><?= htmlspecialchars($mov['responsable'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($mov['observaciones'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No hay movimientos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard_tesoreria.php" class="btn btn-secondary mt-3">Volver al Dashboard</a>
</div>

</body>
</html>
