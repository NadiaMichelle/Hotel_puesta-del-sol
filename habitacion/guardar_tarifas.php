<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_id = intval($_POST['tipo_id'] ?? 0);
    $inapam_aplica = isset($_POST['inapam_aplica']) ? 1 : 0;
    $inapam_monto = floatval($_POST['inapam_monto'] ?? 0);

    if ($tipo_id <= 0) {
        die("ID inválido");
    }

    // Tarifas Normales (con temporada, fechas y pax)
    $tarifas_normales = $_POST['tarifas_normales'] ?? [];
    $tarifas_normales = array_filter($tarifas_normales, function($t) {
        return !empty($t['precio']) && !empty($t['temporada']);
    });

    // Tarifas CD (con pax y noches)
    $tarifas_cd = $_POST['tarifas_cd'] ?? [];
    $tarifas_cd = array_filter($tarifas_cd, function($t) {
        return !empty($t['precio']);
    });

    // Convertir a JSON
    $json_tarifas_normales = json_encode(array_values($tarifas_normales), JSON_UNESCAPED_UNICODE);
    $json_tarifas_cd = json_encode(array_values($tarifas_cd), JSON_UNESCAPED_UNICODE);

    // Guardar en base de datos
    $stmt = $pdo->prepare("UPDATE tipos_habitacion SET 
        tarifas_normales = ?, 
        tarifas_cd = ?, 
        inapam_aplica = ?, 
        inapam_monto = ? 
        WHERE id = ?");

    $success = $stmt->execute([
        $json_tarifas_normales,
        $json_tarifas_cd,
        $inapam_aplica,
        $inapam_monto,
        $tipo_id
    ]);

    if ($success) {
        header("Location: tarifas.php?id=$tipo_id&guardado=1");
        exit;
    } else {
        echo "❌ Error al guardar tarifas.";
    }
} else {
    echo "🚫 Acceso no permitido.";
}
