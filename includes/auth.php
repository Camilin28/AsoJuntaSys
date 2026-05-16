<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Verifica que el usuario haya iniciado sesión
 */
function requireLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../views/login.php");
        exit();
    }
}

/**
 * Verifica que el usuario tenga uno de los roles permitidos
 */
function requireRole($rolesPermitidos) {

    requireLogin();

    if (!in_array($_SESSION['usuario_rol'], $rolesPermitidos)) {

        echo "Acceso denegado.";
        exit();
    }
}