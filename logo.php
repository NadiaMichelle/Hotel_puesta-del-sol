<?php
require 'libs/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Activar carga de recursos locales
$dompdf = new Dompdf();
$dompdf->set_option('isRemoteEnabled', true);

// Obtener ruta absoluta al logo
$logoPath = realpath('assets/img/logot.png');

// Verificación opcional
if (!$logoPath || !file_exists($logoPath)) {
    die("El logo no se encontró en: $logoPath");
}

// HTML simple con el logo
$html = '
<!DOCTYPE html>
<html>
  <head><meta charset="UTF-8"><title>Prueba Logo</title></head>
  <body>
    <h2>Prueba de logo</h2>
    <img src="file://' . $logoPath . '" style="width: 120px;">
  </body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4');
$dompdf->render();
$dompdf->stream("prueba_logo.pdf");
?>
