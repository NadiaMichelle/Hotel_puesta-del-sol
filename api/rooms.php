<?php
require_once '../config.php';
header('Content-Type: application/json');

if ($_GET['action'] === 'get_all') {
    try {
        $stmt = $pdo->query("SELECT r.id, r.numero, r.capacidad, r.status, t.nombre AS tipo, t.tarifas_normales
                             FROM rooms r
                             JOIN tipos_habitacion t ON r.id_tipo = t.id");
        $habitaciones = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $tarifas = json_decode($row['tarifas_normales'], true);
            $tarifa_vigente = 0;

            if (!empty($tarifas)) {
                $ultima = end($tarifas);
                $tarifa_vigente = floatval($ultima['precio'] ?? 0);
            }

            $habitaciones[] = [
                'id' => $row['id'],
                'numero' => $row['numero'],
                'capacidad' => $row['capacidad'],
                'tipo' => $row['tipo'],
                'status' => strtolower($row['status']), // 👈 ¡aquí!
                'tarifa_vigente' => $tarifa_vigente
            ];
        }

        echo json_encode($habitaciones);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al obtener habitaciones', 'message' => $e->getMessage()]);
    }
    exit;
}

if ($_GET['action'] === 'get' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT id, numero, capacidad, id_tipo, status FROM rooms WHERE id = ?");
    $stmt->execute([$id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room) {
        $room['status'] = strtolower($room['status']); // 👈 importante para que compare bien en JS
        echo json_encode($room);
    } else {
        echo json_encode(null);
    }
    exit;
}
if ($_GET['action'] === 'get' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT r.*, t.nombre AS tipo FROM rooms r JOIN tipos_habitacion t ON r.id_tipo = t.id WHERE r.id = ?");
    $stmt->execute([$id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($room) {
        echo json_encode($room);
    } else {
        echo json_encode(null);
    }
    exit;
}

// Si no hay acción válida
echo json_encode(['error' => 'Acción no válida']);
