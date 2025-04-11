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
let ultimoEventoRegistrado = null;


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
function actualizarListaClientes() {
    fetch('http://192.168.4.1/clientes')
        .then(res => res.json())
        .then(clientes => {
            const ul = document.getElementById("lista-clientes");
            ul.innerHTML = "";

            if (clientes.length === 0) {
                ul.innerHTML = "<li>No hay dispositivos conectados.</li>";
                return;
            }

            clientes.forEach(mac => {
                const li = document.createElement("li");
                li.textContent = mac + " ";

                const btn = document.createElement("button");
                btn.textContent = "Desconectar";
                btn.style.marginLeft = "10px";
                btn.onclick = () => {
                    if (confirm(`¿Desconectar ${mac}?`)) {
                        fetch(`http://192.168.4.1/desconectar?mac=${mac}`)
                            .then(r => r.json())
                            .then(response => {
                                if (response.status === "ok") {
                                    alert("✅ Dispositivo desconectado.");
                                    actualizarListaClientes(); // refresca
                                } else {
                                    alert("⚠️ No se pudo desconectar.");
                                }
                            });
                    }
                };

                li.appendChild(btn);
                ul.appendChild(li);
            });
        })
        .catch(err => {
            console.error("❌ Error al obtener lista de clientes:", err);
        });
}

document.addEventListener("DOMContentLoaded", () => {
    actualizarListaClientes();
    setInterval(actualizarListaClientes, 30000);
});


function actualizarDatosReales() {
    fetch('http://192.168.4.1/status')
        .then(res => res.json())
        .then(data => {
            // Calcular threatLevel si no viene del ESP32
            if (!data.threatLevel) {
                if (data.rssi < -85 || data.connections > 5) {
                    data.threatLevel = "Alto";
                } else if (data.rssi < -70) {
                    data.threatLevel = "Medio";
                } else {
                    data.threatLevel = "Bajo";
                }
            }

            // ✅ Estado del dron
            try {
                // Mostrar estado del dron si viene en la respuesta
                const estadoElem = document.getElementById("estadoDron");
                const alertaElem = document.getElementById("alertaDronDesconectado");

                if (!data.dronStatus || data.dronStatus !== "conectado") {
                    // Si no viene el campo o no está conectado
                    estadoElem.textContent = "desconectado";
                    estadoElem.style.color = "red";
                    alertaElem.style.display = "block";
                } else {
                    estadoElem.textContent = data.dronStatus;
                    estadoElem.style.color = "green";
                    alertaElem.style.display = "none";
                }
            } catch (err) {
                console.error("⚠️ Error al mostrar el estado del dron:", err);

                const estadoElem = document.getElementById("estadoDron");
                const alertaElem = document.getElementById("alertaDronDesconectado");

                estadoElem.textContent = "desconectado";
                estadoElem.style.color = "red";
                alertaElem.style.display = "block";
            }


            // Datos globales y UI
            securityData.attacksBlocked = data.attacksBlocked || 0;
            securityData.connections = data.connections;
            securityData.uptime = data.uptime;
            securityData.threatLevel = data.threatLevel;

            document.getElementById("uptime").innerText = `${data.uptime} seg`;
            document.getElementById("connections").innerText = data.connections;
            document.getElementById("attacks").innerText = securityData.attacksBlocked;
            document.getElementById("threat").innerText = data.threatLevel;

            const threatElem = document.getElementById("threat");
            threatElem.style.color = (data.threatLevel === "Bajo") ? "green" :
                                     (data.threatLevel === "Medio") ? "orange" :
                                     (data.threatLevel === "Alto") ? "red" : "black";


            // 🚨 Detección de conexiones no autorizadas
            const alertaConexiones = document.getElementById("alertaIntrusos");

            if (data.connections > 1) {
                alertaConexiones.style.display = "block";

                // Enviar evento a la BD
                fetch('registrar_evento.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        tipo: "Intrusión",
                        descripcion: `Conexiones activas sospechosas: ${data.connections}`
                    })
                }).catch(err => {
                    console.error("⚠️ No se pudo registrar el evento de intrusión:", err);
                });

            } else {
                alertaConexiones.style.display = "none";
            }

            // Gráfica
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

            // Guardar en base de datos
            fetch('guardar_log.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(res => res.json())
            .then(response => {
                if (response.status !== "ok") {
                    console.warn("⚠️ No se pudo guardar en BD:", response.msg);
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


function actualizarTablaEventos() {
    fetch('obtener_eventos.php')
        .then(res => res.json())
        .then(eventos => {
            const tbody = document.getElementById("eventos-body");
            tbody.innerHTML = "";

            if (eventos.length === 0) {
                tbody.innerHTML = "<tr><td colspan='4'>No hay eventos registrados.</td></tr>";
                return;
            }

            for (const evento of eventos) {
                const fila = document.createElement("tr");

                fila.innerHTML = `
                    <td>${evento.id}</td>
                    <td>${evento.tipo_evento}</td>
                    <td>${evento.descripcion}</td>
                    <td>${evento.fecha}</td>
                `;

                tbody.appendChild(fila);
            }
        })
        .catch(err => {
            console.error("❌ Error al obtener eventos:", err);
        });
}

// Llamar al cargar
document.addEventListener('DOMContentLoaded', () => {
    actualizarTablaEventos();
    setInterval(actualizarTablaEventos, 30000); // cada 30 segundos
});




