<?php
require_once('../includes/auth.php');
require('../config/db.php');
header('Content-Type: application/json; charset=utf-8');

requireLogin();

$jac_id = $_SESSION['jac_id'];

$stmt = $pdo->prepare("
    SELECT titulo as title, fecha as start, color
    FROM agenda
    WHERE jac_id = ?
");
$stmt->execute([$jac_id]);

$eventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($eventos);