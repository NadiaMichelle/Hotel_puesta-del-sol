<?php
require_once '../libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$id = $_GET['id'] ?? null;
if (!$id) die("Falta ID");

require '../config.php';
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
$stmt->execute([$id]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$res) die("Reserva no encontrada");

// Datos del huésped
$guest = null;
if (!empty($res['guestId'])) {
    $stmtG = $pdo->prepare("SELECT * FROM guests WHERE id = ?");
    $stmtG->execute([$res['guestId']]);
    $guest = $stmtG->fetch(PDO::FETCH_ASSOC);
}

$logo_path = __DIR__ . '/../assets/img/logo.png';
$logo_base64 = '';
if (file_exists($logo_path)) {
    $logo_base64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logo_path));
}

$start = new DateTime($res['start_date']);
$end = new DateTime($res['end_date']);
$noches = $start->diff($end)->days;

// Lista de personas hospedadas
$personas = json_decode($res['checkinGuests'] ?? '[]', true);
$nombres_personas = '';
for ($i = 0; $i < 10; $i++) {
    $nombre = $personas[$i]['name'] ?? '';
    $nombres_personas .= "<tr><td>" . ($i + 1) . ".</td><td>" . htmlspecialchars($nombre) . "</td></tr>";
}

// Artículos entregados
function checkItem($items, $key, $label) {
    $checked = isset($items[$key]['delivered']) && $items[$key]['delivered'] ? '✔' : ' ';
    return "<td>{$label} [{$checked}]</td>";
}

$items = json_decode($res['checkinItems'] ?? '{}', true);

// Reemplazo de plantilla
$template = file_get_contents("template_recepcion.html");
$reemplazos = [
    '{{logo_path}}' => $logo_base64,
    '{{fecha_actual}}' => date("d/m/Y"),
    '{{habitacion}}' => $res['resourceId'],
    '{{tarifa}}' => number_format($res['rate'], 2),
    '{{noches}}' => $noches,
    '{{cliente}}' => $res['guestNameManual'] ?? '',
    '{{direccion}}' => $guest['calle'] ?? '',
    '{{cp}}' => $guest['cp'] ?? '',
    '{{ciudad}}' => $guest['ciudad'] ?? '',
    '{{estado}}' => $guest['estado'] ?? '',
    '{{pais}}' => $guest['nacionalidad'] ?? '',
    '{{telefono}}' => $guest['telefono'] ?? '',
    '{{nacionalidad}}' => $guest['nacionalidad'] ?? '',
    '{{auto}}' => $res['auto'] ?? '',
    '{{recepcionista}}' => $res['receptionistName'] ?? '',
    '{{fecha_fin}}' => $res['end_date'],
    '{{nombres_personas}}' => $nombres_personas ?: '<tr><td colspan="2">Sin registrar</td></tr>',

    '{{check_loza}}' => checkItem($items, 'loza', 'Loza'),
    '{{check_cafetera}}' => checkItem($items, 'cafetera', 'Cafetera'),
    '{{check_licuadora}}' => checkItem($items, 'licuadora', 'Licuadora'),
    '{{check_control_tv}}' => checkItem($items, 'controltv', 'Control TV'),
    '{{check_control_aa}}' => checkItem($items, 'controlaa', 'Control AA'),
    '{{check_toallas}}' => checkItem($items, 'toallashab', 'Toallas Entregadas'),
    '{{check_para_habitacion}}' => checkItem($items, 'toallashab', 'Para habitación'),
    '{{check_para_alberca}}' => checkItem($items, 'toallasalb', 'Para alberca'),
];

foreach ($reemplazos as $clave => $valor) {
    $template = str_replace($clave, $valor, $template);
}

// Generar PDF
$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true);
$dompdf->loadHtml($template);
$dompdf->setPaper('legal', 'portrait');
$dompdf->render();
$dompdf->stream("Recepcion_" . preg_replace('/[^A-Za-z0-9]/', '_', $res['guestNameManual']) . ".pdf");
?>
