<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $pdo->prepare("SELECT id, username, fullName, role, password_hash FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        session_start();
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['fullName'] = $user['fullName'];
        $_SESSION['role'] = $user['role'];
        
        header("location: index.php");
        exit;
    } else {
        $login_err = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - Hotel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg,rgb(227, 129, 54),rgb(171, 116, 44));
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-box {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .login-box img.logo {
            width: 167px;
            margin-bottom: 25px;
        }

        .login-box h2 {
            margin-bottom: 20px;
            font-weight: bold;
            color: #2c3e50;
        }

        .form-control {
            border-radius: 12px;
        }

        .btn-login {
            width: 100%;
            border-radius: 12px;
        }

        .alert-danger {
            border-radius: 12px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <!-- LOGO -->
        <img src="assets/img/logot.png" alt="Logo del Hotel" class="logo">

        <h2><i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión</h2>
        <?php if (!empty($login_err)): ?>
            <div class="alert alert-danger"><?= $login_err ?></div>
        <?php endif; ?>
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" method="post">
            <div class="mb-3 text-start">
                <label for="username" class="form-label"><i class="fas fa-user"></i> Usuario</label>
                <input type="text" name="username" class="form-control" id="username" required autofocus>
            </div>
            <div class="mb-3 text-start">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Contraseña</label>
                <input type="password" name="password" class="form-control" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-login"><i class="fas fa-arrow-right"></i> Ingresar</button>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
