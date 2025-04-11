<?php
// Muy importante: esto debe ser la primera línea del archivo
session_start();

require_once('C:\AppServ\www\tesina\conexion\db_connect.php');

// Cargar configuraciones desde la base de datos
$configs = [];
$result = $conn->query("SELECT nombre_config, valor_config FROM config_sistema");

while ($row = $result->fetch_assoc()) {
    $configs[$row['nombre_config']] = $row['valor_config'];
}

// Ejemplo: obtener frecuencia
$frecuencia = isset($configs['frecuencia_monitoreo']) ? (int)$configs['frecuencia_monitoreo'] : 3000;
// Mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ../menu.php"); // Cambiado a la ruta correcta
    exit();
}

// Recuperar datos de sesión
$usuario = $_SESSION['usuario'] ?? "Marco Rodríguez";
$id_uav = $_SESSION['id_uav'] ?? "DRN001A1";
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Monitoreo - Firewall UAV</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script defer src="script2.js"></script>
    <style>
        #logout-btn {
            padding: 6px 12px;
            font-size: 14px;
        }
    </style>
    <script>
        const FRECUENCIA_MONITOREO = <?= $frecuencia ?>;
    </script>

</head>
<body>
    <!-- Botones superiores -->
    <div style="position: absolute; top: 15px; right: 20px; z-index: 1000; display: flex; gap: 10px;">
        <a href="configuracion.php"><button id="config-btn">⚙️ Configuración</button></a>
        <a href="../menu.php"><button id="logout-btn">Cerrar sesión</button></a>
    </div>


    <header style="padding-top: 50px;">
        <h1>Firewall UAV - Monitoreo Activo</h1>
        <div class="user-info">
            👤 Usuario: <strong><?= htmlspecialchars($usuario) ?></strong>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            🚁 ID UAV: <strong><?= htmlspecialchars($id_uav) ?></strong>
        </div>
    </header>

    <div class="dashboard">
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-value" id="attacks">0</div>
                <div class="stat-label">Ataques Bloqueados</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="connections">0</div>
                <div class="stat-label">Conexiones Activas</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="uptime">0 seg</div>
                <div class="stat-label">Tiempo Activo</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="threat">Desconocido</div>
                <div class="stat-label">Nivel de Amenaza</div>
            </div>
        </div>
    </div>
        <div style="margin: 40px auto; max-width: 800px;">
        <h2>Actividad del Firewall</h2>
        <canvas id="graficaUptime" style="height: 300px;"></canvas>
    </div>
    <div class="threat-map">
        <h2>Mapa de Amenazas</h2>
        <div class="map-container" id="mapaAmenazas">
            <!-- Aquí se insertan los puntos -->
        </div>
    </div>


</body>
</html>
