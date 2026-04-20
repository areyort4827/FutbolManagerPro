<?php
$club_id = $_SESSION['club_id'] ?? null;
$esAdminGlobal = empty($club_id);

$sql = "
SELECT e.*,
       eq.nombre AS nombre_equipo,
       eq.categoria,
       c.nombre AS nombre_club,
       (SELECT COUNT(*) FROM jugadores j WHERE j.equipo_id = e.equipo_id) AS total_jugadores_equipo
FROM entrenamientos e
LEFT JOIN equipos eq ON e.equipo_id = eq.id
LEFT JOIN clubes c ON eq.equipo_id = c.id
";

$params = [];
if (!$esAdminGlobal) {
    $sql .= " WHERE eq.equipo_id = :club_id";
    $params[':club_id'] = $club_id;
}

$sql .= " ORDER BY e.fecha DESC, e.hora DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$entrenamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$sqlEquipos = $esAdminGlobal
    ? "SELECT id, nombre, categoria FROM equipos ORDER BY nombre ASC"
    : "SELECT id, nombre, categoria FROM equipos WHERE equipo_id = :club_id ORDER BY nombre ASC";
$stmtEquipos = $pdo->prepare($sqlEquipos);
$stmtEquipos->execute($esAdminGlobal ? [] : [':club_id' => $club_id]);
$equipos = $stmtEquipos->fetchAll(PDO::FETCH_ASSOC);

$totalEntrenamientos = count($entrenamientos);
$horasTotales = 0;
$asistenciaTotal = 0;
$entrenamientosConAsistencia = 0;

foreach ($entrenamientos as $entrenamiento) {
    $horasTotales += (int) ($entrenamiento['duracion'] ?? 0);
    if (!empty($entrenamiento['num_asistentes'])) {
        $asistenciaTotal += (int) $entrenamiento['num_asistentes'];
        $entrenamientosConAsistencia++;
    }
}

$asistenciaPromedio = $entrenamientosConAsistencia > 0
    ? round($asistenciaTotal / $entrenamientosConAsistencia, 1)
    : 0;
?>

