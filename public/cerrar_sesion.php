<?php
session_start();
session_destroy();
header("Location: ../views/login.php"); // Redirige al login después de cerrar sesión
exit();
?>
