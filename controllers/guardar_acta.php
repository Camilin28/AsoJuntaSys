<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
                (titulo, documento_id, fecha_reunion, hora_reunion, lugar, asistentes, orden_dia, acuerdos, observaciones) 
                VALUES 
                (:titulo, :documento_id, :fecha_reunion, :hora_reunion, :lugar, :asistentes, :orden_dia, :acuerdos, :observaciones)";
        
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
            ':observaciones' => $observaciones
        ]);

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