<style>
    .entrenamientosContenedor { padding: 30px; }
    .entrenamientosHeader {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        gap: 20px;
        flex-wrap: wrap;
    }
    .entrenamientosHeader span { color: #64748b; font-size: 14px; }
    .btnAñadir {
        background: #16a34a;
        color: white;
        border: none;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: bold;
        cursor: pointer;
    }
    .estadisticas {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }
    .estadisticasCard {
        border: 2px solid #bbf7d0;
        border-radius: 12px;
        padding: 20px;
        background: #f8fafc;
    }
    .estadisticasTitle { color: #64748b; font-size: 14px; }
    .estadisticasValue { font-size: 22px; font-weight: bold; margin-top: 8px; }
    .entrenamientoCard {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #e2e8f0;
        gap: 20px;
        transition: .2s;
    }
    .entrenamientoCard:hover { border-color: #16a34a; }
    .entrenamientoInfo {
        display: flex;
        gap: 15px;
        align-items: flex-start;
        flex: 1;
    }
    .icon-box {
        background: #dcfce7;
        color: #16a34a;
        padding: 10px;
        border-radius: 10px;
        font-size: 20px;
    }
    .entrenamientoTitle { font-weight: bold; margin-bottom: 5px; }
    .entrenamientoMeta { color: #64748b; font-size: 14px; margin-top: 4px; }
    .entrenamientoDescripcion { font-size: 14px; margin-top: 6px; }
    .actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .btn-action {
        padding: 8px 14px;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.2s;
        min-width: 90px;
        text-align: center;
    }
    .btn-editar { background: #dcfce7; color: #16a34a; border: 1px solid #16a34a; }
    .btn-asistencia { background: #dbeafe; color: #1e40af; border: 1px solid #1e40af; }
    .btn-eliminar { background: #fee2e2; color: #ef4444; border: 1px solid #ef4444; }
    .btn-action:hover { transform: translateY(-2px); }
    .modal {
    display: none; 
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    /* Cambio importante: permite el scroll si el contenido es más alto que la pantalla */
    overflow-y: auto; 
    padding: 20px 0;
}

.modal-content {
    background: white;
    margin: 2% auto; /* Bajamos el margen del 5% al 2% para que suba el panel */
    padding: 25px;
    border-radius: 12px;
    width: 90%;
    max-width: 520px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    /* Evitamos que el contenido se desborde */
    position: relative;
}

/* Ajuste para que los inputs no ocupen tanto espacio vertical */
.modal-content label {
    display: block;
    margin: 8px 0 4px; /* Reducimos márgenes */
    font-weight: 500;
}

.modal-content input, .modal-content select, .modal-content textarea {
    width: 100%;
    padding: 8px; /* Reducimos de 10px a 8px */
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 8px; /* Reducimos de 10px a 8px */
}
    .btn-verde {
        background: #16a34a;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        margin-top: 10px;
    }
</style>

<div class="entrenamientosContenedor">
    <div class="entrenamientosHeader">
        <div>
            <h2>Gestión de Entrenamientos</h2>
            <span>Todos los entrenamientos disponibles en administración</span>
        </div>
        <button type="button" onclick="abrirModalEntrenamiento('modalAñadirEntrenamiento')" class="btnAñadir">+ Añadir entrenamiento</button>
    </div>

    <div class="estadisticas">
        <div class="estadisticasCard">
            <div class="estadisticasTitle">Total Entrenamientos</div>
            <div class="estadisticasValue"><?= $totalEntrenamientos ?></div>
        </div>
        <div class="estadisticasCard">
            <div class="estadisticasTitle">Asistencia Promedio</div>
            <div class="estadisticasValue"><?= $asistenciaPromedio ?></div>
        </div>
        <div class="estadisticasCard">
            <div class="estadisticasTitle">Minutos Totales</div>
            <div class="estadisticasValue"><?= $horasTotales ?></div>
        </div>
    </div>

    <?php if (empty($entrenamientos)): ?>
        <p>No hay entrenamientos registrados.</p>
    <?php endif; ?>

 <?php foreach ($entrenamientos as $e): ?>
    <div class="entrenamientoCard">
        <div class="entrenamientoInfo">
            <div class="icon-box">
                <?php
                $icono = match($e['titulo']) {
                    'Sesión técnica'    => 'fa-futbol',
                    'Sesión táctica'    => 'fa-clipboard-list',
                    'Sesión de físico'  => 'fa-dumbbell',
                    'Sesión pre-partido'=> 'fa-flag-checkered',
                    default             => 'fa-dumbbell' // Icono por defecto del primer código
                };
                echo "<i class='fa-solid $icono'></i>";
                ?>
            </div>

            <div style="flex:1">
                <div class="entrenamientoTitle"><?= htmlspecialchars($e['titulo']) ?></div>
                <div class="entrenamientoMeta">
                    📅 <?= date("d/m/Y", strtotime($e['fecha'])) ?> · 
                    🕒 <?= substr((string) $e['hora'], 0, 5) ?> · 
                    ⏱ <?= (int) $e['duracion'] ?> min
                </div>
                <div class="entrenamientoMeta">
                    📍 <?= htmlspecialchars($e['lugar'] ?? 'No especificado') ?>
                </div>
                <div class="entrenamientoMeta">
                    <?= htmlspecialchars($e['nombre_equipo'] ?? 'Sin equipo') ?> (<?= htmlspecialchars($e['categoria'] ?? '') ?>)
                    · <?= htmlspecialchars($e['nombre_club'] ?? 'Sin club') ?>
                </div>
                <div class="entrenamientoDescripcion"><?= htmlspecialchars($e['descripcion'] ?? '') ?></div>
                <div class="entrenamientoMeta" style="margin-top: 8px;">
                    👥 Asistentes: <strong><?= (int) ($e['num_asistentes'] ?? 0) ?></strong> / <?= (int) ($e['total_jugadores_equipo'] ?? 0) ?>
                </div>
            </div>
        </div>

        <div class="actions">
            <button type="button" onclick='abrirAsistenciaEntrenamiento(<?= json_encode(["id" => (int) $e["id"], "num_asistentes" => (int) ($e["num_asistentes"] ?? 0)]) ?>)' class="btn-action btn-asistencia">Asistencia</button>
            <button type="button" onclick='abrirEditarEntrenamiento(<?= json_encode($e, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)' class="btn-action btn-editar">Editar</button>
            <form action="eliminar_entrenamiento.php" method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= (int) $e['id'] ?>">
                <button type="submit" class="btn-action btn-eliminar" onclick="return confirm('¿Eliminar este entrenamiento?')">Eliminar</button>
            </form>
        </div>
    </div>
<?php endforeach; ?>
</div>

<div id="modalAñadirEntrenamiento" class="modal">
    <div class="modal-content">
        <h3>Añadir entrenamiento</h3>
        <form method="POST" action="añadir_entrenamiento.php">
            <label>Título</label>
            <select name="titulo" required>
                <option value="">Seleccionar tipo</option>
                <option value="Sesión táctica">Sesión táctica</option>
                <option value="Sesión técnica">Sesión técnica</option>
                <option value="Sesión de físico">Sesión de físico</option>
                <option value="Sesión pre-partido">Sesión pre-partido</option>
            </select>

            <label>Equipo</label>
            <select name="equipo_id" required>
                <option value="">Seleccionar equipo</option>
                <?php foreach ($equipos as $equipo): ?>
                    <option value="<?= (int) $equipo['id'] ?>"><?= htmlspecialchars($equipo['nombre']) ?> (<?= htmlspecialchars($equipo['categoria']) ?>)</option>
                <?php endforeach; ?>
            </select>

            <label>Fecha</label>
            <input type="date" name="fecha" required>

            <label>Hora</label>
            <input type="time" name="hora" required>

            <label>Duración</label>
            <input type="number" name="duracion" min="1" required>

            <label>Lugar</label>
            <input type="text" name="lugar">

            <label>Descripción</label>
            <textarea name="descripcion" rows="4"></textarea>

            <button type="submit" class="btn-verde">Guardar entrenamiento</button>
            <button type="button" class="btn-verde" onclick="cerrarModalEntrenamiento('modalAñadirEntrenamiento')">Cancelar</button>
        </form>
    </div>
</div>

<div id="modalAsistenciaEntrenamiento" class="modal">
    <div class="modal-content">
        <h3>Actualizar asistencia</h3>
        <form method="POST" action="guardar_asistencia.php">
            <input type="hidden" name="entrenamiento_id" id="asistencia_entrenamiento_id">
            <label>Número de asistentes</label>
            <input type="number" name="num_asistentes" id="asistencia_entrenamiento_num" min="0" required>
            <button type="submit" class="btn-verde">Guardar asistencia</button>
            <button type="button" class="btn-verde" onclick="cerrarModalEntrenamiento('modalAsistenciaEntrenamiento')">Cancelar</button>
        </form>
    </div>
</div>

<div id="modalEditarEntrenamiento" class="modal">
    <div class="modal-content">
        <h3>Editar entrenamiento</h3>
        <form method="POST" action="editar_entrenamiento.php">
            <input type="hidden" name="id" id="editar_entrenamiento_id">

            <label>Título</label>
            <select name="titulo" id="editar_entrenamiento_titulo" required>
                <option value="Sesión táctica">Sesión táctica</option>
                <option value="Sesión técnica">Sesión técnica</option>
                <option value="Sesión de físico">Sesión de físico</option>
                <option value="Sesión pre-partido">Sesión pre-partido</option>
            </select>

            <label>Equipo</label>
            <select name="equipo_id" id="editar_entrenamiento_equipo" required>
                <?php foreach ($equipos as $equipo): ?>
                    <option value="<?= (int) $equipo['id'] ?>"><?= htmlspecialchars($equipo['nombre']) ?> (<?= htmlspecialchars($equipo['categoria']) ?>)</option>
                <?php endforeach; ?>
            </select>

            <label>Fecha</label>
            <input type="date" name="fecha" id="editar_entrenamiento_fecha" required>

            <label>Hora</label>
            <input type="time" name="hora" id="editar_entrenamiento_hora" required>

            <label>Duración</label>
            <input type="number" name="duracion" id="editar_entrenamiento_duracion" min="1" required>

            <label>Lugar</label>
            <input type="text" name="lugar" id="editar_entrenamiento_lugar">

            <label>Descripción</label>
            <textarea name="descripcion" id="editar_entrenamiento_descripcion" rows="4"></textarea>

            <button type="submit" class="btn-verde">Guardar cambios</button>
            <button type="button" class="btn-verde" onclick="cerrarModalEntrenamiento('modalEditarEntrenamiento')">Cancelar</button>
        </form>
    </div>
</div>

<script>
function abrirModalEntrenamiento(id) {
    document.getElementById(id).style.display = 'block';
}

function cerrarModalEntrenamiento(id) {
    document.getElementById(id).style.display = 'none';
}

function abrirAsistenciaEntrenamiento(data) {
    document.getElementById('asistencia_entrenamiento_id').value = data.id;
    document.getElementById('asistencia_entrenamiento_num').value = data.num_asistentes || 0;
    abrirModalEntrenamiento('modalAsistenciaEntrenamiento');
}

function abrirEditarEntrenamiento(data) {
    document.getElementById('editar_entrenamiento_id').value = data.id;
    document.getElementById('editar_entrenamiento_titulo').value = data.titulo || '';
    document.getElementById('editar_entrenamiento_equipo').value = data.equipo_id || '';
    document.getElementById('editar_entrenamiento_fecha').value = data.fecha || '';
    document.getElementById('editar_entrenamiento_hora').value = data.hora ? data.hora.substring(0, 5) : '';
    document.getElementById('editar_entrenamiento_duracion').value = data.duracion || '';
    document.getElementById('editar_entrenamiento_lugar').value = data.lugar || '';
    document.getElementById('editar_entrenamiento_descripcion').value = data.descripcion || '';
    abrirModalEntrenamiento('modalEditarEntrenamiento');
}
</script>
