<?php
require_once '../config.php';

$id_tipo = isset($_GET['id_tipo']) ? intval($_GET['id_tipo']) : 0;

if ($id_tipo <= 0) {
    die("Tipo de habitación no especificado.");
}

$tipo = $pdo->prepare("SELECT nombre FROM tipos_habitacion WHERE id = ?");
$tipo->execute([$id_tipo]);
$tipoNombre = $tipo->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $numero = $_POST['numero'] ?? '';
    $capacidad = intval($_POST['capacidad'] ?? 0);

    if ($numero && $capacidad > 0) {
        $stmt = $pdo->prepare("INSERT INTO rooms (numero, capacidad, id_tipo) VALUES (?, ?, ?)");
        $stmt->execute([$numero, $capacidad, $id_tipo]);
        $mensaje = "Habitación registrada exitosamente.";
    } else {
        $error = "Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear Habitación</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    :root {
      --primary-orange: #ff6b35;
      --secondary-orange: #ff8c42;
      --light-orange: #ffa366;
      --primary-yellow: #ffd23f;
      --light-yellow: #ffe066;
      --dark-orange: #e55a2b;
      --gradient-sunset: linear-gradient(135deg, #ff6b35 0%, #ffd23f 100%);
      --gradient-warm: linear-gradient(135deg, #ff8c42 0%, #ffe066 100%);
      --gradient-soft: linear-gradient(135deg, #fff5e6 0%, #fff9e6 50%, #fffacd 100%);
    }

    body {
      background: var(--gradient-soft);
      min-height: 100vh;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .main-header {
      background: var(--gradient-sunset);
      color: white;
      padding: 2rem 0;
      margin-bottom: 3rem;
      border-radius: 0 0 25px 25px;
      box-shadow: 0 6px 25px rgba(255, 107, 53, 0.3);
    }

    .main-header h2 {
      font-weight: 700;
      font-size: 2rem;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
      margin: 0;
    }

    .tipo-badge {
      background: var(--primary-yellow);
      color: var(--dark-orange);
      padding: 0.5rem 1rem;
      border-radius: 25px;
      font-weight: 600;
      display: inline-block;
      margin-top: 0.5rem;
      box-shadow: 0 3px 10px rgba(255, 210, 63, 0.3);
    }

    .form-card {
      background: linear-gradient(145deg, #ffffff 0%, #fffef7 100%);
      border: none;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(255, 107, 53, 0.15);
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .form-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 40px rgba(255, 107, 53, 0.2);
    }

    .form-header {
      background: var(--gradient-warm);
      color: white;
      padding: 1.5rem;
      text-align: center;
    }

    .form-header h4 {
      margin: 0;
      font-weight: 600;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    .form-body {
      padding: 2.5rem;
    }

    .form-label {
      color: var(--dark-orange);
      font-weight: 600;
      font-size: 1.1rem;
      margin-bottom: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .form-control {
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      padding: 1rem;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #fafafa;
    }

    .form-control:focus {
      border-color: var(--primary-orange);
      box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
      background: white;
      transform: scale(1.02);
    }

    .btn-custom-primary {
      background: var(--gradient-sunset);
      border: none;
      color: white;
      font-weight: 600;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
    }

    .btn-custom-primary:hover {
      transform: translateY(-2px) scale(1.05);
      box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4);
      background: var(--gradient-warm);
    }

    .btn-custom-secondary {
      background: transparent;
      border: 2px solid var(--light-orange);
      color: var(--dark-orange);
      font-weight: 600;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-size: 1.1rem;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }

    .btn-custom-secondary:hover {
      background: var(--light-orange);
      color: white;
      transform: translateY(-2px) scale(1.05);
      box-shadow: 0 4px 15px rgba(255, 163, 102, 0.3);
    }

    .alert-custom-success {
      background: linear-gradient(135deg, #d4edda 0%, #e8f5e8 100%);
      border: 2px solid #28a745;
      border-radius: 15px;
      color: #155724;
      padding: 1.2rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
    }

    .alert-custom-danger {
      background: linear-gradient(135deg, #f8d7da 0%, #fde2e4 100%);
      border: 2px solid #dc3545;
      border-radius: 15px;
      color: #721c24;
      padding: 1.2rem;
      margin-bottom: 2rem;
      box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
    }

    .button-group {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-top: 2rem;
    }

    .icon-accent {
      color: var(--primary-yellow);
      text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    }

    .input-icon {
      color: var(--secondary-orange);
    }

    @keyframes slideInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .form-card {
      animation: slideInUp 0.6s ease forwards;
    }

    @media (max-width: 768px) {
      .button-group {
        flex-direction: column;
      }
      
      .main-header h2 {
        font-size: 1.5rem;
      }
      
      .form-body {
        padding: 1.5rem;
      }
    }
  </style>
</head>
<body>
  <div class="main-header">
    <div class="container">
      <h2 class="text-center">
        <i class="fas fa-plus-circle me-3 icon-accent"></i>
        Nueva Habitación
      </h2>
      <div class="text-center">
        <span class="tipo-badge">
          <i class="fas fa-tag me-2"></i>
          <?= htmlspecialchars($tipoNombre) ?>
        </span>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6">
        
        <?php if (!empty($mensaje)): ?>
          <div class="alert alert-custom-success">
            <i class="fas fa-check-circle me-2"></i>
            <?= $mensaje ?>
          </div>
        <?php elseif (!empty($error)): ?>
          <div class="alert alert-custom-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?= $error ?>
          </div>
        <?php endif; ?>

        <div class="card form-card">
          <div class="form-header">
            <h4>
              <i class="fas fa-bed me-2"></i>
              Datos de la Habitación
            </h4>
          </div>
          
          <form method="POST" class="form-body">
            <div class="mb-4">
              <label for="numero" class="form-label">
                <i class="fas fa-hashtag input-icon"></i>
                Número de habitación
              </label>
              <input type="text" 
                     name="numero" 
                     id="numero" 
                     class="form-control" 
                     placeholder="Ej: 101, A-205, Suite 1"
                     required>
            </div>
            
            <div class="mb-4">
              <label for="capacidad" class="form-label">
                <i class="fas fa-users input-icon"></i>
                Capacidad máxima
              </label>
              <input type="number" 
                     name="capacidad" 
                     id="capacidad" 
                     class="form-control" 
                     placeholder="Número de personas"
                     min="1" 
                     max="20"
                     required>
            </div>
            
            <div class="button-group">
              <button type="submit" class="btn btn-custom-primary">
                <i class="fas fa-save me-2"></i>
                Guardar Habitación
              </button>
              <a href="habitaciones.php" class="btn btn-custom-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Volver
              </a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>