<?php
session_start();
require('../config/db.php');

$jac_id = $_SESSION['jac_id'];

$stmt = $pdo->prepare("
    SELECT titulo as title, fecha as start, color
    FROM agenda
    WHERE jac_id = ?
");
$stmt->execute([$jac_id]);

$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($eventos);