<?php
require_once "../config/conexion.php";

$club_id = $_SESSION['club_id'] ?? 0;

// ── Partidos: totales, victorias, empates, derrotas ──────────────────────────
$sqlPartidos = "
    SELECT COUNT(*) AS total,
           SUM(CASE
               WHEN p.equipo_local_id IN (SELECT id FROM equipos WHERE equipo_id = :club_id)
                    AND CAST(SUBSTRING_INDEX(p.resultado,'-',1) AS UNSIGNED)
                      > CAST(SUBSTRING_INDEX(p.resultado,'-',-1) AS UNSIGNED) THEN 1
               WHEN p.equipo_visitante_id IN (SELECT id FROM equipos WHERE equipo_id = :club_id2)
                    AND CAST(SUBSTRING_INDEX(p.resultado,'-',-1) AS UNSIGNED)
                      > CAST(SUBSTRING_INDEX(p.resultado,'-',1) AS UNSIGNED) THEN 1
               ELSE 0 END) AS victorias,
           SUM(CASE WHEN SUBSTRING_INDEX(p.resultado,'-',1) = SUBSTRING_INDEX(p.resultado,'-',-1) THEN 1 ELSE 0 END) AS empates,
           SUM(CAST(SUBSTRING_INDEX(p.resultado,'-',1) AS UNSIGNED)) AS goles_local_total,
           SUM(CAST(SUBSTRING_INDEX(p.resultado,'-',-1) AS UNSIGNED)) AS goles_visitante_total
    FROM partidos p
    WHERE p.resultado IS NOT NULL AND p.resultado != ''
      AND (p.equipo_local_id IN (SELECT id FROM equipos WHERE equipo_id = :club_id3)
        OR p.equipo_visitante_id IN (SELECT id FROM equipos WHERE equipo_id = :club_id4))";

$stmtP = $pdo->prepare($sqlPartidos);
$stmtP->execute([
    ':club_id' => $club_id, ':club_id2' => $club_id,
    ':club_id3' => $club_id, ':club_id4' => $club_id
]);
$statsPartidos = $stmtP->fetch(PDO::FETCH_ASSOC);

$total    = (int)($statsPartidos['total'] ?? 0);
$victorias = (int)($statsPartidos['victorias'] ?? 0);
$empates   = $total > 0 ? $total - $victorias - max(0, $total - $victorias - (int)($statsPartidos['empates']??0)) : 0;
$empates   = (int)($statsPartidos['empates'] ?? 0);
$derrotas  = $total - $victorias - $empates;
$pct_victorias = $total > 0 ? round($victorias / $total * 100) : 0;
$goles_favor   = (int)($statsPartidos['goles_local_total'] ?? 0);
$goles_contra  = (int)($statsPartidos['goles_visitante_total'] ?? 0);
$diferencia_goles = $goles_favor - $goles_contra;

// ── Asistencia media a entrenamientos ────────────────────────────────────────
$sqlAsist = "
    SELECT ROUND(AVG(e.num_asistentes), 1) AS media
    FROM entrenamientos e
    WHERE e.club_id = :club_id AND e.num_asistentes > 0";
$stmtA = $pdo->prepare($sqlAsist);
$stmtA->execute([':club_id' => $club_id]);
$asistencia_media = $stmtA->fetchColumn() ?? 'N/A';

// ── Rendimiento mensual (últimos 6 meses) ────────────────────────────────────
$meses_labels = [];
$data_victorias = [];
$data_empates = [];
$data_derrotas = [];

