<?php
require_once '../config.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'get_all') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM reservations ORDER BY id DESC");
        $stmt->execute();

        $data = [];

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Decodificar campos JSON complejos correctamente
            $row['anticipo'] = json_decode($row['anticipo'] ?? '{}', true);
            $row['verification'] = json_decode($row['verification'] ?? '{}', true);
            $row['pagosHotel'] = json_decode($row['pagosHotel'] ?? '[]', true);
            $row['pagosExtra'] = json_decode($row['pagosExtra'] ?? '[]', true);
            $row['checkinGuests'] = json_decode($row['checkinGuests'] ?? '[]', true);
            $row['checkinItems'] = json_decode($row['checkinItems'] ?? '{}', true);

            // 🔥 Incluye totalReserva correctamente
            $data[] = [
                'id' => $row['id'],
                'resourceId' => $row['resourceId'],
                'title' => $row['title'],
                'start_date' => $row['start_date'],
                'end_date' => $row['end_date'],
                'color' => $row['color'],
                'guestId' => $row['guestId'],
                'guestNameManual' => $row['guestNameManual'],
                'status' => $row['status'],
                'rate' => $row['rate'],
                'iva' => $row['iva'],
                'ish' => $row['ish'],
                'inapamDiscount' => $row['inapamDiscount'],
                'inapamCredential' => $row['inapamCredential'],
                'inapamDiscountValue' => $row['inapamDiscountValue'],
                'notes' => $row['notes'],
                'anticipo' => $row['anticipo'],
                'verification' => $row['verification'],
                'pagosHotel' => $row['pagosHotel'],
                'pagosExtra' => $row['pagosExtra'],
                'checkinGuests' => $row['checkinGuests'],
                'checkinItems' => $row['checkinItems'],
                'receptionistName' => $row['receptionistName'],
                'totalReserva' => $row['totalReserva'] // 👈 ¡Aquí el totalReserva!
            ];
        }

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener las reservas',
            'error' => $e->getMessage()
        ]);
    }
    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Acción no válida'
]);
exit;
