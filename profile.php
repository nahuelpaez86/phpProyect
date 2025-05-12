<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once 'components/header.php';
require_once 'components/loader.php';
require_once 'components/footer.php';
require_once 'components/alert.php';

$user = [
  'name' => $_SESSION['user_name'],
  'email' => $_SESSION['user_email'],
  'phone' => '+54 9 11 1234-5678',
];

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
        c.new_price 
    FROM reservations r
    JOIN cars c ON r.car_id = c.id
    WHERE r.user_id = ?
");
$stmt->bind_param("i", $_SESSION['user_id']);
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
  
  $reservations[] = $row;
  
}

/// -------------------- GET

$isLogged = isset($_SESSION['user_id']);
$name = $_SESSION['user_name'];
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Mi Cuenta | RENTAVELOZ</title>
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .perfil-wrapper {
      background-color: #f8f9fa;
      padding: 4rem 1rem;
    }
    .perfil-container {
      max-width: 850px;
      background: #fff;
      padding: 2rem 2.5rem;
      margin: 0 auto;
      border-radius: 15px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }
    .perfil-container h3 {
      font-weight: bold;
      color: #2d2f33;
      margin-bottom: 1.5rem;
    }
    .perfil-info p {
      margin: 0.25rem 0;
    }
    .table th, .table td {
      vertical-align: middle;
    }
  </style>
</head>

<body>
  <?php renderLoader(); ?>

  <?php renderHeader('profile',$isLogged); ?>

  <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <div class="cta-content">
            <br><br>
            <h2>Mi <em>Perfil</em></h2>
            <p>Información personal y tus reservas activas</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="perfil-wrapper">
    <div class="perfil-container">
      <h3>Mi cuenta:</h3>
      <div class="perfil-info">
        <p><strong>Nombre:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($user['phone']) ?></p>
      </div>

      <hr>

      <h3>Reservas Realizadas</h3>
      <div class="table-responsive">
      <?php if (!empty($reservations)): ?>
        <table class="table table-bordered table-hover">
          <thead class="table-light">
            <tr>
              <th>Vehículo</th>
              <th>Desde</th>
              <th>Hasta</th>
              <th>Dias</th>
              <th>Precio</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
        
          <tbody>
          <?php foreach ($reservations as $reservation): ?>
            
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
                <td>
                  <?php if ($reservation['status_code'] === 303): ?>
                    <a href="editReservation.php?id=<?= $reservation['id'] ?>" class="btn btn-sm btn-outline-primary me-1">Editar</a>
                    <a href="services/cancelReservation.php?id=<?= $reservation['id'] ?>" class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('¿Estás seguro de que querés cancelar esta reserva?');">Cancelar</a>
                    <?php else: ?>
                      <span class="text-muted">No disponible</span>
                    <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <?php else: ?>
            <p class="text-center">No tenés reservas registradas.</p>
          <?php endif; ?>
        </table>
        <?php if (isset($_GET['post_success']) || isset($_GET['post_error'])): ?>
          <?php if (isset($_GET['post_success'])): ?>
            <?php renderAlert('success', htmlspecialchars($_GET['post_success'])); ?>
          <?php elseif (isset($_GET['post_error'])): ?>
            <?php renderAlert('danger', htmlspecialchars($_GET['post_error'])); ?>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>

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

<!-- Global Init -->
<script src="assets/js/custom.js"></script>
</body>
</html>