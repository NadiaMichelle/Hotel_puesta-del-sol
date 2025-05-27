<?php
require_once '../config.php';
header('Content-Type: application/json');

if (!isAuthenticated()) {
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    $action = $_GET['action'] ?? '';

    switch($action) {
        case 'get_all':
            $stmt = $pdo->query("SELECT * FROM rooms");
            echo json_encode($stmt->fetchAll());
            break;
            
        case 'get':
            $id = $_GET['id'] ?? '';
            $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode($stmt->fetch());
            break;
            
        case 'add':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Requiere privilegios de admin']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO rooms (id, type, number, beds, capacity, price, inapam, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['id'],
                $data['type'],
                $data['number'],
                $data['beds'],
                $data['capacity'],
                $data['price'],
                $data['inapam'] ? 1 : 0,
                $data['status']
            ]);
            echo json_encode(['success' => true]);
            break;
            
        case 'update':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Requiere privilegios de admin']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE rooms SET type=?, number=?, beds=?, capacity=?, price=?, inapam=?, status=? WHERE id=?");
            $stmt->execute([
                $data['type'],
                $data['number'],
                $data['beds'],
                $data['capacity'],
                $data['price'],
                $data['inapam'] ? 1 : 0,
                $data['status'],
                $data['id']
            ]);
            echo json_encode(['success' => true]);
            break;
            
        case 'delete':
            if (!isAdmin()) {
                echo json_encode(['error' => 'Requiere privilegios de admin']);
                break;
            }
            
            $id = $_GET['id'] ?? '';
            $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>