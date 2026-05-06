<?php
require_once "../config/conexion.php";

$usuario_id = $_SESSION['user']['id'] ?? 0;

// Obtener equipo del jugador logueado
$stmtJ = $pdo->prepare("SELECT j.equipo_id, eq.nombre AS nombre_equipo, eq.categoria
                         FROM jugadores j
                         LEFT JOIN equipos eq ON j.equipo_id = eq.id
                         WHERE j.usuario_id = :uid LIMIT 1");
$stmtJ->execute([':uid' => $usuario_id]);
$mi_data = $stmtJ->fetch(PDO::FETCH_ASSOC);
$mi_equipo_id = $mi_data['equipo_id'] ?? 0;

$lista_jugadores = [];
if ($mi_equipo_id > 0) {
    $stmt = $pdo->prepare("
        SELECT j.id, j.nombre AS jugador,
               TIMESTAMPDIFF(YEAR, j.fecha_nacimiento, CURDATE()) AS edad,
               j.posicion,
               eq.nombre AS equipo, eq.categoria,
               COALESCE(SUM(ej.goles),0) AS goles,
               COALESCE(SUM(ej.asistencias),0) AS asistencias
        FROM jugadores j
        INNER JOIN equipos eq ON j.equipo_id = eq.id
        LEFT JOIN estadisticas_jugador ej ON ej.jugador_id = j.id
        WHERE j.equipo_id = :eid AND (j.eliminado IS NULL OR j.eliminado = 0)
        GROUP BY j.id, j.nombre, j.fecha_nacimiento, j.posicion, eq.nombre, eq.categoria
        ORDER BY j.posicion DESC, j.nombre ASC
    ");
    $stmt->execute([':eid' => $mi_equipo_id]);
    $lista_jugadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<style>
.jugadoresContenedor { padding: 20px; font-family: 'Inter', sans-serif; color: #374151; }
.jugadoresHeader { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 25px; flex-wrap: wrap; gap: 12px; }
.jugadoresHeader h2 { margin: 0; font-size: 22px; }
.jugadoresHeader span { color: #64748b; font-size: 14px; }
#jugadoresGrid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 20px; }
.jugadorCard {
    background: #fff; border-radius: 14px; padding: 22px;
    box-shadow: 0 4px 14px rgba(0,0,0,0.08); transition: .25s; text-align: center;
}
.jugadorCard:hover { transform: translateY(-4px); box-shadow: 0 10px 24px rgba(0,0,0,0.12); }
.avatar { font-size: 32px; color: #6b7280; margin-bottom: 8px; }
.jugadorCard h3 { margin: 5px 0 2px; font-size: 17px; font-weight: 600; }
.posicion { font-size: 11px; color: #6b7280; letter-spacing: .5px; text-transform: uppercase; }
.info { margin: 10px 0; font-size: 13px; color: #374151; }
.categoria { display: inline-block; margin-top: 8px; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; color: white; }
.cadete { background:#22c55e; } .senior,.primer.equipo { background:#3b82f6; } .juvenil { background:#f59e0b; }
.infantil { background:#ef4444; } .filial { background:#176df7; }
.stats-row { display: flex; justify-content: center; gap: 18px; margin-top: 12px; }
.stat { text-align: center; }
.stat .val { font-size: 1.3rem; font-weight: 700; color: #16a34a; }
.stat .lbl { font-size: 10px; color: #9ca3af; text-transform: uppercase; letter-spacing:.5px; }
.aviso-error { background:#fee2e2;color:#dc2626;padding:20px;border-radius:10px;border:1px solid #fecaca;text-align:center; }
</style>

<div class="jugadoresContenedor">
    <?php if ($mi_equipo_id == 0): ?>
        <div class="aviso-error">
            <h3><i class="fa-solid fa-circle-exclamation"></i> Sin equipo asignado</h3>
            <p>Tu perfil de jugador no tiene un equipo asociado. Contacta con el administrador.</p>
        </div>
    <?php else: ?>
        <div class="jugadoresHeader">
            <div>
                <h2>Mi Equipo — <?= htmlspecialchars($mi_data['nombre_equipo'] ?? '') ?></h2>
                <span>Compañeros de plantilla · Total: <strong><?= count($lista_jugadores) ?></strong></span>
            </div>
        </div>

        <div id="jugadoresGrid">
            <?php if (empty($lista_jugadores)): ?>
                <p style="color:#94a3b8;grid-column:1/-1;">No hay jugadores registrados en tu equipo.</p>
            <?php else: ?>
                <?php foreach ($lista_jugadores as $jug): ?>
                <div class="jugadorCard">
                    <div class="avatar"><i class="fa-regular fa-user"></i></div>
                    <h3><?= htmlspecialchars($jug['jugador']) ?></h3>
                    <div class="posicion"><?= htmlspecialchars($jug['posicion']) ?></div>
                    <p class="info">
                        <?php if ($jug['edad']): ?><?= $jug['edad'] ?> años &nbsp;·&nbsp; <?php endif; ?>
                        <strong><?= htmlspecialchars($jug['equipo']) ?></strong>
                    </p>
                    <div class="stats-row">
                        <div class="stat"><div class="val"><?= $jug['goles'] ?></div><div class="lbl">Goles</div></div>
                        <div class="stat"><div class="val"><?= $jug['asistencias'] ?></div><div class="lbl">Asist.</div></div>
                    </div>
                    <span class="categoria <?= strtolower(str_replace(' ','.',htmlspecialchars($jug['categoria']))) ?>">
                        <?= htmlspecialchars($jug['categoria']) ?>
                    </span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