for ($i = 5; $i >= 0; $i--) {
    $ts   = strtotime("-$i months");
    $mes  = date('m', $ts);
    $anio = date('Y', $ts);
    $meses_labels[] = date('M', $ts);

    $sqlMes = "
        SELECT
            SUM(CASE
                WHEN eq_local.equipo_id = :club AND
                     CAST(SUBSTRING_INDEX(resultado,'-',1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(resultado,'-',-1) AS UNSIGNED) THEN 1
                WHEN eq_vis.equipo_id = :club2 AND
                     CAST(SUBSTRING_INDEX(resultado,'-',-1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(resultado,'-',1) AS UNSIGNED) THEN 1
                ELSE 0 END) AS v,
            SUM(CASE WHEN SUBSTRING_INDEX(resultado,'-',1) = SUBSTRING_INDEX(resultado,'-',-1) THEN 1 ELSE 0 END) AS e,
            COUNT(*) AS t
        FROM partidos p
        LEFT JOIN equipos eq_local ON p.equipo_local_id = eq_local.id
        LEFT JOIN equipos eq_vis   ON p.equipo_visitante_id = eq_vis.id
        WHERE MONTH(p.fecha) = :mes AND YEAR(p.fecha) = :anio
          AND p.resultado IS NOT NULL AND p.resultado != ''
          AND (eq_local.equipo_id = :club3 OR eq_vis.equipo_id = :club4)";

    $s = $pdo->prepare($sqlMes);
    $s->execute([':club'=>$club_id,':club2'=>$club_id,':club3'=>$club_id,':club4'=>$club_id,':mes'=>$mes,':anio'=>$anio]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    $v = (int)($r['v'] ?? 0);
    $e = (int)($r['e'] ?? 0);
    $t = (int)($r['t'] ?? 0);
    $data_victorias[] = $v;
    $data_empates[]   = $e;
    $data_derrotas[]  = max(0, $t - $v - $e);
}

// ── Evolución de goles (últimos 6 meses) ─────────────────────────────────────
$data_goles_favor  = [];
$data_goles_contra = [];

for ($i = 5; $i >= 0; $i--) {
    $ts   = strtotime("-$i months");
    $mes  = date('m', $ts);
    $anio = date('Y', $ts);

    $sqlG = "
        SELECT
            SUM(CAST(SUBSTRING_INDEX(resultado,'-',1)  AS UNSIGNED)) AS gf,
            SUM(CAST(SUBSTRING_INDEX(resultado,'-',-1) AS UNSIGNED)) AS gc
        FROM partidos p
        LEFT JOIN equipos eq_local ON p.equipo_local_id = eq_local.id
        LEFT JOIN equipos eq_vis   ON p.equipo_visitante_id = eq_vis.id
        WHERE MONTH(p.fecha) = :mes AND YEAR(p.fecha) = :anio
          AND p.resultado IS NOT NULL AND p.resultado != ''
          AND (eq_local.equipo_id = :club OR eq_vis.equipo_id = :club2)";
    $sg = $pdo->prepare($sqlG);
    $sg->execute([':club'=>$club_id,':club2'=>$club_id,':mes'=>$mes,':anio'=>$anio]);
    $rg = $sg->fetch(PDO::FETCH_ASSOC);
    $data_goles_favor[]  = (int)($rg['gf'] ?? 0);
    $data_goles_contra[] = (int)($rg['gc'] ?? 0);
}

// ── Máximos goleadores (desde tabla estadisticas_jugador) ────────────────────
$sqlGoleadores = "
    SELECT j.nombre, j.posicion,
           SUM(ej.goles) AS total_goles,
           SUM(ej.asistencias) AS total_asistencias
    FROM estadisticas_jugador ej
    JOIN jugadores j ON ej.jugador_id = j.id
    JOIN equipos eq ON j.equipo_id = eq.id
    WHERE eq.equipo_id = :club_id
    GROUP BY j.id, j.nombre, j.posicion
    ORDER BY total_goles DESC
    LIMIT 5";
$stmtG = $pdo->prepare($sqlGoleadores);
$stmtG->execute([':club_id' => $club_id]);
$goleadores = $stmtG->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - Análisis del rendimiento del club</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',Tahoma,sans-serif; background:#f8fafc; color:#1f2937; line-height:1.5; }
    .container { padding:30px; }
    h2 { font-size:24px; }
    .subtitle { color:#6b7280; font-size:14px; margin-bottom:30px; }
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:24px; margin-bottom:40px; }
    .card { background:white; border-radius:20px; padding:28px 24px; box-shadow:0 10px 15px -3px rgba(0,0,0,.1); transition:transform .3s; min-height:160px; }
    .card:hover { transform:translateY(-6px); }
    .card-header { display:flex; justify-content:space-between; align-items:flex-start; height:100%; }
    .card-title { font-size:.95rem; color:#6b7280; margin-bottom:10px; }
    .card-value { font-size:2.7rem; font-weight:700; color:#1f2937; line-height:1; }
    .card-sub { font-size:.95rem; margin-top:8px; }
    .icon { font-size:3.2rem; color:#10b981; flex-shrink:0; }
    .charts-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(500px,1fr)); gap:30px; margin-bottom:40px; }
    .chart-card { background:white; border-radius:24px; padding:30px; box-shadow:0 10px 15px -3px rgba(0,0,0,.1); }
    .chart-title { font-size:1.35rem; font-weight:600; margin-bottom:25px; color:#1f2937; }
    .top-scorers { background:white; border-radius:24px; padding:30px; box-shadow:0 10px 15px -3px rgba(0,0,0,.1); }
    .player { display:flex; align-items:center; background:#f8fafc; padding:20px; border-radius:16px; margin-bottom:16px; }
    .player:last-child { margin-bottom:0; }
    .rank { width:52px; height:52px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:bold; color:white; }
    .rank-1 { background:#fbbf24; } .rank-2 { background:#9ca3af; } .rank-3,.rank-4,.rank-5 { background:#cd7c2f; }
    .player-info { flex:1; margin-left:20px; }
    .player-name { font-weight:600; font-size:1.1rem; }
    .goals { font-size:2.2rem; font-weight:700; color:#10b981; text-align:right; }
    .no-data { color:#94a3b8; text-align:center; padding:40px; font-size:1rem; }
    @media(max-width:1024px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:640px){ .stats-grid{ grid-template-columns:1fr; } .charts-grid{ grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="container">
    <h2>Estadísticas</h2>
    <p class="subtitle">Análisis del rendimiento del club</p>

    <div class="stats-grid">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Partidos Jugados</div>
                    <div class="card-value"><?= $total ?></div>
                    <div class="card-sub" style="color:#10b981;"><?= $victorias ?>V - <?= $empates ?>E - <?= $derrotas ?>D</div>
                </div>
                <div class="icon"><i class="fas fa-futbol"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">% Victorias</div>
                    <div class="card-value"><?= $pct_victorias ?>%</div>
                    <div class="card-sub" style="color:#10b981;">Tasa de éxito</div>
                </div>
                <div class="icon"><i class="fas fa-trophy"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Diferencia Goles</div>
                    <div class="card-value" style="color:<?= $diferencia_goles >= 0 ? '#10b981' : '#ef4444' ?>">
                        <?= ($diferencia_goles >= 0 ? '+' : '') . $diferencia_goles ?>
                    </div>
                    <div class="card-sub"><?= $goles_favor ?> a favor / <?= $goles_contra ?> en contra</div>
                </div>
                <div class="icon"><i class="fas fa-exchange-alt"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Asistencia Media</div>
                    <div class="card-value"><?= $asistencia_media ?></div>
                    <div class="card-sub">A entrenamientos</div>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-title">Rendimiento Mensual</div>
            <canvas id="rendimientoChart" height="110"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-title">Evolución de Goles</div>
            <canvas id="golesChart" height="110"></canvas>
        </div>
    </div>

    <div class="top-scorers">
        <div class="chart-title">Máximos Goleadores</div>
        <?php if (empty($goleadores)): ?>
            <div class="no-data">
                <i class="fas fa-chart-bar" style="font-size:2rem; margin-bottom:10px; display:block;"></i>
                Sin estadísticas registradas aún. Los datos aparecerán al registrar goles por partido.
            </div>
        <?php else: ?>
            <?php foreach ($goleadores as $i => $g): ?>
            <div class="player">
                <div class="rank rank-<?= $i+1 ?>"><?= $i+1 ?></div>
                <div class="player-info">
                    <div class="player-name"><?= htmlspecialchars($g['nombre']) ?></div>
                    <div style="color:#6b7280; font-size:.95rem;"><?= (int)$g['total_asistencias'] ?> asistencias</div>
                </div>
                <div>
                    <div class="goals"><?= (int)$g['total_goles'] ?></div>
                    <div style="text-align:right; font-size:.85rem; color:#6b7280;">goles</div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
const meses = <?= json_encode($meses_labels) ?>;

new Chart(document.getElementById('rendimientoChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [
            { label:'Victorias', data: <?= json_encode($data_victorias) ?>, backgroundColor:'#10b981' },
            { label:'Empates',   data: <?= json_encode($data_empates)   ?>, backgroundColor:'#eab308' },
            { label:'Derrotas',  data: <?= json_encode($data_derrotas)  ?>, backgroundColor:'#ef4444' }
        ]
    },
    options: { responsive:true, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true}} }
});

new Chart(document.getElementById('golesChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: meses,
        datasets: [
            { label:'Goles a favor',   data: <?= json_encode($data_goles_favor)  ?>, borderColor:'#10b981', backgroundColor:'rgba(16,185,129,.1)', tension:.4, borderWidth:3 },
            { label:'Goles en contra', data: <?= json_encode($data_goles_contra) ?>, borderColor:'#ef4444', backgroundColor:'rgba(239,68,68,.1)',    tension:.4, borderWidth:3 }
        ]
    },
    options: { responsive:true, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true}} }
});
</script>
</body>
</html>
