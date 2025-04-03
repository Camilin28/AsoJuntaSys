<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$conn = new mysqli("localhost", "root", "", "asojuntasys");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "Datos recibidos:<br>";
    print_r($_POST); // Verifica que los datos llegan correctamente

    if (!empty($_POST['email']) && !empty($_POST['contraseña'])) {
        $email = $_POST['email'];
        $contraseña = md5($_POST['contraseña']); // Si usaste MD5 en la BD

        $sql = "SELECT nombre, email FROM usuarios WHERE email='$email' AND contraseña='$contraseña'";
        echo "Query generada: $sql <br>"; // Depuración

        $result = $conn->query($sql);
        if ($result === false) {
            echo "Error en la consulta: " . $conn->error;
        } elseif ($result->num_rows > 0) {
            $usuario = $result->fetch_assoc();
            $_SESSION['usuario'] = $usuario['nombre']; // Guarda el nombre del usuario en sesión
            header("Location: dashboard.php"); // Redirige al usuario logueado
            exit();
        } else {
            echo "Usuario o contraseña incorrectos.";
        }
    } else {
        echo "Faltan datos en el formulario.";
    }
}
?>
