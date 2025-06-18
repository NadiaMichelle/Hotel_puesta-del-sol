<?php
require_once '../config.php';

$id = $_GET['id'] ?? 0;

if (!$id || !is_numeric($id)) {
    die("ID inválido");
}

$stmt = $pdo->prepare("SELECT * FROM tipos_habitacion WHERE id = ?");
$stmt->execute([$id]);
$tipo = $stmt->fetch();

if (!$tipo) {
    die("Tipo de habitación no encontrado");
}

$tarifas_normales = json_decode($tipo['tarifas_normales'], true) ?: [];
$tarifas_cd = json_decode($tipo['tarifas_cd'], true) ?: [];
$inapam_aplica = $tipo['inapam_aplica'] ?? 0;
$inapam_monto = $tipo['inapam_monto'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Tarifas</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --hotel-orange: #FF8C42;
      --hotel-orange-light: #FFB366;
      --hotel-orange-dark: #E6732D;
      --hotel-cream: #FFF5E6;
      --hotel-brown: #8B4513;
      --hotel-gold: #FFD700;
    }

    body {
      background: linear-gradient(135deg, var(--hotel-cream) 0%, #FFF0D6 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
    }

    .main-container {
      background: white;
      border-radius: 20px;
      box-shadow: 0 15px 35px rgba(255, 140, 66, 0.1);
      border: 2px solid var(--hotel-orange-light);
      overflow: hidden;
    }

    .header-section {
      background: linear-gradient(135deg, var(--hotel-orange) 0%, var(--hotel-orange-dark) 100%);
      color: white;
      padding: 2rem;
      position: relative;
      overflow: hidden;
    }

    .header-section::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -20%;
      width: 200px;
      height: 200px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 50%;
      transform: rotate(45deg);
    }

    .header-section h2 {
      margin: 0;
      font-weight: 600;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
      position: relative;
      z-index: 2;
    }

    .content-section {
      padding: 2rem;
    }

    .section-title {
      color: var(--hotel-orange-dark);
      font-weight: 600;
      margin-bottom: 1.5rem;
      padding-bottom: 0.5rem;
      border-bottom: 3px solid var(--hotel-orange-light);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .tarifa-card, .cd-card {
      background: var(--hotel-cream);
      border: 2px solid var(--hotel-orange-light);
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      position: relative;
      transition: all 0.3s ease;
    }

    .tarifa-card:hover, .cd-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(255, 140, 66, 0.15);
    }

    .tarifa-row, .cd-row { 
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
      gap: 1rem;
      align-items: end;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-label-sm { 
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--hotel-brown);
      margin-bottom: 0.3rem;
    }

    .form-control {
      border: 2px solid #E0E0E0;
      border-radius: 8px;
      padding: 0.6rem;
      transition: all 0.3s ease;
      font-size: 0.9rem;
    }

    .form-control:focus {
      border-color: var(--hotel-orange);
      box-shadow: 0 0 0 0.2rem rgba(255, 140, 66, 0.25);
    }

    .delete-btn { 
      background: linear-gradient(135deg, #FF4757, #FF3838);
      border: none;
      color: white;
      border-radius: 8px;
      width: 40px;
      height: 40px;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .delete-btn:hover {
      transform: scale(1.1);
      background: linear-gradient(135deg, #FF3838, #FF2828);
    }

    .btn-add {
      background: linear-gradient(135deg, var(--hotel-orange), var(--hotel-orange-dark));
      border: none;
      color: white;
      border-radius: 10px;
      padding: 0.8rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    }

    .btn-add:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
      background: linear-gradient(135deg, var(--hotel-orange-dark), var(--hotel-orange));
    }

    .btn-add-cd {
      background: linear-gradient(135deg, #28a745, #20c997);
      border: none;
      color: white;
      border-radius: 10px;
      padding: 0.8rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }

    .btn-add-cd:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
      background: linear-gradient(135deg, #20c997, #17a2b8);
    }

    .alert-custom {
      background: linear-gradient(135deg, #FFF3CD, #FFE69C);
      border: 2px solid var(--hotel-gold);
      border-radius: 12px;
      color: var(--hotel-brown);
      padding: 1rem;
      margin-bottom: 1.5rem;
    }

    .form-switch .form-check-input {
      width: 3rem;
      height: 1.5rem;
    }

    .form-switch .form-check-input:checked {
      background-color: var(--hotel-orange);
      border-color: var(--hotel-orange);
    }

    .form-switch .form-check-label {
      font-weight: 600;
      color: var(--hotel-brown);
      margin-left: 0.5rem;
    }

    .inapam-section {
      background: linear-gradient(135deg, #F8F9FA, #E9ECEF);
      border: 2px solid var(--hotel-orange-light);
      border-radius: 15px;
      padding: 1.5rem;
      margin-bottom: 2rem;
    }

    .action-buttons {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 2px solid var(--hotel-orange-light);
    }

    .btn-primary-custom {
      background: linear-gradient(135deg, var(--hotel-orange), var(--hotel-orange-dark));
      border: none;
      color: white;
      border-radius: 12px;
      padding: 1rem 2rem;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 140, 66, 0.3);
    }

    .btn-primary-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(255, 140, 66, 0.4);
    }

    .btn-secondary-custom {
      background: linear-gradient(135deg, #6c757d, #5a6268);
      border: none;
      color: white;
      border-radius: 12px;
      padding: 1rem 2rem;
      font-weight: 600;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }

    .btn-secondary-custom:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
    }

    @media (max-width: 768px) {
      .tarifa-row, .cd-row {
        grid-template-columns: 1fr;
      }
      
      .action-buttons {
        flex-direction: column;
      }
      
      .header-section {
        padding: 1.5rem;
      }
      
      .content-section {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
<?php if (isset($_GET['guardado']) && $_GET['guardado'] == 1): ?>
<div class="alert alert-success alert-dismissible fade show m-3" role="alert">
  ✅ Las tarifas fueron guardadas correctamente.
  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
</div>
<?php endif; ?>
<div class="container py-4">
  <div class="header-section mb-4">
    <h2><i class="fas fa-dollar-sign me-2"></i> Tarifas para tipo: <?= htmlspecialchars($tipo['nombre'] ?? 'Habitación') ?></h2>
  </div>
  <form method="POST" action="guardar_tarifas.php">
    <input type="hidden" name="tipo_id" value="<?= htmlspecialchars($id) ?>">

    <h4 class="section-title">Tarifas Normales</h4>
    <div id="tarifasNormales">
      <?php foreach ($tarifas_normales as $index => $tarifa): ?>
      <div class="row g-2 mb-3">
        <div class="col"><input type="text" name="tarifas_normales[<?= $index ?>][temporada]" class="form-control" placeholder="Temporada" value="<?= htmlspecialchars($tarifa['temporada'] ?? '') ?>"></div>
        <div class="col"><input type="text" name="tarifas_normales[<?= $index ?>][inicio]" class="form-control" placeholder="Inicio (dd/mm)" value="<?= htmlspecialchars($tarifa['inicio'] ?? '') ?>"></div>
        <div class="col"><input type="text" name="tarifas_normales[<?= $index ?>][fin]" class="form-control" placeholder="Fin (dd/mm)" value="<?= htmlspecialchars($tarifa['fin'] ?? '') ?>"></div>
        <div class="col"><input type="number" name="tarifas_normales[<?= $index ?>][pax_min]" class="form-control" placeholder="Pax Min" value="<?= htmlspecialchars($tarifa['pax_min'] ?? '') ?>"></div>
        <div class="col"><input type="number" name="tarifas_normales[<?= $index ?>][pax_max]" class="form-control" placeholder="Pax Max" value="<?= htmlspecialchars($tarifa['pax_max'] ?? '') ?>"></div>
        <div class="col"><input type="number" name="tarifas_normales[<?= $index ?>][precio]" class="form-control" placeholder="Precio" value="<?= htmlspecialchars($tarifa['precio'] ?? '') ?>"></div>
      </div>
      <?php endforeach; ?>
    </div>
    <button type="button" class="btn btn-outline-primary add-btn" onclick="agregarTarifaNormal()"><i class="fas fa-plus"></i> Agregar tarifa normal</button>

    <h4 class="section-title">Tarifas con Descuento (CD)</h4>
    <div class="alert alert-warning">Estas tarifas se aplican automáticamente en enero, febrero y marzo.</div>
    <div id="tarifasCD">
      <?php foreach ($tarifas_cd as $index => $cd): ?>
      <div class="row g-2 mb-3">
        <input type="hidden" name="tarifas_cd[<?= $index ?>][meses]" value="1,2,3">
        <div class="col"><input type="number" name="tarifas_cd[<?= $index ?>][pax_min]" class="form-control" placeholder="Pax Min" value="<?= htmlspecialchars($cd['pax_min'] ?? '') ?>"></div>
        <div class="col"><input type="number" name="tarifas_cd[<?= $index ?>][pax_max]" class="form-control" placeholder="Pax Max" value="<?= htmlspecialchars($cd['pax_max'] ?? '') ?>"></div>
        <div class="col"><input type="number" name="tarifas_cd[<?= $index ?>][noches_min]" class="form-control" placeholder="Noches Min" value="<?= htmlspecialchars($cd['noches_min'] ?? '') ?>"></div>
        <div class="col"><input type="number" name="tarifas_cd[<?= $index ?>][noches_max]" class="form-control" placeholder="Noches Max" value="<?= htmlspecialchars($cd['noches_max'] ?? '') ?>"></div>
        <div class="col"><input type="number" name="tarifas_cd[<?= $index ?>][precio]" class="form-control" placeholder="Precio" value="<?= htmlspecialchars($cd['precio'] ?? '') ?>"></div>
      </div>
      <?php endforeach; ?>
    </div>
    <button type="button" class="btn btn-outline-success add-btn" onclick="agregarTarifaCD()"><i class="fas fa-plus"></i> Agregar tarifa CD</button>

    <h4 class="section-title">Descuento INAPAM</h4>
    <div class="form-check form-switch mb-3">
      <input class="form-check-input" type="checkbox" name="inapam_aplica" id="inapamSwitch" <?= $inapam_aplica ? 'checked' : '' ?>>
      <label class="form-check-label" for="inapamSwitch">¿Aplicar descuento INAPAM?</label>
    </div>
    <div class="mb-4">
      <input type="number" step="0.01" name="inapam_monto" value="<?= htmlspecialchars($inapam_monto) ?>" class="form-control" placeholder="Monto fijo de descuento (MXN)">
    </div>

    <div class="text-center mt-4">
      <button type="submit" class="btn btn-success px-4">Guardar tarifas</button>
      <a href="habitaciones.php" class="btn btn-secondary px-4 ms-2">Volver</a>
    </div>
  </form>
</div>
<script>
function agregarTarifaNormal() {
  const container = document.getElementById('tarifasNormales');
  const index = container.children.length;
  const div = document.createElement('div');
  div.className = 'row g-2 mb-3';
  div.innerHTML = `
    <div class="col"><input type="text" name="tarifas_normales[\${index}][temporada]" class="form-control" placeholder="Temporada"></div>
    <div class="col"><input type="text" name="tarifas_normales[\${index}][inicio]" class="form-control" placeholder="Inicio (dd/mm)"></div>
    <div class="col"><input type="text" name="tarifas_normales[\${index}][fin]" class="form-control" placeholder="Fin (dd/mm)"></div>
    <div class="col"><input type="number" name="tarifas_normales[\${index}][pax_min]" class="form-control" placeholder="Pax Min"></div>
    <div class="col"><input type="number" name="tarifas_normales[\${index}][pax_max]" class="form-control" placeholder="Pax Max"></div>
    <div class="col"><input type="number" name="tarifas_normales[\${index}][precio]" class="form-control" placeholder="Precio"></div>
  `;
  container.appendChild(div);
}

function agregarTarifaCD() {
  const container = document.getElementById('tarifasCD');
  const index = container.children.length;
  const div = document.createElement('div');
  div.className = 'row g-2 mb-3';
  div.innerHTML = `
    <input type="hidden" name="tarifas_cd[\${index}][meses]" value="1,2,3">
    <div class="col"><input type="number" name="tarifas_cd[\${index}][pax_min]" class="form-control" placeholder="Pax Min"></div>
    <div class="col"><input type="number" name="tarifas_cd[\${index}][pax_max]" class="form-control" placeholder="Pax Max"></div>
    <div class="col"><input type="number" name="tarifas_cd[\${index}][noches_min]" class="form-control" placeholder="Noches Min"></div>
    <div class="col"><input type="number" name="tarifas_cd[\${index}][noches_max]" class="form-control" placeholder="Noches Max"></div>
    <div class="col"><input type="number" name="tarifas_cd[\${index}][precio]" class="form-control" placeholder="Precio"></div>
  `;
  container.appendChild(div);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
