<?php
session_start();
require('../config/db.php');
require_once('../includes/auth.php');

if (!isset($_SESSION['usuario_id']) ||
   !in_array($_SESSION['usuario_rol'], ['Secretaría', 'Presidente General', 'Presidentes de JAC'])) {
    header("Location: ../views/login.php");
    exit();
}

$csrfToken = generarTokenCSRF();
$puedeGestionar = ($_SESSION['usuario_rol'] === 'Secretaría');

$dashboardsPorRol = [
    'Presidente General' => 'dashboard_presidente.php',
    'Presidentes de JAC' => 'dashboard_jac.php',
    'Secretaría'         => 'dashboard_secretario.php',
];
$urlDashboard = $dashboardsPorRol[$_SESSION['usuario_rol']] ?? 'dashboard.php';


$nombre = $_SESSION['usuario_nombre'];

// Consultar actas (filtradas por JAC del usuario, salvo Presidente General que ve todas)
$sql = "SELECT a.id, a.titulo, d.titulo AS documento, a.fecha_reunion, a.lugar, 
               a.asistentes, a.acuerdos, a.observaciones
        FROM actas a
        LEFT JOIN documentos d ON a.documento_id = d.id";
$paramsActas = [];
if ($_SESSION['usuario_rol'] !== 'Presidente General' && !empty($_SESSION['jac_id'])) {
    $sql .= " WHERE a.jac_id = :jac_id";
    $paramsActas[':jac_id'] = $_SESSION['jac_id'];
}
$sql .= " ORDER BY a.fecha_reunion DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsActas);
$actas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Sacar lugares únicos para filtro
$lugares = $pdo->query("SELECT DISTINCT lugar FROM actas ORDER BY lugar ASC")->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_web.png">
<meta charset="UTF-8">
<title>Actas - Secretaría</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<style>
body { background-color: #fff9c4; }
.navbar { background: linear-gradient(135deg, #2E7D32, #1b5e20) !important; }
.navbar .nav-link:hover { color: #FBC02D !important; }
.table thead { background-color: #2E7D32; color: #fff; }
.table tbody tr:hover { background-color: #E8F5E9; }
.btn-custom { background-color: #2E7D32; color: #fff; }
.btn-custom:hover { background-color: #FBC02D; color: #000; }
.alert { transition: opacity 0.8s ease-out, transform 0.8s ease-out; }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= htmlspecialchars($urlDashboard) ?>">Junta de Acción Comunal</a>
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

    <!-- 🔹 Mensajes de éxito -->
    <?php if (isset($_GET['success'])): ?>
        <?php if ($_GET['success'] == "1"): ?>
            <div id="successMessage" class="alert alert-success">El acta fue registrada exitosamente.</div>
        <?php elseif ($_GET['success'] == "edit"): ?>
            <div id="successMessage" class="alert alert-warning">El acta fue actualizada correctamente.</div>
        <?php elseif ($_GET['success'] == "delete"): ?>
            <div id="successMessage" class="alert alert-danger">El acta fue eliminada correctamente.</div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- 🔹 Barra de filtros -->
    <div class="row mb-3">
        <div class="col-md-4">
            <input type="text" id="searchInput" class="form-control" placeholder="🔍 Buscar por título, asistentes o lugar">
        </div>
        <div class="col-md-3">
            <input type="date" id="fechaInicio" class="form-control">
        </div>
        <div class="col-md-3">
            <input type="date" id="fechaFin" class="form-control">
        </div>
        <div class="col-md-2">
            <select id="filtroLugar" class="form-select">
                <option value="">📍 Todos los lugares</option>
                <?php foreach ($lugares as $l): ?>
                    <option value="<?= htmlspecialchars($l) ?>"><?= htmlspecialchars($l) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <a href="registrar_acta.php" class="btn btn-custom mb-3">Registrar Nueva Acta</a>
    <a href="<?= htmlspecialchars($urlDashboard) ?>" class="btn btn-custom mb-3">Volver al Dashboard</a>

    <!-- 🔹 Tabla de Actas -->
    <table class="table table-bordered table-hover" id="tablaActas">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Documento Asociado</th>
                <th>Fecha</th>
                <th>Lugar</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($actas) > 0): ?>
                <?php foreach ($actas as $acta): ?>
                    <tr>
                        <td><?= $acta['id'] ?></td>
                        <td><?= htmlspecialchars($acta['titulo']) ?></td>
                        <td><?= $acta['documento'] ?? '-' ?></td>
                        <td><?= $acta['fecha_reunion'] ?></td>
                        <td><?= $acta['lugar'] ?></td>
                        <td>
                            <button class="btn btn-info btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detalleActa<?= $acta['id'] ?>">Ver</button>
                            <?php if ($puedeGestionar): ?>
                            <a href="editar_acta.php?id=<?= $acta['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                            <form action="../controllers/eliminar_acta.php" method="POST" style="display:inline;"
                                  onsubmit="return confirm('¿Seguro que deseas eliminar esta acta?')">
                                <input type="hidden" name="id" value="<?= $acta['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                            <?php endif; ?>
                             <a href="../controllers/exportar_acta.php?id=<?= $acta['id'] ?>" target="_blank" class="btn btn-info btn-sm">
                            ⬇ Descargar en PDF</a>
                        </td>
                    </tr>

                    <!-- Modal -->
                    <div class="modal fade" id="detalleActa<?= $acta['id'] ?>" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Detalles del Acta</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Título:</strong> <?= htmlspecialchars($acta['titulo']) ?></p>
                                    <p><strong>Documento Asociado:</strong> <?= $acta['documento'] ?? '-' ?></p>
                                    <p><strong>Fecha:</strong> <?= $acta['fecha_reunion'] ?></p>
                                    <p><strong>Lugar:</strong> <?= $acta['lugar'] ?></p>
                                    <p><strong>Asistentes:</strong> <?= nl2br(htmlspecialchars($acta['asistentes'] ?? '')) ?></p>
                                    <p><strong>Acuerdos:</strong> <?= nl2br(htmlspecialchars($acta['acuerdos'] ?? '')) ?></p>
                                    <p><strong>Observaciones:</strong> <?= nl2br(htmlspecialchars($acta['observaciones'] ?? '')) ?></p>
                                </div>
                                <div class="modal-footer">
                                    
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">No hay actas registradas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- 🔹 Scripts -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    let msg = document.getElementById("successMessage");
    if (msg) {
        setTimeout(() => {
            msg.style.opacity = "0";
            msg.style.transform = "translateY(-10px)";
            setTimeout(() => msg.remove(), 800);
        }, 3000);
    }

    // Filtros
    const searchInput = document.getElementById("searchInput");
    const fechaInicio = document.getElementById("fechaInicio");
    const fechaFin = document.getElementById("fechaFin");
    const filtroLugar = document.getElementById("filtroLugar");
    const tabla = document.getElementById("tablaActas").getElementsByTagName("tbody")[0];

    function filtrar() {
        const texto = searchInput.value.toLowerCase();
        const inicio = fechaInicio.value ? new Date(fechaInicio.value) : null;
        const fin = fechaFin.value ? new Date(fechaFin.value) : null;
        const lugar = filtroLugar.value.toLowerCase();

        Array.from(tabla.rows).forEach(row => {
            const titulo = row.cells[1].innerText.toLowerCase();
            const fecha = new Date(row.cells[3].innerText);
            const sitio = row.cells[4].innerText.toLowerCase();
            const asistentes = row.cells[4].innerText.toLowerCase();

            let visible = true;

            if (texto && !(titulo.includes(texto) || asistentes.includes(texto) || sitio.includes(texto))) {
                visible = false;
            }
            if (inicio && fecha < inicio) visible = false;
            if (fin && fecha > fin) visible = false;
            if (lugar && sitio !== lugar) visible = false;

            row.style.display = visible ? "" : "none";
        });
    }

    searchInput.addEventListener("keyup", filtrar);
    fechaInicio.addEventListener("change", filtrar);
    fechaFin.addEventListener("change", filtrar);
    filtroLugar.addEventListener("change", filtrar);
});
</script>

</body>
</html>
