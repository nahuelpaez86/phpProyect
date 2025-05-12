<?php 
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  require_once 'components/header.php';
  require_once 'components/loader.php';
  require_once 'components/footer.php';

  /// -------------------- GET
$conexion = new mysqli("localhost", "root", "", "rentaveloz");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$stmt = $conexion->prepare("
    SELECT 
        r.*, 
        c.id AS car_id_alias,
        c.title, 
        c.image, 
        c.new_price AS car_unit_price
    FROM reservations r
    JOIN cars c ON r.car_id = c.id
");

$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
$reservationStatus = '';
// 303 -- confirmada
// 302 -- retirado el auto
// 301 -- terminada la reserva
// 300 -- cancelada
while ($row = $result->fetch_assoc()) {
  
  switch ($row['status_code']) {
    case 303:
        $row['status_text'] = 'Confirmada';
        $row['status_color'] = 'warning';
        break;
    case 302: 
        $row['status_text'] = 'En curso';
        $row['status_color'] = 'primary';
        break;
    case 301: 
        $row['status_text'] = 'Terminada';
        $row['status_color'] = 'success';
        break;
    case 300:
        $row['status_text'] = 'Cancelada';
        $row['status_color'] = 'danger';
        break;
   }

   switch($row['payment_method']) {
    case 'mercado':
      $row['payment_text'] = 'Mercado Pago';
      break;
  
    case 'debito':
        $row['payment_text'] = 'Tarjeta de Debito';
        break;

   case 'credito':
          $row['payment_text'] = 'Tarjeta de Credito';
          break;
   }
  
  $reservations[] = $row;
  
}


$isLogged = isset($_SESSION['user_id']);
/// -------------------- GET
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>RentaVeloz | Admin</title>

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

<body>

  <!-- ***** Preloader Start ***** -->
  
  <!-- ***** Preloader End ***** -->

  <!-- ***** Admin Header Start ***** -->
  <?php renderHeader('reservations',$isLogged); ?>
  <!-- ***** Admin Header End ***** -->
  <div class="py-5"></div>
  <section class="section">
  <div class="container-fluid px-300">
      <h3 class="fw-bold mb-4 mt-4">Reservas Realizadas</h3>
      <div class="table-responsive">
        <table class="table table-bordered align-middle text-center">
          <thead class="table-light">
            <tr>
             <th>Vehículo</th>
              <th>Desde</th>
              <th>Hasta</th>
              <th>Dias</th>
              <th>Precio</th>
              <th>Estado</th>
              <th>Metodo de pago</th>
              <th>Acciones</th>
            </tr>
          </thead>
         
          <tbody>
          <?php if (!empty($reservations)): ?>
              <?php $editingId = $_GET['edit'] ?? null; 
                  foreach ($reservations as $reservation): ?>
                <!-- ***** tabla de edicion ***** -->
              <?php if ($editingId == $reservation['id']): ?>
                <form method="POST" action="services/reservationsServices.php?action=update">
                  <input type="hidden" name="id" value="<?= $reservation['id'] ?>">
                  <tr>
                  
                    <td><?= htmlspecialchars($reservation['title']) ?></td>
                    <td><input type="date" name="init_date" class="form-control" value="<?= $reservation['init_date'] ?>"></td>
                    <td><input type="date" name="end_date" class="form-control" value="<?= $reservation['end_date'] ?>"></td>
                    <td><input type="number" name="days" class="form-control days-input" value="<?= $reservation['days'] ?>" data-price="<?= $reservation['car_unit_price'] ?? 10000 ?>"></td>
                    <td><input type="number" name="total_amount" class="form-control total-amount-input" value="<?= $reservation['total_amount'] ?>"  readonly></td>
                    <td>
                    <select name="status_code" class="form-select">
                        <option value="303" <?= $reservation['status_code'] === 303 ? 'selected' : '' ?>>Confirmada</option>
                        <option value="302" <?= $reservation['payment_method'] === 302 ? 'selected' : '' ?>>En curso</option>
                        <option value="301" <?= $reservation['payment_method'] === 301 ? 'selected' : '' ?>>Terminado</option>
                        <option value="300" <?= $reservation['payment_method'] === 300 ? 'selected' : '' ?>>Cancelada</option>
                    </select>
                  </td>
                    <td>
                      <select name="payment_method" class="form-select">
                        <option value="credito" <?= $reservation['payment_method'] === 'credito' ? 'selected' : '' ?>>Crédito</option>
                        <option value="debito" <?= $reservation['payment_method'] === 'debito' ? 'selected' : '' ?>>Débito</option>
                        <option value="mercado" <?= $reservation['payment_method'] === 'mercado' ? 'selected' : '' ?>>MercadoPago</option>
                      </select>
                    </td>
                    <td>
                      <button type="submit" class="btn btn-sm btn-success">Guardar</button>
                      <a href="admin-dashboard.php" class="btn btn-sm btn-secondary">Cancelar</a>
                    </td>
                  </tr>
                </form>
              <?php else: ?>
                <!-- ***** tabla visual ***** -->
                <tr>
                  <td><?= htmlspecialchars($reservation['title']) ?></td>
                  <td><?= htmlspecialchars($reservation['init_date']) ?></td>
                  <td><?= htmlspecialchars($reservation['end_date']) ?></td>
                  <td><?= htmlspecialchars($reservation['days']) ?></td>
                  <td><?= htmlspecialchars('$' . number_format($reservation['total_amount'], 2, ',', '.')) ?></td>
                  <td>
                    <span class="badge bg-<?= htmlspecialchars($reservation['status_color']) ?>">
                      <?= htmlspecialchars($reservation['status_text']) ?>
                    </span>
                  </td>
                  <td><?= htmlspecialchars($reservation['payment_text']) ?></td>
                  <td>
                    <a href="admin-dashboard.php?edit=<?= $reservation['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                    <a href="services/reservationsServices.php?action=delete&id=<?= $reservation['id'] ?>" class="btn btn-sm btn-outline-danger"
                          onclick="return confirm('¿Estás seguro de que querés eliminar esta reserva?');">Eliminar</a>
                  </td>
                </tr>
              <?php endif; ?>
              <?php endforeach; ?>
           <?php else: ?>
             <p class="text-center">No tenés reservas registradas.</p>
            <?php endif; ?>
          </tbody>
         
        </table>
      </div>
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
              window.location.href = "admin-dashboard.php"; 
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
