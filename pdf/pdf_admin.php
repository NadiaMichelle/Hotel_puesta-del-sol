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

// Nombre huésped
$nombreCliente = preg_replace('/[^A-Za-z0-9 _-]/', '', $res['guestNameManual'] ?? 'Reserva');

// Datos del huésped
$guest = null;
if (!empty($res['guestId'])) {
    $stmtG = $pdo->prepare("SELECT * FROM guests WHERE id = ?");
    $stmtG->execute([$res['guestId']]);
    $guest = $stmtG->fetch(PDO::FETCH_ASSOC);
}

// Logo
$logo_path = __DIR__ . '/../assets/img/logo.png';
$logo_base64 = '';
if (file_exists($logo_path)) {
    $logo_data = file_get_contents($logo_path);
    $logo_base64 = 'data:image/png;base64,' . base64_encode($logo_data);
} else {
    $logo_base64 = 'https://via.placeholder.com/80x80.png?text=LOGO';
}

// Cálculos
$start = new DateTime($res['start_date']);
$end = new DateTime($res['end_date']);
$noches = $start->diff($end)->days;

$tarifa = floatval($res['rate']);
$iva = floatval($res['iva']);
$ish = floatval($res['ish']);
$subtotal = $tarifa * $noches;
$ivaMonto = $subtotal * ($iva / 100);
$ishMonto = $subtotal * ($ish / 100);
$inapam = $res['inapamDiscount'] ? floatval($res['inapamDiscountValue']) : 0;
$total = $subtotal + $ivaMonto + $ishMonto - $inapam;

$status = strtoupper(trim($res['status'] ?? ''));
$statusClass = 'status-activa'; // Default

if ($status === 'CANCELADA') {
    $statusClass = 'status-cancelada';
} elseif ($status === 'PENDIENTE') {
    $statusClass = 'status-pendiente';
}


// Anticipo
$anticipo = json_decode($res['anticipo'], true);
$anticipoMonto = is_numeric($anticipo['monto'] ?? null) ? floatval($anticipo['monto']) : 0;
$anticipoMetodo = $anticipo['metodo'] ?? '';

// Pagos extra
$pagosExtra = json_decode($res['pagosExtra'], true);
$pagado = $anticipoMonto;
$filasPagos = '';
if (is_array($pagosExtra)) {
    foreach ($pagosExtra as $p) {
        $montoPago = is_numeric($p['monto'] ?? null) ? floatval($p['monto']) : 0;
        $pagado += $montoPago;
        $filasPagos .= "<tr>
            <td>$" . number_format($montoPago, 2) . "</td>
            <td>" . htmlspecialchars($p['metodo'] ?? '') . "</td>
            <td>" . htmlspecialchars($p['clave'] ?? '') . "</td>
            <td>" . htmlspecialchars($p['autorizacion'] ?? '') . "</td>
            <td>" . htmlspecialchars($p['fecha'] ?? '') . "</td>
        </tr>";
    }
}
if (empty($filasPagos)) {
    $filasPagos = '<tr><td colspan="5" style="text-align:center;">No hay pagos registrados</td></tr>';
}

// Verificación
$verif = $res['verification'] ? json_decode($res['verification'], true) : null;
$verificado = (isset($verif['whatsAppVerified']) && strtolower(trim($verif['whatsAppVerified'])) === 'si') ? 'Sí' : 'No';
$verificacionFecha = $verif['dateTime'] ?? '';
$verificacionPersona = $verif['senderName'] ?? '';

// Reemplazo de plantilla
$html = file_get_contents("template_admin.html");

$reemplazos = [
    '{{logo_path}}' => $logo_base64,
    "{{cliente}}" => $res['guestNameManual'],
    "{{telefono}}" => $guest['telefono'] ?? '',
    "{{email}}" => $guest['email'] ?? '',
    "{{nacionalidad}}" => $guest['nacionalidad'] ?? '',
    "{{direccion}}" => $guest['calle'] ?? '',
    "{{ciudad}}" => $guest['ciudad'] ?? '',
    "{{estado}}" => $guest['estado'] ?? '',
    "{{cp}}" => $guest['cp'] ?? '',
    "{{rfc}}" => $guest['rfc'] ?? '',
    "{{auto}}" => $guest['auto'] ?? '',
    "{{habitacion}}" => $res['resourceId'],
    "{{fecha_inicio}}" => $res['start_date'],
    "{{fecha_fin}}" => $res['end_date'],
    "{{noches}}" => $noches,
    "{{anticipo_monto}}" => is_numeric($anticipoMonto) ? number_format($anticipoMonto, 2) : '—',
    "{{anticipo_metodo}}" => $anticipoMetodo,
    "{{filas_pagos}}" => $filasPagos,
    "{{verificado}}" => $verificado,
    "{{verificacion_fecha}}" => $verificacionFecha,
    "{{verificacion_persona}}" => $verificacionPersona,
    "{{tarifa_noche}}" => is_numeric($tarifa) ? number_format($tarifa, 2) : '—',
    "{{subtotal}}" => is_numeric($subtotal) ? number_format($subtotal, 2) : '—',
    "{{iva}}" => $iva,
    "{{iva_monto}}" => is_numeric($ivaMonto) ? number_format($ivaMonto, 2) : '—',
    "{{ish}}" => $ish,
    "{{ish_monto}}" => is_numeric($ishMonto) ? number_format($ishMonto, 2) : '—',
    "{{inapam}}" => is_numeric($inapam) ? number_format($inapam, 2) : '—',
    "{{total}}" => is_numeric($total) ? number_format($total, 2) : '—',
    "{{pagado}}" => is_numeric($pagado) ? number_format($pagado, 2) : '—',
    "{{saldo}}" => is_numeric($total - $pagado) ? number_format($total - $pagado, 2) : '—',
    "{{fecha_actual}}" => date("d/m/Y")
];

foreach ($reemplazos as $clave => $valor) {
    $html = str_replace($clave, $valor, $html);
}

// Generar PDF
$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true); // si usas imágenes externas
$dompdf->loadHtml($html);
$dompdf->setPaper('letter', 'portrait');
$dompdf->render();
$dompdf->stream("Huesped_" . $nombreCliente . "_Administracion.pdf");
?>
