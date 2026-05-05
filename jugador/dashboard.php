<?php
require_once "../config/conexion.php";

$usuario_id   = $_SESSION['user']['id'] ?? 0;
$mi_equipo_id = 0;
$jugador_id   = 0;
$jugador      = null;

// Obtener jugador vinculado al usuario
$stmtJ = $pdo->prepare("SELECT j.id, j.nombre, j.posicion, j.equipo_id,
                                TIMESTAMPDIFF(YEAR, j.fecha_nacimiento, CURDATE()) AS edad,
                                eq.nombre AS nombre_equipo, eq.categoria
                         FROM jugadores j
                         LEFT JOIN equipos eq ON j.equipo_id = eq.id
                         WHERE j.usuario_id = :uid
                         LIMIT 1");
$stmtJ->execute([':uid' => $usuario_id]);
$jugador = $stmtJ->fetch(PDO::FETCH_ASSOC);

$jugador_id   = $jugador['id']        ?? 0;
$mi_equipo_id = $jugador['equipo_id'] ?? 0;

// Estadísticas personales
$total_goles = $total_asistencias = $partidos_jugados = $pct_asistencia = 0;
if ($jugador_id > 0) {
    $s = $pdo->prepare("SELECT COALESCE(SUM(goles),0) g, COALESCE(SUM(asistencias),0) a, COUNT(*) c
                        FROM estadisticas_jugador WHERE jugador_id = :jid");
    $s->execute([':jid' => $jugador_id]);
    $r = $s->fetch(PDO::FETCH_ASSOC);
    $total_goles       = (int)$r['g'];
    $total_asistencias = (int)$r['a'];
    $partidos_jugados  = (int)$r['c'];

    $sa = $pdo->prepare("SELECT COUNT(*) total, SUM(asistio) asistidos FROM entrenamiento_asistencia WHERE jugador_id = :jid");
    $sa->execute([':jid' => $jugador_id]);
    $ra = $sa->fetch(PDO::FETCH_ASSOC);
    $pct_asistencia = $ra['total'] > 0 ? round($ra['asistidos'] / $ra['total'] * 100) : 0;
}

// Próximo entrenamiento
$proximo_ent = null;
if ($mi_equipo_id > 0) {
    $se = $pdo->prepare("SELECT titulo, fecha, hora, lugar FROM entrenamientos WHERE equipo_id = :eid AND fecha >= CURDATE() ORDER BY fecha ASC, hora ASC LIMIT 1");
    $se->execute([':eid' => $mi_equipo_id]);
    $proximo_ent = $se->fetch(PDO::FETCH_ASSOC);
}

// Próximo partido
$proximo_part = null;
if ($mi_equipo_id > 0) {
    $sp = $pdo->prepare("SELECT p.fecha, el.nombre local_nombre, ev.nombre visitante_nombre
                         FROM partidos p
                         LEFT JOIN equipos el ON p.equipo_local_id = el.id
                         LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
                         WHERE (p.equipo_local_id = :eid OR p.equipo_visitante_id = :eid2)
                           AND p.fecha >= CURDATE()
                         ORDER BY p.fecha ASC LIMIT 1");
    $sp->execute([':eid' => $mi_equipo_id, ':eid2' => $mi_equipo_id]);
    $proximo_part = $sp->fetch(PDO::FETCH_ASSOC);
}
?>
<style>
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 22px;
    margin-bottom: 30px
}

.box {
    background: linear-gradient(145deg, #22c55e, #16a34a);
    color: white;
    border-radius: 15px;
    padding: 25px;
    text-align: center;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    transition: transform .3s, box-shadow .3s
}

.box:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 25px rgba(0, 0, 0, 0.3)
}

.box i {
    font-size: 44px;
    margin-bottom: 12px
}

.box h3 {
    margin: 8px 0 4px;
    font-size: 1.3rem
}

.box p {
    font-size: 1rem;
    margin: 0
}

.bar-container {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
    height: 8px;
    margin-top: 10px
}

.bar-fill {
    height: 100%;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.8);
    width: 0%;
    transition: width 1.2s ease-in-out
}

.perfil-card {
    background: white;
    border-radius: 16px;
    padding: 28px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 30px;
    flex-wrap: wrap
}

.perfil-avatar {
    width: 80px;
    height: 80px;
    background: #dcfce7;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    color: #16a34a;
    flex-shrink: 0
}

.perfil-info h2 {
    margin: 0 0 6px;
    font-size: 1.5rem;
    color: #1f2937
}

.perfil-info .badge-pos {
    display: inline-block;
    background: #16a34a;
    color: white;
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 12px;
    font-weight: 600;
    margin-right: 6px
}

.perfil-info .badge-cat {
    display: inline-block;
    background: #3b82f6;
    color: white;
    border-radius: 20px;
    padding: 4px 14px;
    font-size: 12px;
    font-weight: 600
}

.perfil-info p {
    margin: 8px 0 0;
    color: #6b7280;
    font-size: .95rem
}

.eventos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px
}

.evento-card {
    background: white;
    border-radius: 14px;
    padding: 22px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
    border-left: 5px solid #16a34a
}

