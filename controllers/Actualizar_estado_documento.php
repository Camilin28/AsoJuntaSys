<?php
require_once('../includes/auth.php');
require_once('../includes/auditoria.php');
require('../config/db.php');
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'], $_POST['estado'])) {
    $id = $_POST['id'];
    $estado = $_POST['estado'];

    try {
        $sql = "UPDATE documentos SET estado = :estado, fecha_modificacion = NOW() WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':estado' => $estado, ':id' => $id]);

        registrarAuditoria($pdo, 'editar', 'documento', (int) $id, "Nuevo estado: {$estado}");

        echo json_encode(['success' => true, 'message' => 'Estado actualizado correctamente']);
        exit();
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar el estado']);
        exit();
    }
}

echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
