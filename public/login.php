<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require '../config/db.php';

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Preparar la consulta con marcadores de posición
        $sql = "SELECT id, nombre, email, contraseña FROM usuarios WHERE email = :email";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar la contraseña con password_verify
        if ($usuario && password_verify($password, $usuario['contraseña'])) {
            $_SESSION['usuario'] = $usuario['nombre'];
            header("Location: ../views/dashboard.php");
            exit();
        } else {
            echo "❌ Usuario o contraseña incorrectos.";
        }
    } else {
        echo "❌ Faltan datos en el formulario.";
    }
}
?>