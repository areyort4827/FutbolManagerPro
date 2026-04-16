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
foreach($entrenamientos as $e){
    $horasTotales += $e['duracion'];
}
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

/* Botones unificados */
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
}

.btn-editar {
    background: #dcfce7;
    color: #16a34a;
    border: 1px solid #16a34a;
}

.btn-asistencia {
    background: #dbeafe;
    color: #1e40af;
    border: 1px solid #1e40af;
}

.btn-eliminar {
    background: #fee2e2;
    color: #ef4444;
    border: 1px solid #ef4444;
}

.btn-action:hover {
    transform: translateY(-2px);
}
</style>

<div class="entrenamientosContenedor">

    <div class="entrenamientosHeader">
        <div>
            <h2>Gestión de Entrenamientos</h2>
            <span style="color:#64748b">Planifica y registra las sesiones de entrenamiento</span>
        </div>

        <a href="añadir_entrenamiento.php" class="btnAñadir">
            + Añadir entrenamiento
        </a>
    </div>

    <!-- Estadísticas -->
    <div class="estadisticas">
        <div class="estadisticasCard">
            <div class="estadisticasTitle">Total Entrenamientos</div>
            <div class="estadisticasValue"><?= $totalEntrenamientos ?></div>
        </div>
        <div class="estadisticasCard">
            <div class="estadisticasTitle">Asistencia Promedio</div>
            <div class="estadisticasValue">20</div>
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

            <div>
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
             <button onclick="editarAsistencia(<?= $e['id'] ?>)" class="btn-action btn-asistencia">
                Asistencia
            </button>
            
            <a href="editar_entrenamiento.php?id=<?= $e['id'] ?>" class="btn-action btn-editar">Editar</a>
            
           

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

<!-- Modal Asistencia -->
<div id="modalAsistencia" style="display:none; position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); background:white; padding:25px; border-radius:12px; box-shadow:0 10px 30px rgba(0,0,0,0.2); z-index:1000; min-width:320px;">
    <h3>Actualizar Asistentes</h3>
    <form method="POST" action="guardar_asistencia.php">
        <input type="hidden" name="entrenamiento_id" id="modal_entrenamiento_id">
        
        <label style="display:block; margin:15px 0 8px;">Número de jugadores que asistieron:</label>
        <input type="number" name="num_asistentes" id="modal_num_asistentes" min="0" style="width:100%; padding:12px; font-size:1.1rem; border:1px solid #ddd; border-radius:8px;">
        
        <br><br>
        <button type="submit" class="btn-verde" style="width:100%; padding:12px;">Guardar Asistencia</button>
        <button type="button" onclick="cerrarModal()" style="width:100%; margin-top:8px; padding:12px; background:#f1f5f9; border:none; border-radius:8px; cursor:pointer;">
            Cancelar
        </button>
    </form>
</div>

<script>
function editarAsistencia(id) {
    document.getElementById('modal_entrenamiento_id').value = id;
    document.getElementById('modal_num_asistentes').value = document.getElementById('asistentes-' + id)?.innerText || 0;
    document.getElementById('modalAsistencia').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modalAsistencia').style.display = 'none';
}
</script>