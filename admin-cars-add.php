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
  <title>RentaVeloz | Agregar Auto Admin</title>

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
  <?php renderHeader('cars',$isLogged); ?>
  <!-- ***** Admin Header End ***** -->

  <section class="section">
  <div class="container px-300">
      <h3 class="fw-bold mb-4 mt-20">Agregar Autos</h3>
      <div >
      <a href="admin-users.php" class="btn btn-sm btn-primary">Volver a todos los Autos</a>
      <div>
      <div class="container mt-5">
        <h2>Agregar nuevo auto</h2>
        <form action="services/carsServices.php?action=create&path=admin-cars.php" enctype="multipart/form-data" method="POST" class="mt-2">
            <div class="mb-3">
                <label for="image" class="form-label"> <strong> Imagen del auto</strong></label>
                <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
            <label for="title" class="form-label"><strong>Título</strong></label>
            <input type="text" name="title" id="title" class="form-control" required>
            </div>

            <div class="mb-3">
            <label for="km" class="form-label"><strong>Kilómetros</strong></label>
            <input type="text" name="km" id="km" class="form-control" required>
            </div>

            <div class="mb-3">
            <label for="old_price" class="form-label"><strong>Precio anterior</strong></label>
            <input type="number" name="old_price" id="old_price" class="form-control" step="0.01">
            </div>

            <div class="mb-3">
            <label for="new_price" class="form-label"><strong>Precio actual</strong></label>
            <input type="number" name="new_price" id="new_price" class="form-control" step="0.01" required>
            </div>

            <div class="mb-3">
            <label for="engine" class="form-label"><strong>Motor</strong></label>
            <input type="text" name="engine" id="engine" class="form-control">
            </div>

            <div class="mb-3">
            <label for="transmission" class="form-label"><strong>Transmisión</strong></label>
            <select name="transmission" id="transmission" class="form-control">
                <option value="manual">Manual</option>
                <option value="automatic">Automática</option>
            </select>
            </div>

            <div class="mb-3">
            <label for="passengers" class="form-label"><strong>Pasajeros</strong></label>
            <input type="number" name="passengers" id="passengers" class="form-control" min="1" required>
            </div>

            <div class="mb-3">
            <label for="air_conditioning" class="form-label"><strong>Aire acondicionado</strong></label>
            <select name="air_conditioning" id="air_conditioning" class="form-control">
                <option value="1">Sí</option>
                <option value="0">No</option>
            </select>
            </div>
           <button type="submit" class="btn btn-primary">Guardar auto</button>
         </form>
        </div>
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
