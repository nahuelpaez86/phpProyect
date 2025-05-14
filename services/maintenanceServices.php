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
        break;
    case 'create':
        if ($method === 'POST') {
            createMaintenance(!empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl);
        }
        break;
    case 'update':
        updateMaintenance(!empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl);
        break;
    case 'delete':
        deleteMaintenance(!empty($redirectUrlCustom) ? "../{$redirectUrlCustom}" : $redirectUrl, $isAdmin);
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

function getAllMantenances()
{
    $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
    }
    $stmt = $connection->prepare("
    SELECT m.*, c.title AS car_title
    FROM maintenance m
    JOIN cars c ON m.car_id = c.id");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
    exit;
}

function updateMaintenance($redirectUrl)
{
    $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
    }

    $id           = intval($_POST['id'] ?? 0);
    $car_id       = intval($_POST['car_id'] ?? 0);
    $start_date   = $_POST['start_date'] ?? '';
    $end_date     = $_POST['end_date'] ?? null;
    $service_type = trim($_POST['service_type'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $cost         = floatval($_POST['cost'] ?? 0);
    $status       = $_POST['status'] ?? 'pending';

    // Validaciones mínimas
    if (
        $id <= 0 || $car_id <= 0 || $start_date === '' ||
        $service_type === '' || $cost < 0 ||
        !in_array($status, ['pending', 'in_progress', 'completed'])
    ) {
        http_response_code(400);
        echo "Datos inválidos";
        exit;
    }

    $stmt = $connection->prepare("
        UPDATE maintenance
        SET car_id = ?, 
            start_date = ?, 
            end_date = ?, 
            service_type = ?, 
            description = ?, 
            cost = ?, 
            status = ?
        WHERE id = ?
    ");

    if (!$stmt) {
        die("Error en prepare(): " . $connection->error);
    }

    // si end_date está vacío, lo pasamos como NULL
    $end_date = $end_date !== '' ? $end_date : null;

    $stmt->bind_param(
        "issssdsi",
        $car_id,
        $start_date,
        $end_date,
        $service_type,
        $description,
        $cost,
        $status,
        $id
    );

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: $redirectUrl?post_success=Mantenimiento+actualizado+correctamente");
        exit;
    } else {
        header("Location: $redirectUrl?post_error=No+se+pudo+actualizar+el+mantenimiento");
        exit;
    }
}

function createMaintenance($redirectUrl)
{
    $connection = getConnection();
    if (!$connection) {
        die("Error de conexión a la base de datos.");
    }

    $car_id       = intval($_POST['car_id'] ?? 0);
    $service_type = trim($_POST['service_type'] ?? '');
    $start_date   = $_POST['start_date'] ?? '';
    $end_date     = $_POST['end_date'] ?? null;
    $description  = trim($_POST['description'] ?? '');
    $cost         = floatval($_POST['cost'] ?? 0);
    $status       = $_POST['status'] ?? 'pending';

    if (
        $car_id <= 0 || $service_type === '' || $start_date === '' ||
        !in_array($status, ['pending', 'in_progress', 'completed'])
    ) {
        http_response_code(400);
        header("Location: $redirectUrl?post_error=Datos+inválidos");
        exit;
    }

    $stmt = $connection->prepare("
        INSERT INTO maintenance (
            car_id,
            start_date,
            end_date,
            service_type,
            description,
            cost,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Error en prepare(): " . $connection->error);
    }

    $end_date = ($end_date === '') ? null : $end_date;

    $stmt->bind_param(
        "issssds",
        $car_id,
        $start_date,
        $end_date,
        $service_type,
        $description,
        $cost,
        $status
    );

    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: $redirectUrl?post_success=Mantenimiento+agregado+correctamente");
        exit;
    } else {
        header("Location: $redirectUrl?post_error=No+se+pudo+crear+el+mantenimiento");
        exit;
    }
}

function deleteMaintenance($redirectUrl, $isAdmin)
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
        header("Location: $redirectUrl?post_error=ID+inválido+para+eliminación");
        exit;
    }

    $stmt = $connection->prepare("DELETE FROM maintenance WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        header("Location: $redirectUrl?post_success=Mantenimiento+eliminado+correctamente");
    } else {
        header("Location: $redirectUrl?post_error=No+se+pudo+eliminar+el+mantenimiento");
    }
    exit;
}
