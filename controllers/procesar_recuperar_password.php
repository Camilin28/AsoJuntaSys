<?php
session_start();
require_once '../config/db.php';
require_once '../config/mail.php';

// RF-005: recuperación de contraseña. Endpoint público por diseño
// (el usuario aún no ha iniciado sesión), pero no revela si un correo
// existe o no en el sistema, para evitar enumeración de usuarios.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../views/recuperar_password.php");
    exit();
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);

// Mensaje genérico: se muestra exista o no el correo, para no filtrar
// qué correos están registrados en el sistema.
$_SESSION['info'] = "Si el correo está registrado, te enviamos un enlace para restablecer tu contraseña. Revisa tu bandeja de entrada (y spam).";

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../views/recuperar_password.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, nombre FROM usuarios WHERE email = :email");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        // Token aleatorio y seguro, válido por 60 minutos (RF-005)
        $token = bin2hex(random_bytes(32));
        $expira = date('Y-m-d H:i:s', time() + 60 * 60);

        $update = $pdo->prepare("UPDATE usuarios SET reset_token = :token, reset_token_expira = :expira WHERE id = :id");
        $update->execute([':token' => $token, ':expira' => $expira, ':id' => $usuario['id']]);

        $baseUrl = 'https://' . $_SERVER['HTTP_HOST'];
        $enlace = $baseUrl . '/views/restablecer_password.php?token=' . $token;

        $mail = crearMailer();

        if ($mail) {
            try {
                $mail->addAddress($email, $usuario['nombre']);
                $mail->Subject = 'Recuperación de contraseña - AsoJuntaSys';
                $mail->isHTML(true);
                $mail->Body = "
                    <p>Hola " . htmlspecialchars($usuario['nombre']) . ",</p>
                    <p>Recibimos una solicitud para restablecer tu contraseña en AsoJuntaSys.</p>
                    <p><a href='{$enlace}'>Haz clic aquí para crear una nueva contraseña</a></p>
                    <p>Este enlace vence en 60 minutos. Si tú no solicitaste este cambio, ignora este correo.</p>
                ";
                $mail->AltBody = "Para restablecer tu contraseña visita: {$enlace} (válido por 60 minutos)";
                $mail->send();
            } catch (Exception $e) {
                error_log('procesar_recuperar_password.php: error al enviar correo: ' . $e->getMessage());
            }
        }
    }
    // Si el usuario no existe, no hacemos nada — pero mostramos el mismo
    // mensaje genérico de arriba para no revelar qué correos existen.

} catch (PDOException $e) {
    error_log('procesar_recuperar_password.php: ' . $e->getMessage());
}

header("Location: ../views/recuperar_password.php");
exit();