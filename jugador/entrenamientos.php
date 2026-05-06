<?php
require_once "../config/conexion.php";

$usuario_id = $_SESSION['user']['id'] ?? 0;

// Obtener jugador
$stmtJ = $pdo->prepare("SELECT j.id, j.equipo_id FROM jugadores j WHERE j.usuario_id = :uid LIMIT 1");
$stmtJ->execute([':uid' => $usuario_id]);
$mi_jugador = $stmtJ->fetch(PDO::FETCH_ASSOC);
$jugador_id   = $mi_jugador['id']        ?? 0;
$mi_equipo_id = $mi_jugador['equipo_id'] ?? 0;

$entrenamientos = [];
$totalEntrenamientos = 0;
$horasTotales = 0;
$totalAsistidos = 0;
$hoy = date('Y-m-d');

if ($mi_equipo_id > 0) {
    $sql = "SELECT e.*,
                   eq.nombre AS nombre_equipo,
                   ea.asistio
            FROM entrenamientos e
            LEFT JOIN equipos eq ON e.equipo_id = eq.id
            LEFT JOIN entrenamiento_asistencia ea ON ea.entrenamiento_id = e.id AND ea.jugador_id = :jid
            WHERE e.equipo_id = :eid
            ORDER BY e.fecha DESC, e.hora DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':jid' => $jugador_id, ':eid' => $mi_equipo_id]);
    $entrenamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $totalEntrenamientos = 0;
    foreach ($entrenamientos as $e) {
        $horasTotales  += (int)$e['duracion'];
        $esPasado = !empty($e['fecha']) && $e['fecha'] < $hoy;
        if ($esPasado) {
            $totalEntrenamientos++;
            $totalAsistidos += ((int)($e['asistio'] ?? 0) === 1) ? 1 : 0;
        }
    }
}

$pct_asistencia = $totalEntrenamientos > 0 ? round($totalAsistidos / $totalEntrenamientos * 100) : 0;
?>

<style>
.entrenamientosContenedor {
    padding: 20px;
}

.entrenamientosHeader {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 22px;
    flex-wrap: wrap;
    gap: 12px;
}

.entrenamientosHeader h2 {
    margin: 0;
    font-size: 22px;
}

.estadisticas {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 24px;
}

.estadisticasCard {
    border: 2px solid #bbf7d0;
    border-radius: 12px;
    padding: 18px;
    background: #fff;
    text-align: center;
}

.estadisticasCard .val {
    font-size: 2rem;
    font-weight: 700;
    color: #16a34a;
}

.estadisticasCard .lbl {
    font-size: 13px;
    color: #6b7280;
    margin-top: 4px;
}

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
    padding: 14px 16px;
    text-align: left;
    font-size: 13px;
}

.tabla td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
    color: #374151;
}

.tabla tr:hover td {
    background: #f0fdf4;
}

.tabla tr:last-child td {
    border-bottom: none;
}

.badge-titulo {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
}

.tactica {
    background: #dbeafe;
    color: #1d4ed8;
}

.tecnica {
    background: #ede9fe;
    color: #7c3aed;
}

.pre-partido {
    background: #fef3c7;
    color: #92400e;
}

.fisico {
    background: #dcfce7;
    color: #166534;
}

.asistio-si {
    color: #16a34a;
    font-weight: 600;
}

.asistio-no {
    color: #ef4444;
    font-weight: 600;
}

.asistio-nd {
    color: #94a3b8;
}

.asistio-prox {
    color: #2563eb;
    font-weight: 600;
}

.asist-checkbox {
    transform: scale(1.2);
    cursor: pointer;
}

.asist-form {
    display: inline-flex;
    align-items: center;
    gap: 10px;
}

.no-data {
    text-align: center;
    color: #94a3b8;
    padding: 50px;
}

@media(max-width:640px) {
    .estadisticas {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="entrenamientosContenedor">
    <div class="entrenamientosHeader">
        <h2>Mis Entrenamientos</h2>
    </div>

    <div class="estadisticas">
        <div class="estadisticasCard">
            <div class="val"><?= $totalEntrenamientos ?></div>
            <div class="lbl">Total entrenamientos</div>
        </div>
        <div class="estadisticasCard">
            <div class="val"><?= $totalAsistidos ?></div>
            <div class="lbl">Sesiones asistidas</div>
        </div>
        <div class="estadisticasCard">
            <div class="val"><?= $pct_asistencia ?>%</div>
            <div class="lbl">% de asistencia</div>
        </div>
    </div>

    <div class="tabla-wrapper">
        <?php if (empty($entrenamientos)): ?>
        <div class="no-data">
            <i class="fa-solid fa-dumbbell" style="font-size:2.5rem;margin-bottom:10px;display:block;"></i>
            No hay entrenamientos registrados para tu equipo.
        </div>
        <?php else: ?>
        <table class="tabla">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Hora</th>
                    <th>Sesión</th>
                    <th>Lugar</th>
                    <th>Duración</th>
                    <th>Mi asistencia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entrenamientos as $e): ?>
                <?php
                    $claseT = 'tactica';
                    if (str_contains($e['titulo'], 'técnica'))     $claseT = 'tecnica';
                    if (str_contains($e['titulo'], 'pre-partido')) $claseT = 'pre-partido';
                    if (str_contains($e['titulo'], 'físico'))      $claseT = 'fisico';
                    $esProximo = !empty($e['fecha']) && $e['fecha'] >= $hoy;
                ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($e['fecha'])) ?></td>
                    <td><?= date('H:i', strtotime($e['hora'])) ?>h</td>
                    <td><span class="badge-titulo <?= $claseT ?>"><?= htmlspecialchars($e['titulo']) ?></span></td>
                    <td><?= htmlspecialchars($e['lugar'] ?? 'â€”') ?></td>
                    <td><?= $e['duracion'] > 0 ? $e['duracion'] . ' min' : 'â€”' ?></td>
                    <td>
                        <form class="asist-form" action="guardar_asistencia.php" method="POST"
                            style="margin-bottom:6px;">
                            <input type="hidden" name="entrenamiento_id" value="<?= (int)$e['id'] ?>">
                            <label>
                                <input class="asist-checkbox" type="checkbox" name="asistio"
                                    <?= ((int)($e['asistio'] ?? 0) === 1) ? 'checked' : '' ?>
                                    onchange="this.form.submit()">
                                <?= $esProximo ? 'Asistiré' : 'He asistido' ?>
                            </label>
                        </form>
                        <?php if ($esProximo): ?>
                        <?php if ((int)($e['asistio'] ?? 0) === 1): ?>
                        <span class="asistio-prox"><i class="fa-solid fa-calendar-check"></i> Asistiré</span>
                        <?php else: ?>
                        <span class="asistio-nd">—</span>
                        <?php endif; ?>
                        <?php else: ?>
                        <?php if (is_null($e['asistio'])): ?>
                        <span class="asistio-nd">—</span>
                        <?php elseif ($e['asistio'] == 1): ?>
                        <span class="asistio-si"><i class="fa-solid fa-circle-check"></i> Asistí</span>
                        <?php else: ?>
                        <span class="asistio-no"><i class="fa-solid fa-circle-xmark"></i> No asistí</span>
                        <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>