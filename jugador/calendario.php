<?php
require_once "../config/conexion.php";

$usuario_id = $_SESSION['user']['id'] ?? 0;
$club_id    = $_SESSION['club_id']   ?? 0;

// Obtener equipo del jugador
$stmtJ = $pdo->prepare("SELECT j.id, j.equipo_id FROM jugadores j WHERE j.usuario_id = :uid LIMIT 1");
$stmtJ->execute([':uid' => $usuario_id]);
$mi_jugador   = $stmtJ->fetch(PDO::FETCH_ASSOC);
$jugador_id   = $mi_jugador['id']        ?? 0;
$mi_equipo_id = $mi_jugador['equipo_id'] ?? 0;

// Mes/año actual
$mes  = isset($_GET['mes'])  ? (int)$_GET['mes']  : (int)date('n');
$anio = isset($_GET['anio']) ? (int)$_GET['anio'] : (int)date('Y');
if ($mes < 1) $mes = 1; if ($mes > 12) $mes = 12;

$primerDia       = mktime(0,0,0,$mes,1,$anio);
$diasEnMes       = (int)date('t', $primerDia);
$diaInicioSemana = (int)date('w', $primerDia);
$fechaInicio     = date('Y-m-d');
$fechaFin        = date('Y-m-d', strtotime('+7 days'));

// Eventos totales y entrenamientos del mes del equipo del jugador
$stmtCnt = $pdo->prepare("SELECT COUNT(*) FROM entrenamientos WHERE equipo_id = :eid AND YEAR(fecha)=:a AND MONTH(fecha)=:m");
$stmtCnt->execute([':eid' => $mi_equipo_id, ':a' => $anio, ':m' => $mes]);
$totalEventos = $stmtCnt->fetchColumn();

$stmtEtot = $pdo->prepare("SELECT COUNT(*) FROM entrenamientos WHERE equipo_id = :eid");
$stmtEtot->execute([':eid' => $mi_equipo_id]);
$totalEntrenamientos = $stmtEtot->fetchColumn();

// Entrenamientos del mes
$stmtEM = $pdo->prepare("SELECT fecha, titulo, hora, lugar FROM entrenamientos WHERE equipo_id = :eid AND YEAR(fecha)=:a AND MONTH(fecha)=:m ORDER BY fecha, hora");
$stmtEM->execute([':eid' => $mi_equipo_id, ':a' => $anio, ':m' => $mes]);
$eventosDelMes = $stmtEM->fetchAll(PDO::FETCH_GROUP);

// Partidos del mes del equipo
$stmtPM = $pdo->prepare("
    SELECT p.fecha, el.nombre nombre_local, ev.nombre nombre_visitante, p.resultado, p.hora
    FROM partidos p
    LEFT JOIN equipos el ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
    WHERE YEAR(p.fecha)=:a AND MONTH(p.fecha)=:m
      AND (p.equipo_local_id=:eid OR p.equipo_visitante_id=:eid2)
    ORDER BY p.fecha, p.hora");
$stmtPM->execute([':a' => $anio, ':m' => $mes, ':eid' => $mi_equipo_id, ':eid2' => $mi_equipo_id]);
$partidosDelMes = $stmtPM->fetchAll(PDO::FETCH_GROUP);
$totalPartidos  = count(array_merge(...array_values($partidosDelMes ?: [[]])));

// Próximos entrenamientos
$stmtPE = $pdo->prepare("SELECT titulo, fecha, hora, lugar FROM entrenamientos WHERE equipo_id = :eid AND fecha BETWEEN :ini AND :fin ORDER BY fecha, hora LIMIT 6");
$stmtPE->execute([':eid' => $mi_equipo_id, ':ini' => $fechaInicio, ':fin' => $fechaFin]);
$proximosEventos = $stmtPE->fetchAll(PDO::FETCH_ASSOC);

// Próximos partidos
$stmtPP = $pdo->prepare("SELECT p.fecha, el.nombre nombre_local, ev.nombre nombre_visitante, p.hora
    FROM partidos p
    LEFT JOIN equipos el ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
    WHERE p.fecha BETWEEN :ini AND :fin
      AND (p.equipo_local_id=:eid OR p.equipo_visitante_id=:eid2)
    ORDER BY p.fecha LIMIT 6");
$stmtPP->execute([':ini' => $fechaInicio, ':fin' => $fechaFin, ':eid' => $mi_equipo_id, ':eid2' => $mi_equipo_id]);
$proximosPartidos = $stmtPP->fetchAll(PDO::FETCH_ASSOC);

// Tooltips JS
$jsEventos = [];
foreach ($eventosDelMes as $fecha => $eventos) {
    $lista = [];
    foreach ($eventos as $ev) {
        $hora  = substr($ev['hora'], 0, 5);
        $lugar = $ev['lugar'] ? ' - ' . htmlspecialchars($ev['lugar']) : '';
        $lista[] = '🔵 ' . htmlspecialchars($ev['titulo']) . " ($hora)$lugar";
    }
    $jsEventos[$fecha] = implode('<br>', $lista);
}
foreach ($partidosDelMes as $fecha => $parts) {
    foreach ($parts as $p) {
        $hora     = $p['hora'] ? ' ' . substr($p['hora'], 0, 5) : '';
        $resultado = $p['resultado'] ? " [{$p['resultado']}]" : '';
        $linea    = '⚽ ' . htmlspecialchars($p['nombre_local']) . ' vs ' . htmlspecialchars($p['nombre_visitante']) . $hora . $resultado;
        $jsEventos[$fecha] = isset($jsEventos[$fecha]) ? $jsEventos[$fecha] . '<br>' . $linea : $linea;
    }
}
?>
<style>
.calendario-contenedor {
    padding: 20px;
    font-family: 'Inter', sans-serif;
}

.calendario-contenedor h1 {
    font-size: 22px;
    margin: 0 0 4px;
}

.subtitle {
    color: #64748b;
    font-size: 14px;
    margin-bottom: 22px;
}

.stats-grid-full {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 22px;
}

.stat-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
}

.card-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.card-title {
    font-size: 13px;
    color: #6b7280;
    font-weight: 500;
}

.card-top i {
    font-size: 22px;
    color: #16a34a;
}

.card-number {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
}

.card-subtitle {
    font-size: 12px;
    color: #94a3b8;
    margin-top: 4px;
}

.calendar-main {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 22px;
}

.calendar-box {
    background: white;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
}

.calendar-header h2 {
    font-size: 1.2rem;
    text-transform: capitalize;
    margin: 0;
}

.btn-nav {
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    color: #10b981;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    font-size: 1.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
}

.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 6px;
    text-align: center;
}

