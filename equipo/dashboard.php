<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once '../config/conexion.php';

$club_id = (int)($_SESSION['club_id'] ?? 0);

// Jugadores del club (de todos sus equipos)
$stJug = $pdo->prepare("
    SELECT COUNT(*)
    FROM jugadores j
    INNER JOIN equipos e ON j.equipo_id = e.id
    WHERE e.equipo_id = :club_id
      AND (j.eliminado = 0 OR j.eliminado IS NULL)
");
$stJug->execute([':club_id' => $club_id]);
$totalJugadores = (int)$stJug->fetchColumn();

// Entrenamientos del club
$stEnt = $pdo->prepare("SELECT COUNT(*) FROM entrenamientos WHERE club_id = :club_id");
$stEnt->execute([':club_id' => $club_id]);
$totalEntrenamientos = (int)$stEnt->fetchColumn();

// Partidos próximos (futuros) del club, enlazados via equipos
$stPart = $pdo->prepare("
    SELECT COUNT(*)
    FROM partidos p
    LEFT JOIN equipos el ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
    WHERE p.fecha >= CURDATE()
      AND (el.equipo_id = :club_id OR ev.equipo_id = :club_id2)
");
$stPart->execute([':club_id' => $club_id, ':club_id2' => $club_id]);
$totalPartidos = (int)$stPart->fetchColumn();

// Total partidos jugados (histórico)
$stPartJug = $pdo->prepare("
    SELECT COUNT(*)
    FROM partidos p
    LEFT JOIN equipos el ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
    WHERE p.resultado IS NOT NULL AND p.resultado != ''
      AND (el.equipo_id = :club_id OR ev.equipo_id = :club_id2)
");
$stPartJug->execute([':club_id' => $club_id, ':club_id2' => $club_id]);
$totalPartidosJugados = (int)$stPartJug->fetchColumn();

// Victorias recientes (últimos 30 días) de equipos del club (según resultado)
$stVic = $pdo->prepare("
    SELECT COUNT(*)
    FROM partidos p
    LEFT JOIN equipos el ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
    WHERE p.fecha >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
      AND p.resultado IS NOT NULL AND p.resultado != ''
      AND (
        (el.equipo_id = :club_id AND CAST(SUBSTRING_INDEX(p.resultado,'-',1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(p.resultado,'-',-1) AS UNSIGNED))
        OR
        (ev.equipo_id = :club_id2 AND CAST(SUBSTRING_INDEX(p.resultado,'-',-1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(p.resultado,'-',1) AS UNSIGNED))
      )
");
$stVic->execute([':club_id' => $club_id, ':club_id2' => $club_id]);
$totalVictorias = (int)$stVic->fetchColumn();
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
        <p><?= $totalJugadores ?> registrados</p>
        <?php $barJug = $totalJugadores > 0 ? min(100, round($totalJugadores / max($totalJugadores, 30) * 100)) : 0; ?>
        <div class="bar-container"><div class="bar-fill" style="width: <?= $barJug ?>%;"></div></div>
    </div>
    <div class="box">
        <i class="fa-solid fa-futbol"></i>
        <h3>Partidos</h3>
        <p><?= (int)$totalPartidos ?> programados</p>
        <?php $barPart = ($totalPartidos + $totalPartidosJugados) > 0 ? min(100, round(($totalPartidos) / max($totalPartidos + $totalPartidosJugados, 1) * 100)) : 0; ?>
        <div class="bar-container"><div class="bar-fill" style="width: <?= $barPart ?>%;"></div></div>
    </div>
    <div class="box">
        <i class="fa-solid fa-dumbbell"></i>
        <h3>Entrenamientos</h3>
        <p><?= $totalEntrenamientos ?> entrenamientos pendientes</p>
        <?php $barEnt = $totalEntrenamientos > 0 ? min(100, round($totalEntrenamientos / max($totalEntrenamientos, 20) * 100)) : 0; ?>
        <div class="bar-container"><div class="bar-fill" style="width: <?= $barEnt ?>%;"></div></div>
    </div>
    <div class="box">
        <i class="fa-solid fa-trophy"></i>
        <h3>Victorias</h3>
        <p><?= (int)$totalVictorias ?> recientes</p>
        <?php $barVic = $totalVictorias > 0 && $totalPartidosJugados > 0 ? min(100, round($totalVictorias / $totalPartidosJugados * 100)) : 0; ?>
        <div class="bar-container"><div class="bar-fill" style="width: <?= $barVic ?>%;"></div></div>
    </div>
</div>