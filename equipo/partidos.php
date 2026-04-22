<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../config/conexion.php");

// Obtenemos el club_id del usuario 
$club_id = $_SESSION['user']['club_id'] ?? 0;

/* ===== GUARDAR ===== */
if (isset($_POST['guardar'])) {
    $sql = "INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha, resultado)
            VALUES (:local, :visitante, :fecha, :resultado)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':local' => $_POST['equipo_local_id'],
        ':visitante' => $_POST['equipo_visitante_id'],
        ':fecha' => $_POST['fecha'],
        ':resultado' => $_POST['resultado']
    ]);
    
    $_SESSION['paginaActual'] = 'partidos';
    echo "<script>window.location.href='menu.php';</script>";
    exit();
}

/* ===== ACTUALIZAR ===== */
if (isset($_POST['actualizar'])) {
    $sql = "UPDATE partidos SET resultado = :resultado WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':resultado' => $_POST['resultado'],
        ':id' => $_POST['id']
    ]);
    
    $_SESSION['paginaActual'] = 'partidos';
    echo "<script>window.location.href='menu.php';</script>";
    exit();
}

/* ===== CONSULTA FILTRADA POR CLUB ===== */
// Esta consulta trae los partidos donde al menos uno de los equipos pertenece al club del usuario
$sql_consulta = "
    SELECT p.*, 
           el.nombre AS local_nombre, 
           ev.nombre AS visitante_nombre
    FROM partidos p
    INNER JOIN equipos el ON p.equipo_local_id = el.id
    INNER JOIN equipos ev ON p.equipo_visitante_id = ev.id
    WHERE el.equipo_id = :club_id OR ev.equipo_id = :club_id
    ORDER BY p.fecha DESC
";
$stmt = $pdo->prepare($sql_consulta);
$stmt->execute([':club_id' => $club_id]);
$partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lista de equipos del club para el formulario
$stmt_eq = $pdo->prepare("SELECT id, nombre FROM equipos WHERE equipo_id = :club_id ORDER BY nombre ASC");
$stmt_eq->execute([':club_id' => $club_id]);
$mis_equipos = $stmt_eq->fetchAll(PDO::FETCH_ASSOC);

// Lista de todos los equipos (para elegir rivales)
$todos_equipos = $pdo->query("SELECT id, nombre FROM equipos ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="partidos-grid">
    <?php foreach ($partidos as $fila): ?>
        <div class="partido-card">
            <div class="partido-equipos">
                <?= htmlspecialchars($fila['local_nombre'] ?? 'Equipo') ?> 
                vs 
                <?= htmlspecialchars($fila['visitante_nombre'] ?? 'Equipo') ?>
            </div>

            <div class="partido-info">
                Fecha: <?= htmlspecialchars($fila['fecha'] ?? '') ?>
            </div>

            <div class="resultado">
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $fila['id'] ?>">
                    <input type="text" name="resultado" value="<?= htmlspecialchars($fila['resultado'] ?? '') ?>" placeholder="0-0">
                    <button type="submit" name="actualizar">Guardar</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<h2>Registrar Partido de Club</h2>

<div class="form-box">
    <form method="POST">
        <div class="form-group">
            <label>Equipo del Club (Local)</label>
            <select name="equipo_local_id" required>
                <option value="">Seleccionar equipo de mi club</option>
                <?php foreach ($mis_equipos as $eq): ?>
                    <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Equipo Rival (Visitante)</label>
            <select name="equipo_visitante_id" required>
                <option value="">Seleccionar rival</option>
                <?php foreach ($todos_equipos as $eq): ?>
                    <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?></option>
                <?php endforeach; ?>
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