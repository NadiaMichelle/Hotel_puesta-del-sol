<?php
function calcularPrecio($tipo_id, $fecha_entrada, $fecha_salida, $pax, $inapam = false) {
    require 'config.php';

    $stmt = $pdo->prepare("SELECT tarifas_normales, tarifas_cd, inapam_aplica, inapam_monto FROM tipos_habitacion WHERE id = ?");
    $stmt->execute([$tipo_id]);
    $tipo = $stmt->fetch();
    if (!$tipo) return ['error' => 'Tipo de habitación no encontrado'];

    $tarifas_normales = json_decode($tipo['tarifas_normales'], true) ?? [];
    $tarifas_cd = json_decode($tipo['tarifas_cd'], true) ?? [];
    $inapam_aplica = (int)$tipo['inapam_aplica'];
    $inapam_monto = (float)$tipo['inapam_monto'];

    $entrada = DateTime::createFromFormat('Y-m-d', $fecha_entrada);
    $salida = DateTime::createFromFormat('Y-m-d', $fecha_salida);
    $noches = $entrada && $salida ? $entrada->diff($salida)->days : 1;
    $mes = (int)$entrada->format('n');

    // 1. Buscar tarifa CD si aplica
    if (in_array($mes, [1, 2, 3])) {
        foreach ($tarifas_cd as $cd) {
            if (
                $pax >= $cd['pax_min'] && $pax <= $cd['pax_max'] &&
                $noches >= $cd['noches_min'] && $noches <= $cd['noches_max']
            ) {
                $precio = $cd['precio'];
                $final = $inapam && $inapam_aplica ? max(0, $precio - $inapam_monto) : $precio;
                return [
                    'precio_base' => $precio,
                    'descuento_inapam' => $inapam && $inapam_aplica ? $inapam_monto : 0,
                    'precio_final' => $final,
                    'tipo_tarifa' => 'cd'
                ];
            }
        }
    }

    // 2. Buscar tarifa normal por temporada
    foreach ($tarifas_normales as $t) {
        [$d1, $m1] = explode('/', $t['inicio']);
        [$d2, $m2] = explode('/', $t['fin']);

        $inicio = DateTime::createFromFormat('Y-m-d', $entrada->format("Y-{$m1}-{$d1}"));
        $fin = DateTime::createFromFormat('Y-m-d', $entrada->format("Y-{$m2}-{$d2}"));

        if ($entrada >= $inicio && $entrada <= $fin && $pax >= $t['pax_min'] && $pax <= $t['pax_max']) {
            $precio = $t['precio'];
            $final = $inapam && $inapam_aplica ? max(0, $precio - $inapam_monto) : $precio;
            return [
                'precio_base' => $precio,
                'descuento_inapam' => $inapam && $inapam_aplica ? $inapam_monto : 0,
                'precio_final' => $final,
                'tipo_tarifa' => 'normal'
            ];
        }
    }

    return ['error' => 'No se encontró tarifa aplicable'];
}
?>
