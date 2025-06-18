<?php
require_once '../config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    if ($id > 0) {
        // Eliminar habitaciones asociadas primero (si las hay)
        $pdo->prepare("DELETE FROM rooms WHERE id_tipo = ?")->execute([$id]);

        // Luego eliminar el tipo
        $pdo->prepare("DELETE FROM tipos_habitacion WHERE id = ?")->execute([$id]);

        echo json_encode([
            "success" => true,
            "message" => "🧹 Tipo de habitación y sus habitaciones eliminados con éxito."
        ]);
        exit;
    }
}

echo json_encode([
    "success" => false,
    "message" => "⚠️ No se pudo eliminar el tipo de habitación."
]);
