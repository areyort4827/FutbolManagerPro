/* ===== CONTROL DE MODALES ===== */
function abrirModalPartidos() {
    document.getElementById("modalPartido").style.display = "flex";
}

function cerrarModalPartidos() {
    document.getElementById("modalPartido").style.display = "none";
}

function mostrarTab(id, boton) {
    document.querySelectorAll(".contenido-tab").forEach(tab => {
        tab.style.display = "none";
    });

    document.getElementById(id).style.display = "block";

    document.querySelectorAll(".tab").forEach(btn => {
        btn.classList.remove("active");
    });

    boton.classList.add("active");
}

function cambiarTipo() {
    let tipo = document.getElementById("tipo_partido").value;
    let labelEquipo = document.getElementById("label_equipo");
    let labelRival = document.getElementById("label_rival");

    if (tipo === "local") {
        labelEquipo.innerText = "Equipo local";
        labelRival.innerText = "Equipo visitante (Rival)";
    } else {
        labelEquipo.innerText = "Equipo visitante";
        labelRival.innerText = "Equipo local (Rival)";
    }
}

/* ===== VALIDAR FECHA + GOLEADORES ===== */

function validarResultado() {
    let fechaInput = document.getElementById("fecha_partido");
    let resultadoInput = document.getElementById("resultado_partido");
    let contenedor = document.getElementById("contenedor_goleadores");
    let botonAgregar = document.getElementById("btn_agregar_goleador");

    if (!fechaInput.value) return;

    let partes = fechaInput.value.split('-');
    let fechaSeleccionada = new Date(partes[0], partes[1] - 1, partes[2]);

    let hoy = new Date();
    hoy.setHours(0, 0, 0, 0);

    if (fechaSeleccionada < hoy) {
        resultadoInput.readOnly = false;
        resultadoInput.placeholder = "Ej: 2-1";
        contenedor.style.display = "block";
        botonAgregar.style.display = "inline-block";
    } else {
        resultadoInput.readOnly = true;
        resultadoInput.value = "";
        resultadoInput.placeholder = "Se añadirá después del partido";
        contenedor.style.display = "none";
        botonAgregar.style.display = "none";
    }
}

function agregarGoleador() {
    let contenedor = document.getElementById("contenedor_goleadores");
    let nuevo = document.querySelector(".goleador-item").cloneNode(true);

    nuevo.querySelectorAll("select").forEach(select => {
        select.value = "";
    });

    nuevo.querySelectorAll("input").forEach(input => {
        // Se mantiene la lógica de inicialización para goles y asistencias
        if (input.name === "cantidad_goles[]") input.value = 1;
        if (input.name === "asistencias[]") input.value = 0;
    });

    contenedor.appendChild(nuevo);
}

/* ===== LÓGICA DE ESTADÍSTICAS DEL HISTORIAL ===== */

