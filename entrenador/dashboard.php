<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/conexion.php';

$usuario_id = $_SESSION['user']['id'] ?? 0;
$mi_equipo_id = 0;
if ($usuario_id) {
    $stEq = $pdo->prepare("SELECT equipo_id FROM entrenadores WHERE usuario_id = :uid LIMIT 1");
    $stEq->execute([':uid' => $usuario_id]);
    $mi_equipo_id = (int)($stEq->fetchColumn() ?: 0);
}

$totalJugadores = 0;
$totalPartidosProgramados = 0;
$entrenamientosSemana = 0;
$victoriasRecientes = 0;

if ($mi_equipo_id > 0) {
    $stJ = $pdo->prepare("SELECT COUNT(*) FROM jugadores WHERE equipo_id = :eid AND (eliminado = 0 OR eliminado IS NULL)");
    $stJ->execute([':eid' => $mi_equipo_id]);
    $totalJugadores = (int)$stJ->fetchColumn();

    $stP = $pdo->prepare("
        SELECT COUNT(*)
        FROM partidos
        WHERE (equipo_local_id = :eid OR equipo_visitante_id = :eid2)
          AND fecha >= CURDATE()
    ");
    $stP->execute([':eid' => $mi_equipo_id, ':eid2' => $mi_equipo_id]);
    $totalPartidosProgramados = (int)$stP->fetchColumn();

    $stE = $pdo->prepare("
        SELECT COUNT(*)
        FROM entrenamientos
        WHERE equipo_id = :eid
          AND YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)
    ");
    $stE->execute([':eid' => $mi_equipo_id]);
    $entrenamientosSemana = (int)$stE->fetchColumn();

    $stV = $pdo->prepare("
        SELECT COUNT(*)
        FROM partidos p
        WHERE (p.equipo_local_id = :eid OR p.equipo_visitante_id = :eid2)
          AND p.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
          AND p.resultado IS NOT NULL AND p.resultado != ''
          AND (
            (p.equipo_local_id = :eid3 AND CAST(SUBSTRING_INDEX(p.resultado,'-',1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(p.resultado,'-',-1) AS UNSIGNED))
            OR
            (p.equipo_visitante_id = :eid4 AND CAST(SUBSTRING_INDEX(p.resultado,'-',-1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(p.resultado,'-',1) AS UNSIGNED))
          )
    ");
    $stV->execute([':eid' => $mi_equipo_id, ':eid2' => $mi_equipo_id, ':eid3' => $mi_equipo_id, ':eid4' => $mi_equipo_id]);
    $victoriasRecientes = (int)$stV->fetchColumn();
}
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
        <p><?= (int)$totalJugadores ?> registrados</p>
        <div class="bar-container"><div class="bar-fill" style="width: 80%;"></div></div>
    </div>
    <div class="box">
        <i class="fa-solid fa-futbol"></i>
        <h3>Partidos</h3>
        <p><?= (int)$totalPartidosProgramados ?> programados</p>
        <div class="bar-container"><div class="bar-fill" style="width: 60%;"></div></div>
    </div>
    <div class="box">
        <i class="fa-solid fa-dumbbell"></i>
        <h3>Entrenamientos</h3>
        <p><?= (int)$entrenamientosSemana ?> esta semana</p>
        <div class="bar-container"><div class="bar-fill" style="width: 50%;"></div></div>
    </div>
    <div class="box">
        <i class="fa-solid fa-trophy"></i>
        <h3>Victorias</h3>
        <p><?= (int)$victoriasRecientes ?> recientes</p>
        <div class="bar-container"><div class="bar-fill" style="width: 70%;"></div></div>
    </div>
</div>
