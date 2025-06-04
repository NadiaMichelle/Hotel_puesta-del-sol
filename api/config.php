<?php
require_once '../config.php'; // Ajusta la ruta si es necesario
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'get') {
    $stmt = $pdo->query("SELECT * FROM configuracion LIMIT 1");
    $config = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($config) {
        echo json_encode(['success' => true, 'iva' => $config['iva'], 'ish' => $config['ish']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Configuración no encontrada']);
    }
} elseif ($action === 'update') {
    $data = json_decode(file_get_contents("php://input"), true);
    $iva = $data['iva'] ?? 0;
    $ish = $data['ish'] ?? 0;

    $stmt = $pdo->prepare("UPDATE configuracion SET iva = ?, ish = ? WHERE id = 1");
    $stmt->execute([$iva, $ish]);

    echo json_encode(['success' => true, 'message' => 'Configuración actualizada']);
} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
