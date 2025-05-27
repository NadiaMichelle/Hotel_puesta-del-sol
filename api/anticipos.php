<?php
header('Content-Type: application/json');
require_once '../config.php'; // Asegúrate de que este archivo tenga conexión $pdo

$action = $_GET['action'] ?? '';

// ========================= AGREGAR ANTICIPO =========================
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode(["success" => false, "message" => "Datos inválidos"]);
        exit;
    }

    // Inserta anticipo sin ticket (se genera después)
    $stmt = $pdo->prepare("INSERT INTO anticipos 
        (guest, reserva_id, entrada, salida, tipoHabitacion, personas, tarifa, total, anticipo, saldo, metodo_pago, tasa_cambio, observaciones, fecha) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $success = $stmt->execute([
        $input['guest'],
        $input['reserva'],
        $input['entrada'],
        $input['salida'],
        $input['tipoHabitacion'],
        $input['personas'],
        $input['tarifa'],
        $input['total'],
        $input['anticipo'],
        $input['saldo'],
        $input['metodo_pago'],
        $input['tasaCambio'],
        $input['observaciones'],
        $input['fecha']
    ]);

    if ($success) {
        $ultimoId = $pdo->lastInsertId();

        // 🎟️ Generar folio automático
        $folio = 'ANT-' . date('Ymd') . '-' . str_pad($ultimoId, 4, '0', STR_PAD_LEFT);

        // Actualizar con el folio generado
        $pdo->prepare("UPDATE anticipos SET ticket = ? WHERE id = ?")->execute([$folio, $ultimoId]);

        echo json_encode(["success" => true, "id" => $ultimoId, "ticket" => $folio]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar en BD"]);
    }
    exit;
}

// ========================= LISTAR ANTICIPOS =========================
if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT * FROM anticipos ORDER BY id DESC");
        $anticipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(["success" => true, "data" => $anticipos]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Error al obtener anticipos",
            "error" => $e->getMessage()
        ]);
    }
    exit;
}

if ($action === 'search') {
  $query = $_GET['query'] ?? '';
  $stmt = $pdo->prepare("SELECT * FROM anticipos WHERE ticket LIKE ? OR guest LIKE ? OR fecha LIKE ?");
  $stmt->execute(["%$query%", "%$query%", "%$query%"]);
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode(['success' => true, 'data' => $data]);
  exit;
}
