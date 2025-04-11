<?php
require_once('C:\AppServ\www\tesina\conexion\db_connect.php'); // Conexión a la BD

$mensaje_error = "";

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uav_id = sanitize($conn, $_POST["uav-id"]);
    $username = sanitize($conn, $_POST["username"]);
    $password = $_POST["password"];

    // Consultar usuario por ID UAV y nombre
    $query = "SELECT * FROM usuarios WHERE id_uav = '$uav_id' AND nombre_usuario = '$username'";
    $resultado = $conn->query($query);

    if ($resultado && $resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['contrasena_hash'])) {
            // Login exitoso (puedes iniciar sesión aquí)
            header("Location: dashboard.php"); /////////////////////////////////////////////// Cambia a dashboard.php si lo haces dinámico
            exit;
        } else {
            $mensaje_error = "Contraseña incorrecta.";
        }
    } else {
        $mensaje_error = "Credenciales inválidas o ID UAV no registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión - DroneShield</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Tu mismo estilo visual */
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body {
            background: linear-gradient(135deg, #0b3d91 0%, #1a73e8 100%);
            height: 100vh; display: flex; justify-content: center; align-items: center;
        }
        .login-container {
            background-color: white; border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 400px; padding: 2rem; text-align: center;
        }
        .logo { font-size: 2rem; font-weight: bold; color: #0b3d91; margin-bottom: 1rem; }
        .login-title { color: #333; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; text-align: left; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #555; font-weight: 500; }
        .form-group input {
            width: 100%; padding: 0.8rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem;
        }
        .form-group input:focus {
            border-color: #1a73e8; outline: none; box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
        }
        .login-button {
            background-color: #1a73e8; color: white; border: none; border-radius: 5px;
            padding: 0.8rem; width: 100%; font-size: 1rem; font-weight: bold; cursor: pointer;
        }
        .login-button:hover { background-color: #0b3d91; }
        .options { display: flex; justify-content: space-between; margin-top: 1rem; font-size: 0.9rem; }
        .options a { color: #1a73e8; text-decoration: none; transition: color 0.3s ease; }
        .options a:hover { color: #0b3d91; text-decoration: underline; }
        .back-button {
            position: absolute; top: 20px; left: 20px;
            background-color: rgba(255, 255, 255, 0.8); color: #0b3d91;
            padding: 0.5rem 1rem; border-radius: 5px; text-decoration: none;
            font-weight: bold; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .back-button:hover {
            background-color: white; transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        .input-error {
            color: #e74c3c; font-size: 0.9rem; margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<a href="../index.html" class="back-button">Volver al Inicio</a>

<div class="login-container">
    <div class="logo">DroneShield</div>
    <h2 class="login-title">Acceso al Sistema de Firewall</h2>

    <?php if ($mensaje_error): ?>
        <p class="input-error"><?= $mensaje_error ?></p>
    <?php endif; ?>

    <form action="" method="post">
        <div class="form-group">
            <label for="uav-id">ID UAV</label>
            <input type="text" id="uav-id" name="uav-id" maxlength="8" pattern="[a-zA-Z0-9]+" required>
        </div>

        <div class="form-group">
            <label for="username">Nombre de Usuario</label>
            <input type="text" id="username" name="username" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="login-button">Iniciar Sesión</button>

        <div class="options">
            <a href="password.php">¿Olvidaste tu contraseña?</a>
            <a href="create.php">Crear una cuenta</a>
        </div>
    </form>
</div>
</body>
</html>
