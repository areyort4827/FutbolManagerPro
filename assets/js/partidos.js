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

function validarResultado() {
    let fechaInput = document.getElementById("fecha_partido");
    let resultadoInput = document.getElementById("resultado_partido");

    let fechaSeleccionada = new Date(fechaInput.value);
    let hoy = new Date();

    hoy.setHours(0, 0, 0, 0);

    if (fechaSeleccionada < hoy) {
        resultadoInput.disabled = false;
        resultadoInput.placeholder = "Ej: 2-1";
    } else {
        resultadoInput.disabled = true;
        resultadoInput.value = "";
        resultadoInput.placeholder = "Se añadirá después del partido";
    }
}