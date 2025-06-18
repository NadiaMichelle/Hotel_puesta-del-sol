<?php
require_once '../config.php';

$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');

if ($id > 0 && $nombre !== '') {
    $stmt = $pdo->prepare("UPDATE tipos_habitacion SET nombre = ? WHERE id = ?");
    $stmt->execute([$nombre, $id]);
    echo "ok";
    exit;
}
echo "error";
