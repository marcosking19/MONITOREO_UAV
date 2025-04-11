<?php
session_start();

// Recuperar variables desde POST o usar valores por defecto
$usuario = $_POST['usuario'] ?? "Marco Rodríguez";
$id_uav = $_POST['id_uav'] ?? "DRN001A1";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Simulación - Firewall UAV</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script defer src="script.js"></script>
    <style>
        /* Estilo opcional para que el botón se vea bien */
        #logout-btn {
            padding: 6px 12px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div id="start-menu" style="display: none;"></div> <!-- oculto ya que estamos en simulación -->

    <div id="simulacion-contenido" style="display: block;">
        <!-- Botón de cerrar sesión en la esquina superior derecha -->
        <div style="position: absolute; top: 15px; right: 20px; z-index: 1000;">
            <a href="menu.php"><button id="logout-btn">Cerrar sesión</button></a>
        </div>

        <header style="padding-top: 50px;"> <!-- espacio para que no se encime con el botón -->
            <h1>Firewall UAV - Monitor de Seguridad</h1>
            <div class="controls">
                <button id="scan-btn">Escanear Ahora</button>
                <button id="toggle-firewall">Activar/Desactivar</button>
                <button class="danger" id="emergency-btn">Modo Emergencia</button>
            </div>
            <div class="user-info">
                👤 Usuario: <strong><?php echo htmlspecialchars($usuario); ?></strong>
                &nbsp;&nbsp;|&nbsp;&nbsp;
                🚁 ID UAV: <strong><?php echo htmlspecialchars($id_uav); ?></strong>
            </div>
        </header>
        
        <div class="dashboard">
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-value" id="attacks-blocked">576</div>
                    <div class="stat-label">Ataques Bloqueados</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="connections">42</div>
                    <div class="stat-label">Conexiones Activas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="uptime">99.9%</div>
                    <div class="stat-label">Disponibilidad</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="threat-level">Medio</div>
                    <div class="stat-label">Nivel de Amenaza</div>
                </div>
            </div>
        </div>

        <section id="status">
            <h2>Estado del Firewall</h2>
            <p id="firewall-status">Firewall activo y protegiendo</p>
        </section>

        <section id="traffic">
            <h2>Análisis de Tráfico</h2>
            <div class="chart-container">
                <canvas id="trafficChart"></canvas>
            </div>
        </section>

        <section id="threats">
            <h2>Mapa de Amenazas</h2>
            <div class="threat-map">
                <div class="map-container"></div>
                <!-- Los puntos de amenaza se generarán dinámicamente -->
            </div>
        </section>

        <section id="logs">
            <h2>Eventos de Seguridad</h2>
            <ul id="log-list">
                <li>
                    <span class="timestamp">15:32:45</span>
                    <span class="log-content">Conexión autorizada desde 192.168.1.5</span>
                    <div class="log-actions">
                        <button class="log-action">🔍</button>
                        <button class="log-action">🚫</button>
                    </div>
                </li>
                <li class="warning">
                    <span class="timestamp">15:30:12</span>
                    <span class="log-content">Intento de autenticación fallido desde 203.45.67.89</span>
                    <div class="log-actions">
                        <button class="log-action">🔍</button>
                        <button class="log-action">🚫</button>
                    </div>
                </li>
                <li class="danger">
                    <span class="timestamp">15:28:03</span>
                    <span class="log-content">Ataque de fuerza bruta detectado desde 78.34.21.90</span>
                    <div class="log-actions">
                        <button class="log-action">🔍</button>
                        <button class="log-action">🚫</button>
                    </div>
                </li>
                <li>
                    <span class="timestamp">15:25:18</span>
                    <span class="log-content">Actualización de reglas completada</span>
                    <div class="log-actions">
                        <button class="log-action">🔍</button>
                    </div>
                </li>
            </ul>
        </section>

        <section id="settings">
            <h2>Configuración</h2>
            <div class="settings-form">
                <div>
                    <div class="form-group">
                        <label for="security-level">Nivel de Seguridad</label>
                        <select id="security-level">
                            <option value="low">Bajo</option>
                            <option value="medium" selected>Medio</option>
                            <option value="high">Alto</option>
                            <option value="custom">Personalizado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="scan-frequency">Frecuencia de Escaneo</label>
                        <select id="scan-frequency">
                            <option value="5">Cada 5 minutos</option>
                            <option value="15" selected>Cada 15 minutos</option>
                            <option value="30">Cada 30 minutos</option>
                            <option value="60">Cada hora</option>
                        </select>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label>Bloqueo Automático</label>
                        <label class="toggle-switch">
                            <input type="checkbox" checked id="auto-block">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    <div class="form-group">
                        <label>Alertas en Tiempo Real</label>
                        <label class="toggle-switch">
                            <input type="checkbox" checked id="real-time-alerts">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="notifications" id="notifications">
        <!-- Las notificaciones se generarán dinámicamente -->
    </div>

    <script src="script.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startMenu = document.getElementById('start-menu');
            const btnSimulation = document.getElementById('btn-simulation');
            const btnDrone = document.getElementById('btn-drone');
            const simulationContent = document.getElementById('simulacion-contenido');
            const connectForm = document.getElementById('connect-form');
            
            // Botón para ver la simulación
            btnSimulation.addEventListener('click', function() {
                startMenu.style.display = 'none';
                simulationContent.style.display = 'block';
                // La simulación ya se inicia en script.js
            });
            
            // Botón para mostrar formulario de conexión
            btnDrone.addEventListener('click', function() {
                connectForm.style.display = 'block';
            });
            
            // Manejar el envío del formulario
            connectForm.addEventListener('submit', function(e) {
                e.preventDefault();
                startMenu.style.display = 'none';
                simulationContent.style.display = 'block';
                showNotification('Conexión establecida', 'Conectado a UAV correctamente', 'success');
            });
            
            // Función para mostrar notificaciones (copia de la que está en script.js)
            function showNotification(title, message, type = '') {
                const notificationsContainer = document.getElementById('notifications');
                
                // Crear elemento de notificación
                const notification = document.createElement('div');
                notification.className = `notification ${type}`;
                notification.innerHTML = `
                    <div class="notification-title">
                        ${title}
                        <button class="notification-close">✕</button>
                    </div>
                    <p>${message}</p>
                `;
                
                // Añadir notificación al contenedor
                notificationsContainer.appendChild(notification);
                
                // Configurar cierre automático
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                }, 5000);
                
                // Configurar cierre manual
                notification.querySelector('.notification-close').addEventListener('click', function() {
                    notification.style.animation = 'slideOut 0.3s ease forwards';
                    setTimeout(() => {
                        notification.remove();
                    }, 300);
                });
            }
        });
    </script>
</body>
</html>