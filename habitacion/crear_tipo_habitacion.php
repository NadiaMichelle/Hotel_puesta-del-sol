<?php
require_once '../config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');

  if ($nombre === '') {
    $error = 'El nombre no puede estar vacío.';
  } else {
    $stmt = $pdo->prepare("INSERT INTO tipos_habitacion (nombre, tarifas_normales, tarifas_cd, inapam_aplica, inapam_monto) VALUES (?, '[]', '[]', 0, 0.00)");
    if ($stmt->execute([$nombre])) {
      header('Location: habitaciones.php?creado=1');
      exit;
    } else {
      $error = 'Error al guardar el tipo de habitación.';
    }
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Tipo de Habitación</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container py-5">
  <h2><i class="fas fa-bed"></i> Crear Tipo de Habitación</h2>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Nombre del Tipo de Habitación</label>
      <input type="text" name="nombre" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="habitaciones.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
