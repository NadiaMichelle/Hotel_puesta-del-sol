<?php
require_once __DIR__ . '/../libs/dompdf/autoload.inc.php';
require_once __DIR__ . '/../config.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Zona horaria de México
date_default_timezone_set('America/Mexico_City');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    http_response_code(400);
    echo "ID inválido";
    exit;
}

$id = intval($_GET['id']);

// CONSULTAR ANTICIPO Y NOMBRE DE HABITACIÓN
$stmt = $pdo->prepare("SELECT a.*, r.type as habitacion_nombre FROM anticipos a LEFT JOIN rooms r ON a.tipoHabitacion = r.id WHERE a.id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    http_response_code(404);
    echo "Anticipo no encontrado";
    exit;
}

// REGISTRAR HORA DE IMPRESIÓN SI NO ESTÁ GUARDADA
if (empty($data['hora_impresion'])) {
    $horaMexico = date('Y-m-d H:i:s');
    $stmtUpdate = $pdo->prepare("UPDATE anticipos SET hora_impresion = ? WHERE id = ?");
    $stmtUpdate->execute([$horaMexico, $id]);
    $data['hora_impresion'] = $horaMexico;
}

$tasa = floatval($data['tasa_cambio']);
$anticipo = floatval($data['anticipo']);
$totalMXN = ($data['metodo_pago'] === 'Efectivo' && $tasa > 0) ? $anticipo * $tasa : $anticipo;

// CONFIGURACIÓN DOMPDF
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$html = "
<!DOCTYPE html>
<html>
<head>
  <meta charset='UTF-8'>
  <style>
    body { margin: 0; padding: 0; font-family: DejaVu Sans, sans-serif; font-size: 9pt; }
    .ticket { width: 62mm; padding: 5px 10px; }
    h2 { text-align: center; font-size: 12pt; margin: 4px 0; }
    .center { text-align: center; }
    .line { border-top: 1px dashed #000; margin: 6px 0; }
    .label { font-weight: bold; display: inline-block; width: 80px; }
    .firma { text-align: center; margin: 20px 0 10px; font-size: 9pt; }
    .notes { font-size: 7pt; line-height: 1.2; }
    table.notes td { vertical-align: top; padding: 4px; }
  </style>
</head>
<body>
  <div class='ticket'>
    <div class='center'>
      <strong style='font-size: 11pt;'>Hotel Melaque Puesta del Sol</strong><br>
      Gómez Farías No. 31<br>
      Tels. 315 355 5797 / 315 355 5777<br>
      San Patricio Melaque, Jal. C.P. 48980
    </div>

    <h2>RECIBO DE ANTICIPO</h2>
    <div class='line'></div>

    <div><span class='label'>Folio:</span> {$data['ticket']}</div>
    <div><span class='label'>Hósped:</span> {$data['guest']}</div>
    <div><span class='label'>Habitación:</span> {$data['habitacion_nombre']}</div>
    <div><span class='label'>Reserva:</span> {$data['reserva_id']}</div>
    <div><span class='label'>Entrada:</span> {$data['entrada']}</div>
    <div><span class='label'>Salida:</span> {$data['salida']}</div>
    <div><span class='label'>Método:</span> {$data['metodo_pago']}</div>
    <div><span class='label'>Tasa:</span> " . ($tasa > 0 ? number_format($tasa, 2) : '-') . "</div>
    <div><span class='label'>Anticipo:</span> $" . number_format($anticipo, 2) . "</div>
    <div><span class='label'>Total MXN:</span> $" . number_format($totalMXN, 2) . "</div>
    <div><span class='label'>Fecha:</span> {$data['fecha']}</div>
    <div><span class='label'>Hora Imp.:</span> {$data['hora_impresion']}</div>
    <div><span class='label'>Obs.:</span> " . ($data['observaciones'] ?: '—') . "</div>

    <div class='line'></div>
    <div class='firma'>____________________________<br>Firma del huésped</div>

    <div class='notes'>
      <strong>NOTAS IMPORTANTES / IMPORTANT NOTES</strong>
      <table class='notes'>
        <tr>
          <td width='50%'>
            1. El Hotel no se hace responsable por valores no dejados en recepción.<br>
            2. El cobro inicia al entregar la habitación.<br>
            3. Prohibido introducir animales o vendedores.<br>
            4. En caso de cancelación se cobra el 30%.<br>
            5. Acepto pagar $290 por toalla de alberca no devuelta.<br>
            6. Conforme a ley de protección de datos.<br>
            7. No se permite música grabada o en vivo.<br>
            8. Prohibido fumar en todo el hotel.<br>
            9. No se aceptan visitas no registradas.
          </td>
          <td>
            1. Hotel is not responsible for valuables not left at reception.<br>
            2. Room charge starts when the room is delivered.<br>
            3. Animals or vendors are not allowed.<br>
            4. 30% charge applies for cancellations.<br>
            5. I agree to pay $290 for each unreturned pool towel.<br>
            6. Data privacy law applies.<br>
            7. No recorded or live music allowed.<br>
            8. Smoking is strictly prohibited.<br>
            9. No unregistered guests allowed.
          </td>
        </tr>
      </table>
    </div>
  </div>
</body>
</html>
";

if (ob_get_contents()) ob_end_clean();

$dompdf->loadHtml($html);
$dompdf->setPaper([0, 0, 186.8, 1000], 'portrait'); // 66mm de ancho
$dompdf->render();

header('Content-Type: application/pdf');
header('Cache-Control: no-store, no-cache');
header('Pragma: no-cache');
$dompdf->stream("anticipo_{$id}.pdf", ["Attachment" => false]);
exit;
