<?php
require_once '../config.php';

header('Content-Type: application/json'); // 👈 Añade esto

$id = intval($_POST['id'] ?? 0);
$numero = trim($_POST['numero'] ?? '');
$capacidad = intval($_POST['capacidad'] ?? 0);
$status = trim($_POST['status'] ?? '');

if ($id > 0 && $numero && $capacidad > 0 && $status) {
    $stmt = $pdo->prepare("UPDATE rooms SET numero = ?, capacidad = ?, status = ? WHERE id = ?");
    $stmt->execute([$numero, $capacidad, $status, $id]);
    echo json_encode(['success' => true]); // 👈 Devuelve un JSON
    exit;
}
echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
