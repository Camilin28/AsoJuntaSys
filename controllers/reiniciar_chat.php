<?php
require_once '../includes/auth.php';

// Reinicia la memoria de conversación del Asistente Virtual (RF-028).
requireLogin();
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false]);
    exit();
}

if (
    empty($_SESSION['csrf_token']) ||
    empty($_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    echo json_encode(['ok' => false]);
    exit();
}

$_SESSION['chat_historial'] = [];
echo json_encode(['ok' => true]);