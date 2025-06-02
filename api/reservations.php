<?php
require_once '../config.php';
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!function_exists('isAuthenticated') || !isAuthenticated()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

if (!isset($pdo)) {
    echo json_encode(['success' => false, 'message' => 'No se pudo conectar a la base de datos']);
    exit;
}

$action = $_GET['action'] ?? null;

if ($action === 'get_all') {
    try {
        $stmt = $pdo->query("SELECT * FROM reservations ORDER BY start_date ASC");
        $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $formatted = array_map(function ($res) {
            // Decodificar anticipos aquí, antes del return:
            $res['anticipo'] = json_decode($res['anticipo'] ?? '{}', true);
            $res['pagosHotel'] = json_decode($res['pagosHotel'] ?? '[]', true);
            $res['pagosExtra'] = json_decode($res['pagosExtra'] ?? '[]', true);
            $res['verification'] = json_decode($res['verification'] ?? '{}', true);
            $res['checkinGuests'] = json_decode($res['checkinGuests'] ?? '[]', true);
            $res['checkinItems'] = json_decode($res['checkinItems'] ?? '{}', true);

            return [
                'id' => $res['id'],
                'title' => $res['title'],
                'start' => date('Y-m-d\TH:i:s', strtotime($res['start_date'])),
                'end' => date('Y-m-d\TH:i:s', strtotime($res['end_date'])),
                'color' => $res['color'],
                'resourceId' => $res['resourceId'],
                'extendedProps' => [
                    'guestId' => $res['guestId'],
                    'guestNameManual' => $res['guestNameManual'],
                    'status' => $res['status'],
                    'rate' => floatval($res['rate']),
                    'iva' => floatval($res['iva']),
                    'ish' => floatval($res['ish']),
                    'inapamDiscount' => boolval($res['inapamDiscount']),
                    'inapamCredential' => $res['inapamCredential'],
                    'inapamDiscountValue' => floatval($res['inapamDiscountValue']),
                    'inapamDiscountType' => $res['inapamDiscountType'] ?? 'porcentaje',
                    'notes' => $res['notes'],
                    'anticipo' => $res['anticipo'],
                    'pagosHotel' => $res['pagosHotel'],
                    'pagosExtra' => $res['pagosExtra'],
                    'verification' => $res['verification'],
                    'checkinGuests' => $res['checkinGuests'],
                    'checkinItems' => $res['checkinItems'],
                    'receptionistName' => $res['receptionistName'],
                    'totalReserva' => floatval($res['totalReserva']),
                ]
            ];
        }, $reservations);

        echo json_encode(['success' => true, 'data' => $formatted]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener reservas', 'error' => $e->getMessage()]);
    }
    exit;
}


if ($action === 'add' || $action === 'update') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'JSON inválido', 'received' => $json]);
        exit;
    }

    $required = ['resourceId', 'start', 'end', 'extendedProps'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            echo json_encode(['success' => false, 'message' => "Falta el campo: $field"]);
            exit;
        }
    }

    $roomId = $data['resourceId'];
    $checkIn = $data['start'];
    $checkOut = $data['end'];
    $color = $data['color'] ?? '#FFD700';
    $estado = $data['extendedProps']['status'] ?? 'RESERVACION_PREVIA';
    $rate = floatval($data['extendedProps']['rate'] ?? 0);
    $iva = floatval($data['extendedProps']['iva'] ?? 0);
    $ish = floatval($data['extendedProps']['ish'] ?? 0);
    $inapam = $data['extendedProps']['inapamDiscount'] ? 1 : 0;
    $inapamValue = floatval($data['extendedProps']['inapamDiscountValue'] ?? 0);
    $inapamCredential = $data['extendedProps']['inapamCredential'] ?? '';
    $inapamDiscountType = $data['extendedProps']['inapamDiscountType'] ?? 'porcentaje'; // Añadido
    $notes = $data['extendedProps']['notes'] ?? '';
    $guestId = $data['extendedProps']['guestId'] ?? null;
    $guestNameManual = $data['extendedProps']['guestNameManual'] ?? null;
    $receptionistName = $data['extendedProps']['receptionistName'] ?? '';

    $anticipo = json_encode($data['extendedProps']['anticipo'] ?? []);

    $pagosHotel = json_encode($data['extendedProps']['pagosHotel'] ?? []);
    $pagosExtra = json_encode($data['extendedProps']['pagosExtra'] ?? []);
    $verification = json_encode($data['extendedProps']['verification'] ?? []);
    $checkinGuests = json_encode($data['extendedProps']['checkinGuests'] ?? []);
    $checkinItems = json_encode($data['extendedProps']['checkinItems'] ?? []);
    $totalReserva = floatval($data['extendedProps']['totalReserva'] ?? 0);

    $title = 'Reserva ' . ($guestNameManual ?? '');

    try {
        if ($action === 'add') {
            $stmt = $pdo->prepare("INSERT INTO reservations (
                resourceId, title, start_date, end_date, color,
                guestId, guestNameManual, status, rate, iva, ish,
                inapamDiscount, inapamCredential, inapamDiscountValue, inapamDiscountType, notes,
                anticipo, pagosHotel, pagosExtra, verification, checkinGuests, checkinItems, receptionistName, totalReserva,
                created_at, updated_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

            $stmt->execute([
                $roomId, $title, $checkIn, $checkOut, $color,
                $guestId, $guestNameManual, $estado, $rate, $iva, $ish,
                $inapam, $inapamCredential, $inapamValue, $inapamDiscountType, $notes,
                $anticipo, $pagosHotel, $pagosExtra, $verification, $checkinGuests, $checkinItems, $receptionistName, $totalReserva
            ]);

            echo json_encode(['success' => true, 'message' => 'Reserva guardada', 'insert_id' => $pdo->lastInsertId()]);
        } elseif ($action === 'update') {
            if (!isset($data['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado para actualización']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE reservations SET
                resourceId = ?, title = ?, start_date = ?, end_date = ?, color = ?,
                guestId = ?, guestNameManual = ?, status = ?, rate = ?, iva = ?, ish = ?,
                inapamDiscount = ?, inapamCredential = ?, inapamDiscountValue = ?, inapamDiscountType = ?, notes = ?,
                anticipo = ?, pagosHotel = ?, pagosExtra = ?, verification = ?, checkinGuests = ?, checkinItems = ?, receptionistName = ?, totalReserva = ?,
                updated_at = NOW()
                WHERE id = ?");

            $stmt->execute([
                $roomId, $title, $checkIn, $checkOut, $color,
                $guestId, $guestNameManual, $estado, $rate, $iva, $ish,
                $inapam, $inapamCredential, $inapamValue, $inapamDiscountType, $notes,
                $anticipo, $pagosHotel, $pagosExtra, $verification, $checkinGuests, $checkinItems, $receptionistName, $totalReserva,
                $data['id']
            ]);

            echo json_encode(['success' => true, 'message' => 'Reserva actualizada']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al guardar', 'error' => $e->getMessage()]);
    }

    exit;
}
?>
