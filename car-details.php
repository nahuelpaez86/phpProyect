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

// Validar y obtener el ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    die("ID inválido");
}

// Consulta preparada para evitar inyecciones SQL
$stmt = $conexion->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Auto no encontrado");
}

$car = $result->fetch_assoc();
$isLogged = isset($_SESSION['user_id']);

function reloadPage() 
{
  global $car;
  header("Location: car-details.php?id=" . $car['id']);
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">

  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>PHPJabbers.com | Free Car Dealer Website Template</title>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">

    <link rel="stylesheet" href="assets/css/style.css">

    </head>
    
    <body>
    
     <!-- ***** Preloader Start ***** -->
     <?php renderLoader(); ?>
    <!-- ***** Preloader End ***** -->

    <!-- ***** Header Area Start ***** -->
    <?php renderHeader('cars',$isLogged); ?>
    <!-- ***** Header Area End ***** -->
    
    <!-- ***** Call to Action Start ***** -->
    <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="cta-content">
                        <br>
                        <br>
                        <h2><small><del><?php echo '$' . number_format($car['old_price'], 2, ',', '.'); ?></del></small> <em><?php echo '$' . number_format($car['new_price'], 2, ',', '.'); ?></em></h2>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** Call to Action End ***** -->

    <!-- ***** Fleet Starts ***** -->
    <section class="section" id="trainers">
    <div class="container mt-5">
     <div class="row g-4">
    <!-- Info del auto -->
    <div class="col-md-8">
      <div class="card p-3">
        <div class="row g-3">
          <div class="col-md-4 text-center">
            <img src= <?php echo $car['image']; ?> alt="Auto" class="img-fluid rounded">
          </div>
          <div class="col-md-8">
            <h5 class="mb-1"> <?php echo $car['title']; ?></h5>
            <p class="text-muted small">o Mini similar</p>

            <div class="d-flex gap-2 mb-3">
              <span><i class="fa fa-users"></i> <?php echo $car['passengers']; ?></span>
              <span><i class="fa fa-snowflake-o"></i>  <?php echo$car['air_conditioning'] ? '❄️ A/C' : 'Sin A/C' ?></span>
              <span><i class="fa fa-cog"></i><?= $car['transmission'] ?></span>
            </div>

            <p class="fw-bold">Esta reserva incluye:</p>
            <ul class="list-unstyled">
              <li>✔ Protección del Vehículo</li>
              <li>✔ Protección Contra Terceros</li>
            </ul>

          </div>
        </div>
      </div>
    </div>

        <!-- RESERVA PASO 1 -->
         <!-- PASO 1 Y 2 -->
      <div class="col-md-4">
         <!-- PASO 1 -->
           <!-- modales de error o exito. -->
         <?php if (isset($_GET['post_success']) || isset($_GET['post_error'])): ?>
          <?php if (isset($_GET['post_success'])): ?>
              <div class="container mt-4">
                  <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                      <?= htmlspecialchars($_GET['post_success']) ?>
                      <a href="car-details.php?id=<?= $car['id'] ?>" class="btn-close" role="button"></a>
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
            
         <?php else: ?>
          <div class="card p-3" id="reserva-paso-1">
            <h6>Tu reserva</h6>
            <div class="mb-3">
              <label for="rentalDays" class="form-label">Cantidad de días</label>
              <input type="number" class="form-control" id="rentalDays" value="1" min="1">
            </div>
            <div class="d-flex justify-content-between">
              <span>Valor del vehículo</span>
              <strong id="vehicleValue"><?= '$' . number_format($car['new_price'], 2, ',', '.') ?></strong>
            </div>
            <hr>
            <p class="fw-bold mb-1">Plan Flex</p>
            <ul class="list-unstyled small">
              <li>✔ Protección del Vehículo</li>
              <li>✔ Protección Contra Terceros</li>
            </ul>
            <hr>
            <div class="d-flex justify-content-between fs-5">
              <span><strong>Monto total</strong></span>
              <strong id="totalAmount" name = "total_amount"><?= '$' . number_format($car['new_price'], 2, ',', '.') ?></strong>
            </div>
            <button onclick="showStep2()" class="btn btn-success w-100 mt-2">Reservar</button>
          </div>
          <?php endif; ?>
          <!-- PASO 2 -->
          <form action="services/reservationsServices.php?action=create" method="POST" id="reserva-paso-2" class="card p-3" style="display: none;">
            <h6 class="mb-3">Finalizar reserva</h6>
            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
            <input type="hidden" name="days" id="inputDays">
            <input type="hidden" name="car_id" value="<?= $car['id'] ?>">
            <input type="hidden" name="total_amount" id="inputTotal_amount">
            <div class="mb-3">
              <label for="init_date" class="form-label">Fecha de inicio</label>
              <input type="date" class="form-control" name="init_date" required>
            </div>
            <div class="mb-3">
              <label for="end_date" class="form-label">Fecha de fin</label>
              <input type="date" class="form-control" name="end_date" required>
            </div>
            <div class="mb-3">
              <label for="payment_method" class="form-label">Método de pago</label>
              <select class="form-select" name="payment_method" required>
                <option value="credito">Tarjeta de crédito</option>
                <option value="debito">Tarjeta de débito</option>
                <option value="mercado">MercadoPago</option>
                </select>
              </div>
                <button type="submit" class="btn btn-primary w-100">Confirmar Reserva</button>
              </form>
              </div>
              </div>
        </div>

  </div>
</div>

    </section>
    <!-- ***** Fleet Ends ***** -->
    
    <!-- ***** Footer Start ***** -->
    <?php echo renderFooter(); ?>


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
    <!-- JavaScript para actualizar el monto total -->
    <script>
        const dailyRate = <?= json_encode($car['new_price']) ?>;
        const rentalDaysInput = document.getElementById('rentalDays');
        const totalAmountElLabel = document.getElementById('totalAmount');
        let totalAmount = dailyRate

        rentalDaysInput.addEventListener('input', () => {
          const days = parseInt(rentalDaysInput.value) || 1;
          const total = (dailyRate * days).toLocaleString('es-AR', {
            style: 'currency',
            currency: 'ARS',
            minimumFractionDigits: 2
          });
          totalAmountElLabel.textContent = total;
          totalAmount = dailyRate * days;
        });

        function showStep2() {
          const days = document.getElementById('rentalDays').value;
          document.getElementById('inputDays').value = days;
          document.getElementById('inputTotal_amount').value = totalAmount;
          document.getElementById('reserva-paso-1').style.display = 'none';
          document.getElementById('reserva-paso-2').style.display = 'block';
        }
       
      </script>
    <!-- Global Init -->
    <script src="assets/js/custom.js"></script>

  </body>
</html>