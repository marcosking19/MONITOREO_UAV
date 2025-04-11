<?php
require_once('C:\AppServ\www\monitoreo\uav\conexion\db_connect.php');
session_start();

// Mostrar errores (opcional para pruebas)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Procesar formulario si se envió
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $clave => $valor) {
        $stmt = $conn->prepare("UPDATE config_sistema SET valor_config = ? WHERE nombre_config = ?");
        $stmt->bind_param("ss", $valor, $clave);
        $stmt->execute();
        $stmt->close();
    }
    $mensaje = "✅ Cambios guardados correctamente.";
}

// Obtener configuraciones actuales
$configs = [];
$result = $conn->query("SELECT nombre_config, valor_config FROM config_sistema ORDER BY nombre_config ASC");

while ($row = $result->fetch_assoc()) {
    $configs[$row['nombre_config']] = $row['valor_config'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración del Sistema - Firewall UAV</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .config-form {
            max-width: 600px;
            margin: 40px auto;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 0 8px rgba(0,0,0,0.1);
        }
        .config-form label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
        }
        .config-form input,
        .config-form select {
            width: 100%;
            padding: 6px;
            font-size: 14px;
        }
        .config-form button {
            margin-top: 20px;
            padding: 10px 20px;
        }
        .msg {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>

    <h1 style="text-align:center;">🔧 Configuración del Sistema - Firewall UAV</h1>

    <?php if (!empty($mensaje)) echo "<p class='msg'>$mensaje</p>"; ?>

    <form class="config-form" method="POST">
        <label for="modo_operacion">Modo de operación:</label>
        <select name="modo_operacion" id="modo_operacion">
            <option value="real" <?= $configs['modo_operacion'] === 'real' ? 'selected' : '' ?>>Real</option>
            <option value="simulacion" <?= $configs['modo_operacion'] === 'simulacion' ? 'selected' : '' ?>>Simulación</option>
        </select>

        <label for="frecuencia_monitoreo">Frecuencia de monitoreo (ms):</label>
        <input type="number" name="frecuencia_monitoreo" value="<?= htmlspecialchars($configs['frecuencia_monitoreo']) ?>">

        <label for="nivel_alerta">Nivel de amenaza para alerta:</label>
        <select name="nivel_alerta">
            <option value="Bajo" <?= $configs['nivel_alerta'] === 'Bajo' ? 'selected' : '' ?>>Bajo</option>
            <option value="Medio" <?= $configs['nivel_alerta'] === 'Medio' ? 'selected' : '' ?>>Medio</option>
            <option value="Alto" <?= $configs['nivel_alerta'] === 'Alto' ? 'selected' : '' ?>>Alto</option>
        </select>

        <label for="intentos_maximos">Intentos máximos permitidos:</label>
        <input type="number" name="intentos_maximos" value="<?= htmlspecialchars($configs['intentos_maximos']) ?>">

        <label for="clave_firma_digital">Clave para firma digital:</label>
        <input type="text" name="clave_firma_digital" value="<?= htmlspecialchars($configs['clave_firma_digital']) ?>">

        <label for="firma_digital_activada">Firma digital activada:</label>
        <select name="firma_digital_activada">
            <option value="1" <?= $configs['firma_digital_activada'] === '1' ? 'selected' : '' ?>>Sí</option>
            <option value="0" <?= $configs['firma_digital_activada'] === '0' ? 'selected' : '' ?>>No</option>
        </select>

        <button type="submit">Guardar cambios</button>
    </form>

</body>
</html>
