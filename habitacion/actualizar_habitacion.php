<?php
require_once '../config.php';

$id = intval($_POST['id'] ?? 0);
$numero = trim($_POST['numero'] ?? '');
$capacidad = intval($_POST['capacidad'] ?? 0);

if ($id > 0 && $numero && $capacidad > 0) {
    $stmt = $pdo->prepare("UPDATE rooms SET numero = ?, capacidad = ? WHERE id = ?");
    $stmt->execute([$numero, $capacidad, $id]);
    echo "ok";
    exit;
}
echo "error";
