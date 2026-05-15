<?php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $contraseña = $_POST['password'];

    if (!empty($email) && !empty($contraseña)) {
        try {
            $sql = "SELECT * FROM usuarios WHERE email = :email";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':email' => $email]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && password_verify($contraseña, $usuario['contraseña'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_rol'] = $usuario['rol'];
                $_SESSION['jac_id'] = $usuario['jac_id'];
                

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
            $_SESSION['error'] = "Correo o contraseña incorrectos.";
            
        }

    } catch (PDOException $e) {
        $_SESSION['error'] = "Error en la autenticación.";
        
    }

} else {
    $_SESSION['error'] = "Todos los campos son obligatorios.";
    
}
header("Location: ../views/login.php");
exit();}

 