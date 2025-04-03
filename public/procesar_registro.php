<?php
session_start();
require_once '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $rol = $_POST['rol'];

    if (!empty($nombre) && !empty($email) && !empty($password)) {
        try {
            $sql = "INSERT INTO usuarios (nombre, email, contraseña, rol) VALUES (:nombre, :email, :password, :rol)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':email' => $email,
                ':password' => $password,
                ':rol'=> $rol
                
            ]);

            $_SESSION['mensaje'] = "✅ Registro exitoso. Inicia sesión.";
            header("Location: ../views/login.php");
            exit();
        } catch (PDOException $e) {
            $_SESSION['error'] = "❌ Error en el registro: " . $e->getMessage();
        }
    } else {
        $_SESSION['error'] = "❌ Todos los campos son obligatorios.";
    }
}

header("Location: ../views/registrar.php");
exit();
