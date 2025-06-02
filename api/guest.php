<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!function_exists('isAuthenticated') || !isAuthenticated()) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

$action = $_GET['action'] ?? null;
try {
    switch ($action) {
        case 'get_all':
            $stmt = $pdo->query("SELECT id, nombre, nacionalidad, telefono, auto FROM guests ORDER BY nombre");
            echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
            break;

        case 'get':
            if (!isset($_GET['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                exit;
            }

            $stmt = $pdo->prepare("SELECT * FROM guests WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $guest = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($guest) {
                echo json_encode($guest);
            } else {
                echo json_encode(['success' => false, 'message' => 'Huésped no encontrado']);
            }
            break;

        case 'add':
            $nombre = $_POST['guestName'] ?? '';
            $nacionalidad = $_POST['guestNationality'] ?? '';
            $telefono = $_POST['guestPhone'] ?? '';
            $calle = $_POST['guestAddress'] ?? '';
            $ciudad = $_POST['guestCity'] ?? '';
            $estado = $_POST['guestState'] ?? '';
            $auto = $_POST['guestCar'] ?? '';
            $cp = $_POST['guestPostalCode'] ?? '';
            $rfc = $_POST['guestRFC'] ?? '';
            $email = $_POST['guestEmail'] ?? '';

            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
                exit;
            }

            $stmt = $pdo->prepare("INSERT INTO guests (nombre, nacionalidad, telefono, calle, ciudad, estado, cp, rfc, email, auto)
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre, $nacionalidad, $telefono, $calle, $ciudad, $estado, $cp, $rfc, $email, $auto]);

            echo json_encode([
                'success' => true,
                'id' => $pdo->lastInsertId(),
                'message' => 'Huésped creado correctamente'
            ]);
            break;

        case 'update':
            if (!isset($_POST['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                exit;
            }

            $id = $_POST['id'];
            $nombre = $_POST['guestName'] ?? '';
            $nacionalidad = $_POST['guestNationality'] ?? '';
            $telefono = $_POST['guestPhone'] ?? '';
            $calle = $_POST['guestAddress'] ?? '';
            $ciudad = $_POST['guestCity'] ?? '';
            $estado = $_POST['guestState'] ?? '';
            $auto = $_POST['guestCar'] ?? '';
            $cp = $_POST['guestPostalCode'] ?? '';
            $rfc = $_POST['guestRFC'] ?? '';
            $email = $_POST['guestEmail'] ?? '';

            if (empty($nombre)) {
                echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
                exit;
            }

            $stmt = $pdo->prepare("UPDATE guests SET nombre = ?, nacionalidad = ?, telefono = ?, calle = ?, ciudad = ?, estado = ?, cp = ?, rfc = ?, email = ?, auto = ? WHERE id = ?");
            $stmt->execute([$nombre, $nacionalidad, $telefono, $calle, $ciudad, $estado, $cp, $rfc, $email, $auto, $id]);

            echo json_encode(['success' => true, 'message' => 'Huésped actualizado correctamente']);
            break;

        case 'delete':
            if (!isset($_GET['id'])) {
                echo json_encode(['success' => false, 'message' => 'ID no proporcionado']);
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM guests WHERE id = ?");
            $stmt->execute([$_GET['id']]);

            echo json_encode(['success' => true, 'message' => 'Huésped eliminado']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Acción no reconocida']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos', 'error' => $e->getMessage()]);
}
