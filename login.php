<?php
require_once 'config.php';

$login_err = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT id, username, fullName, role, password_hash FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            // Login exitoso
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullName'] = $user['fullName'];
            $_SESSION['role'] = $user['role'];

            header("Location: index.php");
            exit;
        } else {
            $login_err = "Usuario o contraseña incorrectos";
        }
    } else {
        $login_err = "Por favor ingresa todos los campos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Iniciar Sesión - Hotel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap + Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

  <style>
    body {
        margin: 0;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', sans-serif;
        background: #ab742c;
        overflow: hidden;
        position: relative;
    }

    .wave {
        position: absolute;
        width: 200%;
        height: 100%;
        background: linear-gradient(135deg, #e38136, #ab742c);
        top: 0;
        left: 0;
        animation: waveAnim 8s infinite linear;
        z-index: -1;
        opacity: 0.8;
    }

    .wave::before, .wave::after {
        content: "";
        position: absolute;
        width: 200%;
        height: 100%;
        background: inherit;
        top: 0;
        left: 0;
        opacity: 0.5;
    }

    .wave::before {
        animation: waveAnim 12s infinite linear reverse;
    }

    .wave::after {
        animation: waveAnim 20s infinite linear;
    }

    @keyframes waveAnim {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    .login-box {
        background: white;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
        width: 100%;
        max-width: 400px;
        text-align: center;
        position: relative;
        z-index: 1;
    }

    .login-box img.logo {
        width: 160px;
        margin-bottom: 25px;
    }

    .login-box h2 {
        color: #544920;
        font-weight: bold;
        margin-bottom: 20px;
    }

    .form-control {
        border-radius: 12px;
    }

    .btn-login {
        border-radius: 12px;
        width: 100%;
    }

    .alert {
        border-radius: 12px;
    }
</style>

<body>
    <div class="wave"></div>
    <div class="login-box">
        <img src="assets/img/logot.png" class="logo" alt="Logo del Hotel" />
        <h2><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</h2>

        <?php if (!empty($login_err)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($login_err) ?></div>
        <?php endif; ?>

        <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
            <div class="mb-3 text-start">
                <label for="username" class="form-label"><i class="fas fa-user"></i> Usuario</label>
                <input type="text" id="username" name="username" class="form-control" required autofocus />
            </div>
            <div class="mb-3 text-start">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary btn-login">
                <i class="fas fa-arrow-right"></i> Ingresar
            </button>
        </form>
    </div>
</body>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
