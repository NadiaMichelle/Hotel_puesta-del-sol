<?php
require_once '../config.php';

$stmt = $pdo->query("SELECT * FROM paquetes_tarifas ORDER BY tipo_paquete, pax, noches_min");
$paquetes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestionar Paquetes</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
  <h2 class="mb-4"><i class="fas fa-box"></i> Gestión de Paquetes de Tarifa</h2>
  <a href="crear_paquete.php" class="btn btn-success mb-3">
    <i class="fas fa-plus"></i> Nuevo Paquete
  </a>
  <table class="table table-bordered table-hover bg-white">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Tipo de Paquete</th>
        <th>Pax</th>
        <th>Noches Min</th>
        <th>Noches Max</th>
        <th>Tarifa</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($paquetes as $paq): ?>
      <tr>
        <td><?= $paq['id'] ?></td>
        <td><?= htmlspecialchars($paq['tipo_paquete']) ?></td>
        <td><?= $paq['pax'] ?></td>
        <td><?= $paq['noches_min'] ?></td>
        <td><?= $paq['noches_max'] ?></td>
        <td>$<?= number_format($paq['tarifa'], 2) ?></td>
        <td>
          <a href="editar_paquete.php?id=<?= $paq['id'] ?>" class="btn btn-sm btn-warning">
            <i class="fas fa-edit"></i>
          </a>
          <a href="eliminar_paquete.php?id=<?= $paq['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este paquete?')">
            <i class="fas fa-trash"></i>
          </a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
