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
<title>Editar Acta - AsoJuntaSys</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    :root{
        --verde:#2e7d32;
        --verde-oscuro:#1b5e20;
        --verde-claro:#e8f5e9;
        --texto:#1f2a21;
        --suave:#5b665d;
        --linea:#d9e2da;
        --fondo:#f4f7f4;
    }
    body{
        background:var(--fondo);
        color:var(--texto);
        font-family:"Segoe UI", system-ui, Arial, sans-serif;
    }

    /* Encabezado */
    .cabecera{
        background:var(--verde-oscuro);
        color:#fff;
        padding:1.6rem 0 1.4rem;
        margin-bottom:1.75rem;
    }
    .cabecera .ruta{
        font-size:.85rem;
        opacity:.85;
        margin-bottom:.35rem;
    }
    .cabecera .ruta a{ color:#fff; text-decoration:none; }
    .cabecera .ruta a:hover{ text-decoration:underline; }
    .cabecera h1{
        font-size:1.7rem;
        font-weight:600;
        margin:0;
    }
    .cabecera .meta{
        margin-top:.35rem;
        font-size:.9rem;
        opacity:.85;
    }

    /* Formulario */
    .hoja{
        background:#fff;
        border:1px solid var(--linea);
        border-radius:10px;
        padding:1.75rem 1.75rem .5rem;
    }
    .seccion{
        border-left:4px solid var(--verde);
        padding-left:.75rem;
        margin:0 0 1.1rem;
    }
    .seccion h2{
        font-size:1.05rem;
        font-weight:600;
        color:var(--verde-oscuro);
        margin:0;
    }
    .seccion p{
        font-size:.88rem;
        color:var(--suave);
        margin:0;
    }
    .bloque{ margin-bottom:1.75rem; }
    .form-label{ font-weight:600; font-size:.92rem; margin-bottom:.3rem; }
    .form-control, .form-select{
        border-color:#c9d6cb;
        border-radius:8px;
    }
    .form-control:focus, .form-select:focus{
        border-color:var(--verde);
        box-shadow:0 0 0 .22rem rgba(46,125,50,.2);
    }
    textarea.form-control{ resize:vertical; }
    .form-text{ color:var(--suave); }
    .contador{
        display:inline-block;
        background:var(--verde-claro);
        color:var(--verde-oscuro);
        border-radius:999px;
        padding:0 .6rem;
        font-weight:600;
    }

    /* Barra de acciones fija abajo */
    .acciones{
        position:sticky;
        bottom:0;
        background:#fff;
        border-top:1px solid var(--linea);
        margin:0 -1.75rem;
        padding:.9rem 1.75rem;
        border-radius:0 0 10px 10px;
        display:flex;
        gap:.6rem;
        align-items:center;
    }
    .acciones .estado{
        margin-left:auto;
        font-size:.85rem;
        color:var(--suave);
    }
    .btn-verde{
        background:var(--verde);
        border-color:var(--verde);
        color:#fff;
        font-weight:600;
        padding:.5rem 1.25rem;
        border-radius:8px;
    }
    .btn-verde:hover, .btn-verde:focus{
        background:var(--verde-oscuro);
        border-color:var(--verde-oscuro);
        color:#fff;
    }
    .btn-verde:focus-visible{ box-shadow:0 0 0 .22rem rgba(46,125,50,.35); }
    .btn-cancelar{
        border:1px solid #b9c5bb;
        color:var(--texto);
        background:#fff;
        padding:.5rem 1.1rem;
        border-radius:8px;
    }
    .btn-cancelar:hover{ background:var(--verde-claro); border-color:var(--verde); color:var(--verde-oscuro); }

    .alert-error{
        background:#fdecea;
        border:1px solid #f5c2c0;
        border-left:4px solid #c62828;
        color:#7f1d1d;
        border-radius:8px;
    }

    @media (max-width:576px){
        .hoja{ padding:1.25rem 1rem .5rem; }
        .acciones{ margin:0 -1rem; padding:.8rem 1rem; }
        .acciones .estado{ display:none; }
        .cabecera h1{ font-size:1.4rem; }
    }
</style>
</head>
<body>

<header class="cabecera">
    <div class="container" style="max-width: 900px;">
        <div class="ruta"><a href="actas.php">Actas</a> / Editar acta</div>
        <h1>✏️ Editar acta</h1>
        <div class="meta">
            Acta #<?= (int)$acta['id'] ?>
            <?php if ($fechaCreacion): ?> · Creada el <?= e($fechaCreacion) ?><?php endif; ?>
        </div>
    </div>
</header>

<div class="container pb-4" style="max-width: 900px;">

    <?php if (!empty($_GET['error'])): ?>
        <div class="alert alert-error" role="alert">
            <strong>No se guardaron los cambios.</strong> <?= e($_GET['error']) ?>
        </div>
    <?php endif; ?>

    <form action="../controllers/actualizar_acta.php" method="POST" id="formActa" class="hoja">
        <input type="hidden" name="id" value="<?= (int)$acta['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

        <!-- Datos de la reunión -->
        <div class="bloque">
            <div class="seccion">
                <h2>Datos de la reunión</h2>
                <p>Los campos marcados con * son obligatorios.</p>
            </div>

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

        <!-- Contenido del acta -->
        <div class="bloque">
            <div class="seccion">
                <h2>Contenido del acta</h2>
                <p>Quiénes asistieron, qué se trató y qué se acordó.</p>
            </div>

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

        <!-- Soporte -->
        <div class="bloque">
            <div class="seccion">
                <h2>Soporte</h2>
                <p>Opcional: vincula un documento ya cargado en Gestión Documental.</p>
            </div>

            <div class="mb-3">
                <label for="documento_id" class="form-label">Documento de soporte</label>
                <select class="form-select" id="documento_id" name="documento_id">
                    <option value="">Sin documento vinculado</option>
                    <?php foreach ($documentos as $d): ?>
                        <option value="<?= (int)$d['id'] ?>" <?= (string)($acta['documento_id'] ?? '') === (string)$d['id'] ? 'selected' : '' ?>>
                            <?= e($d['titulo']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Por ejemplo, el acta firmada y escaneada.</div>
            </div>
        </div>

        <!-- Acciones -->
        <div class="acciones">
            <button type="submit" class="btn btn-verde" id="btnGuardar">💾 Guardar cambios</button>
            <a href="actas.php" class="btn btn-cancelar">Cancelar</a>
            <span class="estado" id="estadoCambios">Sin cambios</span>
        </div>
    </form>
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