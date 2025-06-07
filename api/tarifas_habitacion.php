<?php
require_once '../config.php';
header('Content-Type: application/json');

ini_set('display_errors', 1);
error_reporting(E_ALL);

$action = $_GET['action'] ?? '';

// ======================== AGREGAR (por fetch con JSON) ========================
if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    $habitaciones = $input['habitaciones'] ?? [];
    $temporada = $input['temporada'] ?? null;
    $precio = $input['precio'] ?? null;

    if (!is_array($habitaciones) || !$temporada || !$precio) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        exit;
    }

    // Obtener fechas de la temporada
    $stmtTemporada = $pdo->prepare("SELECT fecha_inicio, fecha_fin FROM plantillas_temporada WHERE id = ?");
    $stmtTemporada->execute([$temporada]);
    $temp = $stmtTemporada->fetch();

    if (!$temp) {
        echo json_encode(['success' => false, 'message' => 'Temporada no encontrada']);
        exit;
    }

    $hoy = date('Y-m-d');
    $esTemporadaActual = ($hoy >= $temp['fecha_inicio'] && $hoy <= $temp['fecha_fin']);

    // Insertar tarifas y actualizar precios si es temporada actual
    $stmtInsert = $pdo->prepare("INSERT INTO tarifas_habitacion (habitacion_id, temporada_id, tarifa) VALUES (?, ?, ?)");
    $stmtUpdate = $pdo->prepare("UPDATE rooms SET price = ? WHERE id = ?");

    foreach ($habitaciones as $habitacion_id) {
        $stmtInsert->execute([$habitacion_id, $temporada, $precio]);

        if ($esTemporadaActual) {
            $stmtUpdate->execute([$precio, $habitacion_id]);
        }
    }

    echo json_encode(['success' => true]);
    exit;
}

// ======================== ACTUALIZAR ========================
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
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
    exit;
}

// ======================== LISTAR ========================
if ($action === 'get_all') {
    $stmt = $pdo->query("SELECT t.id, r.type AS habitacion, r.number, p.nombre AS temporada, p.fecha_inicio, p.fecha_fin, t.tarifa
                         FROM tarifas_habitacion t
                         JOIN rooms r ON t.habitacion_id = r.id
                         JOIN plantillas_temporada p ON t.temporada_id = p.id");
    $tarifas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'tarifas' => $tarifas]);
    exit;
}

// ======================== ELIMINAR ========================
if ($action === 'delete') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'Falta ID']);
    } else {
        $stmt = $pdo->prepare("DELETE FROM tarifas_habitacion WHERE id = ?");
        $stmt->execute([$id]);
        echo json_encode(['success' => true]);
    }
    exit;
}

// ======================== AGREGAR FORMULARIO CLÁSICO (opcional) ========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($action)) {
    $habitacion_id = $_POST['habitacion_id'] ?? null;
    $temporada_id = $_POST['temporada_id'] ?? null;
    $tarifa = $_POST['tarifa'] ?? null;

    if ($habitacion_id && $temporada_id && is_numeric($tarifa)) {
        $stmt = $pdo->prepare("INSERT INTO tarifas_habitacion (habitacion_id, temporada_id, tarifa) VALUES (?, ?, ?)");
        $stmt->execute([$habitacion_id, $temporada_id, $tarifa]);
        echo json_encode(["success" => true, "message" => "Tarifa guardada correctamente."]);
    } else {
        echo json_encode(["success" => false, "message" => "Campos incompletos."]);
    }
    exit;
}

// ======================== DEFAULT ========================
echo json_encode(['success' => false, 'message' => 'Acción no válida']);
