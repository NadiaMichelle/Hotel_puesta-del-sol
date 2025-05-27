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
            $stmt = $pdo->query("SELECT id, nombre, nacionalidad, telefono FROM guests ORDER BY nombre");
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
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            
            if (!$data || empty($data['nombre'])) {
                echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
                exit;
            }
            
            $stmt = $pdo->prepare("INSERT INTO guests (nombre, nacionalidad, calle, ciudad, estado, cp, telefono, rfc, email) 
                                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['nombre'] ?? '',
                $data['nacionalidad'] ?? '',
                $data['calle'] ?? '',
                $data['ciudad'] ?? '',
                $data['estado'] ?? '',
                $data['cp'] ?? '',
                $data['telefono'] ?? '',
                $data['rfc'] ?? '',
                $data['email'] ?? ''
            ]);
            
            echo json_encode([
                'success' => true,
                'id' => $pdo->lastInsertId(),
                'message' => 'Huésped creado'
            ]);
            break;

        case 'update':
            // Similar a 'add' pero con UPDATE
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