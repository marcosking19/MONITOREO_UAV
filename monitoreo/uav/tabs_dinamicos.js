// Generación de métricas y tráfico simulado en pestañas

let logIndex = 0;

function actualizarMetricasSimuladas() {
  // Calcular valores aleatorios
  const dbm = -Math.round(Math.random() * 25 + 45); // -45 a -70 dBm
  const velocidad = (Math.random() * 45).toFixed(1); // Mbps
  const latencia = Math.floor(Math.random() * 80 + 10); // ms
  const perdida = (Math.random() * 3).toFixed(1); // %

  // Insertar en HTML
  document.getElementById("signal-value").textContent = `${dbm} dBm`;
  document.getElementById("speed-value").textContent = `${velocidad} Mbps`;
  document.getElementById("latency-value").textContent = `${latencia} ms`;
  document.getElementById("packet-loss").textContent = `${perdida}%`;

  // Colores por calidad
  const signalElem = document.getElementById("signal-value");
  if (dbm > -60) signalElem.className = "stat-value good";
  else if (dbm > -75) signalElem.className = "stat-value warning";
  else signalElem.className = "stat-value critical";
}

function generarLogPaquete() {
  const now = new Date().toLocaleTimeString();
  const protocolos = ['TCP', 'UDP', 'ICMP', 'TLS'];
  const puertos = [443, 80, 53, 22];
  const proto = protocolos[Math.floor(Math.random() * protocolos.length)];
  const puerto = puertos[Math.floor(Math.random() * puertos.length)];
  const entrada = `[${now}] > ${proto} 192.168.1.105:${50000 + logIndex} -> 192.168.1.1:${puerto}`;
  logIndex++;

  const logDiv = document.createElement("div");
  logDiv.className = "log-entry";
  logDiv.textContent = entrada;

  const contenedor = document.getElementById("packet-logs");
  contenedor.prepend(logDiv);

  if (contenedor.children.length > 50) {
    contenedor.removeChild(contenedor.lastChild);
  }
}

// Iniciar simulación de pestañas
document.addEventListener("DOMContentLoaded", () => {
  setInterval(actualizarMetricasSimuladas, 4000); // cada 4 seg
  setInterval(generarLogPaquete, 6000); // cada 6 seg
});