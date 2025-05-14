<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once 'components/header.php';
require_once 'components/loader.php';
require_once 'components/footer.php';
require_once 'services/carsServices.php';

$cars = getAllCars();
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
  <?php renderHeader('cars', $isLogged); ?>
  <!-- ***** Admin Header End ***** -->
  <div class="py-5"></div>
  <section class="section mt-3">
    <div class="container-fluid px-3">
      <div class="container-fluid px-3">
        <a href="admin-cars-add.php" class="btn btn-sm btn-primary">Agregar auto</a>
        <!-- … -->
      </div>
      <div class="table-responsive">

        <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
              <th>Imagen</th>
              <th>Precio Ant.</th>
              <th>Precio Nuevo</th>
              <th>Modelo</th>
              <th>Km</th>
              <th>Motor</th>
              <th>Transmisión</th>
              <th>Pasajeros</th>
              <th>A/C</th>
              <th>Acciones</th>
            </tr>
          </thead>

          <!-- ***** tablas ***** -->
          <tbody>
            <?php if (!empty($cars)): ?>
              <?php $editingId = $_GET['edit'] ?? null;
              foreach ($cars  as $car): ?>
                <!-- ***** tabla de edicion ***** -->
                <?php if ($editingId == $car['id']): ?>
                  <form method="POST" action="services/carsServices.php?action=update&id=<?= $car['id'] ?>&path=admin-cars.php" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $car['id'] ?>">
                    <tr>
                      <td>
                        <div>
                          <div class="mb-2">
                            <input type="hidden" name="current_image" value="<?= htmlspecialchars($car['image']) ?>">
                            <img src="<?= htmlspecialchars($car['image']) ?>" alt="Auto actual" width="150" height="100" style="object-fit: cover; border: 1px solid #ccc;">
                            <input type="file" name="image" class="form-control" accept="image/*">
                          </div>
                        </div>
                      </td>
                      <td> <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($car['title']) ?>"></td>
                      <td> <input type="text" name="km" class="form-control" value="<?= htmlspecialchars($car['km']) ?>" readonly></td>
                      <td><input type="text" name="old_price" class="form-control" value="<?= htmlspecialchars($car['old_price']) ?>"></td>
                      <td><input type="text" name="new_price" class="form-control" value="<?= htmlspecialchars($car['new_price']) ?>"></td>
                      <td><input type="text" name="engine" class="form-control" value="<?= htmlspecialchars($car['engine']) ?>" readonly></td>
                      <td><input type="text" name="transmission" class="form-control" value="<?= htmlspecialchars($car['transmission']) ?>" readonly></td>
                      <td><input type="number" name="passengers" class="form-control" value="<?= htmlspecialchars($car['passengers']) ?>" readonly></td>
                      <td class="text-center">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="acSwitch<?= $car['id'] ?>" name="air_conditioning" value="1" <?= $car['air_conditioning'] ? 'checked' : '' ?>>
                          <label class="form-check-label" for="acSwitch<?= $car['id'] ?>"></label>
                        </div>
                      </td>
                      <td>
                        <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                        <a href="admin-cars.php" class="btn btn-sm btn-secondary">Cancelar</a>
                      </td>
                    </tr>
                  </form>
                <?php else: ?>
                  <!-- ***** tabla visual ***** -->
                  <tr>
                    <td><img height='100' width='150' src="<?= htmlspecialchars($car['image']) ?>" /></td>
                    <td><?= number_format((float)$car['old_price'], 2, ',', '.') ?></td>
                    <td><?= number_format((float)$car['new_price'], 2, ',', '.') ?></td>
                    <td> <?= htmlspecialchars($car['title']) ?></td>
                    <td><?= htmlspecialchars($car['km']) ?></td>
                    <td><?= htmlspecialchars($car['engine']) ?></td>
                    <td><?= htmlspecialchars($car['transmission']) ?></td>
                    <td><?= htmlspecialchars($car['passengers']) ?></td>
                    <td><?= $car['air_conditioning'] ? 'Sí' : 'No' ?> </td>
                    <td>
                      <a href="admin-cars.php?edit=<?= $car['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                      <a href="services/carsServices.php?action=delete&id=<?= $car['id'] ?>&path=admin-cars.php" class="btn btn-sm btn-outline-danger"
                        onclick="return confirm('¿Estás seguro de que querés eliminar este usuario?');">Eliminar</a>
                    </td>
                  </tr>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="text-center">No tenés autos cargados.</p>
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
            window.location.href = "admin-cars.php";
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
      input.addEventListener('input', function() {
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