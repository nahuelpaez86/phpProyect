<?php 
  if (session_status() === PHP_SESSION_NONE) {
    session_start();
  }
  require_once 'components/header.php';
  require_once 'components/loader.php';
  require_once 'components/footer.php';
  require_once 'services/usersServices.php';
  
  $users = handleUserGet();
  $isLogged = isset($_SESSION['user_id']);
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
  <?php renderHeader('users',$isLogged); ?>
  <!-- ***** Admin Header End ***** -->

  <section class="section">
  <div class="container px-300">
      <h3 class="fw-bold mb-4 mt-20">Usuarios</h3>
      <div >
      <a href="admin-users.php" class="btn btn-sm btn-primary">Volver a todos los usuarios</a>
      <div>
      <form method="POST" action='services/usersServices.php?action=create&path=admin-users.php'>
            <div class="mb-3 mt-4">
              <label for="name" class="form-label fw-bold"><strong>Nombre completo</strong></label>
              <input type="text" class="form-control" id="name" name="name" required>
             </div>
             <div class="mb-3">
              <label for="email" class="form-label"><strong>Correo electrónico</strong></label>
              <input type="email" class="form-control" id="email" name="email" required>
             </div>
             <div class="mb-3">
               <label for="password" class="form-label"><strong>Contraseña</strong></label>
               <input type="password" class="form-control" id="password" name="password" required>
             </div>
             <div class="mb-3">
                <label for="confirmPassword" class="form-label"><strong>Confirmar contraseña</strong></label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required>
             </div>
             <div class="mb-3">
                <label for="confirmPassword" class="form-label"><strong>Rol</strong></label><br/>
                <select name="role" class="form-select">
                    <option value="client" >Cliente</option>
                    <option value="admin" selected>Administrador</option>
                </select>
             </div>
             
            <button type="submit" class="btn btn-sm btn-success">Crear usuario</button>
       </form>
      </div>  
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