function abrirModalStats(partidoId, nombre, resultado, miEquipoId, localId) {
    document.getElementById('stats-partido-id').value = partidoId;
    document.getElementById('stats-partido-nombre').textContent = nombre;
    document.getElementById('stats-resultado').value = resultado;

    // DETERMINAR ROL: Esto guarda si eres local o visitante para la validación
    document.getElementById('formStats').dataset.rol = (miEquipoId == localId) ? 'local' : 'visitante';

    // 1. Limpiar todos los inputs a 0 antes de cargar los nuevos datos
    document.querySelectorAll('#formStats .stats-num-input').forEach(i => {
        i.value = 0;
    });

    // 2. Cargar estadísticas existentes vía fetch
    fetch('../get_estadisticas.php?partido_id=' + partidoId + '&equipo_id=' + miEquipoId)
        .then(r => r.json())
        .then(data => {

            console.log(data);

            data.forEach(row => {
                const jid = row.jugador_id;

                // Buscamos los inputs específicos para este jugador por su ID
                let inputGoles = document.querySelector(`input[name="jugadores[${jid}][goles]"]`);
                let inputAsistencias = document.querySelector(`input[name="jugadores[${jid}][asistencias]"]`);
                let inputAmarillas = document.querySelector(`input[name="jugadores[${jid}][amarillas]"]`);
                let inputRojas = document.querySelector(`input[name="jugadores[${jid}][rojas]"]`);

                // Asignamos el valor que viene de la base de datos
                if (inputGoles) inputGoles.value = row.goles;
                if (inputAsistencias) inputAsistencias.value = row.asistencias;
                if (inputAmarillas) inputAmarillas.value = row.tarjetas_amarillas;
                if (inputRojas) inputRojas.value = row.tarjetas_rojas;
            });
        })
        .catch(error => console.error('Error al cargar estadísticas:', error));

    document.getElementById('modalStats').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModalStats() {
    document.getElementById('modalStats').style.display = 'none';
    document.body.style.overflow = '';
}

// Nueva lógica para validar ANTES de enviar al servidor (Impide la recarga si hay error)
document.addEventListener("DOMContentLoaded", function () {

    // Validación Formulario Añadir Partido
    const formulario = document.querySelector('#modalPartido form');
    if (formulario) {
        formulario.onsubmit = function (e) {
            let fechaInput = document.getElementById("fecha_partido");
            let resultadoInput = document.getElementById("resultado_partido");
            let tipoPartido = document.getElementById("tipo_partido").value;

            let hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            let partes = fechaInput.value.split('-');
            let fechaSeleccionada = new Date(partes[0], partes[1] - 1, partes[2]);

            if (fechaSeleccionada < hoy && resultadoInput.value !== "") {
                let resultado = resultadoInput.value;

                if (!/^\d+-\d+$/.test(resultado)) {
                    alert("El resultado debe tener el formato número-número (ej: 2-1)");
                    e.preventDefault(); return false;
                }

                let golesPartes = resultado.split('-');
                let misGolesEsperados = (tipoPartido === "local") ? parseInt(golesPartes[0]) : parseInt(golesPartes[1]);

                let totalAsignado = 0;
                let inputsGoles = document.getElementsByName("cantidad_goles[]");
                let selectsJugadores = document.getElementsByName("jugador_id[]");

                for (let i = 0; i < inputsGoles.length; i++) {
                    if (selectsJugadores[i].value !== "") {
                        totalAsignado += parseInt(inputsGoles[i].value || 0);
                    }
                }

                if (totalAsignado !== misGolesEsperados) {
                    alert("Error: Tu equipo marcó " + misGolesEsperados + " goles, pero asignaste " + totalAsignado + ". Deben coincidir exactamente.");
                    e.preventDefault(); return false;
                }
            }
        };
    }

    // Validación Formulario Estadísticas Historial
    const formStats = document.getElementById('formStats');
    if (formStats) {
        formStats.onsubmit = function (e) {
            let resultado = document.getElementById("stats-resultado").value;
            let rol = formStats.dataset.rol;

            if (!/^\d+-\d+$/.test(resultado)) {
                alert("Formato de resultado incorrecto.");
                e.preventDefault(); return false;
            }

            let partes = resultado.split('-');
            let esperado = (rol === 'local') ? parseInt(partes[0]) : parseInt(partes[1]);

            let totalGoles = 0;
            let totalAsistencias = 0;
            formStats.querySelectorAll('input[name*="[goles]"]').forEach(i => totalGoles += parseInt(i.value || 0));
            formStats.querySelectorAll('input[name*="[asistencias]"]').forEach(i => totalAsistencias += parseInt(i.value || 0));

            if (totalGoles !== esperado) {
                alert("Error: Los goles asignados en la lista (" + totalGoles + ") no coinciden con lo esperado según el resultado (" + esperado + ").");
                e.preventDefault();
                return false;
            }

            if (totalAsistencias > totalGoles) {
                alert("Error: Las asistencias no pueden superar a los goles.");
                e.preventDefault(); return false;
            }
        };
    }
});

window.onclick = function (event) {
    let modal = document.getElementById("modalPartido");
    let modalStats = document.getElementById("modalStats");
    if (event.target == modal) cerrarModalPartidos();
    if (event.target == modalStats) cerrarModalStats();
};