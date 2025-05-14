<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once 'components/header.php';
require_once 'components/loader.php';
require_once 'components/footer.php';

$conexion = new mysqli("localhost", "root", "", "rentaveloz");

if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

$reservationId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$userId = $_SESSION['user_id'] ?? 0;

if ($reservationId <= 0 || $userId <= 0) {
  die("ID inválido o sesión expirada");
}

// Obtener reserva
$stmt = $conexion->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $reservationId, $userId);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
  die("Reserva no encontrada o no autorizada");
}
$reservation = $res->fetch_assoc();

// Obtener auto
$stmtCar = $conexion->prepare("SELECT * FROM cars WHERE id = ?");
$stmtCar->bind_param("i", $reservation['car_id']);
$stmtCar->execute();
$carResult = $stmtCar->get_result();
$car = $carResult->fetch_assoc();

$isLogged = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="es">

<head>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <title>RentaVeloz | Editar reserva</title>

  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">

  <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>
  <?php renderLoader(); ?>
  <?php renderHeader('profile', $isLogged); ?>

  <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <div class="cta-content">
            <br>
            <br>
            <h2>Editar <em>Reserva</em></h2>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="container mt-5">
    <div class="row g-4">
      <div class="col-md-8">
        <div class="card p-3">
          <div class="row g-3">
            <div class="col-md-4 text-center">
              <img src="<?= $car['image'] ?>" alt="Auto" class="img-fluid rounded">
            </div>
            <div class="col-md-8">
              <h5><?= $car['title'] ?></h5>
              <p class="text-muted small">o Mini similar</p>
              <div class="d-flex gap-2 mb-3">
                <span><i class="fa fa-users"></i> <?= $car['passengers'] ?></span>
                <span><i class="fa fa-snowflake-o"></i> <?= $car['air_conditioning'] ? '❄️ A/C' : 'Sin A/C' ?></span>
                <span><i class="fa fa-cog"></i> <?= $car['transmission'] ?></span>
              </div>
              <p class="fw-bold">Incluye:</p>
              <ul class="list-unstyled">
                <li>✔ Protección del Vehículo</li>
                <li>✔ Protección Contra Terceros</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <form action="services/reservationsServices.php?action=update&path=profile.php" method="POST" class="card p-3">
          <input type="hidden" name="id" value="<?= $reservation['id'] ?>">
          <input type="hidden" name="status_code" value="<?= $reservation['status_code'] ?>">
          <input type="hidden" name="total_amount" id="inputTotal_amount">

          <h6 class="mb-3">Modificar datos</h6>
          <div class="mb-3">
            <label class="form-label">Fecha de inicio</label>
            <input type="date" name="init_date" class="form-control" value="<?= $reservation['init_date'] ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Fecha de fin</label>
            <input type="date" name="end_date" class="form-control" value="<?= $reservation['end_date'] ?>" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Cantidad de días</label>
            <input type="number" id="days" name="days" class="form-control" value="<?= $reservation['days'] ?>" min="1" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Método de pago</label>
            <select class="form-select" name="payment_method" required>
              <option value="credito" <?= $reservation['payment_method'] === 'credito' ? 'selected' : '' ?>>Crédito</option>
              <option value="debito" <?= $reservation['payment_method'] === 'debito' ? 'selected' : '' ?>>Débito</option>
              <option value="mercado" <?= $reservation['payment_method'] === 'mercado' ? 'selected' : '' ?>>MercadoPago</option>
            </select>
          </div>
          <div class="d-flex justify-content-between fs-5">
            <span><strong>Monto total</strong></span>
            <strong id="totalAmount" name="total_amount"><?= '$' . number_format($reservation['total_amount'], 2, ',', '.') ?></strong>
          </div>
          <button type="submit" class="btn btn-primary w-100">Actualizar Reserva</button>
        </form>
      </div>
    </div>
  </div>

  <?php echo renderFooter(); ?>
</body>

<!-- jQuery -->
<script src="assets/js/jquery-2.1.0.min.js"></script>

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

<!-- Global Init -->
<script src="assets/js/custom.js"></script>
<script>
  const dailyRate = <?= json_encode((int) $car['new_price']) ?>;
  const daysInput = document.getElementById('days');
  const totalAmountEl = document.getElementById('totalAmount');
  const inputTotalAmount = document.getElementById('inputTotal_amount');

  function updateTotal() {
    const days = parseInt(daysInput.value) || 1;
    const totalRaw = dailyRate * days;
    const totalFormatted = totalRaw.toLocaleString('es-AR', {
      style: 'currency',
      currency: 'ARS',
      minimumFractionDigits: 2
    });
    totalAmountEl.textContent = totalFormatted;
    inputTotalAmount.value = totalRaw;
  }

  daysInput.addEventListener('input', updateTotal);
  updateTotal(); // Set initial value
</script>

</html>