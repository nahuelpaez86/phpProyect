<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'components/header.php';
require_once 'components/loader.php';
require_once 'components/footer.php';

$cars = [
    [
        'image'         => 'assets/images/product-1-720x480.jpg',
        'old_price'     => '11999',
        'new_price'     => '11779',
        'title'         => 'Dacia Sandero',
        'engine'        => '1800 cc',
        'transmission'  => 'Manual'
    ],
    [
        'image'         => 'assets/images/product-2-720x480.jpg',
        'old_price'     => '11999',
        'new_price'     => '11779',
        'title'         => 'BMW Z8',
        'engine'        => '1800 cc',
        'transmission'  => 'Manual'
    ],
    [
        'image'         => 'assets/images/product-3-720x480.jpg',
        'old_price'     => '11999',
        'new_price'     => '11779',
        'title'         => 'Mazda CX-3',
        'engine'        => '1800 cc',
        'transmission'  => 'Manual'
    ],
];

$testimonials = [
    [
        'image' => 'assets/images/features-first-icon.png',
        'name'  => 'Juan Pérez',
        'text'  => '“¡Servicio rápido y confiable! Me ayudó a moverme por la ciudad sin complicaciones.”'
    ],
    [
        'image' => 'assets/images/features-first-icon.png',
        'name'  => 'Ana Gómez',
        'text'  => '“Excelente atención al cliente y precios muy competitivos. ¡Repetiré sin duda!”'
    ],
    [
        'image' => 'assets/images/features-first-icon.png',
        'name'  => 'Mario Rodríguez',
        'text'  => '“El coche estaba en perfectas condiciones y la reserva fue muy sencilla.”'
    ],
    [
        'image' => 'assets/images/features-first-icon.png',
        'name'  => 'Laura Fernández',
        'text'  => '“Todo el proceso fue muy rápido. ¡Definitivamente lo recomiendo a mis amigos!”'
    ],
];

$first_testimonials = array_slice($testimonials, 0, 2);
$seconds_testimonials = array_slice($testimonials, 2, 4);
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

    <title>Rental car</title>

    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">

    <link rel="stylesheet" type="text/css" href="assets/css/font-awesome.css">

    <link rel="stylesheet" href="assets/css/style.css">

    </head>
    
    <body>
    
    <!-- ***** Preloader Start ***** -->
    <?php renderLoader(); ?>
    <!-- ***** Preloader End ***** -->
    
    
    <!-- ***** Header Area Start ***** -->
    <?php renderHeader('home',$isLogged); ?>
    <!-- ***** Header Area End ***** -->

    <!-- ***** Main Banner Area Start ***** -->
    <div class="main-banner" id="top">
        <video autoplay muted loop id="bg-video">
            <source src="assets/images/video.mp4" type="video/mp4" />
        </video>

        <div class="video-overlay header-text">
            <div class="caption">
                <h2>Mejor <em>rental car</em> del pais!</h2>
            </div>
        </div>
    </div>
    <!-- ***** Main Banner Area End ***** -->

   <!-- ***** Cars Starts ***** -->
    <section class="section" id="trainers">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="section-heading">
                        <h2>Autos <em>modernos</em></h2>
                        <img src="assets/images/line-dec.png" alt="">
                        <p>Contamos con una flota de autos modernos y nuevos</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php foreach($cars as $car): ?>
                <div class="col-lg-4">
                    <div class="trainer-item">
                        <div class="image-thumb">
                            <img src="<?php echo $car['image']; ?>" alt="">
                        </div>
                        <div class="down-content">
                            <span>
                                <del><sup>$</sup><?php echo $car['old_price']; ?></del> &nbsp; <sup>$</sup><?php echo $car['new_price']; ?>
                            </span>

                            <h4><?php echo $car['title']; ?></h4>

                            <p>
                                <i class="fa fa-cube"></i> <?php echo $car['engine']; ?> &nbsp;&nbsp;&nbsp;
                                <i class="fa fa-cog"></i> <?php echo $car['transmission']; ?> &nbsp;&nbsp;&nbsp;
                            </p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <br>

            <div class="main-button text-center">
                <a href="cars.php">Ver autos</a>
            </div>
        </div>
    </section>
    <!-- ***** Cars Ends ***** -->

    <section class="section section-bg" id="schedule" style="background-image: url(assets/images/about-fullscreen-1-1920x700.jpg)">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="section-heading dark-bg">
                        <h2>Sobre <em>Nosotros</em></h2>
                        <img src="assets/images/line-dec.png" alt="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="cta-content text-center  ">
                        <p>En <em style="color: #ed563b;">RentaVeloz</em> ofrecemos un servicio de alquiler de autos ágil y confiable. Contamos con una flota variada para cada necesidad, con atención personalizada y tarifas competitivas. Nuestra prioridad es tu comodidad y seguridad, para que disfrutes cada viaje con total confianza.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ***** Testimonials Item Start ***** -->
    <section class="section" id="features">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 offset-lg-3">
                    <div class="section-heading">
                        <h2>Nuestros <em>testimonios</em></h2>
                        <img src="assets/images/line-dec.png" alt="waves">
                    </div>
                </div>

                <div class="col-lg-6">
                    <ul class="features-items">
                        <?php foreach($first_testimonials as $testimonial): ?>
                            <li class="feature-item">
                                <div class="left-icon">
                                    <img src="<?php echo $testimonial['image']; ?>" alt="First One">
                                </div>
                                <div class="right-content">
                                    <h4><?php echo $testimonial['name']; ?></h4>
                                    <p><em><?php echo $testimonial['text']; ?></em></p>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
              
                <div class="col-lg-6">
                    <ul class="features-items">
                        <?php foreach($seconds_testimonials as $testimonial): ?>
                            <li class="feature-item">
                                <div class="left-icon">
                                    <img src="<?php echo $testimonial['image']; ?>" alt="First One">
                                </div>
                                <div class="right-content">
                                    <h4><?php echo $testimonial['name']; ?></h4>
                                    <p><em><?php echo $testimonial['text']; ?></em></p>
                                </div>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
            <br>
        </div>
    </section>
    <!-- ***** Testimonials Item End ***** -->
    

    <!-- ***** Call to Action Start ***** -->
    <section class="section section-bg" id="call-to-action" style="background-image: url(assets/images/banner-image-1-1920x500.jpg)">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 offset-lg-1">
                    <div class="cta-content">
                        <h2> <em>Contacto</em></h2>
                        <p>¿Tienes alguna duda o pregunta? ¡Contáctanos y con gusto te ayudaremos!</p>
                        <div class="main-button">
                            <a href="contact.php">Contactarme</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- ***** Call to Action End ***** -->

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
    
    <!-- Global Init -->
    <script src="assets/js/custom.js"></script>

  </body>
</html>