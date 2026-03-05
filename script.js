function mostrarPagina(id){

let pages = document.querySelectorAll('.page');
pages.forEach(p=>p.classList.remove('active'));  // Quita la pagina activa

let links = document.querySelectorAll('.menu a');
links.forEach(l=>l.classList.remove('active')); // Quita el menu activo


document.getElementById(id).classList.add('active'); //Añade la nueva pagina activa

event.target.classList.add('active'); // Añade el nuevo menu activo
}