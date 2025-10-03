<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_rol'] !== 'Secretaría') {
    header("Location: ../views/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    header("Location: ../views/actas.php?success=edit");
    exit();
}
