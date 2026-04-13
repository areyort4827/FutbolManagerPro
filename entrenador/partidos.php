<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../config/conexion.php");

/* ===== GUARDAR ===== */
if (isset($_POST['guardar'])) {

    $sql = "INSERT INTO partidos (equipo_local, equipo_visitante, fecha, resultado)
            VALUES (:local, :visitante, :fecha, :resultado)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':local' => $_POST['equipo_local'],
        ':visitante' => $_POST['equipo_visitante'],
        ':fecha' => $_POST['fecha'],
        ':resultado' => $_POST['resultado']
    ]);
}

/* ===== ACTUALIZAR ===== */
if (isset($_POST['actualizar'])) {

    $sql = "UPDATE partidos SET resultado = :resultado WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ':resultado' => $_POST['resultado'],
        ':id' => $_POST['id']
    ]);
}

/* ===== CONSULTA ===== */
$stmt = $pdo->query("SELECT * FROM partidos ORDER BY fecha DESC");
$partidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>Partidos</h2>

<div id="partidos-grid">

<?php foreach ($partidos as $fila) { ?>

    <div class="partido-card">

        <div class="partido-equipos">
            <?= htmlspecialchars($fila['equipo_local']) ?>
            vs
            <?= htmlspecialchars($fila['equipo_visitante']) ?>
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

</div>

<h2 style="margin-top: 40px;">Añadir Partido</h2>

<form method="POST" class="formulario-añadir">

    <input type="text" name="equipo_local" placeholder="Equipo local" required>
    <input type="text" name="equipo_visitante" placeholder="Equipo visitante" required>
    <input type="date" name="fecha" required>
    <input type="text" name="resultado" placeholder="Resultado (Ej: 2-1)">

    <button type="submit" name="guardar">Guardar Partido</button>

</form>