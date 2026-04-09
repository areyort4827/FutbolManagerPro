function mostrarPagina(id){

let pages = document.querySelectorAll('.page');
pages.forEach(p=>p.classList.remove('active'));  // Quita todas las paginas de activas

let links = document.querySelectorAll('.menu a');
links.forEach(l=>l.classList.remove('active')); // Quita todos los links del menu de activos

document.getElementById(id).classList.add('active'); // Le añade activo a la pagina seleccionada

event.target.classList.add('active'); // El menu seleccionado pasa a activo

 if(id === 'estadisticas'){
        crearRendimientoChart();
        crearGolesChart();
        crearVictoriasChart();
    }
}




