<?php
session_start();

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'hotelito');

// Crear conexión
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec("SET NAMES 'utf8mb4'");
} catch(PDOException $e) {
    die("ERROR: No se pudo conectar. " . $e->getMessage());
}

// Función para verificar autenticación
function isAuthenticated() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// Función para verificar rol de admin
function isAdmin() {
    return isAuthenticated() && $_SESSION['role'] === 'admin';
}

// Redireccionar si no está autenticado
if (!isAuthenticated() && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("location: login.php");
    exit;
}
?>