.day-name {
    font-weight: 600;
    color: #64748b;
    padding: 10px 0;
    font-size: 0.85rem;
}

.day {
    min-height: 90px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 7px;
    background: #fafafa;
    position: relative;
    cursor: pointer;
    transition: all .2s;
}

.day:hover {
    background: #ecfdf5;
    border-color: #4ade80;
}

.day.has-event {
    background: #f0fdf4;
    border: 2px solid #16a34a;
}

.day.has-partido {
    background: #dbeafe;
    border: 2px solid #2563eb;
}

.day.has-partido .day-number {
    color: #1e40af;
    font-weight: 700;
}

.day.empty {
    background: transparent;
    border: none;
    pointer-events: none;
}

.day-number {
    font-size: 1rem;
    font-weight: 600;
    text-align: right;
}

.events-row {
    position: absolute;
    bottom: 6px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 3px;
}

.training-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #16a34a;
    display: inline-block;
}

.match-dot {
    width: 9px;
    height: 9px;
    border-radius: 50%;
    background: #2563eb;
    display: inline-block;
}

.calendar-leyenda {
    display: flex;
    gap: 18px;
    align-items: center;
    margin-bottom: 14px;
    font-size: .88rem;
    color: #64748b;
}

.calendar-leyenda span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.dot-verde {
    display: inline-block;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    background: #16a34a;
}

.dot-azul {
    display: inline-block;
    width: 11px;
    height: 11px;
    border-radius: 50%;
    background: #2563eb;
}

.sidebar-card {
    background: white;
    border-radius: 14px;
    padding: 20px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
    margin-bottom: 18px;
}

.sidebar-card h3 {
    font-size: 15px;
    margin: 0 0 14px;
    color: #1f2937;
}

.event-item {
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    gap: 10px;
    font-size: 13px;
}

.event-item:last-child {
    border-bottom: none;
}

.event-dot {
    width: 10px;
    height: 10px;
    background: #16a34a;
    border-radius: 50%;
    margin-top: 4px;
    flex-shrink: 0;
}

.event-dot.partido {
    background: #2563eb;
}

.no-events {
    color: #94a3b8;
    font-size: 13px;
    text-align: center;
    padding: 20px;
}

.tooltip-cal {
    position: fixed;
    background: #1e2937;
    color: white;
    padding: 12px 16px;
    border-radius: 8px;
    font-size: .88rem;
    pointer-events: none;
    z-index: 9999;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
    display: none;
    max-width: 280px;
    line-height: 1.45;
}

@media(max-width:900px) {
    .calendar-main {
        grid-template-columns: 1fr;
    }

    .stats-grid-full {
        grid-template-columns: 1fr 1fr;
    }
}
</style>

