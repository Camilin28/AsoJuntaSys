<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/auditoria.php';

// Solo el Presidente General puede crear usuarios
requireRole(['Presidente General']);

if ($_SERVER["REQUEST_METHOD"] !== 'POST') {
    header("Location: ../views/dashboard_presidente.php");
    exit();
}

validarTokenCSRF($_POST['csrf_token'] ?? '');

$nombre       = trim($_POST['nombre'] ?? '');
$email        = trim($_POST['email'] ?? '');
$passwordPlano = $_POST['password'] ?? '';
$rol          = $_POST['rol'] ?? '';
$jac_id       = !empty($_POST['jac_id']) ? (int)$_POST['jac_id'] : null;

// Validaciones básicas
if (empty($nombre) || empty($email) || empty($passwordPlano) || empty($rol)) {
    $_SESSION['error'] = "❌ Todos los campos son obligatorios.";
    header("Location: ../views/registrar.php");
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = "❌ El correo electrónico no es válido.";
    header("Location: ../views/registrar.php");
    exit();
}

$rolesPermitidos = ['Presidentes de JAC', 'Secretaría', 'Tesorería'];
if (!in_array($rol, $rolesPermitidos)) {
    $_SESSION['error'] = "❌ Rol no válido.";
    header("Location: ../views/registrar.php");
    exit();
}

$password = password_hash($passwordPlano, PASSWORD_DEFAULT);

try {
    // Verificar que el email no esté registrado
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        $_SESSION['error'] = "❌ Ya existe un usuario con ese correo electrónico.";
        header("Location: ../views/registrar.php");
        exit();
    }

    $sql = "INSERT INTO usuarios (nombre, email, contraseña, rol, jac_id) 
            VALUES (:nombre, :email, :password, :rol, :jac_id)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'   => $nombre,
        ':email'    => $email,
        ':password' => $password,
        ':rol'      => $rol,
        ':jac_id'   => $jac_id,
    ]);

    registrarAuditoria($pdo, 'crear', 'usuario', (int) $pdo->lastInsertId(), "{$nombre} ({$rol})");

    $_SESSION['mensaje'] = "✅ Usuario '{$nombre}' creado correctamente.";
    header("Location: ../views/registrar.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "❌ Error al crear el usuario.";
    header("Location: ../views/registrar.php");
    exit();
}
?>