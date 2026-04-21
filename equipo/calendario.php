<?php

require_once "../config/conexion.php";

$club_id = $_SESSION['club_id'] ?? 0;

$mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : date('n');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : date('Y');

if ($mes < 1)  { $mes = 1; }
if ($mes > 12) { $mes = 12; }

$sqlEventos = "SELECT COUNT(*) FROM entrenamientos 
               WHERE club_id = :club_id 
                 AND YEAR(fecha) = :anio 
                 AND MONTH(fecha) = :mes";
$stmt = $pdo->prepare($sqlEventos);
$stmt->execute(['club_id' => $club_id, 'anio' => $anio, 'mes' => $mes]);
$totalEventos = $stmt->fetchColumn();

$sqlEntrenamientos = "SELECT COUNT(*) FROM entrenamientos WHERE club_id = :club_id";
$stmt = $pdo->prepare($sqlEntrenamientos);
$stmt->execute(['club_id' => $club_id]);
$totalEntrenamientos = $stmt->fetchColumn();

$fechaInicio = date('Y-m-d');
$fechaFin    = date('Y-m-d', strtotime('+7 days'));

$sqlProximos = "
    SELECT titulo as evento, fecha, hora, lugar 
    FROM entrenamientos 
    WHERE club_id = :club_id 
      AND fecha BETWEEN :inicio AND :fin
    ORDER BY fecha ASC, hora ASC
    LIMIT 6";

$stmt = $pdo->prepare($sqlProximos);
$stmt->execute([
    'club_id' => $club_id,
    'inicio'  => $fechaInicio,
    'fin'     => $fechaFin
]);
$proximosEventos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$primerDia = mktime(0, 0, 0, $mes, 1, $anio);
$diasEnMes = date('t', $primerDia);
$diaInicioSemana = date('w', $primerDia);

$sqlEventosMes = "
    SELECT fecha, titulo, hora, lugar 
    FROM entrenamientos 
    WHERE club_id = :club_id 
      AND YEAR(fecha) = :anio 
      AND MONTH(fecha) = :mes
    ORDER BY fecha, hora";

$stmt = $pdo->prepare($sqlEventosMes);
$stmt->execute(['club_id' => $club_id, 'anio' => $anio, 'mes' => $mes]);
$eventosDelMes = $stmt->fetchAll(PDO::FETCH_GROUP);

$jsEventos = [];
foreach ($eventosDelMes as $fecha => $eventos) {
    $lista = [];
    foreach ($eventos as $ev) {
        $hora = substr($ev['hora'], 0, 5);
        $lugar = $ev['lugar'] ? ' - ' . htmlspecialchars($ev['lugar']) : '';
        $lista[] = htmlspecialchars($ev['titulo']) . " ($hora)$lugar";
    }
    $jsEventos[$fecha] = implode("<br>", $lista);
}
?>

<div class="calendario-contenedor">

    <h1>Calendario</h1>
    <p class="subtitle">Planificación de entrenamientos</p>

    <div class="stats-grid-full">
        <div class="stat-card">
            <div class="card-top">
                <span class="card-title">Eventos Totales</span>
                <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div class="card-number"><?= $totalEventos ?></div>
            <div class="card-subtitle">Este mes</div>
        </div>

        <div class="stat-card">
            <div class="card-top">
                <span class="card-title">Entrenamientos</span>
                <i class="fa-solid fa-dumbbell"></i>
            </div>
            <div class="card-number"><?= $totalEntrenamientos ?></div>
            <div class="card-subtitle">Programados</div>
        </div>

        <div class="stat-card">
            <div class="card-top">
                <span class="card-title">Partidos</span>
                <i class="fa-solid fa-futbol"></i>
            </div>
            <div class="card-number">3</div>
            <div class="card-subtitle">Programados</div>
        </div>
    </div>

    <div class="calendar-main">

        <div class="calendar-box">
            <div class="calendar-header">
                <?php
                $mesAnterior = $mes - 1;
                $anioAnterior = $anio;
                if ($mesAnterior < 1) {
                    $mesAnterior = 12;
                    $anioAnterior = $anio - 1;
                }

                $mesSiguiente = $mes + 1;
                $anioSiguiente = $anio;
                if ($mesSiguiente > 12) {
                    $mesSiguiente = 1;
                    $anioSiguiente = $anio + 1;
                }
                ?>

                <a href="menu.php?pagina=calendario&mes=<?= $mesAnterior ?>&anio=<?= $anioAnterior ?>" 
                   class="btn-nav">&lt;</a>

                <h2><?= date("F Y", $primerDia) ?></h2>

                <a href="menu.php?pagina=calendario&mes=<?= $mesSiguiente ?>&anio=<?= $anioSiguiente ?>" 
                   class="btn-nav">&gt;</a>
            </div>

            <div class="calendar-grid">
                <div class="day-name">Dom</div>
                <div class="day-name">Lun</div>
                <div class="day-name">Mar</div>
                <div class="day-name">Mié</div>
                <div class="day-name">Jue</div>
                <div class="day-name">Vie</div>
                <div class="day-name">Sáb</div>

                <?php
                for ($i = 0; $i < $diaInicioSemana; $i++) {
                    echo '<div class="day empty"></div>';
                }

                for ($dia = 1; $dia <= $diasEnMes; $dia++) {
                    $fechaActual = sprintf("%04d-%02d-%02d", $anio, $mes, $dia);
                    $claseEvento = isset($eventosDelMes[$fechaActual]) ? 'has-event' : '';

                    echo "<div class='day $claseEvento' data-fecha='$fechaActual'>";
                    echo "<div class='day-number'>$dia</div>";
                    if (isset($eventosDelMes[$fechaActual])) {
                        echo "<div class='event-indicator'>●</div>";
                    }
                    echo "</div>";
                }
                ?>
            </div>
        </div>

        <div class="next-events-box">
            <h3>Próximos Entrenamientos</h3>
            <p class="next-subtitle">Próximos 7 días</p>

            <?php if (empty($proximosEventos)): ?>
            <p class="no-events">No hay entrenamientos próximos</p>
            <?php else: ?>
            <?php foreach ($proximosEventos as $ev): 
                    $hora = substr($ev['hora'] ?? '00:00', 0, 5);
                ?>
            <div class="event-item">
                <div class="event-dot"></div>
                <div>
                    <strong><?= htmlspecialchars($ev['evento']) ?></strong><br>
                    <small>
                        <?= date("d M", strtotime($ev['fecha'])) ?> • <?= $hora ?>
                        <?= $ev['lugar'] ? ' • ' . htmlspecialchars($ev['lugar']) : '' ?>
                    </small>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="tooltip" class="tooltip"></div>

