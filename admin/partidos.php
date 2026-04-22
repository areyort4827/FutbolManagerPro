<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../config/conexion.php");

/* ===== GUARDAR NUEVO PARTIDO (ADMIN) ===== */
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

/* ===== ACTUALIZAR RESULTADO ===== */
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

/* ===== CONSULTA CON NOMBRES DE EQUIPOS ===== */
// Hacemos LEFT JOIN para obtener los nombres reales de la tabla equipos
$sql_consulta = "
    SELECT p.*, 
           el.nombre AS local_nombre, 
           ev.nombre AS visitante_nombre
    FROM partidos p
    LEFT JOIN equipos el ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id
    ORDER BY p.fecha DESC
";
$stmt = $pdo->query($sql_consulta);
$partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtenemos todos los equipos para los selectores del formulario
$equipos_stmt = $pdo->query("SELECT id, nombre FROM equipos ORDER BY nombre ASC");
$lista_equipos = $equipos_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div id="partidos-grid">
    <?php foreach ($partidos as $fila) { ?>
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
    <?php } ?>
</div>

<h2>Añadir Partido</h2>

<div class="form-box">
    <form method="POST">
        <div class="form-group">
            <label>Equipo Local</label>
            <select name="equipo_local_id" required>
                <option value="">Seleccionar equipo</option>
                <?php foreach ($lista_equipos as $eq): ?>
                    <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Equipo Visitante</label>
            <select name="equipo_visitante_id" required>
                <option value="">Seleccionar equipo</option>
                <?php foreach ($lista_equipos as $eq): ?>
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