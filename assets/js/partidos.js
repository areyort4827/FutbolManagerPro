function abrirModal() {
    document.getElementById("modalPartido").style.display = "flex";
}

function cerrarModal() {
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
        input.value = 1;
    });

    contenedor.appendChild(nuevo);
}

// Nueva lógica para validar ANTES de enviar al servidor
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
            if (fechaSeleccionada < hoy && resultadoInput.value !== "") {
                let resultado = resultadoInput.value;
                
                // Validar formato 2-1
                if (!/^\d+-\d+$/.test(resultado)) {
                    alert("El resultado debe tener el formato número-número (ej: 2-1)");
                    e.preventDefault();
                    return false;
                }

                let golesPartes = resultado.split('-');
                let misGolesEsperados = (tipoPartido === "local") ? parseInt(golesPartes[0]) : parseInt(golesPartes[1]);

                let totalAsignado = 0;
                let inputsGoles = document.getElementsByName("cantidad_goles[]");
                let selectsJugadores = document.getElementsByName("jugador_id[]");

                for (let i = 0; i < inputsGoles.length; i++) {
                    if (selectsJugadores[i].value !== "") {
                        totalAsignado += parseInt(inputsGoles[i].value);
                    }
                }

                if (totalAsignado !== misGolesEsperados) {
                    // Si hay error, detenemos el envío del formulario
                    alert("Error: Tu equipo marcó " + misGolesEsperados + " goles, pero asignaste " + totalAsignado + ". Deben coincidir exactamente.");
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
        cerrarModal();
    }
};