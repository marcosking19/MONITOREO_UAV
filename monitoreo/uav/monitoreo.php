<?php
// Muy importante: esto debe ser la primera línea del archivo
session_start();

require_once('C:\AppServ\www\monitoreo\uav\conexion\db_connect.php');

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
    <script src="monitoreo_uav.js"></script>
    <script src="controlador_uav.js"></script>
    <script src="visualizacion_uav.js"></script>
    <script src="tabs_dinamicos.js"></script>
    <script src="controlador_uav.js"></script>



    <style>
        #logout-btn {
            padding: 6px 12px;
            font-size: 14px;
        }
        table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        }
        th {
            background-color: #333;
            color: white;
        }
        td, th {
            text-align: center;
            padding: 8px;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
    <script>
        const FRECUENCIA_MONITOREO = <?= $frecuencia ?>;
    </script>

</head>
<body>
    <!-- Botones superiores -->
    <div style="position: absolute; top: 15px; right: 20px; z-index: 1000; display: flex; gap: 10px;">
        
        <a href="../menu.php"><button id="logout-btn">Cerrar sesión</button></a>
    </div>


    <header style="padding-top: 50px;">
        <h1>Monitoreo UAV - Monitoreo Activo</h1>
        <div class="user-info">
            👤 Usuario: <strong><?= htmlspecialchars($usuario) ?></strong>
            &nbsp;&nbsp;|&nbsp;&nbsp;
            🚁 ID UAV: <strong><?= htmlspecialchars($id_uav) ?></strong>
        </div>
    </header>


    <div id="alertaDronDesconectado" style="display: none; padding: 15px; margin: 20px auto; max-width: 600px;
        background-color: #ffdddd; color: #a00; font-size: 20px; font-weight: bold; text-align: center;
        border: 2px solid #a00; border-radius: 8px; box-shadow: 0 0 10px rgba(255,0,0,0.5);">
        ⚠️ ¡Pérdida de conexión con el dron!
    </div>

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
    <!-- 🔧 Visualización UAV Simulada -->
<section class="panel">
  <div class="panel-header">
    <h2>Visualización de Proximidad</h2>
    <div id="distance-value">Distancia: 0m</div>
  </div>

  <div class="visualization-area" id="visualization">
    <div class="antenna">
      <div class="antenna-top"></div>
      <div class="antenna-base"></div>
      <div class="antenna-waves wave1"></div>
      <div class="antenna-waves wave2"></div>
      <div class="antenna-waves wave3"></div>
    </div>
    <div class="drone" id="drone"></div>
    <div class="connection-line" id="connection-line"></div>
  </div>

  <div class="signal-strength-meter">
    <div class="signal-bar" id="signal-strength"></div>
  </div>

  <div class="controls">
    <button id="scan-btn">Escanear Vulnerabilidades</button>
    <button id="disconnect-btn">Simular Desconexión</button>
    <button id="simular-intrusion-btn">Simular Intrusión</button>
  </div>
</section>
    <div id="alertaIntrusos" style="display: none; padding: 15px; margin: 20px auto; max-width: 600px;
        background-color: #fff3cd; color: #856404; font-size: 18px; font-weight: bold; text-align: center;
        border: 2px solid #ffeeba; border-radius: 8px; box-shadow: 0 0 8px rgba(255,193,7,0.5);">
        ⚠️ Conexiones no autorizadas detectadas en el AP del UAV
    </div>

    <!-- 🔍 Panel de Tabs: Métricas, Seguridad y Paquetes -->
    <section class="panel">
    <div class="panel-header">
        <h2>Estado Detallado del Sistema</h2>
    </div>

    <div class="tabs">
        <div class="tab active" data-tab="metrics">Métricas</div>
        <div class="tab" data-tab="security">Seguridad</div>
        <div class="tab" data-tab="packets">Análisis de Paquetes</div>
    </div>

    <div class="tab-content active" id="metrics-tab">
        <div class="stats-container">
        <div class="stat-card">
            <div class="stat-label">Potencia de Señal</div>
            <div class="stat-value" id="signal-value">-62 dBm</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Velocidad de Transmisión</div>
            <div class="stat-value" id="speed-value">20 Mbps</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Latencia</div>
            <div class="stat-value" id="latency-value">15 ms</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Paquetes Perdidos</div>
            <div class="stat-value" id="packet-loss">0.1%</div>
        </div>
        </div>
    </div>

    <div class="tab-content" id="security-tab">
        <div class="security-alerts" id="alerts-container">
        <div class="alert-item">
            <div class="alert-title">
            <span class="good">Sistema Estable</span>
            <span class="alert-time">12:00:00</span>
            </div>
            <div class="alert-description">No se han detectado amenazas en los últimos 10 minutos.</div>
        </div>
        </div>
    </div>

    <div class="tab-content" id="packets-tab">
        <div class="packet-sniffing" id="packet-logs">
        <div class="log-entry">[12:00:00] > TCP 192.168.1.105:55000 -> 192.168.1.1:443 [SYN]</div>
        </div>
    </div>
    </section>

    <!-- Nuevo bloque de monitoreo en tiempo real -->
    <div style="margin: 20px auto; max-width: 800px;">
        <h2>Monitoreo en tiempo real del UAV</h2>
        <div id="monitor-info">
            <p>Estado: <span id="estadoDron">Apagado</span></p>
            <p><strong>Tiempo activo:</strong> <span id="uptime">--</span> segundos</p>
            <p><strong>Conexiones:</strong> <span id="connections">--</span></p>
            <p><strong>Intensidad de señal (RSSI):</strong> <span id="rssi">--</span> dBm</p>
            <p><strong>Nivel de amenaza:</strong> <span id="threatLevel">--</span></p>
        </div>
        <script>
            setInterval(() => {
                fetch('http://192.168.4.1/status')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === "ok") {
                            document.getElementById('uptime').textContent = data.uptime;
                            document.getElementById('connections').textContent = data.connections;
                            document.getElementById('rssi').textContent = data.rssi;
                            let threat = "Bajo";
                            if (data.rssi < -85 || data.connections > 5) {
                                threat = "Alto";
                            } else if (data.rssi < -70) {
                                threat = "Medio";
                            }
                            document.getElementById('threatLevel').textContent = threat;
                            // Guardar en PHP
                            fetch('guardar_monitoreo.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    uptime: data.uptime,
                                    connections: data.connections,
                                    rssi: data.rssi,
                                    threatLevel: threat
                                })
                            });
                        }
                    })
                    .catch(error => console.error('Error al consultar ESP32:', error));
            }, 5000);
        </script>
    </div>
    <h2>Dispositivos conectados al UAV</h2>
    <ul id="lista-clientes"></ul>

    <h2>Historial de Monitoreo</h2>

    <div class="tabla-historial">
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Uptime (seg)</th>
                    <th>Conexiones</th>
                    <th>RSSI (dBm)</th>
                    <th>Nivel de amenaza</th>
                    
                </tr>
            </thead>
            <tbody>
                <?php
                $result = $conn->query("SELECT id, timestamp, uptime, connections, rssi, threatLevel FROM log_monitoreo ORDER BY timestamp DESC LIMIT 50");

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>{$row['id']}</td>";
                        echo "<td>{$row['timestamp']}</td>";
                        echo "<td>{$row['uptime']}</td>";
                        echo "<td>{$row['connections']}</td>";
                        echo "<td>{$row['rssi']}</td>";
                        echo "<td>{$row['threatLevel']}</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No hay datos disponibles o error en la consulta.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>


    <div style="margin: 40px auto; max-width: 800px;">
        <h2>Actividad del Firewall</h2>
        <canvas id="graficaUptime" style="height: 300px;"></canvas>
    </div>
        <h2>Historial de Eventos de Seguridad</h2>
    <div class="tabla-eventos">
        <table border="1" cellpadding="6" cellspacing="0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo de Evento</th>
                    <th>Descripción</th>
                    <th>Fecha y Hora</th>
                </tr>
            </thead>
            <tbody id="eventos-body">
                <!-- Se llenará automáticamente por JS -->
            </tbody>
        </table>
    </div>

    <script src="monitoreo_uav.js"></script>
    <script src="controlador_uav.js"></script>
    <script src="visualizacion_uav.js"></script>
    <script src="tabs_dinamicos.js"></script>
    <script src="controlador_uav.js"></script>






</body>
</html>