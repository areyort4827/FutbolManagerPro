<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../config/conexion.php");

/* ===== GUARDAR NUEVO PARTIDO (ADMIN) ===== */
if (isset($_POST['guardar'])) {
    $local = $_POST['equipo_local_id'];
    $visitante = $_POST['equipo_visitante_id'];
    $fecha = $_POST['fecha'];
    $resultado = $_POST['resultado'];

    $sql = "INSERT INTO partidos (equipo_local_id, equipo_visitante_id, fecha, resultado)
            VALUES (:local, :visitante, :fecha, :resultado)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':local' => $local,
        ':visitante' => $visitante,
        ':fecha' => $fecha,
        ':resultado' => $resultado
    ]);
}

/* ===== ACTUALIZAR RESULTADO ===== */
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

/* ===== CONSULTA DE PARTIDOS Y EQUIPOS ===== */
$partidos = $pdo->query("SELECT p.*, el.nombre AS local, ev.nombre AS visitante FROM partidos p 
    LEFT JOIN equipos el ON p.equipo_local_id = el.id 
    LEFT JOIN equipos ev ON p.equipo_visitante_id = ev.id 
    ORDER BY p.fecha DESC")->fetchAll(PDO::FETCH_ASSOC);

$lista_equipos = $pdo->query("SELECT id, nombre, categoria FROM equipos ORDER BY nombre ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="partidos-contenedor">
    <div class="partidos-header">
        <h2>Panel de Partidos (Administración)</h2>
    </div>

    <div id="partidos-grid">
        <?php foreach ($partidos as $p): ?>
            <div class="partido-card">
                <div class="partido-equipos">
                    <?= htmlspecialchars($p['local'] ?? 'Local') ?> vs <?= htmlspecialchars($p['visitante'] ?? 'Visitante') ?>
                </div>
                <div class="partido-info">
                    Fecha: <?= htmlspecialchars($p['fecha']) ?>
                </div>
                <div class="resultado">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                        <input type="text" name="resultado" value="<?= htmlspecialchars($p['resultado'] ?? '') ?>" style="width: 60px; padding: 4px; border-radius: 4px; border: 1px solid #ccc;">
                        <button type="submit" name="actualizar" class="boton">Guardar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #ccc;">

    <h2>Añadir Partido</h2>
    <div class="form-box">
        <form method="POST">
            <div class="form-group">
                <label>Equipo Local</label>
                <select name="equipo_local_id" required>
                    <option value="">Seleccionar local</option>
                    <?php foreach ($lista_equipos as $eq): ?>
                        <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?> (<?= $eq['categoria'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Equipo Visitante</label>
                <select name="equipo_visitante_id" required>
                    <option value="">Seleccionar visitante</option>
                    <?php foreach ($lista_equipos as $eq): ?>
                        <option value="<?= $eq['id'] ?>"><?= htmlspecialchars($eq['nombre']) ?> (<?= $eq['categoria'] ?>)</option>
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
</div>