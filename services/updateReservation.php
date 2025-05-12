<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user_id'])) {
  die("No autorizado.");
}

$userId = $_SESSION['user_id'];

// Validar datos POST
$reservationId = isset($_POST['id']) ? intval($_POST['id']) : 0;
$initDate = $_POST['init_date'] ?? null;
$endDate = $_POST['end_date'] ?? null;
$days = isset($_POST['days']) ? intval($_POST['days']) : 0;
$paymentMethod = $_POST['payment_method'] ?? '';
$total_amount = $_POST['total_amount'] ?? null;


if ($reservationId <= 0 || !$initDate || !$endDate || $days <= 0 || !$paymentMethod) {
  header("Location: ../profile.php?post_error=Datos+inv%C3%A1lidos+para+actualizar+la+reserva");
  exit;
}

$conexion = new mysqli("localhost", "root", "", "rentaveloz");
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

// Verificar que la reserva pertenezca al usuario
$check = $conexion->prepare("SELECT id FROM reservations WHERE id = ? AND user_id = ?");
$check->bind_param("ii", $reservationId, $userId);
$check->execute();
$result = $check->get_result();
if ($result->num_rows === 0) {
  header("Location: ../profile.php?post_error=No+se+encontr%C3%B3+la+reserva+o+no+te+pertenece");
  exit;
}

// Actualizar la reserva
$update = $conexion->prepare("UPDATE reservations SET init_date = ?, end_date = ?, days = ?, payment_method = ? ,total_amount = ? WHERE id = ?");
$update->bind_param("ssisii", $initDate, $endDate, $days, $paymentMethod,$total_amount, $reservationId);
$update->execute();

if ($update->error) {
    header("Location: ../profile.php?post_error=" . urlencode($update->error));
    exit;
}

header("Location: ../profile.php?post_success=Reserva+actualizada");
exit;
