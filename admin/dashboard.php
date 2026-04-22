<?php
// Stats rápidos para el panel admin
$sqlTotalJugadores = "SELECT COUNT(*) as total FROM jugadores";
$resultadoTotal = $pdo->query($sqlTotalJugadores);
$totalJugadores = (int)($resultadoTotal->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

$sqlTotalUsuarios = "SELECT COUNT(*) as total FROM usuarios";
$resultadoUsuarios = $pdo->query($sqlTotalUsuarios);
$totalUsuarios = (int)($resultadoUsuarios->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

$sqlTotalAdmins = "SELECT COUNT(*) as total FROM usuarios WHERE rol = 'admin'";
$resultadoAdmins = $pdo->query($sqlTotalAdmins);
$totalAdmins = (int)($resultadoAdmins->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);
?>

<style>
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    .box {
        background: linear-gradient(145deg, #22c55e, #16a34a);
        color: white;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
        user-select: none;
    }

    .box:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 25px rgba(0,0,0,0.3);
    }

    .box:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.35), 0 15px 25px rgba(0,0,0,0.3);
    }

    .box i {
        font-size: 50px;
        margin-bottom: 15px;
    }

    .box h3 {
        margin: 10px 0 5px;
        font-size: 1.5rem;
    }

    .box p {
        font-size: 1rem;
    }

    .bar-container {
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        height: 8px;
        margin-top: 10px;
    }

    .bar-fill {
        height: 100%;
        border-radius: 10px;
        background: rgba(255,255,255,0.8);
        width: 0%;
        transition: width 1s ease-in-out;
    }
</style>

<div class="dashboard-grid">
    <div class="box" tabindex="0" role="button" aria-label="Abrir Jugadores" data-target="jugadores">
        <i class="fa-solid fa-user"></i>
        <h3>Jugadores</h3>
        <p><?= $totalJugadores ?> registrados</p>
        <div class="bar-container"><div class="bar-fill" style="width: 35%;"></div></div>
    </div>

    <div class="box" tabindex="0" role="button" aria-label="Abrir Crear Admin" data-target="crear_admin">
        <i class="fa-solid fa-user-shield"></i>
        <h3>Crear Admin</h3>
        <p><?= $totalAdmins ?> admins</p>
        <div class="bar-container"><div class="bar-fill" style="width: 55%;"></div></div>
    </div>

    <div class="box" tabindex="0" role="button" aria-label="Abrir Eliminar usuarios" data-target="eliminar_usuarios">
        <i class="fa-solid fa-user-xmark"></i>
        <h3>Eliminar usuarios</h3>
        <p><?= $totalUsuarios ?> usuarios</p>
        <div class="bar-container"><div class="bar-fill" style="width: 70%;"></div></div>
    </div>

    <div class="box" tabindex="0" role="button" aria-label="Cerrar sesión">
        <i class="fa-solid fa-right-from-bracket"></i>
        <h3>Cerrar sesión</h3>
        <p>Salir del panel</p>
        <div class="bar-container"><div class="bar-fill" style="width: 40%;"></div></div>
    </div>
</div>

<script>
(function () {
    function activarSeccion(id) {
        // Paneles
        document.querySelectorAll('.main > .page').forEach(p => p.classList.remove('active'));
        const page = document.getElementById(id);
        if (page) page.classList.add('active');

        // Menú
        const menuLinks = Array.from(document.querySelectorAll('.menu a'));
        menuLinks.forEach(a => a.classList.remove('active'));
        const link = menuLinks.find(a => (a.getAttribute('onclick') || '').includes("mostrarPagina('" + id + "')"));
        if (link) link.classList.add('active');
    }

    function onBoxClick(box) {
        const target = box.getAttribute('data-target');
        if (target) {
            activarSeccion(target);
        } else {
            window.location.href = '../logout.php';
        }
    }

    window.addEventListener('load', function () {
        document.querySelectorAll('.dashboard-grid .box').forEach(box => {
            box.addEventListener('click', function () { onBoxClick(box); });
            box.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    onBoxClick(box);
                }
            });
        });
    });
})();
</script>
