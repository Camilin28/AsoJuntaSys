<?php
require('../config/db.php');
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("
        SELECT 
            id,
            titulo AS title,
            fecha AS start,
            color,
            descripcion AS description
        FROM agenda
    ");

    $eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($eventos);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