<style>
.calendario-contenedor { padding: 20px; }
.subtitle { color: #64748b; margin-bottom: 30px; font-size: 1.1rem; }

.stats-grid-full {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 40px;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 28px 24px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
    transition: transform 0.3s ease;
    text-align: center;
}

.stat-card:hover { transform: translateY(-4px); }

.card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.card-title {
    font-size: 1.05rem;
    color: #64748b;
    font-weight: 500;
}

.stat-card i {
    font-size: 2.2rem;
    color: #10b981;
}

.card-number {
    font-size: 2.85rem;
    font-weight: 700;
    color: #1e2937;
    margin-bottom: 8px;
    line-height: 1;
}

.card-subtitle {
    font-size: 0.98rem;
    color: #16a34a;
    font-weight: 500;
}

.calendar-main {
    display: flex;
    gap: 25px;
    flex-wrap: wrap;
}

.calendar-box, .next-events-box {
    background: white;
    border-radius: 16px;
    padding: 25px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.07);
}

.calendar-box { flex: 2; min-width: 650px; }
.next-events-box { flex: 1; min-width: 320px; align-self: flex-start; }

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.btn-nav {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #10b981;
    width: 48px;
    height: 48px;
    border-radius: 50%;
    font-size: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 8px;
    text-align: center;
}

.day-name {
    font-weight: 600;
    color: #64748b;
    padding: 12px 0;
    font-size: 0.92rem;
}

.day {
    min-height: 108px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 8px;
    background: #fafafa;
    position: relative;
    cursor: pointer;
    transition: all 0.2s;
}

.day:hover { background: #ecfdf5; border-color: #4ade80; }
.day.has-event { background: #f0fdf4; border: 2px solid #10b981; }

.day-number {
    font-size: 1.25rem;
    font-weight: 600;
    text-align: right;
}

.event-indicator {
    position: absolute;
    bottom: 10px;
    left: 50%;
    transform: translateX(-50%);
    color: #10b981;
    font-size: 14px;
}

.next-subtitle {
    color: #64748b;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.event-item {
    padding: 14px 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    gap: 12px;
}

.event-item:last-child { border-bottom: none; }

.event-dot {
    width: 11px;
    height: 11px;
    background: #3b82f6;
    border-radius: 50%;
    margin-top: 5px;
    flex-shrink: 0;
}

.no-events {
    color: #94a3b8;
    text-align: center;
    padding: 50px 20px;
}

.tooltip {
    position: absolute;
    background: #1e2937;
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 0.92rem;
    pointer-events: none;
    z-index: 1000;
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
    display: none;
    max-width: 280px;
    line-height: 1.45;
}
</style>

<script>
const tooltip = document.getElementById('tooltip');
const eventosJS = <?= json_encode($jsEventos) ?>;

document.querySelectorAll('.day.has-event').forEach(day => {
    day.addEventListener('mouseover', function(e) {
        const fecha = this.getAttribute('data-fecha');
        if (eventosJS[fecha]) {
            tooltip.innerHTML = eventosJS[fecha];
            tooltip.style.display = 'block';
            tooltip.style.left = (e.pageX + 15) + 'px';
            tooltip.style.top  = (e.pageY + 10) + 'px';
        }
    });

    day.addEventListener('mouseout', () => {
        tooltip.style.display = 'none';
    });
});
</script>