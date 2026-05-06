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
    
    // Clonamos el primer item como plantilla para el nuevo goleador
    let itemBase = document.querySelector(".goleador-item");

    if (itemBase) {
        let nuevo = itemBase.cloneNode(true);

        // 1. Limpiar los selects
        nuevo.querySelectorAll("select").forEach(select => {
            select.value = "";
        });

        // 2. Limpiar los inputs de goles y asistencias, y poner valores por defecto
        nuevo.querySelectorAll("input").forEach(input => {
            // Si el nombre del input es el de goles, ponemos 1 por defecto
            if (input.name === "cantidad_goles[]") {
                input.value = 1;
            } 
            // Si es el de asistencias, ponemos 0 por defecto
            else if (input.name === "asistencias[]") {
                input.value = 0;
            }
        });

        contenedor.appendChild(nuevo);
    }
}

// Nueva lógica para validar antes de enviar al servidor
document.addEventListener("DOMContentLoaded", function() {
    const formulario = document.querySelector('#modalPartido form');
    
    if (formulario) {
        formulario.onsubmit = function(e) {
            let fechaInput = document.getElementById("fecha_partido");
            let resultadoInput = document.getElementById("resultado_partido");
            let tipoPartido = document.getElementById("tipo_partido").value;
            
            let hoy = new Date();
            hoy.setHours(0, 0, 0, 0);
            let partes = fechaInput.value.split('-');
            let fechaSeleccionada = new Date(partes[0], partes[1] - 1, partes[2]);

            // Solo validamos goles si el partido ya pasó
            if (fechaSeleccionada < hoy) {
                
                if (resultadoInput.value.trim() === "") {
                    alert("Debes ingresar el resultado del partido.");
                    e.preventDefault();
                    return false;
                }

                let resultado = resultadoInput.value;
                
                // Validar formato 2-1
                if (!/^\d+-\d+$/.test(resultado)) {
                    alert("El resultado debe tener el formato número-número (ej: 2-1)");
                    e.preventDefault();
                    return false;
                }

                let golesPartes = resultado.split('-');
                let misGolesEsperados = (tipoPartido === "local") ? parseInt(golesPartes[0]) : parseInt(golesPartes[1]);

                let totalGolesAsignado = 0;
                let totalAsistenciasAsignadas = 0;
                let inputsGoles = document.getElementsByName("cantidad_goles[]");
                let inputsAsistencias = document.getElementsByName("asistencias[]");
                let selectsJugadores = document.getElementsByName("jugador_id[]");

                for (let i = 0; i < inputsGoles.length; i++) {
                    if (selectsJugadores[i].value !== "") {
                        totalGolesAsignado += parseInt(inputsGoles[i].value || 0);
                        totalAsistenciasAsignadas += parseInt(inputsAsistencias[i].value || 0);
                    }
                }

                if (totalGolesAsignado !== misGolesEsperados) {
                    // Si hay error, detenemos el envío del formulario
                    alert("Error: Tu equipo marcó " + misGolesEsperados + " goles, pero asignaste " + totalGolesAsignado + ". Deben coincidir exactamente.");
                    e.preventDefault(); 
                    return false;
                }

                if (misGolesEsperados > 0 && totalAsistenciasAsignadas === 0) {
                    alert("Debes ingresar las asistencias del partido.");
                    e.preventDefault();
                    return false;
                }

                if (totalAsistenciasAsignadas > misGolesEsperados) {
                    alert("Error: No puede haber más asistencias (" + totalAsistenciasAsignadas + ") que goles marcados (" + misGolesEsperados + ").");
                    e.preventDefault();
                    return false;
                }
            }
        };
    }
});

window.onclick = function(event) {
    let modal = document.getElementById("modalPartido");
    if (event.target == modal) {
        cerrarModalPartidos();
    }
};