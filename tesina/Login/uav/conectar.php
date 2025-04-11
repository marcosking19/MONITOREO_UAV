<?php
session_start();
require_once('C:\AppServ\www\tesina\conexion\db_connect.php');

// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mostrarBotonConexion = false;
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_uav = $_POST['id_uav'];
    $password = $_POST['password'];

    $sql = "SELECT nombre_usuario, contrasena_hash FROM usuarios WHERE id_uav = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id_uav);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['contrasena_hash'])) {
            $_SESSION['usuario'] = $row['nombre_usuario'];
            $_SESSION['id_uav'] = $id_uav;
            $mostrarBotonConexion = true;
        } else {
            $error = "❌ Contraseña incorrecta.";
        }
    } else {
        $error = "❌ ID UAV no encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Conectar UAV</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #eef2f3, #cfd9df);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .centered-box {
            background-color: #ffffffdd;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 350px;
        }
        input[type="text"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            color: black;
        }
        button {
            background-color: #2980b9;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #1f618d;
        }
        .btn-secondary {
            background-color: #95a5a6;
            margin-top: 10px;
        }
        .btn-secondary:hover {
            background-color: #7f8c8d;
        }
    </style>
</head>
<body>

<?php if (!$mostrarBotonConexion): ?>
    <div class="centered-box">
        <h2>Conectar a un Dron</h2>
        <?php if ($error): ?>
            <p style="color:red;"><?= $error ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="id_uav">ID UAV:</label><br>
            <input type="text" id="id_uav" name="id_uav" required><br><br>

            <label for="password">Contraseña:</label><br>
            <input type="password" id="password" name="password" required><br><br>

            <button type="submit">Ingresar</button>
        </form>
    </div>
<?php else: ?>
    <header>
        <h1>Conexión UAV - Preparado</h1>
        <div style="text-align:right; margin:10px;">
            <strong>Usuario:</strong> <?= $_SESSION['usuario'] ?><br>
            <strong>ID UAV:</strong> <?= $_SESSION['id_uav'] ?>
        </div>
    </header>

    <div class="centered-box">
        <p>Se validó el acceso correctamente.</p>
        <button class="btn" onclick="conectarDron()">Conectar al Dron</button>
        <form action="Login/menu.php" method="get">
            <button type="submit" class="btn-secondary">Volver al Menú</button>
        </form>
        <div id="resultado" style="margin-top:15px;"></div>
    </div>

    <script>
    function conectarDron() {
        const idUAV = "<?= $_SESSION['id_uav'] ?>";
        const resultado = document.getElementById("resultado");

        resultado.innerHTML = "🔄 Verificando conexión...";

        fetch('http://192.168.4.1/connect?id_uav=' + idUAV)
            .then(response => response.json())
            .then(data => {
                if (data.status === "ok") {
                    window.location.href = "monitoreo.php";
                } else {
                    resultado.innerHTML = `<span style="color:red;">❌ ${data.msg}</span>`;
                }
            })
            .catch(err => {
                resultado.innerHTML = "<span style='color:red;'>❌ Error al conectar con el ESP32</span>";
            });
    }
    </script>
<?php endif; ?>

</body>
</html>
