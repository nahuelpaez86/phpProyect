<?php

function renderHeader($current_selected, $isLogged) {
    // Definir el menú dentro de la función
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $isLogged = isset($_SESSION['user_id']);
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';

    $items = $isAdmin
        ? [
            ['key' => 'reservations', 'name' => 'reservas', 'path' => 'admin-dashboard.php'],
            ['key' => 'users', 'name' => 'usuarios', 'path' => 'admin-users.php'],
            ['key' => 'cars', 'name' => 'Autos', 'path' => 'admin-cars.php'],
            ['key' => 'logout', 'name' => 'Cerrar Sesión', 'path' => 'logout.php']
          ]
        : [
            ['key' => 'home', 'name' => 'Inicio', 'path' => 'index.php'],
            ['key' => 'cars', 'name' => 'Autos', 'path' => 'cars.php'],
            ['key' => 'contact', 'name' => 'Contacto', 'path' => 'contact.php'],
            ...($isLogged ? [
                ['key' => 'profile', 'name' => 'Mi Cuenta', 'path' => 'profile.php'],
                ['key' => 'logout', 'name' => 'Cerrar Sesión', 'path' => 'logout.php']
              ] : [
                ['key' => 'login', 'name' => 'Iniciar Sesión', 'path' => 'login.php'],
                ['key' => 'register', 'name' => 'Crear Cuenta', 'path' => 'register.php']
              ])
        ];
    ?>
    <!-- ***** Header Area Start ***** -->
    <header class="header-area header-sticky <?= $isAdmin ? 'bg-dark' : '' ?>">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <nav class="main-nav ">
                        <!-- ***** Logo Start ***** -->
                        <a href="<?= $isAdmin ? '#' : 'index.php' ?>" class="logo">
                        Rental car<em> RentaVeloz</em>
                        </a>
                        <!-- ***** Logo End ***** -->
                        <!-- ***** Menu Start ***** -->
                        <ul class="nav">
                            <?php foreach ($items as $item): ?> 
                                <li>
                                    <a href="<?php echo $item['path']; ?>" 
                                       class="<?php echo ($current_selected === $item['key']) ? 'active' : ''; ?>">
                                       <?php echo $item['name']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>    
                        <a class="menu-trigger">
                            <span>Menu</span>
                        </a>
                        <!-- ***** Menu End ***** -->
                    </nav>
                </div>
            </div>
        </div>
    </header>
    <!-- ***** Header Area End ***** -->
    <?php
}