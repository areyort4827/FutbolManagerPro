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
}
.box:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 25px rgba(0,0,0,0.3);
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

/* Mini barras para mostrar progreso de victorias, partidos, etc. */
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
    <div class="box">
        <i class="fa-solid fa-user"></i>
        <h3>Jugadores</h3>
        <p>25 registrados</p>
        <div class="bar-container"><div class="bar-fill" style="width: 100%;"></div></div>
    </div>
    <div class="box">
        <i class="fa-solid fa-futbol"></i>
        <h3>Partidos</h3>
        <p>12 programados</p>
        <div class="bar-container"><div class="bar-fill" style="width: 75%;"></div></div>
    </div>
    <div class="box">
        <i class="fa-solid fa-dumbbell"></i>
        <h3>Entrenamientos</h3>
        <p>8 esta semana</p>
        <div class="bar-container"><div class="bar-fill" style="width: 50%;"></div></div>
    </div>
    <div class="box">
        <i class="fa-solid fa-trophy"></i>
        <h3>Victorias</h3>
        <p>4 recientes</p>
        <div class="bar-container"><div class="bar-fill" style="width: 70%;"></div></div>
    </div>
</div>