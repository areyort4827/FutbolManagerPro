<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../config/conexion.php");

/* ===== OBTENER EQUIPO DEL ENTRENADOR ===== */
$usuario_id = $_SESSION['user']['id'] ?? 0;

$sql = "SELECT equipo_id FROM entrenadores WHERE usuario_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id' => $usuario_id]);
$entrenador_data = $stmt->fetch(PDO::FETCH_ASSOC);

$mi_equipo_id = $entrenador_data['equipo_id'] ?? 0;

/* ===== LÓGICA DE GUARDAR NUEVO PARTIDO ===== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar'])) {
    // Validamos que tengamos los IDs necesarios
    $local_id = $_POST['equipo_local_id'] ?? 0;
    $visitante_id = $_POST['equipo_visitante_id'] ?? 0;
    $fecha = $_POST['fecha'] ?? '';
    $resultado = $_POST['resultado'] ?? '';

    if ($local_id > 0 && $visitante_id > 0) {
        $sql_insert = "INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha, resultado) 
                       VALUES (:local, :visitante, :fecha, :resultado)";
        
        $stmt_insert = $pdo->prepare($sql_insert);
        $resultado_insert = $stmt_insert->execute([
            ':local' => $local_id,
            ':visitante' => $visitante_id,
            ':fecha' => $fecha,
            ':resultado' => $resultado
        ]);

        if ($resultado_insert) {
            $_SESSION['paginaActual'] = 'partidos';
            echo "<script>window.location.href='menu.php';</script>";
            exit();
        }
    }
}

/* ===== ACTUALIZAR RESULTADO ===== */
if (isset($_POST['actualizar'])) {
    $sql_update = "UPDATE partidos SET resultado = :resultado WHERE id = :id";
    $stmt_update = $pdo->prepare($sql_update);
    $stmt_update->execute([
        ':resultado' => $_POST['resultado'],
        ':id' => $_POST['id']
    ]);
    $_SESSION['paginaActual'] = 'partidos';
    echo "<script>window.location.href='menu.php';</script>";
    exit();
}

/* ===== CONSULTA DE PARTIDOS ===== */
$sql_partidos = "
    SELECT p.*, el.nombre AS local, ev.nombre AS visitante
    FROM partidos p
    LEFT JOIN equipos el ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
    WHERE p.equipo_local_id = :mi_id OR p.equipo_visitante_id = :mi_id
    ORDER BY p.fecha DESC
";
$stmt_p = $pdo->prepare($sql_partidos);
$stmt_p->execute([':mi_id' => $mi_equipo_id]);
$partidos = $stmt_p->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="partidos-grid">
    <?php foreach ($partidos as $fila): ?>
        <div class="partido-card">
            <div class="partido-equipos">
                <?= htmlspecialchars($fila['local'] ?? 'Rival') ?> vs <?= htmlspecialchars($fila['visitante'] ?? 'Rival') ?>
            </div>
            <div class="partido-info">Fecha: <?= htmlspecialchars($fila['fecha']) ?></div>
            <div class="resultado">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                    <input type="text" name="resultado" value="<?= htmlspecialchars($fila['resultado'] ?? '') ?>">
                    <button type="submit" name="actualizar">Guardar</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<h2>Añadir Partido</h2>
<div class="form-box">
    <form method="POST">
        <input type="hidden" name="equipo_local_id" value="<?= $mi_equipo_id ?>">

        <div class="form-group">
            <label>Equipo local (Tu equipo)</label>
            <input type="text" value="<?php 
                $st = $pdo->prepare("SELECT nombre FROM equipos WHERE id = :id");
                $st->execute([':id' => $mi_equipo_id]);
                echo htmlspecialchars($st->fetchColumn() ?: 'Barcelona'); 
            ?>" disabled>
        </div>

        <div class="form-group">
            <label>Equipo visitante (Rival)</label>
            <select name="equipo_visitante_id" required>
                <option value="">Seleccionar rival</option>
                <?php
                $stmt_rivales = $pdo->prepare("SELECT id, nombre FROM equipos WHERE id != :mi_id");
                $stmt_rivales->execute([':mi_id' => $mi_equipo_id]);
                foreach ($stmt_rivales as $rival) {
                    echo "<option value='{$rival['id']}'>" . htmlspecialchars($rival['nombre']) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Fecha</label>
            <input type="date" name="fecha" required>
        </div>

        <div class="form-group">
            <label>Resultado</label>
            <input type="text" name="resultado" placeholder="0-0">
        </div>

        <button type="submit" name="guardar" class="boton">Guardar Partido</button>
    </form>
</div>