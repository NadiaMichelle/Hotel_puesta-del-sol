<?php
require_once __DIR__ . '/../config.php';

date_default_timezone_set('America/Mexico_City');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "ID inválido";
    exit;
}

$id = intval($_GET['id']);

// Consultar Anticipo + Habitación
$stmt = $pdo->prepare("SELECT a.*, r.type as habitacion_nombre FROM anticipos a LEFT JOIN rooms r ON a.tipoHabitacion = r.id WHERE a.id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    http_response_code(404);
    echo "Anticipo no encontrado";
    exit;
}

if (empty($data['hora_impresion'])) {
    $horaMexico = date('Y-m-d H:i:s');
    $pdo->prepare("UPDATE anticipos SET hora_impresion = ? WHERE id = ?")->execute([$horaMexico, $id]);
    $data['hora_impresion'] = $horaMexico;
}

$tasa = floatval($data['tasa_cambio']);
$anticipo = floatval($data['anticipo']);
$moneda = strtoupper($data['selectMoneda'] ?? 'MXN');
$totalMXN = ($data['metodo_pago'] === 'Efectivo' && $tasa > 0) ? $anticipo * $tasa : $anticipo;

$lineaTotal = ($data['metodo_pago'] === 'Efectivo' && $tasa > 0) 
    ? "TOTAL:         $" . number_format($totalMXN, 2) . " MXN\n" 
    : "TOTAL:         $" . number_format($totalMXN, 2) . " {$moneda}\n";

// ESC/POS Commands
$init = "\x1B\x40"; // Reset
$center = "\x1B\x61\x01";
$left = "\x1B\x61\x00";
$boldOn = "\x1B\x45\x01";
$boldOff = "\x1B\x45\x00";
$cut = "\x1D\x56\x00";

$contenido = $init;
$contenido .= $center . $boldOn . "HOTEL MELAQUE PUESTA DEL SOL\n" . $boldOff;
$contenido .= $center . "Gomez Farias No. 31\n";
$contenido .= "Tels. 315 355 5797 / 315 355 5777\n";
$contenido .= "San Patricio Melaque, Jal. C.P. 48980\n\n";
$contenido .= $center . $boldOn . "*********** RECIBO DE ANTICIPO ***********\n\n" . $boldOff;
$contenido .= $left;
$contenido .= "FOLIO:         {$data['ticket']}\n";
$contenido .= "HUESPED:       {$data['guest']}\n";
$contenido .= "HABITACION:    {$data['habitacion_nombre']}\n";
$contenido .= "RESERVA:       {$data['reserva_id']}\n";
$contenido .= "ENTRADA:       {$data['entrada']}\n";
$contenido .= "SALIDA:        {$data['salida']}\n";
$contenido .= "METODO:        {$data['metodo_pago']}\n";
$contenido .= "TASA:          " . ($tasa > 0 ? number_format($tasa, 2) : '-') . "\n";
$contenido .= "ANTICIPO:      $" . number_format($anticipo, 2) . " {$moneda}\n";
$contenido .= $lineaTotal;
$contenido .= "FECHA:         {$data['fecha']}\n";
$contenido .= "HORA IMP.:     {$data['hora_impresion']}\n";
$contenido .= "OBSERVACIONES: " . ($data['observaciones'] ?: '—') . "\n\n";
$contenido .= "_________________________\n";
$contenido .= "Firma del huesped\n\n";
$contenido .= "=============================================\n";
$contenido .= "*** NOTAS IMPORTANTES / IMPORTANT NOTES ***\n";
$contenido .= "1. El Hotel no se hace responsable por valores no dejados en recepcion.\n";
$contenido .= "2. El cobro inicia al entregar la habitacion.\n";
$contenido .= "3. Prohibido introducir animales o vendedores.\n";
$contenido .= "4. En caso de cancelacion se cobra el 30%.\n";
$contenido .= "5. Acepto pagar \$290 por toalla de alberca no devuelta.\n";
$contenido .= "6. Conforme a ley de proteccion de datos.\n";
$contenido .= "7. No se permite musica grabada o en vivo.\n";
$contenido .= "8. Prohibido fumar en todo el hotel.\n";
$contenido .= "9. No se aceptan visitas no registradas.\n\n\n\n";
$contenido .= $cut;

// Enviar directamente a PrintNode
$apiKey = 'vs8j1s8-7mixFIT8V4_UdO-D7k5b8I4vuomBZX9mdCE';
$printerId = 74419785; // EPSON TM-T20III

$dataPrint = [
    'printerId' => $printerId,
    'title' => 'Anticipo',
    'contentType' => 'raw_base64',
    'content' => base64_encode($contenido),
    'source' => 'Sistema de Anticipos'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.printnode.com/printjobs');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':');
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($dataPrint));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error en la solicitud: ' . curl_error($ch);
} else {
    echo 'Respuesta de PrintNode: ' . $response;
}
curl_close($ch);
