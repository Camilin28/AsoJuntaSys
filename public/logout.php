<?php
session_start();  // Iniciar la sesión

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio de sesión o a la página principal
header("Location: ../views/login.php");
exit();
?>
