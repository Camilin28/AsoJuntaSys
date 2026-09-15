<?php
session_start();
require_once '../config/db.php';
require_once '../includes/auditoria.php';

if (isset($_SESSION['usuario_id'])) {
    registrarAuditoria($pdo, 'logout', 'usuario', $_SESSION['usuario_id']);
}

// Eliminar todas las variables de sesión
session_unset();

// Destruir la sesión
session_destroy();

// Redirigir al usuario a la página de inicio de sesión o a la página principal
header("Location: ../views/login.php");
exit();
