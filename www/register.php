<?php
// Conexión a la base de datos
$host = 'db';       // CAMBIAR: IP o hostname del servidor MySQL
$dbname = 'ymr';    // CAMBIAR: nombre de la base de datos
$user = 'user';     // CAMBIAR: usuario de la base de datos
$pass = 'webguard123'; // CAMBIAR: contraseña del usuario

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

session_start();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);

    if ($username && $password && $confirmPassword && $email) {
        // Validar si la contraseña y la confirmación coinciden
        if ($password !== $confirmPassword) {
            $error = "Las contraseñas no coinciden.";
        } else {
            // Hashear la contraseña
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insertar el nuevo usuario
            $stmt = $pdo->prepare("INSERT INTO user (username, password, email) VALUES (:username, :password, :email)");

            try {
                $stmt->execute([
                    'username' => $username,
                    'password' => $hashedPassword,
                    'email' => $email,
                ]);

                // Guardar sesión y redirigir
                $_SESSION['user'] = $username;
                header('Location: login.php');
                exit;
            } catch (PDOException $e) {
                $error = "Usuario, contraseña o correo inválido.";
            }
        }
    } else {
        $error = "Todos los campos son obligatorios y deben tener formato válido.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" />
    <title>WebGuard</title>
    <link rel="icon" type="image/png" href="img/logo-fondo.png">
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
            padding: 20px;
            position: relative;
        }

        .logo-container {
            width: 100%;
	    margin-top: 60px;
            display: flex;
            justify-content: center;
            margin-bottom: 5px;
        }
        .logo-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 12px;
        }

        .registration-container {
            background: white;
            width: 100%;
            max-width: 350px;
            border-radius: 12px;
            padding: 30px 25px 35px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
	    margin-top: 90px;
        }

        .registration-container h2 {
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
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 20px;
            border: 1.8px solid #bbb;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #667eea;
        }

        button {
            width: 100%;
            padding: 14px 0;
            font-size: 18px;
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #5a67d8;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="logo-container">
        <img src="img/logo-fondo.png" alt="Logo WebGuard" class="logo-img" />
    </div>

    <div class="registration-container">
        <h2>Registro</h2>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlentities($error) ?></div>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Nombre de usuario</label>
            <input type="text" name="username" id="username" required>

            <label for="email">Correo electrónico</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Contraseña</label>
            <input type="password" name="password" id="password" required>

            <label for="confirm_password">Confirmar contraseña</label>
            <input type="password" name="confirm_password" id="confirm_password" required>

            <button type="submit">Registrarse</button>
        </form>
    </div>
</body>
</html>
