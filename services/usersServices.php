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
  case 'get':
    handleUserGet();
    break;
  case 'create':
    handleUserCreate(!empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl );
    break;
  case 'update':
    handleUserUpdate(!empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl );
    break;
  case 'delete':
    handleUserDelete($isAdmin, !empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl );
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

function handleUserGet() {
    $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
    }
    $stmt = $connection->prepare("SELECT * FROM system_user");
    $stmt->execute();
    
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
    exit;
}

function handleUserCreate($redirectUrl) {
  $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
  }
  $name = $_POST['name'] ?? '';
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $role = $_POST['role'] ?? 'user';

  if (!$name || !$email || !$password) {
    http_response_code(400);
    echo "Datos incompletos";
    return;
  }
  // verificamos primero que no exista ya ese usuario.
  $stmt = $connection->prepare("SELECT id FROM system_user WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();

  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
      header("Location: $redirectUrl?post_error=Este+usuario+ya+existe");
      exit;
  } 
  
  //creamos el usuario
  $stmt = $connection->prepare("INSERT INTO system_user (name, email, password,role) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $name, $email, $password, $role);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
     header("Location: $redirectUrl?post_success=Usuario+creado+con+exito!");
     exit;
  } else {
     header("Location: $redirectUrl?post_error=Error+al+crear+usuario");
     exit;
  }
}

function handleUserUpdate($redirectUrl) {
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

function handleUserDelete($isAdmin,$redirectUrl) {
  $connection = getConnection();
  if (!$connection) {
      die("Error de conexión a la base de datos.");
  }
   
  if (!$isAdmin) {
    header("Location: $redirectUrl?post_error=Solo+el+administrador+puede+eliminar+reservas");
    exit;
  }
 
  $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
  if ($id <= 0) {
    http_response_code(400);
    header("Location: $redirectUrl?post_error=Solo+el+administrador+puede+eliminar+reservas");
    exit;
  }
  // Aca no dejamos que pueda eliminar el usuario que esta logueado.
  if($id == $_SESSION['user_id']){
    header("Location: $redirectUrl?post_error=No+se+puede+eliminar+el+usuario+actual");
    exit;
  }

  $stmt = $connection->prepare("DELETE FROM system_user WHERE id = ? ");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  if ($stmt->affected_rows > 0) {
    header("Location: $redirectUrl?post_success=Usuario+eliminado+correctamente");
  } else {
    header("Location: .$redirectUrl?post_error=No+se+pudo+eliminar+el+usuario");
  }
  exit;
}
