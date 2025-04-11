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

// Event listeners al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    // Inicializa la gráfica
    initGraficaUptime();
    
    // Actualizaciones iniciales
    actualizarDatosReales();
    actualizarTablaEventos();
    actualizarListaClientes();
    
    // Actualización periódica
    setInterval(actualizarDatosReales, 5000); // cada 5 segundos
    setInterval(actualizarTablaEventos, 30000); // cada 30 segundos
    setInterval(actualizarListaClientes, 30000); // cada 30 segundos
});