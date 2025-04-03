<?php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $contraseña = trim($_POST['password']);

    if (!empty($email) && !empty($contraseña)) {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($password, $usuario['contraseña'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];

                // Redirigir según el rol
                switch ($usuario['rol']) {
                    case 'Presidente General':
                        header("Location: ../views/dashboard_admin.php");
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
                $_SESSION['error'] = "❌ Credenciales incorrectas.";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "❌ Error en la autenticación: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "❌ Todos los campos son obligatorios.";
    }
}

header("Location: ../views/login.php");
exit();
