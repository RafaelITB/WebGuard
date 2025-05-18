<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CONFIGURACIÓN DE BASE DE DATOS
$host = 'db';
$dbname = 'ymr';
$user = 'user';
$pass = 'webguard123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT password FROM user WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userData && password_verify($password, $userData['password'])) {
        setcookie("loggedIn", "true", time() + (86400 * 30), "/");
        $_SESSION['user'] = $username;
        header('Location: dashboard/index.html');
        exit;
    } else {
        $error = 'Credenciales inválidas';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>WebGuard</title>
    <link rel="icon" typeLogin
Usuario:
￼="image/png" href="img/logo-fondo.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap');

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(to bottom, #333, #ccc);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
        }

        .logo-container {
            position: absolute;
            top: 40px;
            width: 100%;
            display: flex;
            margin-top: 60px;
            justify-content: center;
        }

        .logo-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
        }

        .login-container {
            background: white;
            width: 100%;
            max-width: 350px;
            border-radius: 12px;
            padding: 30px 25px 35px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            margin-top: 100px;
        }

        .login-container h2 {
            margin: 0 0 25px;
            font-weight: 700;
            font-size: 28px;
            color: #333;
            text-align: center;
        }

        label {
            font-size: 14px;
            color: #555;
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 20px;
            border: 1.8px solid #bbb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            width: 100%;
            padding: 14px 0;
            font-size: 18px;
            background-color: #667eea;
            color: white;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #5563c1;
        }

        .error-message {
            color: #d84545;
            font-size: 13px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="img/logo-fondo.png" alt="Logo WebGuard" class="logo-img" />
    </div>

    <div class="login-container" role="main" aria-label="Login form">
        <h2>Login</h2>
        <?php if (!empty($error)) : ?>
            <div class="error-message" role="alert"><?= htmlentities($error) ?></div>
        <?php endif; ?>
        <form method="POST" action="" novalidate>
            <label for="username">Usuario:</label>
            <input type="text" name="username" id="username" required autocomplete="username" />

            <label for="password">Contraseña:</label>
            <input type="password" name="password" id="password" required autocomplete="current-password" />

            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
