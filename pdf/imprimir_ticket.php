<?php
$apiKey = 'vs8j1s8-7mixFIT8V4_UdO-D7k5b8I4vuomBZX9mdCE'; // Reemplaza con tu API Key
$printerId = 74419784; // Reemplaza con tu ID de impresora

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID no proporcionado");
}

$ticketFile = "C:\\tickets\\anticipo_{$id}.txt";

if (!file_exists($ticketFile)) {
    die("Archivo no encontrado: $ticketFile");
}

$ticketContent = file_get_contents($ticketFile);

$data = [
    'printerId' => $printerId,
    'title' => 'Anticipo',
    'contentType' => 'raw_base64',
    'content' => base64_encode($ticketContent),
    'source' => 'Sistema de Anticipos'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.printnode.com/printjobs');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':');
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error en la solicitud: ' . curl_error($ch);
} else {
    echo 'Respuesta de PrintNode: ' . $response;
}
curl_close($ch);
?>
