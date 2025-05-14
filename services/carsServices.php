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
        if ($method === 'POST') {
            createCar(!empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl);
        }
        break;
    case 'update':
        updateCar(!empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl);
        break;
    case 'delete':
        deleteCar(!empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl, $isAdmin);
        break;
    default:
        http_response_code(400);
        echo "Acción no válida";
        break;
}

function getConnection()
{
    $conn = new mysqli("localhost", "root", "", "rentaveloz");
    if ($conn->connect_error) {
        return null;
    }
    return $conn;
}

function getAllCars()
{
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

function updateCar($redirectUrl)
{
    $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
    }

    $id               = intval($_POST['id'] ?? 0);
    $title            = trim($_POST['title'] ?? '');
    $km               = trim($_POST['km'] ?? '');
    $old_price        = floatval($_POST['old_price'] ?? 0);
    $new_price        = floatval($_POST['new_price'] ?? 0);
    $engine           = trim($_POST['engine'] ?? '');
    $transmission     = trim($_POST['transmission'] ?? '');
    $passengers       = intval($_POST['passengers'] ?? 0);
    $air_conditioning = intval($_POST['air_conditioning'] ?? 0);
    $imageRelativePath = $_POST['current_image'] ?? null;

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

    // Manejo de nueva imagen si fue cargada
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = dirname(__DIR__) . '/imagesUploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageFilename = uniqid('car_', true) . '.' . $ext;
        $imagePath = $uploadDir . $imageFilename;
        $imageRelativePath = 'imagesUploads/' . $imageFilename;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            header("Location: $redirectUrl?post_error=Error+al+mover+la+nueva+imagen");
            exit;
        }

        // Opcional: eliminar imagen anterior (si no es la misma y existe físicamente)
        if (!empty($_POST['current_image']) && $_POST['current_image'] !== $imageRelativePath) {
            $oldImagePath = dirname(__DIR__) . '/' . $_POST['current_image'];
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
    }

    // Ahora se incluye el campo de imagen también en el UPDATE
    $stmt = $connection->prepare("
        UPDATE cars
           SET image            = ?,
               title            = ?,
               km               = ?,
               old_price        = ?,
               new_price        = ?,
               engine           = ?,
               transmission     = ?,
               passengers       = ?,
               air_conditioning = ?
         WHERE id = ?
    ");
    $stmt->bind_param(
        "sssddssiii",
        $imageRelativePath,
        $title,
        $km,
        $old_price,
        $new_price,
        $engine,
        $transmission,
        $passengers,
        $air_conditioning,
        $id
    );

    $stmt->execute();
    // VER /
    if ($stmt->affected_rows > 0) {
        header("Location: $redirectUrl?post_success=Auto+actualizado+con+exito!");
        exit;
    } else {
        unlink($oldImagePath);
        header("Location: $redirectUrl?post_error=Error+al+actualizar");
        exit;
    }
}

function deleteCar($redirectUrl, $isAdmin)
{
    $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
    }

    if (!$isAdmin) {
        header("Location: $redirectUrl?post_error=Solo+el+administrador+puede+eliminar");
        exit;
    }

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    if ($id <= 0) {
        http_response_code(400);
        header("Location: $redirectUrl?post_error=Error+por+falta+de+datos");
        exit;
    }

    // revisamos que NO existe una reserva para que pueda eliminarlo.
    $reservationForCar = $connection->prepare("SELECT car_id FROM reservations WHERE car_id = ?");
    $reservationForCar->bind_param('i', $id);
    $reservationForCar->execute();
    $result = $reservationForCar->get_result()->fetch_assoc();

    if (!empty($result)) {
        header("Location: $redirectUrl?post_error=Error+tiene+reserva+asociada");
        exit;
    }

    $stmt = $connection->prepare("DELETE FROM cars WHERE id = ? ");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: $redirectUrl?post_success=Auto+eliminado+correctamente");
    } else {
        header("Location: .$redirectUrl?post_error=No+se+pudo+eliminar+el+auto");
    }
    exit;
}

function createCar($redirectUrl)
{
    $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: $redirectUrl?post_error=Acceso+inválido");
        exit;
    }
    $title            = trim($_POST['title'] ?? '');
    $km               = trim($_POST['km'] ?? '');
    $old_price        = floatval($_POST['old_price'] ?? 0);
    $new_price        = floatval($_POST['new_price'] ?? 0);
    $engine           = trim($_POST['engine'] ?? '');
    $transmission     = trim($_POST['transmission'] ?? '');
    $passengers       = intval($_POST['passengers'] ?? 0);
    $air_conditioning = intval($_POST['air_conditioning'] ?? 0);

    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        header("Location: $redirectUrl?post_error=Error+al+subir+la+imagen");
        exit;
    }

    $uploadDir = dirname(__DIR__) . '/imagesUploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $imageFilename = uniqid('car_', true) . '.' . $ext;
    $imagePath = $uploadDir . $imageFilename;
    $imageRelativePath = 'imagesUploads/' . $imageFilename;

    if (!move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
        header("Location: $redirectUrl?post_error=Error+al+mover+la+imagen");
        exit;
    }

    $stmt = $connection->prepare("
      INSERT INTO cars (image, title, km, old_price, new_price, engine, transmission, passengers, air_conditioning)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
  ");

    if (!$stmt) {
        unlink($imagePath);
        die("Error en prepare(): " . $connection->error);
    }

    $stmt->bind_param(
        "sssddssii",
        $imageRelativePath,
        $title,
        $km,
        $old_price,
        $new_price,
        $engine,
        $transmission,
        $passengers,
        $air_conditioning
    );

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: $redirectUrl?post_success=Auto+agregado+con+exito!");
        exit;
    } else {
        unlink($imagePath);
        header("Location: $redirectUrl?post_error=Error+al+agregar");
        exit;
    }
}
