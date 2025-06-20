
<?php
require_once '../config.php';

try {
    $stmtTipos = $pdo->query("SELECT * FROM tipos_habitacion");
    $tipos = $stmtTipos->fetchAll();

    $habitacionesPorTipo = [];
    foreach ($tipos as $tipo) {
        $stmtHabs = $pdo->prepare("SELECT * FROM rooms WHERE id_tipo = ?");
        $stmtHabs->execute([$tipo['id']]);
        $habitacionesPorTipo[$tipo['id']] = $stmtHabs->fetchAll();
    }
} catch (Exception $e) {
    die("Error al obtener habitaciones: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Habitaciones</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --orange: #FF6B35;
      --orange-light: #FF8F65;
      --orange-dark: #E55A2B;
      --cream: #FEFCF8;
      --white: #FFFFFF;
      --gray-50: #F8FAFC;
      --gray-100: #F1F5F9;
      --gray-200: #E2E8F0;
      --gray-300: #CBD5E1;
      --gray-600: #475569;
      --gray-700: #334155;
      --gray-900: #0F172A;
      --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
      --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, var(--cream) 0%, var(--gray-50) 100%);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: var(--gray-700);
      line-height: 1.6;
      min-height: 100vh;
    }

    .header {
      background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
      color: white;
      padding: 2.5rem 0;
      margin-bottom: 3rem;
      position: relative;
      overflow: hidden;
    }

    .header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
      opacity: 0.1;
    }

    .header .container {
      position: relative;
      z-index: 1;
    }

    .header h2 {
      margin: 0;
      font-weight: 700;
      font-size: 2rem;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-success {
      background: linear-gradient(135deg, #10B981 0%, #059669 100%);
      border: none;
      color: white;
      font-weight: 600;
      border-radius: 12px;
      padding: 0.75rem 1.5rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: var(--shadow-md);
      position: relative;
      overflow: hidden;
    }

    .btn-success::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .btn-success:hover::before {
      left: 100%;
    }

    .btn-success:hover {
      background: linear-gradient(135deg, #34D399 0%, #10B981 100%);
      color: white;
      transform: translateY(-2px);
      box-shadow: var(--shadow-lg);
    }

    .container {
      max-width: 1200px;
    }

    .card {
      background: var(--white);
      border: 1px solid var(--gray-200);
      border-radius: 20px;
      margin-bottom: 2rem;
      overflow: hidden;
      box-shadow: var(--shadow-md);
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    .card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-xl);
    }

    .card-header {
      background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
      color: white;
      padding: 1.5rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      position: relative;
      border-bottom: none;
    }

    .card-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, var(--orange-light), var(--orange), var(--orange-dark));
    }

    .card-header strong {
      font-weight: 600;
      font-size: 1.25rem;
      margin: 0;
    }

    .card-body {
      padding: 2rem;
      background: linear-gradient(135deg, var(--white) 0%, var(--gray-50) 100%);
      min-height: 120px;
    }

    .btn-sm {
      border-radius: 8px;
      font-weight: 500;
      padding: 0.375rem 0.75rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
      border: none;
      color: white;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, var(--orange-light) 0%, var(--orange) 100%);
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .btn-warning {
      background: linear-gradient(135deg, #F59E0B 0%, #D97706 100%);
      border: none;
      color: white;
    }

    .btn-warning:hover {
      background: linear-gradient(135deg, #FBBF24 0%, #F59E0B 100%);
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .btn-danger {
      background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
      border: none;
      color: white;
    }

    .btn-danger:hover {
      background: linear-gradient(135deg, #F87171 0%, #EF4444 100%);
      transform: translateY(-1px);
      box-shadow: var(--shadow-sm);
    }

    .btn-outline-primary {
      border-color: var(--orange);
      color: var(--orange);
      background: rgba(255, 107, 53, 0.05);
    }

    .btn-outline-primary:hover {
      background: var(--orange);
      border-color: var(--orange);
      color: white;
      transform: translateY(-1px);
    }

    .btn-outline-danger {
      border-color: #dc3545;
      color: #dc3545;
      background: rgba(220, 53, 69, 0.05);
    }

    .btn-outline-danger:hover {
      background: #dc3545;
      border-color: #dc3545;
      color: white;
      transform: translateY(-1px);
    }

    .habitacion-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
      padding: 1rem;
      background: var(--white);
      border: 1px solid var(--gray-200);
      border-radius: 12px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .habitacion-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, var(--orange-light), var(--orange));
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .habitacion-item:hover::before {
      transform: scaleX(1);
    }

    .habitacion-item:hover {
      transform: translateY(-2px);
      box-shadow: var(--shadow-md);
      border-color: var(--orange-light);
    }

    .habitacion-item:last-child {
      margin-bottom: 0;
    }

    .habitacion-info {
      display: flex;
      align-items: center;
      gap: 1rem;
    }

    .habitacion-info span {
      font-weight: 500;
      color: var(--gray-900);
    }

    .habitacion-info .badge {
      background: linear-gradient(135deg, var(--orange-light) 0%, var(--orange) 100%);
      color: white;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 500;
    }

    .habitacion-actions {
      display: flex;
      gap: 0.5rem;
    }

    .modal-content {
      border: none;
      border-radius: 20px;
      box-shadow: var(--shadow-xl);
    }

    .modal-header {
      background: linear-gradient(135deg, var(--orange) 0%, var(--orange-dark) 100%);
      color: white;
      border-radius: 20px 20px 0 0;
      border-bottom: none;
    }

    .modal-title {
      font-weight: 600;
    }

    .btn-close {
      filter: brightness(0) invert(1);
      opacity: 0.8;
    }

    .btn-close:hover {
      opacity: 1;
    }

    .modal-body {
      padding: 2rem;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid var(--gray-300);
      padding: 0.75rem 1rem;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: var(--orange);
      box-shadow: 0 0 0 0.2rem rgba(255, 107, 53, 0.25);
    }

    .form-label {
      font-weight: 500;
      color: var(--gray-700);
      margin-bottom: 0.5rem;
    }

    .empty-state {
      text-align: center;
      padding: 3rem 0;
      color: var(--gray-600);
    }

    .empty-state i {
      color: var(--gray-300);
      margin-bottom: 1rem;
    }

    @media (max-width: 768px) {
      .header {
        padding: 2rem 0;
      }
      
      .header h2 {
        font-size: 1.5rem;
      }
      
      .header .d-flex {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
      }
      
      .card-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
      }
      
      .habitacion-item {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
      }
      
      .habitacion-actions {
        justify-content: center;
      }
    }

    /* Animaciones de entrada */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .card {
      animation: fadeInUp 0.6s ease-out forwards;
    }

    .card:nth-child(2) { animation-delay: 0.1s; }
    .card:nth-child(3) { animation-delay: 0.2s; }
    .card:nth-child(4) { animation-delay: 0.3s; }

    .habitacion-item {
      animation: fadeInUp 0.4s ease-out forwards;
    }

    .habitacion-item:nth-child(2) { animation-delay: 0.05s; }
    .habitacion-item:nth-child(3) { animation-delay: 0.1s; }
    .habitacion-item:nth-child(4) { animation-delay: 0.15s; }
    .habitacion-item:nth-child(5) { animation-delay: 0.2s; }
  </style>
</head>
<body>

<div class="header">
  <div class="container">
    <div class="d-flex justify-content-between align-items-center">
      <h2><i class="fas fa-bed me-2"></i>Habitaciones</h2>
      <a href="crear_tipo_habitacion.php" class="btn btn-success">
        <i class="fas fa-plus me-2"></i>Nuevo Tipo
      </a>
    </div>
  </div>
</div>

<div class="container">
  <?php foreach ($tipos as $tipo): ?>
    <div class="card mb-4" id="tipo_<?= $tipo['id'] ?>">
      <div class="card-header d-flex justify-content-between align-items-center">
        <strong><i class="fas fa-door-open me-2"></i><?= htmlspecialchars($tipo['nombre']) ?></strong>
        <div class="d-flex gap-2">
          <a href="crear_habitacion.php?id_tipo=<?= $tipo['id'] ?>" class="btn btn-sm btn-primary">
            <i class="fas fa-plus me-1"></i>Añadir
          </a>
          <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEditarTipo" data-id="<?= $tipo['id'] ?>" data-nombre="<?= htmlspecialchars($tipo['nombre']) ?>">
            <i class="fas fa-edit me-1"></i>Editar
          </button>
          <form class="d-inline form-eliminar-tipo" data-id="<?= $tipo['id'] ?>">
            <input type="hidden" name="id" value="<?= $tipo['id'] ?>">
            <button class="btn btn-sm btn-danger" type="submit">
              <i class="fas fa-trash me-1"></i>Eliminar
            </button>
          </form>
          <!-- 🔽 Botón nuevo para redirigir a tarifas -->
          <a href="tarifas.php?id=<?= $tipo['id'] ?>" class="btn btn-sm btn-info">
    <i class="fas fa-dollar-sign me-1"></i>Tarifas
    </a>
        </div>
      </div>

      <div class="card-body">
        <?php if (empty($habitacionesPorTipo[$tipo['id']])): ?>
          <div class="empty-state">
            <i class="fas fa-bed fa-3x"></i>
            <p class="mb-0">No hay habitaciones de este tipo</p>
          </div>
        <?php else: ?>
          <?php foreach ($habitacionesPorTipo[$tipo['id']] as $hab): ?>
            <div class="habitacion-item" id="hab_<?= $hab['id'] ?>">
              <div class="habitacion-info">
                <span><i class="fas fa-door-closed me-2 text-muted"></i>Habitación <?= htmlspecialchars($hab['numero']) ?></span>
                <span class="badge">
                  <i class="fas fa-users me-1"></i>Capacidad: <?= htmlspecialchars($hab['capacidad']) ?>
                </span>
              </div>
              <div class="habitacion-actions">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarHabitacion" data-id="<?= $hab['id'] ?>" data-numero="<?= htmlspecialchars($hab['numero']) ?>" data-capacidad="<?= htmlspecialchars($hab['capacidad']) ?>">
                  <i class="fas fa-edit me-1"></i>Editar
                </button>
                <form class="d-inline form-eliminar-hab" data-id="<?= $hab['id'] ?>">
                  <input type="hidden" name="id" value="<?= $hab['id'] ?>">
                  <button class="btn btn-sm btn-outline-danger" type="submit">
                    <i class="fas fa-trash me-1"></i>Eliminar
                  </button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<!-- Modal Editar Habitación -->
<div class="modal fade" id="modalEditarHabitacion" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="formEditarHabitacion">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Habitación</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="editHabId">
        <div class="mb-3">
          <label class="form-label"><i class="fas fa-door-closed me-2"></i>Número</label>
          <input type="text" name="numero" id="editHabNumero" class="form-control">
        </div>
        <div class="mb-3">
          <label class="form-label"><i class="fas fa-users me-2"></i>Capacidad</label>
          <input type="number" name="capacidad" id="editHabCapacidad" class="form-control">
        </div>
        <div class="mb-3">
  <label class="form-label"><i class="fas fa-check-circle me-2"></i>Disponibilidad</label>
  <select name="status" id="editHabStatus" class="form-control" required>
    <option value="Disponible">🟢 Disponible</option>
    <option value="Ocupado">🔴 Ocupado</option>
    <option value="Mantenimiento">🔵 Mantenimiento</option>
  </select>
</div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-save me-2"></i>Guardar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Editar Tipo -->
<div class="modal fade" id="modalEditarTipo" tabindex="-1">
  <div class="modal-dialog">
    <form class="modal-content" id="formEditarTipo">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Editar Tipo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id" id="editTipoId">
        <div class="mb-3">
          <label class="form-label"><i class="fas fa-tag me-2"></i>Nombre</label>
          <input type="text" name="nombre" id="editTipoNombre" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">
          <i class="fas fa-save me-2"></i>Guardar
        </button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  // Eliminar habitaciones
  document.querySelectorAll('.form-eliminar-hab').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      if (!confirm('¿Eliminar habitación?')) return;
      const id = form.getAttribute('data-id');
      const formData = new FormData(form);
      fetch('eliminar_habitacion.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(resp => {
        if (resp.success) {
          const card = document.getElementById(`hab_${id}`);
          if (card) {
            card.style.transition = 'opacity 0.5s ease';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 500);
          }
        } else {
          alert(resp.message || 'Error al eliminar');
        }
      });
    });
  });

  // Eliminar tipos de habitación
  document.querySelectorAll('.form-eliminar-tipo').forEach(form => {
    form.addEventListener('submit', e => {
      e.preventDefault();
      if (!confirm('¿Eliminar tipo de habitación y todas sus habitaciones?')) return;
      const id = form.getAttribute('data-id');
      const formData = new FormData(form);
      fetch('eliminar_tipo.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(resp => {
        if (resp.success) {
          const card = document.getElementById(`tipo_${id}`);
          if (card) {
            card.style.transition = 'opacity 0.5s ease';
            card.style.opacity = '0';
            setTimeout(() => card.remove(), 500);
          }
        } else {
          alert(resp.message || 'Error al eliminar tipo');
        }
      });
    });
  });
});

