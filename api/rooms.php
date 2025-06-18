<?php
error_reporting(0); // Evita que los warnings se muestren en el JSON
ini_set('display_errors', 0);

header('Content-Type: application/json');
require_once '../config.php'; // Ajusta la ruta si es necesario


if ($action === 'get_all') {
    try {
        // Obtener todos los tipos con sus habitaciones
        $stmtTipos = $pdo->query("SELECT * FROM tipos_habitacion");
        $tipos = $stmtTipos->fetchAll();

        $response = [];

        foreach ($tipos as $tipo) {
            $stmtHabs = $pdo->prepare("SELECT * FROM rooms WHERE id_tipo = ?");
            $stmtHabs->execute([$tipo['id']]);
            $habitaciones = $stmtHabs->fetchAll();

            foreach ($habitaciones as $hab) {
                $response[] = [
                    'id' => $hab['id'],
                    'number' => $hab['nombre'],
                    'type' => $tipo['nombre'],
                    'inapam' => $tipo['inapam_aplica'] ?? 0
                ];
            }
        }

        echo json_encode($response);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error al obtener habitaciones']);
    }
}
