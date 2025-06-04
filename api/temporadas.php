<?php
require_once '../config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'get_all') {
    $stmt = $pdo->query("SELECT * FROM plantillas_temporada ORDER BY fecha_inicio");
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'temporadas' => $data]);

} elseif ($action === 'add') {
    $data = json_decode(file_get_contents("php://input"), true);
    $nombre = $data['nombre'] ?? null;
    $fecha_inicio = $data['fecha_inicio'] ?? null;
    $fecha_fin = $data['fecha_fin'] ?? null;

    if ($nombre && $fecha_inicio && $fecha_fin) {
        $stmt = $pdo->prepare("INSERT INTO plantillas_temporada (nombre, fecha_inicio, fecha_fin) VALUES (?, ?, ?)");
        $stmt->execute([$nombre, $fecha_inicio, $fecha_fin]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    }

} elseif ($action === 'update') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("UPDATE plantillas_temporada SET nombre = ?, fecha_inicio = ?, fecha_fin = ? WHERE id = ?");
    $ok = $stmt->execute([$data['nombre'], $data['fecha_inicio'], $data['fecha_fin'], $data['id']]);
    echo json_encode(['success' => $ok]);

} elseif ($action === 'delete') {
    $id = $_GET['id'] ?? null;
    if ($id) {
        $ok = $pdo->prepare("DELETE FROM plantillas_temporada WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => $ok]);
    } else {
        echo json_encode(['success' => false, 'message' => 'ID faltante']);
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
