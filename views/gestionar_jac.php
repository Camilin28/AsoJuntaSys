<?php
session_start();
require('../config/db.php');
require_once('../includes/auth.php');

// 🔐 Solo Presidente General
if (
    !isset($_SESSION['usuario_id']) ||
    $_SESSION['usuario_rol'] !== 'Presidente General'
) {
    header("Location: ../views/login.php");
    exit();
}

$csrfToken = generarTokenCSRF();

try {

    $stmt = $pdo->query("
        SELECT
            j.*,
            COUNT(u.id) AS total_usuarios
        FROM juntas j
        LEFT JOIN usuarios u ON u.jac_id = j.id
        GROUP BY j.id
        ORDER BY j.id DESC
    ");

    $juntas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $juntas = [];
}

$totalJacs = count($juntas);

$totalUsuarios = 0;

foreach ($juntas as $jac) {
    $totalUsuarios += $jac['total_usuarios'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de JAC - AsoJuntaSys</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

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

        .navbar-custom{
            background:var(--verde-principal);
        }

        .header-card{
            background:linear-gradient(
                135deg,
                var(--verde-principal),
                var(--verde-secundario)
            );
            color:white;
            border-radius:18px;
            padding:25px;
            margin-bottom:25px;
            box-shadow:0 5px 20px rgba(0,0,0,.12);
        }

        .stat-card{
            border:none;
            border-radius:15px;
            box-shadow:0 3px 15px rgba(0,0,0,.08);
            transition:.3s;
        }

        .stat-card:hover{
            transform:translateY(-4px);
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
            box-shadow:0 3px 15px rgba(0,0,0,.08);
        }

        .table thead{
            background:var(--verde-principal);
            color:white;
        }

        .table tbody tr:hover{
            background:#f1f9f2;
        }

        .btn-crear{
            background:var(--amarillo);
            color:black;
            border:none;
            font-weight:600;
        }

        .btn-crear:hover{
            background:#e0ac00;
        }

        .search-box{
            border-radius:12px;
        }

        .card-header{
            background:white;
            border-bottom:1px solid #eee;
        }

    </style>

</head>

<body>

<!-- NAVBAR -->

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom">

    <div class="container">

        <span class="navbar-brand fw-bold">
            AsoJuntaSys
        </span>

        <div class="ms-auto d-flex align-items-center">

            <span class="text-white me-3">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
            </span>

            <a href="../public/logout.php"
               class="btn btn-outline-light btn-sm">
                Cerrar sesión
            </a>

        </div>

    </div>

</nav>

<?php if(isset($_GET['updated'])): ?>
<div class="alert alert-success">
    ✅ La JAC fue actualizada correctamente.
</div>
<?php endif; ?>

<?php if(isset($_GET['success'])): ?>
<div class="alert alert-success">
    ✅ El estado de la JAC fue actualizado correctamente.
</div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
<div class="alert alert-danger">
    ❌ Ocurrió un error al actualizar el estado de la JAC.
</div>
<?php endif; ?>

<div class="container py-4">

    <!-- HEADER -->

    <div class="header-card d-flex justify-content-between align-items-center flex-wrap">

        <div>

            <h2 class="mb-2">
                🏘️ Gestión de Juntas de Acción Comunal
            </h2>

            <p class="mb-0">
                Administra y supervisa todas las Juntas registradas en el sistema.
            </p>

        </div>

        <a href="dashboard_presidente.php"
           class="btn btn-light mt-3 mt-md-0">
            <i class="bi bi-arrow-left"></i>
            Volver al Dashboard
        </a>

    </div>

    <!-- ESTADÍSTICAS -->

    <div class="row mb-4">

        <div class="col-md-4 mb-3">

            <div class="card stat-card">

                <div class="card-body text-center">

                    <div class="stat-number">
                        <?= $totalJacs ?>
                    </div>

                    <div>
                        Total de JAC
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-4 mb-3">

            <div class="card stat-card">

                <div class="card-body text-center">

                    <div class="stat-number">
                        <?= $totalUsuarios ?>
                    </div>

                    <div>
                        Usuarios Registrados
                    </div>

                </div>

            </div>

        </div>

        <div class="col-md-4 mb-3 d-flex align-items-center justify-content-end">

            <a href="crear_jac.php"
               class="btn btn-crear btn-lg">

                <i class="bi bi-plus-circle"></i>
                Crear Nueva JAC

            </a>

        </div>

    </div>

    <!-- TABLA -->

    <div class="card table-card">

        <div class="card-header">

            <div class="row align-items-center">

                <div class="col-md-6">

                    <h5 class="mb-0">
                        Listado de Juntas
                    </h5>

                </div>

                <div class="col-md-6 mt-3 mt-md-0">

                    <input type="text"
                           id="buscarJAC"
                           class="form-control search-box"
                           placeholder="Buscar Junta...">

                </div>

            </div>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead>

                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Usuarios</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>

                    </thead>

                    <tbody>

                    <?php if(empty($juntas)): ?>

                        <tr>
                            <td colspan="7" class="text-center">
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
                                    <?= $jac['total_usuarios'] ?>
                                </td>

                                <td>

                                    <?php if($jac['estado'] == 'Activa'): ?>

                                        <span class="badge bg-success">
                                            Activa
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">
                                            Inactiva
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>

                                    <div class="d-flex gap-2">

                                        <a href="ver_jac.php?id=<?= $jac['id'] ?>"
                                           class="btn btn-info btn-sm"
                                           title="Ver">

                                            <i class="bi bi-eye-fill"></i>

                                        </a>

                                        <a href="editar_jac.php?id=<?= $jac['id'] ?>"
                                           class="btn btn-warning btn-sm"
                                           title="Editar">

                                            <i class="bi bi-pencil-square"></i>

                                        </a>

                                                                                <a href="usuarios_jac.php?id=<?= $jac['id'] ?>"
                                           class="btn btn-success btn-sm"
                                           title="Usuarios">

                                            <i class="bi bi-people-fill"></i>

                                        </a>

                                        <form action="../controllers/toggle_estado_jac.php" method="POST" style="display:inline;"
                                              onsubmit="return confirm('<?= $jac['estado'] == 'Activa' ? '¿Desactivar esta JAC? Podrás reactivarla después.' : '¿Reactivar esta JAC?' ?>')">
                                            <input type="hidden" name="id" value="<?= $jac['id'] ?>">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                            <button type="submit"
                                                    class="btn btn-<?= $jac['estado'] == 'Activa' ? 'danger' : 'secondary' ?> btn-sm"
                                                    title="<?= $jac['estado'] == 'Activa' ? 'Desactivar' : 'Reactivar' ?>">
                                                <i class="bi bi-<?= $jac['estado'] == 'Activa' ? 'x-circle-fill' : 'arrow-counterclockwise' ?>"></i>
                                            </button>
                                        </form>

                                    </div>

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

<script>

document.getElementById("buscarJAC")
.addEventListener("keyup", function() {

    let filtro = this.value.toLowerCase();

    document.querySelectorAll("tbody tr")
    .forEach(function(fila){

        fila.style.display =
            fila.innerText.toLowerCase().includes(filtro)
            ? ""
            : "none";

    });

});

</script>

</body>
</html>