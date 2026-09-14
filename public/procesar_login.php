<?php
session_start();
require_once '../config/db.php';

// RF-002: máximo 5 intentos fallidos antes de bloquear temporalmente la cuenta.
define('MAX_INTENTOS_FALLIDOS', 5);
define('BLOQUEO_MINUTOS', 15);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $contraseña = $_POST['password'];

    if (!empty($email) && !empty($contraseña)) {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            // ¿La cuenta está bloqueada temporalmente por intentos fallidos?
            if ($usuario && !empty($usuario['bloqueado_hasta']) && strtotime($usuario['bloqueado_hasta']) > time()) {
                $minutosRestantes = (int) ceil((strtotime($usuario['bloqueado_hasta']) - time()) / 60);
                $_SESSION['error'] = "Cuenta bloqueada temporalmente por múltiples intentos fallidos. Intenta de nuevo en {$minutosRestantes} minuto(s).";
                header("Location: ../views/login.php");
                exit();
            }

            if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
                // Login correcto: reiniciar contador de intentos
                $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = 0, bloqueado_hasta = NULL WHERE id = :id");
                $stmt->execute([':id' => $usuario['id']]);

                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                $_SESSION['jac_id'] = $usuario['jac_id'];
                $_SESSION['ultima_actividad'] = time(); // RF-002: base para el timeout de 30 min

                // Redirigir según el rol
                switch ($usuario['rol']) {
                    case 'Presidente General':
                        header("Location: ../views/dashboard_presidente.php");
                        break;
                    case 'Presidentes de JAC':
                        header("Location: ../views/dashboard_jac.php");
                        break;
                    case 'Secretaría':
                        header("Location: ../views/dashboard_secretario.php");
                        break;
                    case 'Tesorería':
                        header("Location: ../views/dashboard_tesoreria.php");
                        break;
                    default:
                        header("Location: ../views/dashboard.php");
                        break;
                }
                exit();
            } else {
                if ($usuario) {
                    $intentos = (int) ($usuario['intentos_fallidos'] ?? 0) + 1;

                    if ($intentos >= MAX_INTENTOS_FALLIDOS) {
                        $bloqueadoHasta = date('Y-m-d H:i:s', time() + BLOQUEO_MINUTOS * 60);
                        $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = :intentos, bloqueado_hasta = :bloqueo WHERE id = :id");
                        $stmt->execute([':intentos' => $intentos, ':bloqueo' => $bloqueadoHasta, ':id' => $usuario['id']]);
                        $_SESSION['error'] = "Demasiados intentos fallidos. Tu cuenta quedó bloqueada por " . BLOQUEO_MINUTOS . " minutos.";
                    } else {
                        $stmt = $pdo->prepare("UPDATE usuarios SET intentos_fallidos = :intentos WHERE id = :id");
                        $stmt->execute([':intentos' => $intentos, ':id' => $usuario['id']]);
                        $restantes = MAX_INTENTOS_FALLIDOS - $intentos;
                        $_SESSION['error'] = "Correo o contraseña incorrectos. Te quedan {$restantes} intento(s) antes del bloqueo temporal.";
                    }
                } else {
                    $_SESSION['error'] = "Correo o contraseña incorrectos.";
                }
            }

        } catch (PDOException $e) {
            error_log('procesar_login.php: ' . $e->getMessage());
            $_SESSION['error'] = "Error en la autenticación.";
        }

    } else {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
    }
}
header("Location: ../views/login.php");
exit();