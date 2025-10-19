<?php
require('../config/db.php');
header('Content-Type: application/json; charset=utf-8');

try {
    $sql = "SELECT id, titulo, descripcion, fecha, hora, color FROM agenda ORDER BY fecha ASC";
    $stmt = $pdo->query($sql);

    $eventos = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $start = $row['fecha'];
        if (!empty($row['hora'])) {
            $start .= 'T' . $row['hora'];
        }

        $eventos[] = [
            'id' => $row['id'],
            'title' => $row['titulo'],
            'start' => $start,
            'description' => $row['descripcion'] ?? '',
            'color' => $row['color'] ?? '#2E7D32'
        ];
    }

    echo json_encode($eventos, JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
