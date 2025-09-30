<?php
// reportes.php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Tesorería') {
    header("Location: ../views/login.php");
    exit();
}

require('../config/db.php');

try {
    $sql = "SELECT * FROM recursos_financieros ORDER BY fecha DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $movimientos = $stmt->fetchAll();

    $sqlIngresos = "SELECT SUM(monto) AS total FROM recursos_financieros 
                    WHERE tipo_movimiento IN ('Ingreso','Donacion','Subsidio') 
                       OR (tipo_movimiento='Otro' AND clasificacion='Ingreso')";
    $stmtIngresos = $pdo->prepare($sqlIngresos);
    $stmtIngresos->execute();
    $totalIngresos = $stmtIngresos->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $sqlEgresos = "SELECT SUM(monto) AS total FROM recursos_financieros 
                   WHERE tipo_movimiento IN ('Gasto','Transferencia') 
                      OR (tipo_movimiento='Otro' AND clasificacion='Egreso')";
    $stmtEgresos = $pdo->prepare($sqlEgresos);
    $stmtEgresos->execute();
    $totalEgresos = $stmtEgresos->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

    $balance = $totalIngresos - $totalEgresos;

} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}

$nombre = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Reportes Financieros</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #fffde7;
        }

        .navbar {
            background-color: #2E7D32 !important;
        }

        .navbar .nav-link:hover {
            color: #FBC02D !important;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
        }

        h2 {
            color: #2E7D32;
            margin-bottom: 20px;
        }

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

        /* 🔥 Colores con mayor prioridad */
        td.text-ingreso {
            color: #2E7D32 !important; /* verde */
            font-weight: bold;
        }

        td.text-egreso {
            color: #c62828 !important; /* rojo */
            font-weight: bold;
        }

        .resumen-card {
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
            color: #fff;
        }

        .card-ingresos {
            background: #2E7D32;
        }

        .card-egresos {
            background: #c62828;
        }

        .card-balance {
            background: #FBC02D;
            color: #333;
            font-weight: bold;
        }

        .resumen-card h3 {
            margin: 0;
            font-size: 20px;
        }

        .resumen-card p {
            font-size: 28px;
            font-weight: bold;
        }

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
    <h2>Reporte de Movimientos Financieros</h2>

    <!-- Totales -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="resumen-card card-ingresos">
                <h3>Total Ingresos</h3>
                <p>$<?= number_format($totalIngresos, 0, ',', '.') ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="resumen-card card-egresos">
                <h3>Total Egresos</h3>
                <p>$<?= number_format($totalEgresos, 0, ',', '.') ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="resumen-card card-balance">
                <h3>Balance</h3>
                <p>$<?= number_format($balance, 0, ',', '.') ?></p>
            </div>
        </div>
    </div>

    <!-- Tabla -->
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
                    <?php
                        $esIngreso = (
                            $mov['tipo_movimiento'] === 'Ingreso' ||
                            $mov['tipo_movimiento'] === 'Donacion' ||
                            $mov['tipo_movimiento'] === 'Subsidio' ||
                            ($mov['tipo_movimiento'] === 'Otro' && $mov['clasificacion'] === 'Ingreso')
                        );
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($mov['id']) ?></td>
                        <td><?= htmlspecialchars($mov['descripcion']) ?></td>
                        <td>
                            <?= htmlspecialchars($mov['tipo_movimiento']) ?>
                            <?php if ($mov['tipo_movimiento'] === 'Otro'): ?>
                                (<?= htmlspecialchars($mov['clasificacion']) ?>)
                            <?php endif; ?>
                        </td>
                        <td class="<?= $esIngreso ? 'text-ingreso' : 'text-egreso' ?>">
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
