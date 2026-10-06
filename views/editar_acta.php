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

$horaReunion = substr((string)($acta['hora_reunion'] ?? ''), 0, 5);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<link rel="icon" type="image/png" href="../imagenes/Logo_web.png">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Editar Acta</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4" style="max-width: 900px;">
    <h2>✏️ Editar Acta</h2>
    <p class="text-muted">Los campos marcados con * son obligatorios.</p>
    <?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger" role="alert">
        <?= e($_GET['error']) ?>
    </div>
<?php endif; ?>

    <form action="../controllers/actualizar_acta.php" method="POST" id="formActa">
        <input type="hidden" name="id" value="<?= (int)$acta['id'] ?>">
        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

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

        <div class="mb-3">
            <label for="asistentes" class="form-label">Asistentes * <span class="text-muted fw-normal">(uno por línea)</span></label>
            <textarea class="form-control" id="asistentes" name="asistentes" rows="4" required><?= e($acta['asistentes'] ?? '') ?></textarea>
            <div class="form-text"><span id="contadorAsistentes">0</span> asistente(s) registrado(s)</div>
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

        <div class="mb-4">
            <label for="documento_id" class="form-label">Documento de soporte</label>
            <select class="form-select" id="documento_id" name="documento_id">
                <option value="">Sin documento vinculado</option>
                <?php foreach ($documentos as $d): ?>
                    <option value="<?= (int)$d['id'] ?>" <?= (string)($acta['documento_id'] ?? '') === (string)$d['id'] ? 'selected' : '' ?>>
                        <?= e($d['titulo']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text">Por ejemplo, el acta firmada que subió a Gestión Documental.</div>
        </div>

        <button type="submit" class="btn btn-success" id="btnGuardar">💾 Guardar Cambios</button>
        <a href="actas.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>

<script>
    const form = document.getElementById('formActa');
    const asistentes = document.getElementById('asistentes');
    const contador = document.getElementById('contadorAsistentes');
    let hayCambios = false;
    let enviando = false;

    function contarAsistentes() {
        contador.textContent = asistentes.value.split('\n').filter(l => l.trim() !== '').length;
    }
    asistentes.addEventListener('input', contarAsistentes);
    contarAsistentes();

    form.addEventListener('input', () => { hayCambios = true; });
    form.addEventListener('submit', () => {
        enviando = true;
        document.getElementById('btnGuardar').disabled = true;
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