.evento-card.partido {
    border-left-color: #2563eb
}

.evento-card .ev-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 700;
    color: white;
    background: #16a34a;
    margin-bottom: 10px
}

.evento-card.partido .ev-badge {
    background: #2563eb
}

.evento-card h4 {
    margin: 0 0 8px;
    color: #1f2937
}

.evento-card p {
    margin: 3px 0;
    color: #6b7280;
    font-size: .9rem
}

.no-evento {
    color: #94a3b8;
    font-size: .9rem
}
</style>

<?php if ($jugador_id == 0): ?>
<div
    style="background:#fee2e2;color:#dc2626;padding:20px;border-radius:10px;border:1px solid #fecaca;text-align:center;">
    <h3><i class="fa-solid fa-circle-exclamation"></i> Perfil no vinculado</h3>
    <p>Tu usuario no tiene un jugador asociado. Contacta con el administrador.</p>
</div>
<?php else: ?>

<div class="perfil-card">
    <div class="perfil-avatar"><i class="fa-solid fa-user"></i></div>
    <div class="perfil-info">
        <h2><?= htmlspecialchars($jugador['nombre'] ?? '') ?></h2>
        <span class="badge-pos"><?= strtoupper(htmlspecialchars($jugador['posicion'] ?? '')) ?></span>
        <?php if (!empty($jugador['categoria'])): ?>
        <span class="badge-cat"><?= htmlspecialchars($jugador['categoria']) ?></span>
        <?php endif; ?>
        <p><i class="fa-solid fa-shield-halved" style="color:#16a34a;"></i>
            <?= htmlspecialchars($jugador['nombre_equipo'] ?? 'Sin equipo') ?>
            <?php if ($jugador['edad']): ?>&nbsp;·&nbsp; <?= $jugador['edad'] ?> años<?php endif; ?>
        </p>
    </div>
</div>

<div class="dashboard-grid">
    <div class="box">
        <i class="fa-solid fa-futbol"></i>
        <h3>Goles</h3>
        <p><?= $total_goles ?> anotados</p>
        <div class="bar-container">
            <div class="bar-fill" data-w="<?= min($total_goles*10,100) ?>"></div>
        </div>
    </div>
    <div class="box">
        <i class="fa-solid fa-handshake"></i>
        <h3>Asistencias</h3>
        <p><?= $total_asistencias ?> repartidas</p>
        <div class="bar-container">
            <div class="bar-fill" data-w="<?= min($total_asistencias*10,100) ?>"></div>
        </div>
    </div>
    <div class="box">
        <i class="fa-solid fa-shirt"></i>
        <h3>Partidos</h3>
        <p><?= $partidos_jugados ?> jugados</p>
        <div class="bar-container">
            <div class="bar-fill" data-w="<?= min($partidos_jugados*5,100) ?>"></div>
        </div>
    </div>
    <div class="box">
        <i class="fa-solid fa-calendar-check"></i>
        <h3>Asistencia</h3>
        <p><?= $pct_asistencia ?>% entrenamientos</p>
        <div class="bar-container">
            <div class="bar-fill" data-w="<?= $pct_asistencia ?>"></div>
        </div>
    </div>
</div>

<h3 style="margin-bottom:16px;color:#1f2937;">Próximos eventos</h3>
<div class="eventos-grid">
    <div class="evento-card">
        <div class="ev-badge"><i class="fa-solid fa-dumbbell"></i> Entrenamiento</div>
        <?php if ($proximo_ent): ?>
        <h4><?= htmlspecialchars($proximo_ent['titulo']) ?></h4>
        <p><i class="fa-solid fa-calendar" style="color:#16a34a;"></i>
            <?= date('d/m/Y', strtotime($proximo_ent['fecha'])) ?> a las
            <?= date('H:i', strtotime($proximo_ent['hora'])) ?>h</p>
        <?php if ($proximo_ent['lugar']): ?>
        <p><i class="fa-solid fa-location-dot" style="color:#16a34a;"></i>
            <?= htmlspecialchars($proximo_ent['lugar']) ?></p>
        <?php endif; ?>
        <?php else: ?>
        <p class="no-evento">Sin entrenamientos próximos programados.</p>
        <?php endif; ?>
    </div>
    <div class="evento-card partido">
        <div class="ev-badge"><i class="fa-solid fa-futbol"></i> Partido</div>
        <?php if ($proximo_part): ?>
        <h4><?= htmlspecialchars($proximo_part['local_nombre']) ?> vs
            <?= htmlspecialchars($proximo_part['visitante_nombre']) ?></h4>
        <p><i class="fa-solid fa-calendar" style="color:#2563eb;"></i>
            <?= date('d/m/Y', strtotime($proximo_part['fecha'])) ?></p>
        <?php else: ?>
        <p class="no-evento">Sin partidos próximos programados.</p>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<script>
document.querySelectorAll('.bar-fill').forEach(b => {
    const w = b.getAttribute('data-w');
    setTimeout(() => {
        b.style.width = w + '%';
    }, 200);
});
</script>