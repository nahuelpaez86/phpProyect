<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user_id'])) {
  die("No autorizado.");
}

$conexion = new mysqli("localhost", "root", "", "rentaveloz");
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}
$redirectUrl = $_SERVER['HTTP_REFERER'] ?? '../profile.php';
$redirectUrlCustom = $_GET['path'] ?? null;
$isAdmin = $_SESSION['user_role'] === 'admin';
$userId = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($method === 'POST' && isset($_POST['id']) ? 'update' : 'create');

switch ($action) {
   case 'cancel': 
    cancelReservation($conexion,$redirectUrl);
    break;
   case 'getAllFromCurrentUser':
    handleGetAll($conexion, $userId);
    break;
   case 'update':
      if ($redirectUrlCustom) {
        handleUpdate($conexion, '../' . $redirectUrlCustom);
      } else {
        handleUpdate($conexion,$redirectUrl);
      }
      break;
   case 'create':
      handleCreate($conexion, $userId);
      break;
   case 'delete':
      handleDelete($conexion,$isAdmin,$redirectUrl);
      break;
   default:
      http_response_code(400);
      echo "Acción no válida";
      break;
}

function getConnectionDB() {
  $conn = new mysqli("localhost", "root", "", "rentaveloz");
  if ($conn->connect_error) {
      return null;
  }
  return $conn;
}


function handleGetAll($conexion, $userId) {
    $stmt = $conexion->prepare("
      SELECT 
        r.*, 
        c.id AS car_id_alias,
        c.title, 
        c.image, 
        c.new_price 
      FROM reservations r
      JOIN cars c ON r.car_id = c.id
      WHERE r.user_id = ?
    ");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
  
    $reservations = [];
  
    while ($row = $result->fetch_assoc()) {
      switch ($row['status_code']) {
        case 303: $row['status_text'] = 'Confirmada'; $row['status_color'] = 'warning'; break;
        case 302: $row['status_text'] = 'En curso'; $row['status_color'] = 'primary'; break;
        case 301: $row['status_text'] = 'Terminada'; $row['status_color'] = 'success'; break;
        case 300: $row['status_text'] = 'Cancelada'; $row['status_color'] = 'danger'; break;
      }
      $reservations[] = $row;
    }
  
    header('Content-Type: application/json');
    echo json_encode($reservations);
    exit;
}
  
function handleUpdate($conexion,$redirectUrl) {
  $reservationId = intval($_POST['id'] ?? 0);
  $initDate = $_POST['init_date'] ?? null;
  $endDate = $_POST['end_date'] ?? null;
  $days = intval($_POST['days'] ?? 0);
  $paymentMethod = $_POST['payment_method'] ?? '';
  $total_amount = $_POST['total_amount'] ?? null;
  $status = $_POST['status_code'] ?? null;
  
  if ($reservationId <= 0 || !$initDate || !$endDate || $days <= 0 || !$paymentMethod) {
    header("Location: $redirectUrl?post_error=Datos+inv%C3%A1lidos+para+actualizar+la+reserva");
    exit;
  }

  $check = $conexion->prepare("SELECT id FROM reservations WHERE id = ?");
  $check->bind_param("i", $reservationId);
  $check->execute();
  $result = $check->get_result();
  if ($result->num_rows === 0) {
    header("Location: $redirectUrl?post_error=No+se+encontr%C3%B3+la+reserva+o+no+te+pertenece");
    exit;
  }
  
  $update = $conexion->prepare("UPDATE reservations SET status_code = ? ,init_date = ?, end_date = ?, days = ?, payment_method = ?, total_amount = ? WHERE id = ?");
  $update->bind_param("sssisii", $status, $initDate, $endDate, $days, $paymentMethod, $total_amount, $reservationId);
  $update->execute();

  if ($update->error) {
    header("Location: $redirectUrl?post_error=" . urlencode($update->error));
    exit;
  }

  header("Location: $redirectUrl?post_success=Reserva+actualizada");
  exit;
}

function handleCreate($conexion, $userId) {
  $car_id = $_POST['car_id'] ?? null;
  $days = $_POST['days'] ?? null;
  $init_date = $_POST['init_date'] ?? null;
  $end_date = $_POST['end_date'] ?? null;
  $total_amount = $_POST['total_amount'] ?? null;
  $payment_method = $_POST['payment_method'] ?? null;
  $status_code = 303;

  if (!$car_id || !$days || !$init_date || !$end_date || !$payment_method) {
    die("Faltan datos obligatorios.");
  }

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

  $stmt = $conexion->prepare("INSERT INTO reservations (user_id, car_id, days, init_date, end_date, total_amount, payment_method, status_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iiissssi", $userId, $car_id, $days, $init_date, $end_date, $total_amount, $payment_method, $status_code);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    header("Location: ../car-details.php?id=$car_id&post_success=Reserva+confirmada+con+éxito");
    exit;
  } else {
    header("Location: ../car-details.php?id=$car_id&post_error=Ocurrio+un+error+intente+mas+tarde.");
    exit;
  }
}

function handleDelete($conexion, $isAdmin,$redirectUrl) {
  if (!$isAdmin) {
    header("Location: ../admin-dashboard.php?post_error=Solo+el+administrador+puede+eliminar+reservas");
    exit;
  }
  
  $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
  if ($id <= 0) {
    http_response_code(400);
    echo "ID inválido";
    exit;
  }

  $stmt = $conexion->prepare("DELETE FROM reservations WHERE id = ? ");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    header("Location: $redirectUrl?post_success=Reserva+eliminada+correctamente");
  } else {
    header("Location: .$redirectUrl?post_error=No+se+pudo+eliminar+la+reserva");
  }
  exit;
}

function cancelReservation($conexion,$redirectUrl) {
  // verificamos que exista la reserva
  $reservationId = isset($_GET['id']) ? intval($_GET['id']) : 0;
  $check = $conexion->prepare("SELECT status_code FROM reservations WHERE id = ?");
  $check->bind_param("i", $reservationId);
  $check->execute();
  $result = $check->get_result();

  $reserva = $result->fetch_assoc();

  // verificamos que exista la reserva para nuestro usuario
  if (!$reserva) {
      header("Location: $redirectUrl?post_error=Reserva+no+encontrada");
      exit;
  }
  // verificamos que NO este ya cancelada
  if ($reserva['status_code'] === 300) {
      header("Location: $redirectUrl?post_error=La+reserva+ya+está+cancelada");
      exit;
  }

  // Actualizar el estado a cancelada (300)
  $update = $conexion->prepare("UPDATE reservations SET status_code = 300 WHERE id = ?");
  $update->bind_param("i", $reservationId);
  $update->execute();

  if ($update->affected_rows > 0) {
      header("Location: $redirectUrl?post_success=Reserva+cancelada+con+éxito");
      exit;
  } else {
      header("Location: $redirectUrl?post_error=No+se+pudo+cancelar+la+reserva");
      exit;
  }
}



// Status codes: 
// 303 -- confirmada
// 302 -- retirado el auto
// 301 -- terminada la reserva
// 300 -- cancelada
// Validar los datos recibidos