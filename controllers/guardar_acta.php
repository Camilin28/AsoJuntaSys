<?php
require_once('../includes/auth.php');
require_once('../includes/auditoria.php');
require('../config/db.php');

requireRole(['Secretaría']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    validarTokenCSRF($_POST['csrf_token'] ?? '');

    $titulo        = $_POST['titulo'] ?? null;
    $documento_id  = !empty($_POST['documento_id']) ? $_POST['documento_id'] : null;
    $fecha_reunion = $_POST['fecha_reunion'] ?? null;
    $hora_reunion  = $_POST['hora_reunion'] ?? null;
    $lugar         = $_POST['lugar'] ?? null;
    $asistentes    = $_POST['asistentes'] ?? null;
    $orden_dia     = $_POST['orden_dia'] ?? null;
    $acuerdos      = $_POST['acuerdos'] ?? null;
    $observaciones = $_POST['observaciones'] ?? null;

    if (!$titulo || !$fecha_reunion || !$lugar) {
        die("❌ Error: Los campos obligatorios no fueron completados.");
    }

    try {
        $sql = "INSERT INTO actas 
                (titulo, documento_id, fecha_reunion, hora_reunion, lugar, asistentes, orden_dia, acuerdos, observaciones, jac_id) 
                VALUES 
                (:titulo, :documento_id, :fecha_reunion, :hora_reunion, :lugar, :asistentes, :orden_dia, :acuerdos, :observaciones, :jac_id)";
        
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':titulo'        => $titulo,
            ':documento_id'  => $documento_id,
            ':fecha_reunion' => $fecha_reunion,
            ':hora_reunion'  => $hora_reunion,
            ':lugar'         => $lugar,
            ':asistentes'    => $asistentes,
            ':orden_dia'     => $orden_dia,
            ':acuerdos'      => $acuerdos,
            ':observaciones' => $observaciones,
            ':jac_id'        => $_SESSION['jac_id'] ?? null
        ]);

        registrarAuditoria($pdo, 'crear', 'acta', (int) $pdo->lastInsertId(), $titulo);

        // 🚀 Redirigir con mensaje de éxito
        header("Location: ./../views/actas.php?success=1");
        exit();
    } catch (PDOException $e) {
        die("❌ Error al guardar el acta: " . $e->getMessage());
    }
} else {
    header("Location: ./../views/actas.php");
    exit();
}