<div class="calendario-contenedor">
    <h1>Calendario</h1>
    <p class="subtitle">Entrenamientos y partidos de mi equipo</p>

    <div class="stats-grid-full">
        <div class="stat-card">
            <div class="card-top"><span class="card-title">Eventos este mes</span><i
                    class="fa-solid fa-calendar-check"></i></div>
            <div class="card-number"><?= $totalEventos ?></div>
            <div class="card-subtitle">Entrenamientos</div>
        </div>
        <div class="stat-card">
            <div class="card-top"><span class="card-title">Partidos este mes</span><i class="fa-solid fa-futbol"></i>
            </div>
            <div class="card-number"><?= $totalPartidos ?></div>
            <div class="card-subtitle">Programados</div>
        </div>
        <div class="stat-card">
            <div class="card-top"><span class="card-title">Total entrenamientos</span><i
                    class="fa-solid fa-dumbbell"></i></div>
            <div class="card-number"><?= $totalEntrenamientos ?></div>
            <div class="card-subtitle">De mi equipo</div>
        </div>
    </div>

    <div class="calendar-leyenda">
        <span><span class="dot-verde"></span> Entrenamientos</span>
        <span><span class="dot-azul"></span> Partidos</span>
    </div>

    <div class="calendar-main">
        <div class="calendar-box">
            <div class="calendar-header">
                <?php
                $mA = $mes-1; $aA = $anio;
                if ($mA<1){$mA=12;$aA--;}
                $mS = $mes+1; $aS = $anio;
                if ($mS>12){$mS=1;$aS++;}
                ?>
                <a href="menu.php?pagina=calendario&mes=<?= $mA ?>&anio=<?= $aA ?>" class="btn-nav">&lt;</a>
                <h2><?= ucfirst(strftime('%B %Y', $primerDia)) ?></h2>
                <a href="menu.php?pagina=calendario&mes=<?= $mS ?>&anio=<?= $aS ?>" class="btn-nav">&gt;</a>
            </div>

            <div class="calendar-grid">
                <?php foreach (['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $d): ?>
                <div class="day-name"><?= $d ?></div>
                <?php endforeach; ?>

                <?php for ($i = 0; $i < $diaInicioSemana; $i++): ?>
                <div class="day empty"></div>
                <?php endfor; ?>

                <?php for ($dia = 1; $dia <= $diasEnMes; $dia++): ?>
                <?php
                    $fechaDia   = date('Y-m-d', mktime(0,0,0,$mes,$dia,$anio));
                    $tieneEnt   = isset($eventosDelMes[$fechaDia]);
                    $tienePart  = isset($partidosDelMes[$fechaDia]);
                    $clase      = $tienePart ? 'has-partido' : ($tieneEnt ? 'has-event' : '');
                ?>
                <div class="day <?= $clase ?>" data-fecha="<?= $fechaDia ?>">
                    <div class="day-number"><?= $dia ?></div>
                    <div class="events-row">
                        <?php if ($tieneEnt):  ?><span class="training-dot"></span><?php endif; ?>
                        <?php if ($tienePart): ?><span class="match-dot"></span><?php endif; ?>
                    </div>
                </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- Sidebar próximos -->
        <div>
            <div class="sidebar-card">
                <h3><i class="fa-solid fa-dumbbell" style="color:#16a34a;"></i> Próximos entrenamientos</h3>
                <?php if (empty($proximosEventos)): ?>
                <div class="no-events">Sin entrenamientos esta semana.</div>
                <?php else: ?>
                <?php foreach ($proximosEventos as $ev): ?>
                <div class="event-item">
                    <div class="event-dot"></div>
                    <div>
                        <strong><?= htmlspecialchars($ev['titulo']) ?></strong><br>
                        <?= date('d/m', strtotime($ev['fecha'])) ?> · <?= date('H:i', strtotime($ev['hora'])) ?>h
                        <?= $ev['lugar'] ? ' · ' . htmlspecialchars($ev['lugar']) : '' ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="sidebar-card">
                <h3><i class="fa-solid fa-futbol" style="color:#2563eb;"></i> Próximos partidos</h3>
                <?php if (empty($proximosPartidos)): ?>
                <div class="no-events">Sin partidos esta semana.</div>
                <?php else: ?>
                <?php foreach ($proximosPartidos as $p): ?>
                <div class="event-item">
                    <div class="event-dot partido"></div>
                    <div>
                        <strong><?= htmlspecialchars($p['nombre_local']) ?> vs
                            <?= htmlspecialchars($p['nombre_visitante']) ?></strong><br>
                        <?= date('d/m/Y', strtotime($p['fecha'])) ?>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="tooltip-cal" id="tooltip-cal"></div>

<script>
const tooltip = document.getElementById('tooltip-cal');
const eventos = <?= json_encode($jsEventos) ?>;
document.querySelectorAll('.day.has-event, .day.has-partido').forEach(day => {
    day.addEventListener('mousemove', function(e) {
        const f = this.getAttribute('data-fecha');
        if (eventos[f]) {
            tooltip.innerHTML = eventos[f];
            tooltip.style.display = 'block';
            tooltip.style.left = (e.clientX + 14) + 'px';
            tooltip.style.top = (e.clientY + 12) + 'px';
        }
    });
    day.addEventListener('mouseleave', () => {
        tooltip.style.display = 'none';
    });
});
</script>