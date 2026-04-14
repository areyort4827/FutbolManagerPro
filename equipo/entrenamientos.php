<?php
include "../config/conexion.php";
$club_id = $_SESSION['club_id'];


$sql = "SELECT e.*, eq.nombre AS nombre_equipo, eq.categoria 
FROM entrenamientos e
LEFT JOIN equipos eq ON e.equipo_id = eq.id
WHERE e.club_id = $club_id
   OR (e.equipo_id IS NOT NULL AND eq.equipo_id = $club_id)
ORDER BY e.fecha DESC, e.hora DESC;";
$stmt = $pdo->query($sql);
$entrenamientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalEntrenamientos = count($entrenamientos);
$horasTotales = 0;

foreach($entrenamientos as $e){
    $horasTotales += $e['duracion'];
}
?>

<style>
.entrenamientosContenedor {
    padding: 30px;
}

 .entrenamientosHeader span {
        color: #64748b;
        font-size: 14px;
    }

/* HEADER */
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

/* Estadisticas */
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
    background: #ffffff
   
}

.estadisticasTitle {
    color: #64748b;
    font-size: 14px;
}

.estadisticasValue {
    font-size: 22px;
    font-weight: bold;
    margin-top: 8px;
}

/* LISTA */
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

.entrenamientoCard:hover {
    border-color: #16a34a;
}

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

.entrenamientoTitle {
    font-weight: bold;
    margin-bottom: 5px;
}

.entrenamientoMeta {
    color: #64748b;
    font-size: 14px;
}

.entrenamientoDescripcion {
    font-size: 14px;
    margin-top: 5px;
}

/* BOTONES */
.actions {
    display: flex;
    gap: 10px;
}

.btnEditar {
    background: #dcfce7;
    border: 1px solid #16a34a;
    color: #16a34a;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
}

.btnBorrar {
    background: #fee2e2;
    border: 1px solid #ef4444;
    color: #ef4444;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
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

    <!-- Estadisticas -->
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
                    • 📍 <?= $e['lugar'] ?>
                    • 👤 <?= $e['nombre_equipo'] ?> (<?= $e['categoria'] ?>)
                </div>

                <div class="entrenamientoDescripcion">
                    <?= htmlspecialchars($e['descripcion']) ?>
                </div>
            </div>

        </div>

        <div class="actions">
            <a class="btnEditar" href="editar_entrenamiento.php?id=<?= $e['id'] ?>">Editar</a>

            <form action="eliminar_entrenamiento.php" method="POST">
                <input type="hidden" name="id" value="<?= $e['id'] ?>">
                <button class="btnBorrar"
                    onclick="return confirm('¿Estás seguro de que deseas eliminar este entrenamiento?')">
                    Eliminar
                </button>
            </form>
        </div>

    </div>

    <?php endforeach; ?>

</div>