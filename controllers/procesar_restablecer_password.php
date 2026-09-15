<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/login.php");
    exit();
}

$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$passwordConfirmar = $_POST['password_confirmar'] ?? '';

if (empty($token)) {
    header("Location: ../views/login.php");
    exit();
}

if (strlen($password) < 8) {
    $_SESSION['error'] = "La contraseña debe tener al menos 8 caracteres.";
    header("Location: ../views/restablecer_password.php?token=" . urlencode($token));
    exit();
}

if ($password !== $passwordConfirmar) {
    $_SESSION['error'] = "Las contraseñas no coinciden.";
    header("Location: ../views/restablecer_password.php?token=" . urlencode($token));
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE reset_token = :token AND reset_token_expira > NOW()");
    $stmt->execute([':token' => $token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        $_SESSION['error'] = "Este enlace ya no es válido. Solicita uno nuevo.";
        header("Location: ../views/recuperar_password.php");
        exit();
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Se invalida el token de un solo uso y se reinician los intentos fallidos (RF-002)
    $update = $pdo->prepare("
        UPDATE usuarios
        SET contraseña = :hash,
            reset_token = NULL,
            reset_token_expira = NULL,
            intentos_fallidos = 0,
            bloqueado_hasta = NULL
        WHERE id = :id
    ");
    $update->execute([':hash' => $hash, ':id' => $usuario['id']]);

    $_SESSION['info_login'] = "Tu contraseña fue actualizada correctamente. Ya puedes iniciar sesión.";
    header("Location: ../views/login.php");
    exit();

} catch (PDOException $e) {
    error_log('procesar_restablecer_password.php: ' . $e->getMessage());
    $_SESSION['error'] = "Ocurrió un error al actualizar la contraseña.";
    header("Location: ../views/restablecer_password.php?token=" . urlencode($token));
    exit();
}