<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
  die("No autorizado.");
}

$conexion = new mysqli("localhost", "root", "", "rentaveloz");
if ($conexion->connect_error) {
  die("Error de conexión: " . $conexion->connect_error);
}

$redirectUrl = $_SERVER['HTTP_REFERER'] ?? 'index.php';
$redirectUrlCustom = $_GET['path'] ?? null;
$isAdmin = $_SESSION['user_role'] === 'admin';
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? ($method === 'POST' && isset($_POST['id']) ? 'update' : 'create');

switch ($action) {
  case 'getAll':
    getAllCars();
    break;
  case 'create':
   ;
    break;
  case 'update':
     updateCar(!empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl );
    break;
  case 'delete':
    
    break;
  default:
    http_response_code(400);
    echo "Acción no válida";
    break;
}

function getConnection() {
    $conn = new mysqli("localhost", "root", "", "rentaveloz");
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}

function getAllCars() {
   $connection = getConnection();
   if (!$connection) {
        die("Error de conexión a la base de datos.");
   }
   $stmt = $connection->prepare("SELECT * FROM cars");
   $stmt->execute();
   $result = $stmt->get_result();
   return $result->fetch_all(MYSQLI_ASSOC);
   exit;
}

function updateCar($redirectUrl) {
    $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
    }
 
     // Recojo datos de la request
     $id               = intval($_POST['id'] ?? 0);
     $title            = trim($_POST['title'] ?? '');
     $km               = trim($_POST['km'] ?? '');
     $old_price        = floatval($_POST['old_price'] ?? 0);
     $new_price        = floatval($_POST['new_price'] ?? 0);
     $engine           = trim($_POST['engine'] ?? '');
     $transmission     = trim($_POST['transmission'] ?? '');
     $passengers       = intval($_POST['passengers'] ?? 0);
     $air_conditioning = intval($_POST['air_conditioning'] ?? 0);
 
     // Validación básica
     if (
         $id <= 0 ||
         $title === '' ||
         $km === '' ||
         $old_price <= 0 ||
         $new_price <= 0 ||
         $engine === '' ||
         $transmission === '' ||
         $passengers <= 0
     ) {
         http_response_code(400);
         echo "Datos inválidos";
         exit;
     }
 
     // Preparo la sentencia incluyendo todos los campos
     $stmt = $connection->prepare("
         UPDATE cars
            SET title            = ?,
                km               = ?,
                old_price        = ?,
                new_price        = ?,
                engine           = ?,
                transmission     = ?,
                passengers       = ?,
                air_conditioning = ?
          WHERE id = ?
     ");
     if (! $stmt) {
         die("Error en prepare(): " . $connection->error);
     }
 
     // s = string, d = double, i = integer
     $stmt->bind_param(
         "ssddssiii",
         $title,            // s
         $km,               // s
         $old_price,        // d
         $new_price,        // d
         $engine,           // s
         $transmission,     // s
         $passengers,       // i
         $air_conditioning, // i
         $id                // i
     );
 
     $stmt->execute();
 
     if ($stmt->affected_rows > 0) {
         header("Location: $redirectUrl?post_success=Auto+actualizado+con+exito!");
     } else {
         header("Location: $redirectUrl?post_error=Error+al+actualizar");
         exit;
     }
    
}

/**
 * 
 * function handleUserUpdate($redirectUrl) {
    $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
    }
    
    $id = intval($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'client';
    
    // Validación básica
    if ($id <= 0 || $name === '' || $email === '') {
        http_response_code(400);
        echo "Datos inválidos";
        exit;
    }
    
    $stmt = $connection->prepare("UPDATE system_user SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        header("Location: $redirectUrl?post_success=Usuario+actualizado");
    } else {
        header("Location: $redirectUrl?post_error=Error+al+actualizar");
       exit;
    }
    
    exit;
}
 */