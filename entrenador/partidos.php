<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../config/conexion.php");

/* ===== OBTENER EQUIPO DEL ENTRENADOR ===== */

$usuario_id = $_SESSION['user']['id'] ?? 0;

$sql = "SELECT equipo_id 
        FROM entrenadores 
        WHERE usuario_id = :id";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':id' => $usuario_id
]);

$entrenador = $stmt->fetch(PDO::FETCH_ASSOC);
$mi_equipo_id = $entrenador['equipo_id'] ?? 0;


/* ===== GUARDAR NUEVO PARTIDO ===== */

if (isset($_POST['guardar'])) {

    $tipo = $_POST['tipo_partido'] ?? 'local';
    $rival_id = $_POST['rival'] ?? 0;
    $fecha = $_POST['fecha'] ?? '';
    $resultado = $_POST['resultado'] ?? null;

    if ($tipo == "local") {
        $local_id = $mi_equipo_id;
        $visitante_id = $rival_id;
    } else {
        $local_id = $rival_id;
        $visitante_id = $mi_equipo_id;
    }

    if ($local_id > 0 && $visitante_id > 0 && !empty($fecha)) {

        $sql_insert = "INSERT INTO partidos
            (equipo_local_id, equipo_visitante_id, fecha, resultado)
            VALUES (:local, :visitante, :fecha, :resultado)";

        $stmt_insert = $pdo->prepare($sql_insert);

        $stmt_insert->execute([
            ':local' => $local_id,
            ':visitante' => $visitante_id,
            ':fecha' => $fecha,
            ':resultado' => $resultado
        ]);

        $_SESSION['paginaActual'] = 'partidos';

        echo "<script>window.location.href='menu.php';</script>";
        exit();
    }
}


/* ===== ACTUALIZAR RESULTADO ===== */

if (isset($_POST['actualizar'])) {

    $sql_update = "UPDATE partidos
                   SET resultado = :resultado
                   WHERE id = :id";

    $stmt_update = $pdo->prepare($sql_update);

    $stmt_update->execute([
        ':resultado' => $_POST['resultado'],
        ':id' => $_POST['id']
    ]);

    $_SESSION['paginaActual'] = 'partidos';

    echo "<script>window.location.href='menu.php';</script>";
    exit();
}

/* ===== ELIMINAR PARTIDO ===== */
if (isset($_POST['eliminar'])) {

    $sql_delete = "DELETE FROM partidos WHERE id = :id";
    $stmt_delete = $pdo->prepare($sql_delete);

    $stmt_delete->execute([
        ':id' => $_POST['id']
    ]);

    $_SESSION['paginaActual'] = 'partidos';

    echo "<script>window.location.href='menu.php';</script>";
    exit();
}


/* ===== PRÓXIMOS PARTIDOS ===== */

$sql_proximos = "
    SELECT p.*,
           el.nombre AS local,
           ev.nombre AS visitante
    FROM partidos p
    LEFT JOIN equipos el
        ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev
        ON p.equipo_visitante_id = ev.id
    WHERE (
        p.equipo_local_id = :mi_id
        OR
        p.equipo_visitante_id = :mi_id
    )
    AND p.fecha >= CURDATE()
    ORDER BY p.fecha ASC
";

$stmt_proximos = $pdo->prepare($sql_proximos);
$stmt_proximos->execute([
    ':mi_id' => $mi_equipo_id
]);

$proximos = $stmt_proximos->fetchAll(PDO::FETCH_ASSOC);


/* ===== HISTORIAL DE PARTIDOS ===== */

$sql_historial = "
    SELECT p.*,
           el.nombre AS local,
           ev.nombre AS visitante
    FROM partidos p
    LEFT JOIN equipos el
        ON p.equipo_local_id = el.id
    LEFT JOIN equipos ev
        ON p.equipo_visitante_id = ev.id
    WHERE (
        p.equipo_local_id = :mi_id
        OR
        p.equipo_visitante_id = :mi_id
    )
    AND p.fecha < CURDATE()
    ORDER BY p.fecha DESC
";

$stmt_historial = $pdo->prepare($sql_historial);
$stmt_historial->execute([
    ':mi_id' => $mi_equipo_id
]);

$historial = $stmt_historial->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="partidos-header">
    <h1>Gestión de Partidos</h1>
</div>

<div class="tabs-header">

    <div class="tabs">

        <button
            type="button"
            class="tab active"
            onclick="mostrarTab('proximos', this)">
            Próximos Partidos
        </button>

        <button
            type="button"
            class="tab"
            onclick="mostrarTab('historial', this)">
            Historial
        </button>

    </div>

    <button
        type="button"
        class="boton-add"
        onclick="abrirModal()">
        + Añadir Partido
    </button>

</div>


