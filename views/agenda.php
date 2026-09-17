<?php
session_start();
require('../config/db.php');
require_once('../includes/auth.php');

if (!isset($_SESSION['usuario_id']) ||
   !in_array($_SESSION['usuario_rol'], ['Secretaría', 'Presidente General', 'Presidentes de JAC'])) {
    header("Location: ../views/login.php");
    exit();
}


$nombre = $_SESSION['usuario_nombre'];
$esSoloLectura = ($_SESSION['usuario_rol'] === 'Secretaría');
$csrfToken = generarTokenCSRF();

$dashboardsPorRol = [
    'Presidente General' => 'dashboard_presidente.php',
    'Presidentes de JAC' => 'dashboard_jac.php',
    'Secretaría'         => 'dashboard_secretario.php',
];
$urlDashboard = $dashboardsPorRol[$_SESSION['usuario_rol']] ?? 'dashboard.php';

// 📌 Obtener eventos desde la tabla agenda (filtrados por JAC del usuario)
$sql = "SELECT id, titulo AS title, fecha AS start, hora, descripcion, color 
        FROM agenda";
$paramsEventos = [];
if (!empty($_SESSION['jac_id'])) {
    $sql .= " WHERE jac_id = :jac_id";
    $paramsEventos[':jac_id'] = $_SESSION['jac_id'];
}
$stmt = $pdo->prepare($sql);
$stmt->execute($paramsEventos);
$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_AsojuntaSys.png">
    <meta charset="UTF-8">
    <title>Agenda - Secretaría</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { background-color: #fff9c4; }
        .navbar { background: linear-gradient(135deg, #2E7D32, #1b5e20) !important; }
        .navbar .nav-link:hover { color: #FBC02D !important; }
        #calendar { background: #ffffffff; padding: 20px; border-radius: 10px; box-shadow: 0px 4px 10px #fbc02d; }
        .btn-custom { background-color: #2E7D32; color: #fff; }
        .btn-custom:hover { background-color: #FBC02D; color: #000; }
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
<?php if (isset($_GET['msg'])): ?>
    <div id="msgAlert" 
         class="alert 
                <?= $_GET['msg'] == 'add' ? 'alert-success' : 
                   ($_GET['msg'] == 'edit' ? 'alert-warning' : 
                   ($_GET['msg'] == 'delete' ? 'alert-danger' : 'alert-info')) ?>">
        <?php if ($_GET['msg'] == 'add'): ?>
            ✅ El evento fue agregado exitosamente.
        <?php elseif ($_GET['msg'] == 'edit'): ?>
            ✏️ El evento fue actualizado correctamente.
        <?php elseif ($_GET['msg'] == 'delete'): ?>
            🗑️ El evento fue eliminado correctamente.
        <?php else: ?>
            ℹ️ Acción realizada.
        <?php endif; ?>
    </div>
<?php endif; ?>
<a href="<?= htmlspecialchars($urlDashboard) ?>" class="btn btn-custom mb-3">Volver al Dashboard</a>
<h2 class="mb-4">📅 Agenda</h2>
<div id="calendar"></div>
</div>

<!-- Modal para agregar/editar evento -->
<div class="modal fade" id="eventoModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="../controllers/guardar_evento.php" method="POST" class="modal-content" onsubmit="return validarAccion(this)">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Registrar / Editar Evento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="eventoId">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <div class="mb-3">
                    <label for="titulo" class="form-label">Título del evento</label>
                    <input type="text" class="form-control" name="titulo" id="eventoTitulo" required>
                </div>
                <div class="mb-3">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea class="form-control" name="descripcion" id="eventoDescripcion" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" class="form-control" name="fecha" id="eventoFecha" required>
                </div>
                <div class="mb-3">
                    <label for="hora" class="form-label">Hora</label>
                    <input type="time" class="form-control" name="hora" id="eventoHora">
                </div>
                <div class="mb-3">
                    <label for="color" class="form-label">🎨 Color del evento</label>
                    <input type="color" class="form-control form-control-color" id="color" name="color" value="#3788d8" title="Elige un color">
                </div>
            </div>
            <div class="modal-footer">
                <?php if (!$esSoloLectura): ?>
                <button type="submit" name="accion" value="guardar" class="btn btn-custom">Guardar</button>
                <button type="submit" name="accion" value="eliminar" class="btn btn-danger">Eliminar</button>
                <?php endif; ?>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </form>
    </div>
</div>

<script>
function validarAccion(form) {
    let accion = form.querySelector("button[type=submit][clicked=true]").value;
    if (accion === "eliminar") {
        return confirm("⚠️ ¿Seguro que deseas eliminar este evento?");
    }
    return true;
}

document.querySelectorAll("form button[type=submit]").forEach(btn => {
    btn.addEventListener("click", function() {
        this.setAttribute("clicked", "true");
        document.querySelectorAll("form button[type=submit]").forEach(other => {
            if (other !== this) other.removeAttribute("clicked");
        });
    });
});

// Inicializar FullCalendar
document.addEventListener('DOMContentLoaded', function() {
    let calendarEl = document.getElementById('calendar');
    let calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        selectable: <?= $esSoloLectura ? 'false' : 'true' ?>,
        events: <?= json_encode($eventos) ?>,
        dateClick: function(info) {
            document.getElementById("eventoId").value = "";
            document.getElementById("eventoTitulo").value = "";
            document.getElementById("eventoDescripcion").value = "";
            document.getElementById("eventoFecha").value = info.dateStr;
            document.getElementById("eventoHora").value = "";
            document.getElementById("color").value = "#3788d8"; // color predeterminado
            let modal = new bootstrap.Modal(document.getElementById('eventoModal'));
            modal.show();
        },
        eventClick: function(info) {
            let event = info.event;
            document.getElementById("eventoId").value = event.id;
            document.getElementById("eventoTitulo").value = event.title;
            document.getElementById("eventoDescripcion").value = event.extendedProps.descripcion || "";
            document.getElementById("eventoFecha").value = event.startStr.split("T")[0];
            document.getElementById("eventoHora").value = event.extendedProps.hora || "";
            document.getElementById("color").value = event.backgroundColor || "#3788d8";
            let modal = new bootstrap.Modal(document.getElementById('eventoModal'));
            modal.show();
        }
    });
    calendar.render();
});

// Desaparecer mensajes automáticamente
document.addEventListener("DOMContentLoaded", function() {
    let msg = document.getElementById("msgAlert");
    if (msg) {
        setTimeout(() => {
            msg.style.opacity = "0";
            msg.style.transform = "translateY(-10px)";
            setTimeout(() => msg.remove(), 800);
        }, 3000);
    }
});
</script>
</body>
</html>
