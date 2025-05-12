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

$query = "SELECT * FROM cars";
$resultado = $conexion->query($query);

$cars = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($row = $resultado->fetch_assoc()) {
        // Armamos el link
        $row['link'] = "car-details.php?id=" . $row['id'];
        $cars[] = $row;
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
    <link
        href="https://fonts.googleapis.com/css?family=Poppins:100,100i,200,200i,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i&display=swap"
        rel="stylesheet">
    <title>RentaVeloz | Autos</title>
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
    <section class="section section-bg" id="call-to-action"
        style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="cta-content">
                        <br>
                        <br>
                        <h2>Nuestros <em>Autos</em></h2>
                        <p>Contamos con una flota de autos modernos y nuevos</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** Call to Action End ***** -->

    <!-- ***** Fleet Starts ***** -->
    <section class="section" id="trainers">
        <div class="container">
            <br>
            <br>
            <div class="row">
                <?php foreach ($cars as $car): ?>
                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="<?php echo $car['image']; ?>" alt="">
                        </div>
                        <div class="down-content">
                            <span>
                                <del><sup>$</sup><?php echo number_format($car['old_price'], 0, ',', '.'); ?></del>
                                &nbsp;
                                <sup>$</sup><?php echo number_format($car['new_price'], 0, ',', '.'); ?>
                            </span>

                            <h4><?php echo $car['title']; ?></h4>
                            <p>
                                <i class="fa fa-dashboard"></i> <?php echo $car['km']; ?> &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-cube"></i> <?php echo $car['engine']; ?> &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-cog"></i> <?php echo $car['transmission']; ?> &nbsp;&nbsp;&nbsp;
                            </p>

                            <div class="main-button text-center">
                                <a href="<?php echo $car['link']; ?>" 
                                class="btn-reservar" 
                                data-link="<?php echo $car['link']; ?>">
                                Reservar Ahora Mismo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
              <!-- Modal  -->
           <!-- Modal Bootstrap -->
            <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Iniciar sesión requerida</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar">
                         <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        Debes iniciar sesión o registrarte para continuar con la reserva.
                    </div>

                    <div class="modal-footer d-flex flex-column">
                        <a href="login.php" class="btn btn-danger w-100 mb-2 py-2">Iniciar sesión</a>
                        <a href="register.php" class="btn btn-success w-100 mb-2 py-2">Registrarse</a>
                        <button type="button" class="btn btn-secondary w-100 py-2" data-bs-dismiss="modal">Cancelar</button>
                    </div>

                    </div>
                </div>
                </div>

          
            <br>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-..." crossorigin="anonymous"></script>

    <!-- Plugins -->
    <script src="assets/js/scrollreveal.min.js"></script>
    <script src="assets/js/waypoints.min.js"></script>
    <script src="assets/js/jquery.counterup.min.js"></script>
    <script src="assets/js/imgfix.min.js"></script>
    <script src="assets/js/mixitup.js"></script>
    <script src="assets/js/accordions.js"></script>
    <script src="assets/js/custom.js"></script>
    <script>
    const isLoggedIn = <?= json_encode($isLogged) ?>;

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.btn-reservar').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!isLoggedIn) {
            e.preventDefault();
            const modal = new bootstrap.Modal(document.getElementById('loginModal'));
            modal.show();
            }
        });
        });
    });

    function closeModal() {
        document.getElementById('loginModal').style.display = 'none';
    }
    </script>
    
</body>

</html>

