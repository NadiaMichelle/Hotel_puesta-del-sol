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

//Nombre huesped
$nombreCliente = preg_replace('/[^A-Za-z0-9 _-]/', '', $res['guestNameManual'] ?? 'Reserva');


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
    $logo_data = file_get_contents($logo_path);
    $logo_base64 = 'data:image/png;base64,' . base64_encode($logo_data);
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

// Anticipo
$anticipo = json_decode($res['anticipo'], true);
$anticipoMonto = isset($anticipo['monto']) ? floatval($anticipo['monto']) : 0;
$anticipoMetodo = $anticipo['metodo'] ?? '';

// Pagos extra
$pagosExtra = json_decode($res['pagosExtra'], true);
$pagado = $anticipoMonto;
$filasPagos = '';
if (is_array($pagosExtra)) {
    foreach ($pagosExtra as $p) {
        $pagado += floatval($p['monto']);
        $filasPagos .= "<tr>
            <td>$" . number_format($p['monto'], 2) . "</td>
            <td>" . htmlspecialchars($p['metodo']) . "</td>
            <td>" . htmlspecialchars($p['clave']) . "</td>
            <td>" . htmlspecialchars($p['autorizacion']) . "</td>
            <td>" . htmlspecialchars($p['fecha']) . "</td>
        </tr>";
    }
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
    "{{habitacion}}" => $res['resourceId'],
    "{{fecha_inicio}}" => $res['start_date'],
    "{{fecha_fin}}" => $res['end_date'],
    "{{noches}}" => $noches,
    "{{anticipo_monto}}" => number_format($anticipoMonto, 2),
    "{{anticipo_metodo}}" => $anticipoMetodo,
    "{{filas_pagos}}" => $filasPagos,
    "{{verificado}}" => $verificado,
    "{{verificacion_fecha}}" => $verificacionFecha,
    "{{verificacion_persona}}" => $verificacionPersona,
    "{{tarifa_noche}}" => number_format($tarifa, 2),
    "{{subtotal}}" => number_format($subtotal, 2),
    "{{iva}}" => $iva,
    "{{iva_monto}}" => number_format($ivaMonto, 2),
    "{{ish}}" => $ish,
    "{{ish_monto}}" => number_format($ishMonto, 2),
    "{{inapam}}" => number_format($inapam, 2),
    "{{total}}" => number_format($total, 2),
    "{{pagado}}" => number_format($pagado, 2),
    "{{saldo}}" => number_format($total - $pagado, 2),
    "{{fecha_actual}}" => date("d/m/Y")  
];

foreach ($reemplazos as $clave => $valor) {
    $html = str_replace($clave, $valor, $html);
}

// Generar PDF
$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true); // si usas imágenes externas
$dompdf->loadHtml($html);
$dompdf->setPaper('letter', 'portrait'); // <-- aquí se define carta vertical
$dompdf->render();
$dompdf->stream("Huesped_" . $nombreCliente . "_Administracion.pdf");




?>
