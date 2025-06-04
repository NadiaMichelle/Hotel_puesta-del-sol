<?php
require_once '../config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'get_all') {
    $stmt = $pdo->query("SELECT id, type, number FROM rooms");
    $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'habitaciones' => $habitaciones]);
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
