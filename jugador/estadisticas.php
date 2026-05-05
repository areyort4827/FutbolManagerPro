<?php
require_once "../config/conexion.php";

$usuario_id = $_SESSION['user']['id'] ?? 0;

// Obtener jugador
$stmtJ = $pdo->prepare("SELECT j.id, j.nombre, j.posicion, j.equipo_id,
                                eq.nombre AS nombre_equipo, eq.equipo_id AS club_id_eq
                         FROM jugadores j
                         LEFT JOIN equipos eq ON j.equipo_id = eq.id
                         WHERE j.usuario_id = :uid LIMIT 1");
$stmtJ->execute([':uid' => $usuario_id]);
$mi_jugador   = $stmtJ->fetch(PDO::FETCH_ASSOC);
$jugador_id   = $mi_jugador['id']        ?? 0;
$mi_equipo_id = $mi_jugador['equipo_id'] ?? 0;
$club_id      = $mi_jugador['club_id_eq'] ?? ($_SESSION['club_id'] ?? 0);

// Estadísticas totales personales
$total_goles = $total_asistencias = $partidos_jugados = $total_minutos = $amarillas = $rojas = 0;
if ($jugador_id > 0) {
    $s = $pdo->prepare("SELECT COALESCE(SUM(goles),0) g, COALESCE(SUM(asistencias),0) a,
                               COUNT(*) c, COALESCE(SUM(minutos_jugados),0) m,
                               COALESCE(SUM(tarjetas_amarillas),0) ta,
                               COALESCE(SUM(tarjetas_rojas),0) tr
                        FROM estadisticas_jugador WHERE jugador_id = :jid");
    $s->execute([':jid' => $jugador_id]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    $total_goles       = (int)$r['g'];
    $total_asistencias = (int)$r['a'];
    $partidos_jugados  = (int)$r['c'];
    $total_minutos     = (int)$r['m'];
    $amarillas         = (int)$r['ta'];
    $rojas             = (int)$r['tr'];
}

// Asistencia a entrenamientos
$pct_asistencia = 0;
if ($jugador_id > 0 && $mi_equipo_id > 0) {
    $stTotal = $pdo->prepare("SELECT COUNT(*) FROM entrenamientos WHERE equipo_id = :eid");
    $stTotal->execute([':eid' => $mi_equipo_id]);
    $totalEntr = (int)$stTotal->fetchColumn();

    $stAs = $pdo->prepare("
        SELECT COUNT(*) 
        FROM entrenamiento_asistencia ea
        JOIN entrenamientos e ON e.id = ea.entrenamiento_id
        WHERE ea.jugador_id = :jid AND ea.asistio = 1 AND e.equipo_id = :eid
    ");
    $stAs->execute([':jid' => $jugador_id, ':eid' => $mi_equipo_id]);
    $asistidos = (int)$stAs->fetchColumn();

    $pct_asistencia = $totalEntr > 0 ? round($asistidos / $totalEntr * 100) : 0;
}

// Rendimiento mensual (últimos 6 meses) - goles y asistencias del jugador
$meses_labels = [];
$data_goles_mes = [];
$data_asist_mes = [];
for ($i = 5; $i >= 0; $i--) {
    $ts   = strtotime("-$i months");
    $mes  = date('m', $ts);
    $anio = date('Y', $ts);
    $meses_labels[] = date('M', $ts);
    $sm = $pdo->prepare("SELECT COALESCE(SUM(ej.goles),0) g, COALESCE(SUM(ej.asistencias),0) a
                         FROM estadisticas_jugador ej
                         JOIN partidos p ON ej.partido_id = p.id
                         WHERE ej.jugador_id = :jid AND MONTH(p.fecha)=:mes AND YEAR(p.fecha)=:anio");
    $sm->execute([':jid'=>$jugador_id,':mes'=>$mes,':anio'=>$anio]);
    $rm = $sm->fetch(PDO::FETCH_ASSOC);
    $data_goles_mes[] = (int)$rm['g'];
    $data_asist_mes[] = (int)$rm['a'];
}

// Resultados del equipo (últimos 6 meses)
$data_victorias = []; $data_empates = []; $data_derrotas = [];
for ($i = 5; $i >= 0; $i--) {
    $ts   = strtotime("-$i months");
    $mes  = date('m', $ts);
    $anio = date('Y', $ts);
    $sq = $pdo->prepare("
        SELECT SUM(CASE
            WHEN eq_l.id = :eid AND CAST(SUBSTRING_INDEX(resultado,'-',1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(resultado,'-',-1) AS UNSIGNED) THEN 1
            WHEN eq_v.id = :eid2 AND CAST(SUBSTRING_INDEX(resultado,'-',-1) AS UNSIGNED) > CAST(SUBSTRING_INDEX(resultado,'-',1) AS UNSIGNED) THEN 1
            ELSE 0 END) v,
               SUM(CASE WHEN SUBSTRING_INDEX(resultado,'-',1)=SUBSTRING_INDEX(resultado,'-',-1) THEN 1 ELSE 0 END) e,
               COUNT(*) t
        FROM partidos p
        LEFT JOIN equipos eq_l ON p.equipo_local_id = eq_l.id
        LEFT JOIN equipos eq_v ON p.equipo_visitante_id = eq_v.id
        WHERE (p.equipo_local_id=:eid3 OR p.equipo_visitante_id=:eid4)
          AND p.resultado IS NOT NULL AND p.resultado!=''
          AND MONTH(p.fecha)=:mes AND YEAR(p.fecha)=:anio");
    $sq->execute([':eid'=>$mi_equipo_id,':eid2'=>$mi_equipo_id,':eid3'=>$mi_equipo_id,':eid4'=>$mi_equipo_id,':mes'=>$mes,':anio'=>$anio]);
    $rq = $sq->fetch(PDO::FETCH_ASSOC);
    $v = (int)($rq['v']??0); $e = (int)($rq['e']??0); $t = (int)($rq['t']??0);
    $data_victorias[] = $v; $data_empates[] = $e; $data_derrotas[] = max(0,$t-$v-$e);
}

// Top goleadores del equipo
$topGoleadores = [];
if ($mi_equipo_id > 0) {
    $stg = $pdo->prepare("SELECT j.nombre, j.posicion,
                                  COALESCE(SUM(ej.goles),0) total_goles,
                                  COALESCE(SUM(ej.asistencias),0) total_asistencias
                           FROM estadisticas_jugador ej
                           JOIN jugadores j ON ej.jugador_id = j.id
                           WHERE j.equipo_id = :eid
                           GROUP BY j.id, j.nombre, j.posicion
                           ORDER BY total_goles DESC LIMIT 5");
    $stg->execute([':eid' => $mi_equipo_id]);
    $topGoleadores = $stg->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Estadísticas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:'Segoe UI',Tahoma,sans-serif; background:#f8fafc; color:#1f2937; }
    .container { padding:30px; }
    h2 { font-size:24px; } .subtitle { color:#6b7280; font-size:14px; margin-bottom:30px; }
    .stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:20px; margin-bottom:36px; }
    .card { background:white; border-radius:18px; padding:26px 22px; box-shadow:0 10px 15px -3px rgba(0,0,0,.08); transition:transform .3s; }
    .card:hover { transform:translateY(-5px); }
    .card-header { display:flex; justify-content:space-between; align-items:flex-start; }
    .card-title { font-size:.9rem; color:#6b7280; margin-bottom:8px; }
    .card-value { font-size:2.5rem; font-weight:700; color:#1f2937; line-height:1; }
    .card-sub { font-size:.9rem; margin-top:6px; }
    .icon { font-size:3rem; color:#10b981; flex-shrink:0; }
    .charts-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(480px,1fr)); gap:28px; margin-bottom:36px; }
    .chart-card { background:white; border-radius:22px; padding:28px; box-shadow:0 10px 15px -3px rgba(0,0,0,.08); }
    .chart-title { font-size:1.2rem; font-weight:600; margin-bottom:22px; color:#1f2937; }
    .top-scorers { background:white; border-radius:22px; padding:28px; box-shadow:0 10px 15px -3px rgba(0,0,0,.08); }
    .player { display:flex; align-items:center; background:#f8fafc; padding:18px; border-radius:14px; margin-bottom:14px; }
    .player:last-child { margin-bottom:0; }
    .rank { width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.4rem;font-weight:bold;color:white;flex-shrink:0; }
    .rank-1{background:#fbbf24}.rank-2{background:#9ca3af}.rank-3,.rank-4,.rank-5{background:#cd7c2f}
    .player-info { flex:1; margin-left:18px; }
    .player-name { font-weight:600; font-size:1.05rem; }
    .goals { font-size:2rem; font-weight:700; color:#10b981; text-align:right; }
    .no-data { color:#94a3b8; text-align:center; padding:40px; }
    .amarilla { color:#eab308; } .roja { color:#ef4444; }
    @media(max-width:1024px){ .stats-grid{ grid-template-columns:repeat(2,1fr); } }
    @media(max-width:640px){ .stats-grid{ grid-template-columns:1fr; } .charts-grid{ grid-template-columns:1fr; } }
    </style>
</head>
<body>
<div class="container">
    <h2>Mis Estadísticas</h2>
    <p class="subtitle">Análisis de tu rendimiento individual · <?= htmlspecialchars($mi_jugador['nombre_equipo'] ?? '') ?></p>

    <div class="stats-grid">
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Goles</div>
                    <div class="card-value"><?= $total_goles ?></div>
                    <div class="card-sub" style="color:#10b981;"><?= $partidos_jugados ?> partidos jugados</div>
                </div>
                <div class="icon"><i class="fas fa-futbol"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Asistencias</div>
                    <div class="card-value"><?= $total_asistencias ?></div>
                    <div class="card-sub" style="color:#10b981;">Pases de gol</div>
                </div>
                <div class="icon"><i class="fas fa-handshake"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Minutos jugados</div>
                    <div class="card-value"><?= $total_minutos ?></div>
                    <div class="card-sub"><span class="amarilla"><i class="fas fa-square"></i> <?= $amarillas ?></span>
                         &nbsp;<span class="roja"><i class="fas fa-square"></i> <?= $rojas ?></span></div>
                </div>
                <div class="icon"><i class="fas fa-stopwatch"></i></div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <div>
                    <div class="card-title">Asistencia</div>
                    <div class="card-value"><?= $pct_asistencia ?>%</div>
                    <div class="card-sub">A entrenamientos</div>
                </div>
                <div class="icon"><i class="fas fa-calendar-check"></i></div>
            </div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-title">Mis goles y asistencias (últimos 6 meses)</div>
            <canvas id="rendimientoPersonalChart" height="120"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-title">Resultados del equipo (últimos 6 meses)</div>
            <canvas id="resultadosEquipoChart" height="120"></canvas>
        </div>
    </div>

    <div class="top-scorers">
        <div class="chart-title">Clasificación goleadora del equipo</div>
        <?php if (empty($topGoleadores)): ?>
            <div class="no-data">
                <i class="fas fa-chart-bar" style="font-size:2rem;margin-bottom:10px;display:block;"></i>
                Sin estadísticas registradas aún.
            </div>
        <?php else: ?>
            <?php foreach ($topGoleadores as $i => $g): ?>
            <div class="player">
                <div class="rank rank-<?= $i+1 ?>"><?= $i+1 ?></div>
                <div class="player-info">
                    <div class="player-name"><?= htmlspecialchars($g['nombre']) ?></div>
                    <div style="color:#6b7280;font-size:.9rem;"><?= (int)$g['total_asistencias'] ?> asistencias · <?= htmlspecialchars($g['posicion']) ?></div>
                </div>
                <div>
                    <div class="goals"><?= (int)$g['total_goles'] ?></div>
                    <div style="text-align:right;font-size:.8rem;color:#6b7280;">goles</div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
const meses = <?= json_encode($meses_labels) ?>;
new Chart(document.getElementById('rendimientoPersonalChart'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [
            { label:'Goles',      data: <?= json_encode($data_goles_mes) ?>, backgroundColor:'#10b981' },
            { label:'Asistencias',data: <?= json_encode($data_asist_mes) ?>, backgroundColor:'#3b82f6' }
        ]
    },
    options: { responsive:true, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true,ticks:{stepSize:1}}} }
});
new Chart(document.getElementById('resultadosEquipoChart'), {
    type: 'bar',
    data: {
        labels: meses,
        datasets: [
            { label:'Victorias', data: <?= json_encode($data_victorias) ?>, backgroundColor:'#10b981' },
            { label:'Empates',   data: <?= json_encode($data_empates)   ?>, backgroundColor:'#eab308' },
            { label:'Derrotas',  data: <?= json_encode($data_derrotas)  ?>, backgroundColor:'#ef4444' }
        ]
    },
    options: { responsive:true, plugins:{legend:{position:'top'}}, scales:{y:{beginAtZero:true,ticks:{stepSize:1}}} }
});
</script>
</body>
</html>
