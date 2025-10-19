function requireRole($roles = []) {
    session_start();
    if (!isset($_SESSION['usuario_id']) || !in_array($_SESSION['usuario_rol'], $roles)) {
        header("Location: ../views/login.php");
        exit();
    }
}
