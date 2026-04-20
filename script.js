function mostrarPagina(id, linkElement) {
    let pages = document.querySelectorAll('.main .page');
    pages.forEach((p) => p.classList.remove('active'));

    let links = document.querySelectorAll('.menu a');
    links.forEach((l) => l.classList.remove('active'));

    let pagina = document.getElementById(id);
    if (pagina) {
        pagina.classList.add('active');
    }

    let enlaceActivo = linkElement || (typeof event !== 'undefined' ? event.currentTarget : null);
    if (enlaceActivo) {
        enlaceActivo.classList.add('active');
    }

    if (id === 'estadisticas') {
        if (typeof crearRendimientoChart === 'function') {
            crearRendimientoChart();
        }
        if (typeof crearGolesChart === 'function') {
            crearGolesChart();
        }
        if (typeof crearVictoriasChart === 'function') {
            crearVictoriasChart();
        }
    }
}
