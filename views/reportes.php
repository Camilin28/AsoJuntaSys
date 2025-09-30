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
    // Total ingresos
    $sql_ingresos = "SELECT SUM(monto) AS total_ingresos FROM recursos_financieros WHERE tipo_movimiento = 'Ingreso'";
    $stmt = $pdo->prepare($sql_ingresos);
    $stmt->execute();
    $totalIngresos = $stmt->fetchColumn() ?? 0;

    // Total egresos
    $sql_egresos = "SELECT SUM(monto) AS total_egresos FROM recursos_financieros WHERE tipo_movimiento = 'Gasto'";
    $stmt = $pdo->prepare($sql_egresos);
    $stmt->execute();
    $totalEgresos = $stmt->fetchColumn() ?? 0;

    // Balance
    $balance = $totalIngresos - $totalEgresos;

    // Movimientos
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
    <style>
        body {
            background-color: #fff9c4; /* Fondo claro
        }

        /* Navbar */
        .navbar {
            background-color: #2E7D32 !important;
        }

        .navbar .nav-link:hover {
            color: #FBC02D !important;
        }

        /* Contenedor */
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }

        h2, h3 {
            color: #2E7D32;
            margin-bottom: 20px;
        }

        /* Cards */
        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }

        .card h3 {
            font-weight: bold;
        }

        /* Tabla */
        .table thead {
            background-color: #2E7D32;
            color: #fff;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .table tbody tr:hover {
            background-color: #E8F5E9;
        }

        /* Botón */
        .btn-custom {
            background-color: #2E7D32;
            color: #fff;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .btn-custom:hover {
            background-color: #FBC02D;
            color: #333;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold" href="dashboard_tesoreria.php">AsoJuntaSys - Tesorería</a>
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

    <!-- Totales -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Ingresos</div>
                <div class="card-body">
                    <h3>$ <?= number_format($totalIngresos, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Total Egresos</div>
                <div class="card-body">
                    <h3>$ <?= number_format($totalEgresos, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card text-dark bg-warning mb-3">
                <div class="card-header">Balance</div>
                <div class="card-body">
                    <h3>$ <?= number_format($balance, 0, ',', '.') ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Movimientos -->
    <h3>Movimientos recientes</h3>
    <table class="table table-striped table-hover">
        <thead>
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
                    <td class="<?= $mov['tipo_movimiento'] === 'Ingreso' ? 'text-success fw-bold' : 'text-danger fw-bold' ?>">
                        $<?= number_format($mov['monto'], 0, ',', '.') ?>
                    </td>
                    <td><?= htmlspecialchars($mov['fecha']) ?></td>
                    <td><?= htmlspecialchars($mov['responsable'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($mov['observaciones'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No hay movimientos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard_tesoreria.php" class="btn btn-custom mt-3">⬅ Volver al Dashboard</a>
</div>

</body>
</html>
