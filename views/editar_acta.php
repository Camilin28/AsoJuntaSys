<?php
session_start();
require('../config/db.php');
require_once('../includes/auth.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

$csrfToken = generarTokenCSRF();

if (!function_exists('e')) {
    function e($v) {
        return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
    }
}

$dashboardsPorRol = [
    'Presidente General' => 'dashboard_presidente.php',
    'Presidentes de JAC' => 'dashboard_jac.php',
    'Secretaría'         => 'dashboard_secretario.php',
];
$urlDashboard = $dashboardsPorRol[$_SESSION['usuario_rol']] ?? 'dashboard.php';
$nombre = $_SESSION['usuario_nombre'];

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("❌ ID de acta no proporcionado.");
}

// JAC del usuario que inició sesión
$stmt = $pdo->prepare("SELECT jac_id FROM usuarios WHERE id = :uid");
$stmt->execute([':uid' => $_SESSION['usuario_id']]);
$jacId = $stmt->fetchColumn();

// Obtener el acta, solo si pertenece a la JAC del usuario
$stmt = $pdo->prepare("SELECT * FROM actas WHERE id = :id AND jac_id = :jac");
$stmt->execute([':id' => $id, ':jac' => $jacId]);
$acta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$acta) {
    die("❌ Acta no encontrada.");
}

