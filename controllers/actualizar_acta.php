<?php
require_once('../includes/auth.php');
require_once('../includes/auditoria.php');
require('../config/db.php');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

requireRole(['Secretaría']);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../views/actas.php");
    exit();
}

validarTokenCSRF($_POST['csrf_token'] ?? '');

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    die("❌ ID de acta no válido.");
}

// JAC del usuario que inició sesión
$stmt = $pdo->prepare("SELECT jac_id FROM usuarios WHERE id = :uid");
$stmt->execute([':uid' => $_SESSION['usuario_id']]);
$jacId = $stmt->fetchColumn();

// El acta debe existir y pertenecer a la JAC del usuario
$stmt = $pdo->prepare("SELECT id FROM actas WHERE id = :id AND jac_id = :jac");
$stmt->execute([':id' => $id, ':jac' => $jacId]);
if (!$stmt->fetchColumn()) {
    die("❌ Acta no encontrada.");
}

// Datos del formulario
$titulo        = trim($_POST['titulo'] ?? '');
$fecha_reunion = trim($_POST['fecha_reunion'] ?? '');
$hora_reunion  = trim($_POST['hora_reunion'] ?? '');
$lugar         = trim($_POST['lugar'] ?? '');
$asistentes    = trim($_POST['asistentes'] ?? '');
$orden_dia     = trim($_POST['orden_dia'] ?? '');
$acuerdos      = trim($_POST['acuerdos'] ?? '');
$observaciones = trim($_POST['observaciones'] ?? '');
$documento_id  = trim($_POST['documento_id'] ?? '');

// Validación
$error = '';

if ($titulo === '' || mb_strlen($titulo) > 150) {
    $error = 'El título es obligatorio y debe tener máximo 150 caracteres.';
} elseif (!($f = DateTime::createFromFormat('Y-m-d', $fecha_reunion)) || $f->format('Y-m-d') !== $fecha_reunion) {
    $error = 'Ingrese una fecha válida.';
} elseif ($hora_reunion !== '' && !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hora_reunion)) {
    $error = 'Ingrese una hora válida (HH:MM).';
} elseif ($lugar === '' || mb_strlen($lugar) > 150) {
    $error = 'El lugar es obligatorio y debe tener máximo 150 caracteres.';
} elseif ($asistentes === '') {
    $error = 'Registre al menos un asistente.';
} elseif ($acuerdos === '') {
    $error = 'Registre al menos un acuerdo.';
} elseif (mb_strlen($asistentes) > 5000 || mb_strlen($orden_dia) > 5000
       || mb_strlen($acuerdos) > 5000 || mb_strlen($observaciones) > 5000) {
    $error = 'Los campos de texto admiten máximo 5000 caracteres.';
}

// El documento de soporte, si se eligió, debe pertenecer a la misma JAC
$docId = null;
if ($error === '' && $documento_id !== '') {
    $stmt = $pdo->prepare("SELECT id FROM documentos WHERE id = :did AND jac_id = :jac");
    $stmt->execute([':did' => (int)$documento_id, ':jac' => $jacId]);
    $docId = $stmt->fetchColumn();
    if (!$docId) {
        $error = 'Seleccione un documento válido.';
    }
}

if ($error !== '') {
    header("Location: ../views/editar_acta.php?id=" . $id . "&error=" . urlencode($error));
    exit();
}

$sql = "UPDATE actas
        SET titulo = :titulo, fecha_reunion = :fecha_reunion, hora_reunion = :hora_reunion,
            lugar = :lugar, asistentes = :asistentes, orden_dia = :orden_dia,
            acuerdos = :acuerdos, observaciones = :observaciones, documento_id = :documento_id
        WHERE id = :id AND jac_id = :jac";
$stmt = $pdo->prepare($sql);

$stmt->execute([
    ':titulo'        => $titulo,
    ':fecha_reunion' => $fecha_reunion,
    ':hora_reunion'  => $hora_reunion !== '' ? $hora_reunion : null,
    ':lugar'         => $lugar,
    ':asistentes'    => $asistentes,
    ':orden_dia'     => $orden_dia !== '' ? $orden_dia : null,
    ':acuerdos'      => $acuerdos,
    ':observaciones' => $observaciones !== '' ? $observaciones : null,
    ':documento_id'  => $docId ?: null,
    ':id'            => $id,
    ':jac'           => $jacId
]);

registrarAuditoria($pdo, 'editar', 'acta', (int) $id, $titulo);

header("Location: ../views/actas.php?success=edit");
exit();