document.addEventListener('DOMContentLoaded', () => {
  // Cargar datos en modal de habitación
  const modalHab = document.getElementById('modalEditarHabitacion');
  modalHab.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;
    document.getElementById('editHabId').value = btn.dataset.id;
    document.getElementById('editHabNumero').value = btn.dataset.numero;
    document.getElementById('editHabCapacidad').value = btn.dataset.capacidad;
     document.getElementById('editHabStatus').value = habitacion.status;
  });

  // Guardar cambios habitación
  document.getElementById('formEditarHabitacion').addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch('actualizar_habitacion.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(resp => {
      if (resp.success) {
        location.reload(); // puedes mejorar esto para hacer update dinámico si gustas
      } else {
        alert(resp.message || 'Error al actualizar');
      }
    });
  });

  // Cargar datos en modal de tipo
  const modalTipo = document.getElementById('modalEditarTipo');
  modalTipo.addEventListener('show.bs.modal', event => {
    const btn = event.relatedTarget;
    document.getElementById('editTipoId').value = btn.dataset.id;
    document.getElementById('editTipoNombre').value = btn.dataset.nombre;
  });

  // Guardar cambios tipo
  document.getElementById('formEditarTipo').addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch('actualizar_tipo.php', {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(resp => {
      if (resp.success) {
        location.reload(); // puedes actualizar solo el DOM si prefieres
      } else {
        alert(resp.message || 'Error al actualizar tipo');
      }
    });
  });
});
</script>
<script>
  const rooms = <?= json_encode(array_map(function ($tipo) {
      return [
          'id' => $tipo['id'],
          'title' => $tipo['nombre']
      ];
  }, $tipos)); ?>;
</script>

</body>
</html>