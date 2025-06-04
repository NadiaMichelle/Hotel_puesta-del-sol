<?php
require_once '../config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    $data = json_decode(file_get_contents("php://input"), true);
    $habitacion = $data['habitacion'] ?? null;
    $temporada = $data['temporada'] ?? null;
    $precio = $data['precio'] ?? null;

    if ($habitacion && $temporada && $precio !== null) {
        $stmt = $pdo->prepare("INSERT INTO tarifas_habitacion (habitacion_id, temporada_id, tarifa) VALUES (?, ?, ?)");
        $stmt->execute([$habitacion, $temporada, $precio]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    }

} elseif ($action === 'update') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;
    $precio = $data['precio'] ?? null;

    if ($id && $precio !== null) {
        $stmt = $pdo->prepare("UPDATE tarifas_habitacion SET tarifa = ? WHERE id = ?");
        $stmt->execute([$precio, $id]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    }

} elseif ($action === 'get_all') {
    $stmt = $pdo->query("SELECT t.id, r.type AS habitacion, r.number, p.nombre AS temporada, p.fecha_inicio, p.fecha_fin, t.tarifa
                         FROM tarifas_habitacion t
                         JOIN rooms r ON t.habitacion_id = r.id
                         JOIN plantillas_temporada p ON t.temporada_id = p.id");
    $tarifas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'tarifas' => $tarifas]);

} else {
    echo json_encode(['success' => false, 'message' => 'Acción no válida']);
}
if ($action === 'delete') {
  $id = $_GET['id'] ?? null;
  if (!$id) exit(json_encode(['success' => false, 'message' => 'Falta ID']));
  $stmt = $pdo->prepare("DELETE FROM tarifas_habitacion WHERE id = ?");
  $stmt->execute([$id]);
  echo json_encode(['success' => true]);
}
