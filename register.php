<?php
require_once 'components/header.php';
require_once 'components/loader.php';
require_once 'components/footer.php';
require_once 'components/alert.php';
// Variables para mensajes
$successMessage = '';
$errorMessage = '';

$conexion = new mysqli("localhost", "root", "", "rentaveloz");
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $confirmPassword = trim($_POST['confirmPassword'] ?? '');

  if (empty($name) || empty($email) || empty($password) || $password !== $confirmPassword) {
    $errorMessage = '⚠️ Las contraseñas no coinciden o hay campos incompletos.';
  } else {
    // Encriptar la contraseña
    //$passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'client';

    // Verificamos si el email ya existe
    $check = $conexion->prepare("SELECT id FROM system_user WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $errorMessage = '❌ El correo ya está registrado.';
    } else {
      // Insertar nuevo usuario
      $stmt = $conexion->prepare("INSERT INTO system_user (name, email, password, role) VALUES (?, ?, ?, ?)");
      $stmt->bind_param("ssss", $name, $email, $password, $role);

      if ($stmt->execute()) {
        $successMessage = '✅ Registro completado correctamente.';
      } else {
        $errorMessage = '❌ Error al registrar el usuario: ' . $stmt->error;
      }
    }
  }
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
  <title>Registrarse | RENTAVELOZ</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    .register-container {
      max-width: 500px;
      margin: 100px auto;
      background: #fff;
      padding: 2rem;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
    }

    .register-container h3 {
      font-weight: 700;
      text-align: center;
      margin-bottom: 1.5rem;
      color: #2d2f33;
    }

    .btn-register {
      background-color: #ff4d30;
      color: white;
      font-weight: 600;
      width: 100%;
    }

    .btn-register:hover {
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
  <?php renderHeader('register', false); ?>

  <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
    <div class="container">
      <div class="row">
        <div class="col-lg-10 offset-lg-1">
          <div class="cta-content">
            <br><br>
            <h2>Crea tu <em>cuenta</em></h2>
            <p>Registrate para acceder a nuestras ofertas y gestionar tus reservas fácilmente.</p>
          </div>
        </div>
      </div>
    </div>
  </section>



  <div class="register-container">
    <h3>Registrarse</h3>
    <form method="POST">
      <div class="mb-3">
        <label for="name" class="form-label">Nombre completo</label>
        <input type="text" class="form-control" id="name" name="name" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Contraseña</label>
        <input type="password" class="form-control" id="password" name="password" required>
      </div>
      <div class="mb-3">
        <label for="confirmPassword" class="form-label">Confirmar contraseña</label>
        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
      </div>
      <button type="submit" class="btn btn-register">Registrarme</button>
    </form>
    <div class="container mt-4">
      <?php if (!empty($errorMessage)) : ?>
        <?php renderAlert('danger', $errorMessage); ?>
      <?php endif; ?>

      <?php if (!empty($successMessage)) : ?>
        <?php renderAlert('success', $successMessage); ?>
      <?php endif; ?>
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