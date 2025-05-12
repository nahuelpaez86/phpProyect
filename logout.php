<?php
session_start(); // Necesario para acceder a la sesión actual

// 1. Eliminar todas las variables de sesión
$_SESSION = [];

// 2. Destruir la sesión
session_destroy();


// 4. Redirigir al inicio
header("Location: index.php");
exit();