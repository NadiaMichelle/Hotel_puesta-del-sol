<?php
session_start();

// ⚙️ Configuración de conexión
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'hotelito');

// 🧠 Crear conexión PDO
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die(json_encode([
        'success' => false,
        'message' => 'Error al conectar con la base de datos',
        'error' => $e->getMessage()
    ]));
}

// 🧩 Función: ¿usuario logueado?
function isAuthenticated() {
    return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
}

// 🛡️ Función: ¿es administrador?
function isAdmin() {
    return isAuthenticated() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// 🔐 Redireccionar si no está logueado (excepto login)
$currentFile = basename($_SERVER['PHP_SELF']);
if (!isAuthenticated() && $currentFile !== 'login.php' && $currentFile !== 'api.php') {
    header("Location: login.php");
    exit;
}
