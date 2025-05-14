<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'components/header.php';
require_once 'components/loader.php';
require_once 'components/footer.php';
require_once 'services/carsServices.php';

$cars = getAllCars();
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
    <!-- ***** Admin Header Start ***** -->
    <?php renderHeader('maintenance', $isLogged); ?>
    <!-- ***** Admin Header End ***** -->

    <section class="section">
        <div class="container px-300">
            <h3 class="fw-bold mb-4 mt-20">Agregar Autos</h3>
            <div>
                <a href="admin-car-maintenance" class="btn btn-sm btn-primary">Volver a todos los mantenimientos</a>
                <div>
                    <div class="container mt-5">
                        <h2>Agregar nuevo mantenimiento de auto</h2>
                        <form action="services/maintenanceServices.php?action=create&path=admin-car-maintenance.php" method="POST" class="mt-2">
                            <div class="mb-3">
                                <label for="car_id" class="form-label"><strong>Auto</strong></label>
                                <select name="car_id" id="car_id" class="form-control" required>
                                    <option value="" disabled selected>Seleccioná un auto</option>
                                    <?php foreach ($cars as $car): ?>
                                        <option value="<?= $car['id'] ?>"><?= htmlspecialchars($car['title']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="service_type" class="form-label"><strong>Tipo de servicio</strong></label>
                                <input type="text" name="service_type" id="service_type" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="start_date" class="form-label"><strong>Fecha de inicio</strong></label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="end_date" class="form-label"><strong>Fecha de fin</strong></label>
                                <input type="date" name="end_date" id="end_date" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label"><strong>Descripción</strong></label>
                                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="cost" class="form-label"><strong>Costo</strong></label>
                                <input type="number" name="cost" id="cost" step="0.01" class="form-control">
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label"><strong>Estado</strong></label>
                                <select name="status" id="status" class="form-control">
                                    <option value="pending" selected>Pendiente</option>
                                    <option value="in_progress">En progreso</option>
                                    <option value="completed">Completado</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Guardar mantenimiento</button>
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
</body>

</html>