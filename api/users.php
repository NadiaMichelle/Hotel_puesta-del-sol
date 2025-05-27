<?php
include_once __DIR__ . '/../config.php'; // Debe definir $pdo (PDO) correctamente

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $username = trim($data['username'] ?? '');
    $fullName = trim($data['fullName'] ?? '');
    $email = trim($data['email'] ?? '');
    $role = trim($data['role'] ?? '');
    $password = $data['password'] ?? '';

    if (!$username || !$fullName || !$email || !$role || !$password) {
        echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, fullName, email, role, password_hash) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$username, $fullName, $email, $role, $hashedPassword]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al insertar: ' . $e->getMessage()]);
    }
    exit;
}

if ($action === 'list') {
    try {
        $stmt = $pdo->query("SELECT id, username, fullName, email, role FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'users' => $users]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error al obtener usuarios: ' . $e->getMessage()]);
    }
    exit;
}