// Documentos de la JAC para vincular como soporte
$stmt = $pdo->prepare("SELECT id, titulo FROM documentos WHERE jac_id = :jac ORDER BY fecha_subida DESC");
$stmt->execute([':jac' => $jacId]);
$documentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$horaReunion   = substr((string)($acta['hora_reunion'] ?? ''), 0, 5);
$fechaCreacion = !empty($acta['fecha_creacion']) ? date('d/m/Y', strtotime($acta['fecha_creacion'])) : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_web.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar Acta - Secretaría</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background-color: #fff9c4; }
.navbar { background: linear-gradient(135deg, #2E7D32, #1b5e20) !important; }
.navbar .nav-link:hover { color: #FBC02D !important; }

.btn-custom { background-color: #2E7D32; color: #fff; font-weight: 600; }
.btn-custom:hover, .btn-custom:focus { background-color: #FBC02D; color: #000; }

.tarjeta-acta {
    border: 0;
    border-radius: 12px;
    box-shadow: 0 4px 14px rgba(0,0,0,.15);
    overflow: hidden;
}
.tarjeta-acta .card-header {
    background: linear-gradient(135deg, #2E7D32, #1b5e20);
    color: #fff;
    font-weight: 600;
    padding: .8rem 1.25rem;
}
.tarjeta-acta .card-body { padding: 1.5rem 1.5rem .5rem; }
.tarjeta-acta .card-footer {
    background: #f6fbf6;
    border-top: 1px solid #dcebdc;
    padding: .9rem 1.5rem;
    display: flex;
    gap: .6rem;
    align-items: center;
}
.tarjeta-acta .estado { margin-left: auto; font-size: .85rem; color: #6c757d; }

.seccion-titulo {
    color: #1b5e20;
    font-weight: 700;
    font-size: 1rem;
    border-bottom: 2px solid #FBC02D;
    padding-bottom: .35rem;
    margin: 0 0 1rem;
}
.seccion + .seccion { margin-top: 1.5rem; }

.form-label { font-weight: 600; font-size: .92rem; }
.form-control:focus, .form-select:focus {
    border-color: #2E7D32;
    box-shadow: 0 0 0 .2rem rgba(46,125,50,.25);
}
.contador {
    display: inline-block;
    background: #E8F5E9;
    color: #1b5e20;
    border-radius: 999px;
    padding: 0 .6rem;
    font-weight: 600;
}

@media (max-width: 576px) {
    .tarjeta-acta .estado { display: none; }
}
</style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= e($urlDashboard) ?>">Junta de Acción Comunal</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><span class="nav-link text-white">Bienvenida, <?= e($nombre) ?></span></li>
                <li class="nav-item"><a class="nav-link text-white" href="../public/logout.php">Cerrar sesión</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <h2 class="mb-1">✏️ Editar Acta</h2>
            <p class="text-muted mb-4">
                Acta N° <?= (int)$acta['id'] ?>
                <?php if ($fechaCreacion): ?> · Registrada el <?= e($fechaCreacion) ?><?php endif; ?>
            </p>

            <?php if (!empty($_GET['error'])): ?>
                <div class="alert alert-danger" role="alert">
                    <strong>No se guardaron los cambios.</strong> <?= e($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <form action="../controllers/actualizar_acta.php" method="POST" id="formActa">
                <input type="hidden" name="id" value="<?= (int)$acta['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <div class="card tarjeta-acta">
                    <div class="card-header">📄 Información del acta <span class="fw-normal">(* obligatorio)</span></div>

                    <div class="card-body">

                        <div class="seccion">
                            <h6 class="seccion-titulo">Datos de la reunión</h6>

                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" maxlength="150"
                                       value="<?= e($acta['titulo']) ?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="fecha_reunion" class="form-label">Fecha *</label>
                                    <input type="date" class="form-control" id="fecha_reunion" name="fecha_reunion"
                                           value="<?= e($acta['fecha_reunion']) ?>" required>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="hora_reunion" class="form-label">Hora</label>
                                    <input type="time" class="form-control" id="hora_reunion" name="hora_reunion"
                                           value="<?= e($horaReunion) ?>">
                                </div>
                                <div class="col-md-5 mb-3">
                                    <label for="lugar" class="form-label">Lugar *</label>
                                    <input type="text" class="form-control" id="lugar" name="lugar" maxlength="150"
                                           value="<?= e($acta['lugar']) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="seccion">
                            <h6 class="seccion-titulo">Contenido del acta</h6>

                            <div class="mb-3">
                                <label for="asistentes" class="form-label">Asistentes * <span class="text-muted fw-normal">(uno por línea)</span></label>
                                <textarea class="form-control" id="asistentes" name="asistentes" rows="4" required><?= e($acta['asistentes'] ?? '') ?></textarea>
                                <div class="form-text"><span class="contador" id="contadorAsistentes">0</span> asistente(s) registrado(s)</div>
                            </div>

                            <div class="mb-3">
                                <label for="orden_dia" class="form-label">Orden del día</label>
                                <textarea class="form-control" id="orden_dia" name="orden_dia" rows="3"><?= e($acta['orden_dia'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="acuerdos" class="form-label">Acuerdos *</label>
                                <textarea class="form-control" id="acuerdos" name="acuerdos" rows="4" required><?= e($acta['acuerdos'] ?? '') ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="3"><?= e($acta['observaciones'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <div class="seccion">
                            <h6 class="seccion-titulo">Soporte</h6>

                            <div class="mb-3">
                                <label for="documento_id" class="form-label">Documento asociado</label>
                                <select class="form-select" id="documento_id" name="documento_id">
                                    <option value="">Sin documento vinculado</option>
                                    <?php foreach ($documentos as $d): ?>
                                        <option value="<?= (int)$d['id'] ?>" <?= (string)($acta['documento_id'] ?? '') === (string)$d['id'] ? 'selected' : '' ?>>
                                            <?= e($d['titulo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Opcional. Por ejemplo, el acta firmada que subió a Documentos Oficiales.</div>
                            </div>
                        </div>

                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-custom" id="btnGuardar">💾 Guardar cambios</button>
                        <a href="actas.php" class="btn btn-secondary">Cancelar</a>
                        <span class="estado" id="estadoCambios">Sin cambios</span>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    const form = document.getElementById('formActa');
    const asistentes = document.getElementById('asistentes');
    const contador = document.getElementById('contadorAsistentes');
    const estado = document.getElementById('estadoCambios');
    let hayCambios = false;
    let enviando = false;

    function contarAsistentes() {
        contador.textContent = asistentes.value.split('\n').filter(l => l.trim() !== '').length;
    }
    asistentes.addEventListener('input', contarAsistentes);
    contarAsistentes();

    form.addEventListener('input', () => {
        hayCambios = true;
        estado.textContent = 'Cambios sin guardar';
    });
    form.addEventListener('submit', () => {
        enviando = true;
        const btn = document.getElementById('btnGuardar');
        btn.disabled = true;
        btn.textContent = 'Guardando...';
    });
    window.addEventListener('beforeunload', (ev) => {
        if (hayCambios && !enviando) {
            ev.preventDefault();
            ev.returnValue = '';
        }
    });
</script>
</body>
</html>