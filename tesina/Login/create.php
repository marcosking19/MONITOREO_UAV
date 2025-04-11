<?php
require_once('C:\AppServ\www\tesina\conexion\db_connect.php');

$mensaje_exito = "";
$mensaje_error = "";

// Procesar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uav_id = sanitize($conn, $_POST["uav-id"]);
    $username = sanitize($conn, $_POST["username"]);
    $email = sanitize($conn, $_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm-password"];

    // Verifica que el UAV esté registrado
    $query_uav = "SELECT id_uav FROM uavs WHERE id_uav = '$uav_id'";
    $result_uav = $conn->query($query_uav);

    if ($result_uav->num_rows === 0) {
        $mensaje_error = "El ID UAV no está registrado en el sistema.";
    } elseif ($password !== $confirm_password) {
        $mensaje_error = "Las contraseñas no coinciden.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Verifica si el correo o nombre de usuario ya existen
        $verificar_usuario = "SELECT * FROM usuarios WHERE correo = '$email' OR nombre_usuario = '$username'";
        $result = $conn->query($verificar_usuario);

        if ($result->num_rows > 0) {
            $mensaje_error = "Ya existe un usuario con ese correo o nombre.";
        } else {
            // Inserta el nuevo usuario
            $insert_query = "INSERT INTO usuarios (id_uav, nombre_usuario, correo, contrasena_hash, verificado)
                             VALUES ('$uav_id', '$username', '$email', '$password_hash', 1)";

            if ($conn->query($insert_query)) {
                $mensaje_exito = "Cuenta creada exitosamente. Ya puedes iniciar sesión.";
            } else {
                $mensaje_error = "Error al registrar usuario: " . $conn->error;
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Cuenta - DroneShield</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Estilos heredados de create.html */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0b3d91 0%, #1a73e8 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 0;
        }

        .signup-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 450px;
            padding: 2rem;
            text-align: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #0b3d91;
            margin-bottom: 1rem;
        }

        .signup-title {
            color: #333;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .info-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.3rem;
        }

        .signup-button {
            background-color: #1a73e8;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 0.8rem;
            width: 100%;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            margin-top: 1rem;
        }

        .signup-button:hover {
            background-color: #0b3d91;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.8);
            color: #0b3d91;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .back-button:hover {
            background-color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .message {
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .success {
            color: #2ecc71;
        }

        .error {
            color: #e74c3c;
        }

        .login-link {
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        .login-link a {
            color: #1a73e8;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<a href="login.php" class="back-button">Volver al Login</a>

<div class="signup-container">
    <div class="logo">DroneShield</div>
    <h2 class="signup-title">Crear Nueva Cuenta</h2>

    <?php if ($mensaje_exito): ?>
        <p class="message success"><?= $mensaje_exito ?></p>
    <?php elseif ($mensaje_error): ?>
        <p class="message error"><?= $mensaje_error ?></p>
    <?php endif; ?>

    <div class="uav-info">
        <p><strong>Nota importante:</strong> Para crear una cuenta, debes contar con un ID UAV previamente registrado.</p>
    </div>

    <form action="" method="post">
        <div class="form-group">
            <label for="uav-id">ID UAV Existente</label>
            <input type="text" id="uav-id" name="uav-id" maxlength="8" pattern="[a-zA-Z0-9]+" required>
        </div>

        <div class="form-group">
            <label for="username">Nombre de Usuario</label>
            <input type="text" id="username" name="username" required>
            <p class="info-text">Entre 5 y 20 caracteres.</p>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
            <div class="info-text">Mínimo 8 caracteres, incluyendo mayúsculas, minúsculas, número y símbolo.</div>
        </div>

        <div class="form-group">
            <label for="confirm-password">Confirmar Contraseña</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
        </div>

        <button type="submit" class="signup-button">Crear Cuenta</button>
    </form>

    <p class="login-link">¿Ya tienes una cuenta? <a href="login.php">Inicia sesión</a></p>
</div>
</body>
</html>
