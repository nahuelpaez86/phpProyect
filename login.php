<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
require_once 'components/header.php';
require_once 'components/loader.php';
require_once 'components/footer.php';
require_once 'components/alert.php';

// Variables para mensajes
$successMessage = '';
$errorMessage = '';

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "rentaveloz");
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');

  // Consulta el usuario por email
  $stmt = $conexion->prepare("SELECT * FROM system_user WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $resultado = $stmt->get_result();
  $usuario = $resultado->fetch_assoc();

  if ($usuario && $password === $usuario['password']) {
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['user_name'] = $usuario['name'];
    $_SESSION['user_email'] = $usuario['email'];
    $_SESSION['user_role'] = $usuario['role'];
    if ($_SESSION['user_role'] == 'admin') {
      header("Location: admin-dashboard.php");
    } else {
      header("Location: index.php");
    }

    exit();
  } else {
    $errorMessage = 'Correo o contraseña incorrectos.';
  }
}
$isLogged = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="description" content="">
  <meta name="author" content="">
  <link href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap" rel="stylesheet">
  <title>Iniciar Sesión | RENTAVELOZ</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .login-container {
      max-width: 400px;
      margin: 100px auto;
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .login-container h3 {
      font-weight: 700;
      text-align: center;
      margin-bottom: 1.5rem;
      color: #2d2f33;
    }

    .btn-login {
      background-color: #ff4d30;
      color: white;
      font-weight: 600;
      width: 100%;
    }

    .btn-login:hover {
      background-color: #e04329;
    }

    .form-control:focus {
      border-color: #ff4d30;
      box-shadow: 0 0 0 0.2rem rgba(255, 77, 48, 0.25);
    }
  </style>
</head>

<body>
  <?php renderLoader(); ?>
  <?php renderHeader('login', $isLogged); ?>

  <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <div class="cta-content">
            <br><br>
            <h2>Accede a tu <em>cuenta</em></h2>
            <p>Inicia sesión para gestionar tus reservas y ver tus autos favoritos.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <div class="login-container">
    <h3>Iniciar Sesión</h3>
    <form method="POST">
      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="usuario@ejemplo.com" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" class="btn btn-login">Ingresar</button>
    </form>
    <div class="container mt-4">
      <?php renderAlert('danger', $errorMessage); ?>
      <?php renderAlert('success', $successMessage); ?>
    </div>
  </div>

  <?php echo renderFooter(); ?>

  <script src="assets/js/jquery-2.1.0.min.js"></script>
  <script src="assets/js/popper.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
  <script src="assets/js/scrollreveal.min.js"></script>
  <script src="assets/js/waypoints.min.js"></script>
  <script src="assets/js/jquery.counterup.min.js"></script>
  <script src="assets/js/imgfix.min.js"></script>
  <script src="assets/js/mixitup.js"></script>
  <script src="assets/js/accordions.js"></script>
  <script src="assets/js/custom.js"></script>
</body>

</html>