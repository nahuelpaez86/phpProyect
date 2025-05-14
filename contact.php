<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'components/header.php';
require_once 'components/loader.php';
require_once 'components/footer.php';
$contacts = [
    [
        'icon' => 'fa fa-phone',
        'contact'  => '+54 264 1221221'
    ],
    [
        'icon' => 'fa fa-envelope',
        'contact'  => 'contact@rentalveloz.com'
    ],
    [
        'icon' => 'fa fa-map-marker',
        'contact'  => 'Av Ignacio de la Roza 22'
    ],
];
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

    <title>RentaVeloz | Contacto</title>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">

    <link rel="stylesheet" href="assets/css/style.css">

</head>

<body>

    <!-- ***** Preloader Start ***** -->
    <?php renderLoader(); ?>
    <!-- ***** Preloader End ***** -->

    <!-- ***** Header Area Start ***** -->
    <?php renderHeader('contact', $isLogged); ?>
    <!-- ***** Header Area End ***** -->

    <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="cta-content">
                        <br>
                        <br>
                        <h2><em>Contacto</em></h2>
                        <p>¿Tienes alguna pregunta o requieres información adicional? ¡Estamos aquí para ayudarte! Ponte en contacto con nosotros y nuestro equipo te brindará la atención y asesoría que necesites. Tu satisfacción es nuestra prioridad.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ***** Features Item Start ***** -->
    <section class="section" id="features">
        <div class="container">
            <div class="row text-center">

                <div class="col-lg-6 offset-lg-3">
                    <div class="section-heading">
                        <h2>medios de <em> contacto</em></h2>
                        <img src="assets/images/line-dec.png" alt="waves">
                    </div>
                </div>

                <?php foreach ($contacts as $contact): ?>
                    <div class="col-md-4">
                        <div class="icon">
                            <i class="<?php echo $contact['icon']; ?>"></i>
                        </div>
                        <h5><a href="#"><?php echo $contact['contact']; ?></a></h5>
                        <br>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <!-- ***** Features Item End ***** -->

    <!-- ***** Contact Us Area Starts ***** -->
    <section class="section" id="contact-us" style="margin-top: 0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-xs-12">
                    <div id="map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3400.5163125337463!2d-68.52871288823721!3d-31.53744210200735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x968140286c494a17%3A0xc2bcc8532b79a0f7!2sAvenida%20Ignacio%20de%20la%20Roza%2022%2C%20J5402DCN%20San%20Juan!5e0!3m2!1ses-419!2sar!4v1741052595153!5m2!1ses-419!2sar" width="100%" height="600px" frameborder="0" style="border:0" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** Contact Us Area Ends ***** -->

    <!-- ***** Footer Start ***** -->
    <?php renderFooter(); ?>

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