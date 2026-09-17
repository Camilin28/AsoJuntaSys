<?php
require_once('../includes/auth.php');
require_once('../includes/auditoria.php');
require('../config/db.php');

requireRole(['Secretaría']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validarTokenCSRF($_POST['csrf_token'] ?? '');

    $id = $_POST['id'];
    $titulo = $_POST['titulo'];
    $fecha_reunion = $_POST['fecha_reunion'];
    $lugar = $_POST['lugar'];
    $asistentes = $_POST['asistentes'];
    $acuerdos = $_POST['acuerdos'];
    $observaciones = $_POST['observaciones'];

    $sql = "UPDATE actas 
            SET titulo = :titulo, fecha_reunion = :fecha_reunion, lugar = :lugar, 
                asistentes = :asistentes, acuerdos = :acuerdos, observaciones = :observaciones
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':titulo' => $titulo,
        ':fecha_reunion' => $fecha_reunion,
        ':lugar' => $lugar,
        ':asistentes' => $asistentes,
        ':acuerdos' => $acuerdos,
        ':observaciones' => $observaciones,
        ':id' => $id
    ]);

    registrarAuditoria($pdo, 'editar', 'acta', (int) $id, $titulo);

    header("Location: ../views/actas.php?success=edit");
    exit();
}
