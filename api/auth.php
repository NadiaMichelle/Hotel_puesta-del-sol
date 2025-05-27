<?php
require_once '../config.php';
header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? '';

    switch($action) {
        case 'login':
            // Iniciar sesión
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['username']) || empty($data['password'])) {
                echo json_encode(['error' => 'Usuario y contraseña son obligatorios']);
                break;
            }
            
            $stmt = $pdo->prepare("SELECT id, username, fullName, role, password_hash FROM users WHERE username = ?");
            $stmt->execute([$data['username']]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($data['password'], $user['password_hash'])) {
                // Iniciar sesión
                session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullName'] = $user['fullName'];
                $_SESSION['role'] = $user['role'];
                
                echo json_encode([
                    'success' => true,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'fullName' => $user['fullName'],
                        'role' => $user['role']
                    ]
                ]);
            } else {
                echo json_encode(['error' => 'Usuario o contraseña incorrectos']);
            }
            break;
            
        case 'logout':
            // Cerrar sesión
            session_start();
            session_destroy();
            echo json_encode(['success' => true]);
            break;
            
        case 'check':
            // Verificar sesión
            session_start();
            if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
                echo json_encode([
                    'authenticated' => true,
                    'user' => [
                        'id' => $_SESSION['id'],
                        'username' => $_SESSION['username'],
                        'fullName' => $_SESSION['fullName'],
                        'role' => $_SESSION['role']
                    ]
                ]);
            } else {
                echo json_encode(['authenticated' => false]);
            }
            break;
            
        case 'change_password':
            // Cambiar contraseña
            session_start();
            if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
                echo json_encode(['error' => 'No autorizado']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (empty($data['currentPassword']) || empty($data['newPassword'])) {
                echo json_encode(['error' => 'La contraseña actual y la nueva son obligatorias']);
                break;
            }
            
            // Verificar contraseña actual
            $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['id']]);
            $user = $stmt->fetch();
            
            if (!$user || !password_verify($data['currentPassword'], $user['password_hash'])) {
                echo json_encode(['error' => 'La contraseña actual es incorrecta']);
                break;
            }
            
            // Actualizar contraseña
            $newHash = password_hash($data['newPassword'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$newHash, $_SESSION['id']]);
            
            echo json_encode(['success' => true]);
            break;
            
        default:
            echo json_encode(['error' => 'Acción no válida']);
    }
} catch(PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>