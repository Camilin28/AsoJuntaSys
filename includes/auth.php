<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// RF-002: el sistema invalidará sesiones inactivas tras 30 minutos.
define('SESSION_TIMEOUT_SEGUNDOS', 1800);

/**
 * Verifica que el usuario haya iniciado sesión y que la sesión
 * no haya expirado por inactividad (RF-002).
 */
function requireLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ../views/login.php");
        exit();
    }

    if (
        isset($_SESSION['ultima_actividad']) &&
        (time() - $_SESSION['ultima_actividad']) > SESSION_TIMEOUT_SEGUNDOS
    ) {
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['error'] = "Tu sesión expiró por inactividad. Inicia sesión nuevamente.";
        header("Location: ../views/login.php");
        exit();
    }

    // Actualiza la marca de actividad en cada verificación
    $_SESSION['ultima_actividad'] = time();
}

/**
 * Verifica que el usuario tenga uno de los roles permitidos
 */
function requireRole($rolesPermitidos) {

    requireLogin();

    if (!in_array($_SESSION['usuario_rol'], $rolesPermitidos)) {

        http_response_code(403);
        echo "Acceso denegado.";
        exit();
    }
}