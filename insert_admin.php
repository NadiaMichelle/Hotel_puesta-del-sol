<?php
// Conexión a la base de datos
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'hotelito');

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES 'utf8mb4'");
} catch (PDOException $e) {
    die("ERROR: No se pudo conectar. " . $e->getMessage());
}

// Datos del nuevo admin
$username = 'admin';
$fullName = 'Admin User';
$email = 'admin@hotel.com';
$role = 'admin';
$password = 'admin1234';
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Insertar admin
try {
    $sql = "INSERT INTO users (username, fullName, email, role, password_hash)
            VALUES (:username, :fullName, :email, :role, :password_hash)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':username' => $username,
        ':fullName' => $fullName,
        ':email' => $email,
        ':role' => $role,
        ':password_hash' => $password_hash
    ]);
    echo "✅ Usuario admin insertado correctamente.";
} catch (PDOException $e) {
    if (str_contains($e->getMessage(), 'Duplicate')) {
        echo "⚠️ Ya existe un usuario con ese nombre.";
    } else {
        echo "❌ Error al insertar: " . $e->getMessage();
    }
}

// Cerrar conexión
$pdo = null;
?>
