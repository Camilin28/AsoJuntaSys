<?php
session_start();
require('../config/db.php');

// 🔐 Solo Presidente General
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Presidente General') {
    header("Location: ../views/login.php");
    exit();
}

try {
    $stmt = $pdo->query("SELECT * FROM juntas ORDER BY id DESC");
    $juntas = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $juntas = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de JAC - AsoJuntaSys</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root{
            --verde-principal:#2E7D32;
            --verde-secundario:#81C784;
            --amarillo:#FBC02D;
            --amarillo-suave:#FFF9C4;
            --gris:#424242;
        }

        body{
            background:#f5f7fa;
        }

        .header-card{
            background:linear-gradient(135deg,var(--verde-principal),var(--verde-secundario));
            color:white;
            border-radius:15px;
            padding:25px;
            margin-bottom:25px;
            box-shadow:0 4px 15px rgba(0,0,0,.15);
        }

        .stat-card{
            border:none;
            border-radius:15px;
            box-shadow:0 2px 10px rgba(0,0,0,.08);
        }

        .stat-number{
            font-size:2rem;
            font-weight:bold;
            color:var(--verde-principal);
        }

        .table-card{
            border:none;
            border-radius:15px;
            overflow:hidden;
            box-shadow:0 2px 15px rgba(0,0,0,.08);
        }

        .table thead{
            background:var(--verde-principal);
            color:white;
        }

        .table tbody tr:hover{
            background:#f0f8f1;
        }

        .btn-crear{
            background:var(--amarillo);
            border:none;
            color:black;
            font-weight:bold;
        }

        .btn-crear:hover{
            background:#e5af00;
        }

        .btn-editar{
            background:var(--verde-principal);
            border:none;
            color:white;
        }

        .btn-editar:hover{
            background:#1f5d24;
            color:white;
        }

        .badge-jac{
            background:var(--verde-secundario);
            color:black;
            font-weight:600;
        }
    </style>
</head>

<body>

<div class="container py-4">

    <div class="header-card">
        <h2 class="mb-2">🏘️ Gestión de Juntas de Acción Comunal</h2>
        <p class="mb-0">
            Administra las Juntas de Acción Comunal registradas en AsoJuntaSys.
        </p>
    </div>

    <div class="row mb-4">

        <div class="col-md-4 mb-3">
            <div class="card stat-card">
                <div class="card-body text-center">
                    <div class="stat-number">
                        <?= count($juntas) ?>
                    </div>
                    <div>Total JAC Registradas</div>
                </div>
            </div>
        </div>

        <div class="col-md-8 mb-3 d-flex align-items-center justify-content-end">
            <a href="crear_jac.php" class="btn btn-crear btn-lg">
                ➕ Crear Nueva JAC
            </a>
        </div>

    </div>

    <div class="card table-card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                Listado de Juntas
            </h5>
        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table align-middle table-hover">

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th width="120">Acciones</th>
                        </tr>
                    </thead>

                    <tbody>

                    <?php if(empty($juntas)): ?>

                        <tr>
                            <td colspan="6" class="text-center">
                                No existen Juntas registradas.
                            </td>
                        </tr>

                    <?php else: ?>

                        <?php foreach ($juntas as $jac): ?>

                        <tr>

                            <td>
                                <?= $jac['id'] ?>
                            </td>

                            <td>
                                <strong>
                                    <?= htmlspecialchars($jac['nombre']) ?>
                                </strong>
                            </td>

                            <td>
                                <?= htmlspecialchars($jac['direccion']) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($jac['telefono']) ?>
                            </td>

                            <td>
                                <span class="badge badge-jac">
                                    Activa
                                </span>
                            </td>

                            <td>
                                <a href="editar_jac.php?id=<?= $jac['id'] ?>"
                                   class="btn btn-sm btn-editar">
                                    ✏️ Editar
                                </a>
                            </td>

                        </tr>

                        <?php endforeach; ?>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

</body>
</html>