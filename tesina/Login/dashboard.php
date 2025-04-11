<?php
// Puedes activar sesión si más adelante quieres restringir esta descarga
// session_start();
// if (!isset($_SESSION['usuario_autenticado'])) {
//     header("Location: login.php");
//     exit;
// }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Descarga del Instalador - DroneShield</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background: linear-gradient(135deg, #0b3d91, #1a73e8);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background: white;
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
            color: #333;
        }

        .container h2 {
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }

        .download-button {
            display: inline-block;
            padding: 1rem 2rem;
            background-color: #1a73e8;
            color: white;
            font-size: 1.1rem;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .download-button:hover {
            background-color: #0b3d91;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Tu instalador está listo para descargar.</h2>
        <a href="../descargas/drone_setup.exe" class="download-button" download>
            💾 Descargar Instalador de DroneShield (.exe)
        </a>
    </div>
</body>
</html>
