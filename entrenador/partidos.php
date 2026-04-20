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

if (isset($_POST['guardar'])) {

    $local = $_POST['equipo_local_id'];
    $visitante = $_POST['equipo_visitante_id'];
    $fecha = $_POST['fecha'];
    $resultado = $_POST['resultado'];

    $sql = "INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha, resultado)
            VALUES (:local, :visitante, :fecha, :resultado)";

    $stmt = $pdo->prepare($sql);

    if ($stmt->execute([
        ':local' => $local,
        ':visitante' => $visitante,
        ':fecha' => $fecha,
        ':resultado' => $resultado
    ])) {
        echo "<p>Partido guardado correctamente</p>";
    } else {
        echo "<p>Error al guardar</p>";
    }
}

/* ===== ACTUALIZAR ===== */
if (isset($_POST['actualizar'])) {
    $sql = "UPDATE partidos SET resultado = :resultado WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':resultado' => $_POST['resultado'],
        ':id' => $_POST['id']
    ]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

/* ===== CONSULTA SOLO DE SU EQUIPO ===== */
$sql_partidos = "
    SELECT p.*,
           el.nombre AS local,
           ev.nombre AS visitante
    FROM partidos p
    LEFT JOIN equipos el ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
    WHERE p.equipo_local_id = :mi_id 
       OR p.equipo_visitante_id = :mi_id
    ORDER BY p.fecha DESC
";
$stmt_p = $pdo->prepare($sql_partidos);
$stmt_p->execute([':mi_id' => $mi_equipo_id]);
$partidos = $stmt_p->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="partidos-grid">
<?php if ($mi_equipo_id == 0): ?>
    <p>Aviso: No tienes un equipo asignado.</p>
<?php elseif (empty($partidos)): ?>
    <p>No hay partidos registrados para tu equipo.</p>
<?php else: ?>
    <?php foreach ($partidos as $fila) { ?>
        <div class="partido-card">
            <div class="partido-equipos">
                <?= htmlspecialchars($fila['local'] ?? 'Desconocido') ?> 
                vs 
                <?= htmlspecialchars($fila['visitante'] ?? 'Desconocido') ?>
            </div>
            <div class="partido-info">
                Fecha: <?= htmlspecialchars($fila['fecha']) ?>
            </div>
            <div class="resultado">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                    <input type="text" name="resultado" value="<?= htmlspecialchars($fila['resultado'] ?? '') ?>">
                    <button type="submit" name="actualizar">Guardar</button>
                </form>
            </div>
        </div>
    <?php } ?>
<?php endif; ?>
</div>

<h2>Añadir Partido</h2>
<div class="form-box">
<form method="POST" onsubmit="this.guardar.disabled=true; return true;">
    <div class="form-group">
        <label>Equipo local (Tu equipo)</label>
       <input type="hidden" name="equipo_local_id" value="<?= $mi_equipo_id ?>">

<div class="form-group">
    <label>Equipo local</label>
    <input type="text" value="<?php
        $stmt = $pdo->prepare("SELECT nombre FROM equipos WHERE id = :id");
        $stmt->execute([':id' => $mi_equipo_id]);
        echo $stmt->fetchColumn();
    ?>" disabled>
</div>
    </div>
    <div class="form-group">
    <label>Equipo visitante (Rival)</label>
    <select name="equipo_visitante_id" required>
        <option value="">Seleccionar rival</option>

        <?php
        $stmt_rivales = $pdo->prepare("SELECT id, nombre FROM equipos WHERE id != :mi_id");
        $stmt_rivales->execute([':mi_id' => $mi_equipo_id]);

        foreach ($stmt_rivales as $rival) {
            echo "<option value='{$rival['id']}'>{$rival['nombre']}</option>";
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