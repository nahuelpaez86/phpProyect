<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Debes estar logueado para confirmar la reserva.");
}
// Status codes: 
// 303 -- confirmada
// 302 -- retirado el auto
// 301 -- terminada la reserva
// 300 -- cancelada
// Validar los datos recibidos
$car_id = $_POST['car_id'] ?? null;
$days = $_POST['days'] ?? null;
$init_date = $_POST['init_date'] ?? null;
$end_date = $_POST['end_date'] ?? null;
$total_amount = $_POST['total_amount'] ?? null;
$payment_method = $_POST['payment_method'] ?? null;
$status_code = 303;
$user_id = $_SESSION['user_id'];

if (!$car_id || !$days || !$init_date || !$end_date || !$payment_method) {
    die("Faltan datos obligatorios.");
}

// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "rentaveloz");

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Crear tabla si no existe
$conexion->query("CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id INT NOT NULL,
    days INT NOT NULL,
    init_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_amount INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    status_code INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insertar reserva
$stmt = $conexion->prepare("INSERT INTO reservations (user_id, car_id, days, init_date, end_date, total_amount,payment_method,status_code) VALUES (?, ?, ?, ?,?, ?, ? ,?)");

$stmt->bind_param("iiisssss", $user_id, $car_id, $days, $init_date, $end_date,$total_amount, $payment_method, $status_code);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    header("Location: ../car-details.php?id=$car_id&post_success=Reserva+confirmada+con+éxito");
    exit;
} else {
    errorHandler($car_id);
}

function errorHandler(int $car_id)
{
    header("Location: ../car-details.php?id=$car_id&post_error=Ocurrio+un+error+intente+mas+tarde.");
    exit;
}
?>
