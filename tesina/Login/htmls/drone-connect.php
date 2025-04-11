<?php
// Iniciar sesión si es necesario para funcionalidades futuras
session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Firewall UAV - Conexión a Dron</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Estilos específicos para la página de conexión a dron */
        .drone-connect-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }
        
        .drone-connect-form {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            padding: 30px;
            width: 100%;
            max-width: 600px;
            margin-top: 30px;
        }
        
        .drone-status {
            margin-top: 40px;
            width: 100%;
            max-width: 600px;
        }
        
        .drone-connect-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .connect-button {
            padding: 12px 20px;
            font-size: 1.1rem;
            width: 100%;
        }
        
        .back-button {
            background-color: transparent;
            border: 1px solid var(--text-secondary);
            color: var(--text-secondary);
            margin-top: 30px;
        }
        
        .back-button:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: var(--text-color);
        }
        
        .connection-status {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
            padding: 15px;
            border-radius: var(--border-radius);
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .status-indicator {
            width: 12px;
            height: 12px;
            background-color: var(--text-secondary);
            border-radius: 50%;
        }
        
        .status-indicator.connecting {
            background-color: var(--warning-color);
            animation: pulse 2s infinite;
        }
        
        .status-indicator.connected {
            background-color: var(--success-color);
        }
        
        .status-indicator.failed {
            background-color: var(--danger-color);
        }
    </style>
</head>
<body>
    <header>
        <h1>Firewall UAV - Conexión a Dron</h1>
        <div class="controls">
            <button id="back-to-menu" class="back-button">Volver al Menú</button>
        </div>
    </header>
    
    <div class="drone-connect-container">
        <div class="drone-connect-form">
            <h2>Configuración de Conexión</h2>
            
            <div class="form-group">
                <label for="drone-id">ID del Dron</label>
                <input type="text" id="drone-id" placeholder="Ingrese el ID del dron">
            </div>
            
            <div class="form-group">
                <label for="connection-type">Tipo de Conexión</label>
                <select id="connection-type">
                    <option value="direct">Conexión Directa</option>
                    <option value="bluetooth">Bluetooth</option>
                    <option value="wifi">WiFi</option>
                    <option value="cellular">Red Celular</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="security-key">Clave de Seguridad</label>
                <input type="password" id="security-key" placeholder="Ingrese la clave de seguridad">
            </div>
            
            <div class="drone-connect-options">
                <button id="btn-scan-drones" class="connect-button">Buscar Drones</button>
                <button id="btn-connect" class="connect-button">Conectar</button>
            </div>
            
            <div class="connection-status">
                <span class="status-indicator"></span>
                <span id="connection-status-text">No conectado</span>
            </div>
        </div>
        
        <div class="drone-status">
            <section id="drone-info" style="display: none;">
                <h2>Información del Dron</h2>
                <div class="settings-form">
                    <div>
                        <div class="form-group">
                            <label>Modelo</label>
                            <div id="drone-model">No disponible</div>
                        </div>
                        <div class="form-group">
                            <label>Batería</label>
                            <div id="drone-battery">No disponible</div>
                        </div>
                    </div>
                    <div>
                        <div class="form-group">
                            <label>Estado</label>
                            <div id="drone-status">No disponible</div>
                        </div>
                        <div class="form-group">
                            <label>Señal</label>
                            <div id="drone-signal">No disponible</div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    
    <div class="notifications" id="notifications">
        <!-- Las notificaciones se generarán dinámicamente -->
    </div>
    
    <script>
        // Función para mostrar notificaciones (igual que en script.js)
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
        
        // Inicializar la página cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            // Botón para volver al menú principal
            document.getElementById('back-to-menu').addEventListener('click', function() {
                window.location.href = 'index.php';
            });
            
            // Botón para buscar drones
            document.getElementById('btn-scan-drones').addEventListener('click', function() {
                showNotification('Buscando', 'Escaneando drones disponibles...', 'warning');
                
                // Simular búsqueda
                setTimeout(() => {
                    showNotification('Drones Encontrados', '2 drones detectados en el área', 'success');
                }, 3000);
            });
            
            // Botón para conectar
            document.getElementById('btn-connect').addEventListener('click', function() {
                const droneId = document.getElementById('drone-id').value;
                const connectionType = document.getElementById('connection-type').value;
                
                if (!droneId) {
                    showNotification('Error', 'Debe ingresar un ID de dron', 'danger');
                    return;
                }
                
                // Mostrar estado de conexión
                const statusIndicator = document.querySelector('.status-indicator');
                const statusText = document.getElementById('connection-status-text');
                
                statusIndicator.className = 'status-indicator connecting';
                statusText.textContent = 'Conectando...';
                
                showNotification('Conectando', `Estableciendo conexión con el dron ${droneId}...`, 'warning');
                
                // Simular conexión
                setTimeout(() => {
                    // Simulación de conexión exitosa (80% probabilidad)
                    if (Math.random() > 0.2) {
                        statusIndicator.className = 'status-indicator connected';
                        statusText.textContent = 'Conectado';
                        
                        showNotification('Éxito', `Conexión establecida con el dron ${droneId}`, 'success');
                        
                        // Mostrar información del dron
                        document.getElementById('drone-info').style.display = 'block';
                        document.getElementById('drone-model').textContent = 'UAV-FIREWALL 2023';
                        document.getElementById('drone-battery').textContent = '87%';
                        document.getElementById('drone-status').textContent = 'Operativo';
                        document.getElementById('drone-signal').textContent = 'Excelente (92%)';
                    } else {
                        statusIndicator.className = 'status-indicator failed';
                        statusText.textContent = 'Error de conexión';
                        
                        showNotification('Error', 'No se pudo establecer conexión con el dron. Intente nuevamente.', 'danger');
                    }
                }, 4000);
            });
        });
    </script>
</body>
</html>