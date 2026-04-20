<?php
include "../config/conexion.php";
$club_id = $_SESSION['club_id'];

$sql = "SELECT e.*, 
               eq.nombre AS nombre_equipo, 
               eq.categoria,
               (SELECT COUNT(*) FROM jugadores j WHERE j.equipo_id = e.equipo_id) as total_jugadores_equipo
        FROM entrenamientos e
        LEFT JOIN equipos eq ON e.equipo_id = eq.id
        WHERE e.club_id = :club_id
           OR (e.equipo_id IS NOT NULL AND eq.equipo_id = :club_id)
        ORDER BY e.fecha DESC, e.hora DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['club_id' => $club_id]);
$entrenamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalEntrenamientos = count($entrenamientos);
$horasTotales = 0;
$asistenciaTotal = 0;
$entrenamientosConAsistencia = 0;

foreach($entrenamientos as $e){
    $horasTotales += $e['duracion'];
    
    if (isset($e['num_asistentes']) && $e['num_asistentes'] > 0) {
        $asistenciaTotal += $e['num_asistentes'];
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
}

.btnAñadir {
    background: #16a34a;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
}

/* Estadísticas */
.estadisticas {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.estadisticasCard {
    border: 2px solid #bbf7d0;
    border-radius: 12px;
    padding: 20px;
    background: #ffffff;
}

.estadisticasTitle { color: #64748b; font-size: 14px; }
.estadisticasValue { font-size: 22px; font-weight: bold; margin-top: 8px; }

/* Lista */
.entrenamientoCard {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #e2e8f0;
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

.entrenamientoMeta {
    color: #64748b;
    font-size: 14px;
    margin-top: 4px;
}

.entrenamientoDescripcion {
    font-size: 14px;
    margin-top: 6px;
}

/* Botones */
.actions {
    display: flex;
    gap: 8px;
}

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

.btn-editar   { background: #dcfce7; color: #16a34a; border: 1px solid #16a34a; }
.btn-asistencia { background: #dbeafe; color: #1e40af; border: 1px solid #1e40af; }
.btn-eliminar { background: #fee2e2; color: #ef4444; border: 1px solid #ef4444; }

.btn-action:hover { transform: translateY(-2px); }

/* Modales */
.modal {
    display: none; 
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: rgba(0,0,0,0.6);
    z-index: 1000;
    overflow-y: auto; 
    padding: 20px 0;
}

.modal-content {
    background: white;
    margin: 2% auto; 
    padding: 25px;
    border-radius: 12px;
    width: 90%;
    max-width: 520px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    /* Evitamos que el contenido se desborde */
    position: relative;
}


.modal-content label {
    display: block;
    margin: 8px 0 4px; 
    font-weight: 500;
}

.modal-content input, .modal-content select, .modal-content textarea {
    width: 100%;
    padding: 8px; 
    border: 1px solid #ddd;
    border-radius: 8px;
    margin-bottom: 8px; 
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
            <span style="color:#64748b">Planifica y registra las sesiones de entrenamiento</span>
        </div>

        <button onclick="abrirModal('modalAñadir')" class="btnAñadir">
            + Añadir entrenamiento
        </button>
    </div>

    <!-- Estadísticas -->
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
            <div class="estadisticasTitle">Horas Totales</div>
            <div class="estadisticasValue"><?= $horasTotales ?> min</div>
        </div>
    </div>

    <!-- LISTA -->
    <?php foreach($entrenamientos as $e): ?>

    <div class="entrenamientoCard">
        <div class="entrenamientoInfo">
            <div class="icon-box">
                <?php
                $icono = match($e['titulo']) {
                    'Sesión técnica' => 'fa-futbol',
                    'Sesión táctica' => 'fa-clipboard-list',
                    'Sesión de físico' => 'fa-dumbbell',
                    'Sesión pre-partido' => 'fa-flag-checkered',
                    default => 'fa-futbol'
                };
                echo "<i class='fa-solid $icono'></i>";
                ?>
            </div>

            <div style="flex:1">
                <div class="entrenamientoTitle"><?= htmlspecialchars($e['titulo']) ?></div>
                <div class="entrenamientoMeta">
                    📅 <?= date("d M Y", strtotime($e['fecha'])) ?>
                    • 🕒 <?= substr($e['hora'],0,5) ?>
                    • ⏱ <?= $e['duracion'] ?> min
                    • 📍 <?= htmlspecialchars($e['lugar'] ?? 'No especificado') ?>
                    • 👤 <?= htmlspecialchars($e['nombre_equipo'] ?? 'Sin equipo') ?> (<?= htmlspecialchars($e['categoria'] ?? '') ?>)
                </div>
                <div class="entrenamientoDescripcion">
                    <?= htmlspecialchars($e['descripcion'] ?? '') ?>
                </div>
                <div class="entrenamientoMeta" style="margin-top: 8px;">
                    👥 Asistentes: <strong><?= $e['num_asistentes'] ?? 0 ?></strong> / 
                    <?= $e['total_jugadores_equipo'] ?? 0 ?> jugadores
                </div>
            </div>
        </div>

        <div class="actions">
            <button onclick="editarAsistencia(<?= $e['id'] ?>)" class="btn-action btn-asistencia">Asistencia</button>
            <button onclick="editarEntrenamiento(<?= htmlspecialchars(json_encode($e)) ?>)" class="btn-action btn-editar">Editar</button>
            
            <form action="eliminar_entrenamiento.php" method="POST" style="display:inline;">
                <input type="hidden" name="id" value="<?= $e['id'] ?>">
                <button type="submit" class="btn-action btn-eliminar" 
                        onclick="return confirm('¿Estás seguro de que deseas eliminar este entrenamiento?')">
                    Eliminar
                </button>
            </form>
        </div>
    </div>

    <?php endforeach; ?>

</div>

<!-- ====================== MODAL AÑADIR ENTRENAMIENTO ====================== -->
<div id="modalAñadir" class="modal">
    <div class="modal-content">
        <h3>Añadir Nuevo Entrenamiento</h3>
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
                <?php
                $equipos = $pdo->query("SELECT id, nombre, categoria FROM equipos WHERE equipo_id = $club_id");
                while($eq = $equipos->fetch(PDO::FETCH_ASSOC)):
                ?>
                    <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?> (<?= htmlspecialchars($eq['categoria']) ?>)</option>
                <?php endwhile; ?>
            </select>

            <label>Fecha</label>
            <input type="date" name="fecha" required>

            <label>Hora</label>
            <input type="time" name="hora" required>

            <label>Duración (minutos)</label>
            <input type="number" name="duracion" required min="1">

            <label>Lugar</label>
            <input type="text" name="lugar">

            <label>Descripción</label>
            <textarea name="descripcion" rows="4"></textarea>

            <button type="submit" class="btn-verde">Guardar Entrenamiento</button>
            <button type="button" onclick="cerrarModal('modalAñadir')">Cancelar</button>
        </form>
    </div>
</div>

<!-- ====================== MODAL ASISTENCIA ====================== -->
<div id="modalAsistencia" class="modal">
    <div class="modal-content">
        <h3>Actualizar Asistentes</h3>
        <form method="POST" action="guardar_asistencia.php">
            <input type="hidden" name="entrenamiento_id" id="asistencia_id">
            <label>Número de jugadores que asistieron:</label>
            <input type="number" name="num_asistentes" id="asistencia_num" min="0" required>
            <button type="submit" class="btn-verde">Guardar Asistencia</button>
            <button type="button" onclick="cerrarModal('modalAsistencia')">Cancelar</button>
        </form>
    </div>
</div>

<!-- ====================== MODAL EDITAR ====================== -->
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <h3>Editar Entrenamiento</h3>
        <form method="POST" action="editar_entrenamiento.php">
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="equipo_id" id="edit_equipo_id">

            <label>Título</label>
            <select name="titulo" id="edit_titulo" required>
                <option value="Sesión táctica">Sesión táctica</option>
                <option value="Sesión técnica">Sesión técnica</option>
                <option value="Sesión de físico">Sesión de físico</option>
                <option value="Sesión pre-partido">Sesión pre-partido</option>
            </select>

            <label>Fecha</label>
            <input type="date" name="fecha" id="edit_fecha" required>

            <label>Hora</label>
            <input type="time" name="hora" id="edit_hora" required>

            <label>Duración (minutos)</label>
            <input type="number" name="duracion" id="edit_duracion" required>

            <label>Lugar</label>
            <input type="text" name="lugar" id="edit_lugar">

            <label>Descripción</label>
            <textarea name="descripcion" id="edit_descripcion" rows="4"></textarea>

            <button type="submit" class="btn-verde">Guardar Cambios</button>
            <button type="button" onclick="cerrarModal('modalEditar')">Cancelar</button>
        </form>
    </div>
</div>

<script>
function abrirModal(id) {
    document.getElementById(id).style.display = 'block';
}

function cerrarModal(id) {
    document.getElementById(id).style.display = 'none';
}

function editarAsistencia(id) {
    document.getElementById('asistencia_id').value = id;
    document.getElementById('asistencia_num').value = 0;
    abrirModal('modalAsistencia');
}

function editarEntrenamiento(entrenamiento) {
    const data = typeof entrenamiento === 'string' ? JSON.parse(entrenamiento) : entrenamiento;

    document.getElementById('edit_id').value = data.id;
    document.getElementById('edit_equipo_id').value = data.equipo_id || '';
    document.getElementById('edit_titulo').value = data.titulo;
    document.getElementById('edit_fecha').value = data.fecha;
    document.getElementById('edit_hora').value = data.hora ? data.hora.substring(0,5) : '';
    document.getElementById('edit_duracion').value = data.duracion;
    document.getElementById('edit_lugar').value = data.lugar || '';
    document.getElementById('edit_descripcion').value = data.descripcion || '';

    abrirModal('modalEditar');
}
</script>