<!-- PRÓXIMOS -->

<div id="proximos" class="contenido-tab">

    <h2>Próximos Partidos</h2>

    <div id="partidos-grid">

        <?php foreach ($proximos as $fila): ?>

            <div class="partido-card">

                <div class="partido-equipos">
                    <?= htmlspecialchars($fila['local']) ?>
                    vs
                    <?= htmlspecialchars($fila['visitante']) ?>
                </div>

                <div class="partido-info">
                    📅 <?= date("d M Y", strtotime($fila['fecha'])) ?>
                </div>

                <div class="resultado">

                    <div class="resultado-pendiente">
                        Resultado pendiente
                    </div>

                    <form method="POST" style="margin-top: 12px;">
                        <input
                            type="hidden"
                            name="id"
                            value="<?= $fila['id'] ?>">

                        <button
                            type="submit"
                            name="eliminar"
                            class="btn-eliminar"
                            onclick="return confirm('¿Eliminar este partido?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>

                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>


<!-- HISTORIAL -->

<div
    id="historial"
    class="contenido-tab"
    style="display:none;">

    <h2>Historial de Partidos</h2>

    <div id="partidos-grid">

        <?php foreach ($historial as $fila): ?>

            <div class="partido-card">

                <div class="partido-equipos">
                    <?= htmlspecialchars($fila['local']) ?>
                    vs
                    <?= htmlspecialchars($fila['visitante']) ?>
                </div>

                <div class="partido-info">
                    📅 <?= date("d M Y", strtotime($fila['fecha'])) ?>
                </div>

                <div class="resultado">
                    <form method="POST">

                        <input
                            type="hidden"
                            name="id"
                            value="<?= $fila['id'] ?>">

                        <input
                            type="text"
                            name="resultado"
                            value="<?= htmlspecialchars($fila['resultado'] ?? '') ?>">

                        <button
                            type="submit"
                            name="actualizar"
                            class="btn-guardar">
                            <i class="fa-solid fa-floppy-disk"></i>
                        </button>

                        <button
                            type="submit"
                            name="eliminar"
                            class="btn-eliminar"
                            onclick="return confirm('¿Eliminar este partido?')">
                            <i class="fa-solid fa-trash"></i>
                        </button>

                    </form>
                </div>

            </div>

        <?php endforeach; ?>

    </div>

</div>


<!-- MODAL -->

<div id="modalPartido" class="modal">

    <div class="modal-contenido">

        <span class="cerrar" onclick="cerrarModal()">
            &times;
        </span>

        <h2>Añadir Partido</h2>

        <form method="POST">

            <div class="form-group">
                <label>Mi equipo juega como</label>

                <select
                    name="tipo_partido"
                    id="tipo_partido"
                    onchange="cambiarTipo()"
                    required>

                    <option value="local">Local</option>
                    <option value="visitante">Visitante</option>

                </select>
            </div>

            <div class="form-group">
                <label id="label_local">
                    Equipo local
                </label>

                <?php
                $stmt_nombre = $pdo->prepare("
        SELECT nombre
        FROM equipos
        WHERE id = :mi_id
    ");

                $stmt_nombre->execute([
                    ':mi_id' => $mi_equipo_id
                ]);

                $nombre_mi_equipo = $stmt_nombre->fetchColumn();
                ?>

                <input
                    type="text"
                    id="campo_local"
                    value="<?= htmlspecialchars($nombre_mi_equipo) ?>"
                    disabled>
            </div>

            <div class="form-group">
                <label id="label_rival">
                    Equipo visitante (Rival)
                </label>

                <select
                    name="rival"
                    required>

                    <option value="">
                        Seleccionar rival
                    </option>

                    <?php
                    $sql_rivales = "SELECT id, nombre
                                    FROM equipos
                                    WHERE id != :id";

                    $stmt_rivales = $pdo->prepare($sql_rivales);
                    $stmt_rivales->execute([
                        ':id' => $mi_equipo_id
                    ]);

                    foreach ($stmt_rivales as $rival) {
                        echo "
                            <option value='{$rival['id']}'>
                                {$rival['nombre']}
                            </option>
                        ";
                    }
                    ?>

                </select>
            </div>

            <div class="form-group">
                <label>Fecha</label>

                <input
                    type="date"
                    name="fecha"
                    id="fecha_partido"
                    onchange="validarResultado()"
                    required>
            </div>

            <div class="form-group">
                <label>Resultado</label>

                <input
                    type="text"
                    name="resultado"
                    id="resultado_partido"
                    placeholder="Se añadirá después del partido"
                    disabled>
            </div>

            <button
                type="submit"
                name="guardar"
                class="boton-add">

                Guardar Partido

            </button>

        </form>

    </div>

</div>

<script src="../assets/js/partidos.js"></script>