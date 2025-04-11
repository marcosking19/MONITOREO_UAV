// Variables globales para monitoreo
let securityData = {
    attacksBlocked: 0,
    connections: 0,
    uptime: 0,
    threatLevel: "Desconocido"
};

let tiempoLabels = [];
let uptimeData = [];
let graficaChart;

// Inicializa la gráfica de uptime en tiempo real
function initGraficaUptime() {
    const ctx = document.getElementById('graficaUptime').getContext('2d');
    graficaChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: tiempoLabels,
            datasets: [{
                label: 'Tiempo Activo (segundos)',
                data: uptimeData,
                borderColor: '#1abc9c',
                backgroundColor: 'rgba(26, 188, 156, 0.2)',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            animation: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Hora'
                    }
                },
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Segundos'
                    }
                }
            }
        }
    });
}

// Actualiza datos desde el ESP32 y alimenta el HTML y la gráfica
function actualizarDatosReales() {
    fetch("http://192.168.4.1/monitor")
        .then(res => res.json())
        .then(data => {
            // ⚠️ Forzar ataques y conexiones en 0
            data.attacksBlocked = 0;
            data.connections = 1;

            // Actualizar valores en objeto global
            securityData.attacksBlocked = data.attacksBlocked;
            securityData.connections = data.connections;
            securityData.uptime = data.uptime;
            securityData.threatLevel = data.threatLevel;

            // Actualizar HTML
            document.getElementById("uptime").innerText = `${data.uptime} seg`;
            document.getElementById("connections").innerText = data.connections;
            document.getElementById("attacks").innerText = data.attacksBlocked;
            document.getElementById("threat").innerText = data.threatLevel;

            // Colores del nivel de amenaza
            const threatElem = document.getElementById("threat");
            if (data.threatLevel === "Bajo") {
                threatElem.style.color = "green";
            } else if (data.threatLevel === "Medio") {
                threatElem.style.color = "orange";
            } else if (data.threatLevel === "Alto") {
                threatElem.style.color = "red";
            } else {
                threatElem.style.color = "black";
            }

            // Actualizar gráfica
            const now = new Date().toLocaleTimeString();
            tiempoLabels.push(now);
            uptimeData.push(data.uptime);

            if (tiempoLabels.length > 10) {
                tiempoLabels.shift();
                uptimeData.shift();
            }

            if (graficaChart) {
                graficaChart.update();
            }

            // Enviar datos forzados a la base de datos
            fetch('guardar_log.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(response => {
                if (response.status !== "ok") {
                    console.warn("⚠️ No se pudo guardar en base de datos:", response.msg);
                }
            })
            .catch(err => {
                console.error("❌ Error al guardar en BD:", err);
            });

        })
        .catch(err => {
            console.error("❌ Error al obtener datos del ESP32:", err);
        });
}


// Inicia monitoreo al cargar la página
document.addEventListener('DOMContentLoaded', function () {
    initGraficaUptime();
    initThreatMap(); // 👉 Asegúrate de incluir esta línea
    const FRECUENCIA_MONITOREO = 3000;
    setInterval(actualizarDatosReales, FRECUENCIA_MONITOREO);
});


function initThreatMap() {
    const mapa = document.getElementById('mapaAmenazas');
    mapa.innerHTML = ""; // Limpiar puntos existentes

    // Simular que no hay amenazas => NO agregar puntos
    // Pero si más adelante quieres agregar amenazas:
    // for (const threat of threatLocations) {
    //     const punto = document.createElement("div");
    //     punto.classList.add("threat-point");
    //     punto.style.left = `${threat.x}%`;
    //     punto.style.top = `${threat.y}%`;
    //     mapa.appendChild(punto);
    // }
}

