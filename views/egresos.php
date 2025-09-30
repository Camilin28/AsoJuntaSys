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
    // Consulta para obtener los egresos (Gasto, Transferencia y Otro clasificado como Egreso)
    $sql = "SELECT * FROM recursos_financieros 
            WHERE tipo_movimiento IN ('Gasto','Transferencia') 
               OR (tipo_movimiento = 'Otro' AND clasificacion = 'Egreso')";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $egresos = $stmt->fetchAll();

    // Consulta para el total de egresos
    $sqlTotal = "SELECT SUM(monto) AS total_egresos 
                 FROM recursos_financieros 
                 WHERE tipo_movimiento IN ('Gasto','Transferencia') 
                    OR (tipo_movimiento = 'Otro' AND clasificacion = 'Egreso')";
    $stmtTotal = $pdo->prepare($sqlTotal);
    $stmtTotal->execute();
    $totalEgresos = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total_egresos'] ?? 0;

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
    <style>
        body {
            background-color: #fff9c4; /* Fondo claro */
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

        h2 {
            color: #2E7D32;
            margin-bottom: 20px;
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

        .table td.fw-bold {
            color: #c62828; /* Rojo para montos de egresos */
        }

        /* Card resumen */
        .resumen-card {
            background: #c62828; /* Rojo para destacar egresos */
            color: #fff;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 25px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        }

        .resumen-card h3 {
            margin: 0;
            font-size: 20px;
        }

        .resumen-card p {
            font-size: 28px;
            font-weight: bold;
            color: #FBC02D;
            margin: 10px 0 0;
        }

        /* Botón volver */
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
    <h2>Lista de Egresos</h2>

    <!-- Card Resumen -->
    <div class="resumen-card">
        <h3>Total de Egresos Registrados</h3>
        <p>$<?= number_format($totalEgresos, 0, ',', '.') ?></p>
    </div>

    <!-- Tabla -->
    <table class="table table-striped table-hover">
        <thead>
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
            <?php if(count($egresos) > 0): ?>
                <?php foreach ($egresos as $egreso): ?>
                <tr>
                    <td><?= htmlspecialchars($egreso['id']) ?></td>
                    <td><?= htmlspecialchars($egreso['descripcion']) ?></td>
                    <td>
                        <?php if ($egreso['tipo_movimiento'] === 'Otro'): ?>
                            Otro (<?= htmlspecialchars($egreso['clasificacion']) ?>)
                        <?php else: ?>
                            <?= htmlspecialchars($egreso['tipo_movimiento']) ?>
                        <?php endif; ?>
                    </td>
                    <td class="fw-bold">$<?= number_format($egreso['monto'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($egreso['fecha']) ?></td>
                    <td><?= htmlspecialchars($egreso['responsable'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($egreso['observaciones'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">No hay egresos registrados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <a href="dashboard_tesoreria.php" class="btn btn-custom mt-3">⬅ Volver al Dashboard</a>
</div>

</body>
</html>
