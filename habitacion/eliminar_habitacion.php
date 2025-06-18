<?php
require_once '../config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        $stmt = $pdo->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode([
            "success" => true,
            "message" => "Habitación eliminada correctamente 🧹"
        ]);
        exit;
    }
}
echo json_encode([
    "success" => false,
    "message" => "ID inválido o error en la solicitud"
]);
