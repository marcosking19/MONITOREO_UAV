// Variables del dron
let dronePosition = { x: 150, y: 150 };
let signalStrength = 75;
let isConnected = true;

// Referencias DOM
const drone = document.getElementById('drone');
const connectionLine = document.getElementById('connection-line');
const signalStrengthBar = document.getElementById('signal-strength');
const distanceValue = document.getElementById('distance-value');
const connectionStatus = document.getElementById('connection-status');
const statusText = document.getElementById('status-text');
const scanBtn = document.getElementById('scan-btn');
const disconnectBtn = document.getElementById('disconnect-btn');
const intrusionBtn = document.getElementById('simular-intrusion-btn');

// Función para actualizar posición del dron
function updateDronePosition(x, y) {
    const visualizationArea = document.getElementById('visualization');
    const rect = visualizationArea.getBoundingClientRect();
    const antennaX = rect.width / 2;
    const antennaY = rect.height - 100;

    // Limitar rango
    x = Math.max(20, Math.min(x, rect.width - 40));
    y = Math.max(20, Math.min(y, rect.height - 40));

    // Posicionar
    drone.style.left = `${x}px`;
    drone.style.top = `${y}px`;

    // Distancia y conexión visual
    const dx = x - antennaX;
    const dy = y - antennaY;
    const distance = Math.sqrt(dx * dx + dy * dy);
    distanceValue.textContent = `Distancia: ${Math.round(distance / 3)}m`;

    // Línea de conexión
    connectionLine.style.width = `${distance}px`;
    connectionLine.style.left = `${antennaX}px`;
    connectionLine.style.top = `${antennaY}px`;
    connectionLine.style.transform = `rotate(${Math.atan2(dy, dx)}rad)`;

    // Señal
    const maxDistance = Math.sqrt(rect.width * rect.width + rect.height * rect.height);
    signalStrength = Math.max(5, Math.min(95, 100 - (distance / maxDistance) * 100));
    signalStrengthBar.style.width = `${signalStrength}%`;
    // Dentro de updateDronePosition o métrica simulada
    const strengthPercent = signalStrength; // ya está calculado entre 5 y 95

    const bar = document.getElementById("signal-strength");
    bar.style.width = `${strengthPercent}%`;

    // Para color dinámico: CSS gradiente ya lo resuelve

}

// Movimiento automático
function simulateDroneMovement() {
    if (!isConnected) return;
    const dx = (Math.random() - 0.5) * 30;
    const dy = (Math.random() - 0.5) * 30;
    dronePosition.x += dx;
    dronePosition.y += dy;
    updateDronePosition(dronePosition.x, dronePosition.y);
}

// Simulación de desconexión
function simulateDisconnection() {
    isConnected = false;
    connectionLine.style.backgroundColor = 'rgba(231, 76, 60, 0.3)';
    signalStrengthBar.style.width = '0%';
    distanceValue.textContent = `Desconectado`;

    setTimeout(() => {
        isConnected = true;
        connectionLine.style.background = 'linear-gradient(90deg, rgba(52,152,219,0.8), rgba(52,152,219,0.2))';
        updateDronePosition(dronePosition.x, dronePosition.y);
    }, 5000);
}

// Simulación de escaneo
function simulateScan() {
    alert("🔍 Escaneo iniciado...");
    setTimeout(() => {
        alert("✅ Escaneo completo: No se detectaron amenazas.");
    }, 2000);
}

// Simulación de intrusión
function simulateIntrusion() {
    alert("🚨 Intrusión detectada: Dispositivo sospechoso en la red.");
}

// Inicialización
document.addEventListener('DOMContentLoaded', () => {
    updateDronePosition(dronePosition.x, dronePosition.y);
    setInterval(simulateDroneMovement, 2500);

    if (scanBtn) scanBtn.onclick = simulateScan;
    if (disconnectBtn) disconnectBtn.onclick = simulateDisconnection;
    if (intrusionBtn) intrusionBtn.onclick = simulateIntrusion;
});
document.querySelectorAll('.tab').forEach(tab => {
    tab.addEventListener('click', () => {
      const tabId = tab.dataset.tab;
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
      tab.classList.add('active');
      document.getElementById(`${tabId}-tab`).classList.add('active');
    });
});
  