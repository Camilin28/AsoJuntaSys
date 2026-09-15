<?php
/**
 * Configuración del envío de correo (Brevo SMTP) para RF-005.
 * Todas las credenciales vienen de variables de entorno de Railway,
 * nunca hardcodeadas en el código.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Crea y configura una instancia de PHPMailer lista para enviar
 * a través de Brevo. Devuelve null si faltan variables de entorno.
 */
function crearMailer(): ?PHPMailer {

    $smtpUser = getenv('BREVO_SMTP_USER');
    $smtpPass = getenv('BREVO_SMTP_PASS');
    $fromEmail = getenv('BREVO_FROM_EMAIL');

    if (!$smtpUser || !$smtpPass || !$fromEmail) {
        error_log('config/mail.php: faltan variables de entorno de Brevo (BREVO_SMTP_USER, BREVO_SMTP_PASS, BREVO_FROM_EMAIL)');
        return null;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp-relay.brevo.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = $smtpUser;
        $mail->Password   = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom($fromEmail, 'AsoJuntaSys');

        return $mail;
    } catch (Exception $e) {
        error_log('config/mail.php: error al configurar PHPMailer: ' . $mail->ErrorInfo);
        return null;
    }
}