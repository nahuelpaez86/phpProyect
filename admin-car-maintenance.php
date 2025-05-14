<?php 
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  require_once 'components/header.php';
  require_once 'components/loader.php';
  require_once 'components/footer.php';
  require_once 'services/maintenanceServices.php';
  
  $maintenances = getAllMantenances();
  $isLogged = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RentaVeloz | Autos Admin</title>

  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .badge-confirmada {
      background-color: #22c55e;
    }
    .badge-cancelada {
      background-color: #ef4444;
    }
    .badge-completada {
      background-color: #6b7280;
    }
  </style>
</head>

<body class="pt-1">

  <!-- ***** Admin Header Start ***** -->
  <?php renderHeader('maintenance',$isLogged); ?>
  <!-- ***** Admin Header End ***** -->
  <div class="py-5"></div>
  <section class="section mt-3">
  <div class="container-fluid px-3">
     <div class="container-fluid px-3">
            <a href="admin-car-maintenance-add.php" class="btn btn-sm btn-primary">Agregar mantenimiento</a>
            <!-- … -->
        </div>
      <div class="table-responsive">
      
      <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>Auto</th>
              <th>Tipo de Servicio</th>
              <th>Inicio</th>
              <th>Fin</th>
              <th>Descripción</th>
              <th>Costo</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($maintenances)) : ?>
              <?php $editingId = $_GET['edit'] ?? null; ?>
              <?php foreach ($maintenances as $m) : ?>
                <?php if ($editingId == $m['id']) : ?>
                  <!-- Formulario de edición -->
                  <form method="POST" action="services/maintenanceServices.php?action=update&id=<?= $m['id'] ?>&path=admin-car-maintenance.php">
                    <input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <input type="hidden" name="car_id" value="<?= $m['car_id'] ?>">
                    <tr>
                      <td><?= htmlspecialchars($m['car_title']) ?></td>
                      <td><input type="text" name="service_type" class="form-control" value="<?= htmlspecialchars($m['service_type']) ?>" required></td>
                      <td><input type="date" name="start_date" class="form-control" value="<?= $m['start_date'] ?>"></td>
                      <td><input type="date" name="end_date" class="form-control" value="<?= $m['end_date'] ?>"></td>
                      <td><textarea name="description" class="form-control"><?= htmlspecialchars($m['description']) ?></textarea></td>
                      <td><input type="number" name="cost" step="0.01" class="form-control" value="<?= htmlspecialchars($m['cost']) ?>"></td>
                      <td>
                        <select name="status" class="form-select">
                          <option value="pending" <?= $m['status'] === 'pending' ? 'selected' : '' ?>>Pendiente</option>
                          <option value="in_progress" <?= $m['status'] === 'in_progress' ? 'selected' : '' ?>>En progreso</option>
                          <option value="completed" <?= $m['status'] === 'completed' ? 'selected' : '' ?>>Completado</option>
                        </select>
                      </td>
                      <td>
                        <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                        <a href="admin-maintenance.php" class="btn btn-sm btn-secondary">Cancelar</a>
                      </td>
                    </tr>
                  </form>
                <?php else : ?>
                  <!-- Visualización -->
                  <tr>
                    <td><?= htmlspecialchars($m['car_title']) ?></td>
                    <td><?= htmlspecialchars($m['service_type']) ?></td>
                    <td><?= htmlspecialchars($m['start_date']) ?></td>
                    <td><?= $m['end_date'] ? htmlspecialchars($m['end_date']) : '—' ?></td>
                    <td><?= nl2br(htmlspecialchars($m['description'])) ?></td>
                    <td>$<?= number_format((float)$m['cost'], 2, ',', '.') ?></td>
                    <td>
                      <?php
                      switch ($m['status']) {
                        case 'pending': echo "<span class='badge bg-warning'>Pendiente</span>"; break;
                        case 'in_progress': echo "<span class='badge bg-primary'>En progreso</span>"; break;
                        case 'completed': echo "<span class='badge bg-success'>Completado</span>"; break;
                      }
                      ?>
                    </td>
                    <td>
                      <a href="admin-car-maintenance.php?edit=<?= $m['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                      <a href="services/maintenanceServices.php?action=delete&id=<?= $m['id'] ?>&path=admin-car-maintenance.php"
                        class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('¿Estás seguro de que querés eliminar este mantenimiento?');">Eliminar</a>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php else : ?>
              <tr><td colspan="8" class="text-center">No hay mantenimientos registrados.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <!-- ***** alerts popup ***** -->
      <?php if (isset($_GET['post_success']) || isset($_GET['post_error'])): ?>
          <?php if (isset($_GET['post_success'])): ?>
              <div class="container mt-4">
                  <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                      <?= htmlspecialchars($_GET['post_success']) ?>
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                  </div>
              </div>
          <?php elseif (isset($_GET['post_error'])): ?>
              <div class="container mt-4">
                  <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                      <?= htmlspecialchars($_GET['post_error']) ?>
                      <a href="car-details.php?id=<?= $car['id'] ?>" class="btn-close" role="button"></a>
                  </div>
              </div>
          <?php endif; ?>
          <script>
            setTimeout(() => {
              window.location.href = "admin-car-maintenance.php"; 
            }, 3000); 
          </script>
        <?php endif; ?>
    </div>
  </section>

  <!-- ***** Footer Start ***** -->
  <?php echo renderFooter(); ?>

  <script src="assets/js/jquery-2.1.0.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <!-- Bootstrap -->
  <script src="assets/js/popper.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <!-- Plugins -->
  <script src="assets/js/scrollreveal.min.js"></script>
  <script src="assets/js/waypoints.min.js"></script>
  <script src="assets/js/jquery.counterup.min.js"></script>
  <script src="assets/js/imgfix.min.js"></script> 
  <script src="assets/js/mixitup.js"></script> 
  <script src="assets/js/accordions.js"></script>
  <script>
    document.querySelectorAll('.days-input').forEach((input) => {
      input.addEventListener('input', function () {
        const days = parseInt(this.value) || 0;
        const price = parseFloat(this.dataset.price) || 0;
        const total = days * price;

        const totalInput = this.closest('tr').querySelector('.total-amount-input');
        if (totalInput) {
          totalInput.value = total;
        }
      });
     });
  </script>
</body>

</html>