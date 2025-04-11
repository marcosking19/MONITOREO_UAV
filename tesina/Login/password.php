<?php
require_once('C:\AppServ\www\tesina\conexion\db_connect.php');

$mensaje = "";
$mensaje_error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $uav_id = sanitize($conn, $_POST["uav-id"]);
    $email = sanitize($conn, $_POST["email"]);

    $query = "SELECT * FROM usuarios WHERE id_uav = '$uav_id' AND correo = '$email'";
    $resultado = $conn->query($query);

    if ($resultado && $resultado->num_rows == 1) {
        $usuario = $resultado->fetch_assoc();
        $id_usuario = $usuario['id'];

        // Generar token único y caducidad
        $token = bin2hex(random_bytes(32));
        $expira_en = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Guardar token
        $insert = "INSERT INTO tokens_recuperacion (id_usuario, token, expira_en) VALUES ('$id_usuario', '$token', '$expira_en')";
        if ($conn->query($insert)) {
            // Simulación de envío de correo (en desarrollo real se enviaría por PHPMailer o similar)
            $mensaje = "Se ha generado un enlace de recuperación. Token: <code>$token</code>";
        } else {
            $mensaje_error = "Error al generar el token de recuperación.";
        }
    } else {
        $mensaje_error = "ID UAV o correo no coinciden con ningún usuario registrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>¿Olvidaste tu Contraseña? - DroneShield</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body {
            background: linear-gradient(135deg, #0b3d91 0%, #1a73e8 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2rem 0;
        }

        .container {
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

        .title {
            color: #333;
            margin-bottom: 1.5rem;
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

        .submit-button {
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

        .submit-button:hover {
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
    </style>
</head>
<body>
<a href="login.php" class="back-button">Volver al Login</a>

<div class="container">
    <div class="logo">DroneShield</div>
    <h2 class="title">¿Olvidaste tu Contraseña?</h2>

    <?php if ($mensaje): ?>
        <p class="message success"><?= $mensaje ?></p>
    <?php elseif ($mensaje_error): ?>
        <p class="message error"><?= $mensaje_error ?></p>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="uav-id">ID UAV</label>
            <input type="text" name="uav-id" id="uav-id" maxlength="8" pattern="[a-zA-Z0-9]+" required>
        </div>

        <div class="form-group">
            <label for="email">Correo Electrónico</label>
            <input type="email" name="email" id="email" required>
        </div>

        <button type="submit" class="submit-button">Enviar enlace de recuperación</button>
    </form>
</div>
</body>
</html>
