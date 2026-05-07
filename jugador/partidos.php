<?php
require_once "../config/conexion.php";

$usuario_id = $_SESSION['user']['id'] ?? 0;

// Obtener equipo del jugador
$stmtJ = $pdo->prepare("SELECT j.id, j.equipo_id FROM jugadores j WHERE j.usuario_id = :uid LIMIT 1");
$stmtJ->execute([':uid' => $usuario_id]);
$mi_jugador   = $stmtJ->fetch(PDO::FETCH_ASSOC);
$jugador_id   = $mi_jugador['id']        ?? 0;
$mi_equipo_id = $mi_jugador['equipo_id'] ?? 0;

$proximos = $historial = [];

if ($mi_equipo_id > 0) {
    // Próximos partidos
    $stmtP = $pdo->prepare("
        SELECT p.*, el.nombre AS local, ev.nombre AS visitante
        FROM partidos p
        LEFT JOIN equipos el ON p.equipo_local_id = el.id
        LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
        WHERE (p.equipo_local_id = :eid OR p.equipo_visitante_id = :eid2)
          AND p.fecha >= CURDATE()
        ORDER BY p.fecha ASC");
    $stmtP->execute([':eid' => $mi_equipo_id, ':eid2' => $mi_equipo_id]);
    $proximos = $stmtP->fetchAll(PDO::FETCH_ASSOC);

    // Historial
    $stmtH = $pdo->prepare("
        SELECT p.*, el.nombre AS local, ev.nombre AS visitante,
               ej.goles AS mis_goles, ej.asistencias AS mis_asistencias,
               ej.minutos_jugados, ej.tarjetas_amarillas, ej.tarjetas_rojas
        FROM partidos p
        LEFT JOIN equipos el ON p.equipo_local_id = el.id
        LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
        LEFT JOIN estadisticas_jugador ej ON ej.partido_id = p.id AND ej.jugador_id = :jid
        WHERE (p.equipo_local_id = :eid OR p.equipo_visitante_id = :eid2)
          AND p.fecha < CURDATE()
        ORDER BY p.fecha DESC");
    $stmtH->execute([':jid' => $jugador_id, ':eid' => $mi_equipo_id, ':eid2' => $mi_equipo_id]);
    $historial = $stmtH->fetchAll(PDO::FETCH_ASSOC);
}

// Helper: resultado relativo al equipo del jugador
function resultadoRelativo($resultado, $localId, $visitanteId, $miEquipo) {
    if (empty($resultado) || !str_contains($resultado, '-')) return null;
    [$gl, $gv] = explode('-', $resultado);
    $gl = (int)$gl; $gv = (int)$gv;
    $soyLocal = ($localId == $miEquipo);
    $miGoles = $soyLocal ? $gl : $gv;
    $rival   = $soyLocal ? $gv : $gl;
    if ($miGoles > $rival)  return ['txt' => 'V', 'clase' => 'victoria'];
    if ($miGoles == $rival) return ['txt' => 'E', 'clase' => 'empate'];
    return ['txt' => 'D', 'clase' => 'derrota'];
}
?>

<style>
.partidos-wrapper {
    padding: 20px;
    font-family: 'Inter', sans-serif;
}

.partidos-wrapper h2 {
    font-size: 22px;
    margin: 0 0 20px;
    color: #1f2937;
}

.tabs-nav {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    border-bottom: 2px solid #e5e7eb;
}

.tab-btn {
    background: none;
    border: none;
    padding: 10px 20px;
    font-size: 15px;
    cursor: pointer;
    color: #6b7280;
    border-bottom: 3px solid transparent;
    margin-bottom: -2px;
    transition: .2s;
    font-weight: 500;
}

.tab-btn.active {
    color: #16a34a;
    border-bottom-color: #16a34a;
    font-weight: 700;
}

.tab-panel {
    display: none;
}

.tab-panel.active {
    display: block;
}

/* Cards próximos */
#proximos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 18px;
}

.partido-card {
    background: white;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
}

.partido-card .equipos {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
    gap: 8px;
}

.equipo-nombre {
    font-weight: 600;
    font-size: 15px;
    color: #1f2937;
    flex: 1;
    text-align: center;
}

.vs {
    font-size: 12px;
    color: #9ca3af;
    font-weight: 600;
    padding: 0 6px;
}

.partido-card .info {
    font-size: 13px;
    color: #6b7280;
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
}

.partido-card .info span {
    display: flex;
    align-items: center;
    gap: 5px;
}

.badge-local {
    display: inline-block;
    background: #dcfce7;
    color: #166534;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}

.badge-visit {
    display: inline-block;
    background: #dbeafe;
    color: #1e40af;
    font-size: 11px;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
}

/* Tabla historial */
.tabla-wrapper {
    background: white;
    border-radius: 14px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
    overflow: hidden;
}

.tabla {
    width: 100%;
    border-collapse: collapse;
}

.tabla th {
    background: #16a34a;
    color: white;
    padding: 13px 15px;
    text-align: left;
    font-size: 13px;
}

.tabla td {
    padding: 13px 15px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
    color: #374151;
}

.tabla tr:last-child td {
    border-bottom: none;
}

.tabla tr:hover td {
    background: #f0fdf4;
}

.resultado.victoria {
    color: #16a34a;
    font-weight: 700;
}

.resultado.empate {
    color: #eab308;
    font-weight: 700;
}

.resultado.derrota {
    color: #ef4444;
    font-weight: 700;
}

.res-badge {
    display: inline-block;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    text-align: center;
    line-height: 26px;
    font-size: 11px;
    font-weight: 800;
    color: white;
    margin-right: 6px;
}

.res-badge.victoria {
    background: #16a34a;
}

.res-badge.empate {
    background: #eab308;
}

.res-badge.derrota {
    background: #ef4444;
}

.no-data {
    text-align: center;
    color: #94a3b8;
    padding: 50px;
    grid-column: 1/-1;
}

.stats-form {
    display: flex;
    gap: 8px;
    justify-content: center;
    align-items: center;
    flex-wrap: wrap;
}

.stats-form input[type=number] {
    width: 70px;
    padding: 6px 8px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
}

.stats-form button {
    padding: 7px 10px;
    border: 0;
    border-radius: 8px;
    background: #16a34a;
    color: #fff;
    font-weight: 700;
    cursor: pointer;
}

.stats-form button:hover {
    background: #15803d;
}

.stats-check {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #374151;
}
</style>

<div class="partidos-wrapper">
    <h2>Mis Partidos</h2>

    <div class="tabs-nav">
        <button class="tab-btn active" onclick="mostrarTabPartidos('proximos', this)">
            <i class="fa-solid fa-calendar-days"></i> Próximos (<?= count($proximos) ?>)
        </button>
        <button class="tab-btn" onclick="mostrarTabPartidos('historial', this)">
            <i class="fa-solid fa-clock-rotate-left"></i> Historial (<?= count($historial) ?>)
        </button>
    </div>

    <!-- PRÓXIMOS -->
    <div id="tab-proximos" class="tab-panel active">
        <div id="proximos-grid">
            <?php if (empty($proximos)): ?>
            <p class="no-data">No hay partidos próximos programados.</p>
            <?php else: ?>
            <?php foreach ($proximos as $p): ?>
            <?php $esLocal = ($p['equipo_local_id'] == $mi_equipo_id); ?>
            <div class="partido-card">
                <div class="equipos">
                    <div class="equipo-nombre">
                        <?= htmlspecialchars($p['local']) ?>
                        <?php if ($esLocal): ?><br><span class="badge-local">LOCAL</span><?php endif; ?>
                    </div>
                    <div class="vs">VS</div>
                    <div class="equipo-nombre">
                        <?= htmlspecialchars($p['visitante']) ?>
                        <?php if (!$esLocal): ?><br><span class="badge-visit">VISITANTE</span><?php endif; ?>
                    </div>
                </div>
                <div class="info">
                    <span><i class="fa-solid fa-calendar" style="color:#2563eb;"></i>
                        <?= date('d/m/Y', strtotime($p['fecha'])) ?></span>
                    <?php if (!empty($p['lugar'])): ?>
                    <span><i class="fa-solid fa-location-dot" style="color:#2563eb;"></i>
                        <?= htmlspecialchars($p['lugar']) ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- HISTORIAL -->
    <div id="tab-historial" class="tab-panel">
        <?php if (empty($historial)): ?>
        <div class="no-data"><i class="fa-solid fa-futbol" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
            No hay partidos jugados aún.</div>
        <?php else: ?>
        <div class="tabla-wrapper">
            <table class="tabla">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Partido</th>
                        <th>Resultado</th>
                        <th>Mis goles</th>
                        <th>Asist.</th>
                        <th>Min.</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historial as $p): ?>
                    <?php $res = resultadoRelativo($p['resultado'], $p['equipo_local_id'], $p['equipo_visitante_id'], $mi_equipo_id); ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($p['fecha'])) ?></td>
                        <td><?= htmlspecialchars($p['local']) ?> vs <?= htmlspecialchars($p['visitante']) ?></td>
                        <td>
                            <?php if ($res): ?>
                            <span class="res-badge <?= $res['clase'] ?>"><?= $res['txt'] ?></span>
                            <span class="resultado <?= $res['clase'] ?>"><?= htmlspecialchars($p['resultado']) ?></span>
                            <?php else: ?>—<?php endif; ?>
                        </td>
                        <td style="text-align:center;font-weight:600;color:#16a34a;"><?= $p['mis_goles'] ?? '—' ?></td>
                        <td style="text-align:center;"><?= $p['mis_asistencias'] ?? '—' ?></td>
                        <td style="text-align:center;"><?= $p['minutos_jugados'] ? $p['minutos_jugados']."'" : '—' ?>
                        </td>
                       
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function mostrarTabPartidos(tab, btn) {
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    btn.classList.add('active');
}
</script>