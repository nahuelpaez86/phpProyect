<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificamos que el usuario esté logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Verificamos que venga el ID por GET
if (!isset($_GET['id'])) {
    header("Location: profile.php?post_error=ID+de+reserva+faltante");
    exit;
}

$reservationId = (int) $_GET['id'];
$userId = $_SESSION['user_id'];

// Conectar a la base de datos
$conexion = new mysqli("localhost", "root", "", "rentaveloz");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// verificamos que exista la reserva
$check = $conexion->prepare("SELECT status_code FROM reservations WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $reservationId, $userId);
$check->execute();
$result = $check->get_result();

$reserva = $result->fetch_assoc();

// verificamos que exista la reserva para nuestro usuario
if (!$reserva) {
    header("Location: ../profile.php?post_error=Reserva+no+encontrada+o+no+te+pertenece");
    exit;
}
// verificamos que NO este ya cancelada
if ($reserva['status_code'] === 300) {
    header("Location: ../profile.php?post_error=La+reserva+ya+está+cancelada");
    exit;
}


// Actualizar el estado a cancelada (300)
$update = $conexion->prepare("UPDATE reservations SET status_code = 300 WHERE id = ?");
$update->bind_param("i", $reservationId);
$update->execute();

if ($update->affected_rows > 0) {
    header("Location: ../profile.php?post_success=Reserva+cancelada+con+éxito");
    exit;
} else {
    header("Location: ../profile.php?post_error=No+se+pudo+cancelar+la+reserva");
    exit;
}
