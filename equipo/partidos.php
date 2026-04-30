<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . "/../config/conexion.php");

/*OBTENER CLUB DEL USUARIO*/

$club_id = $_SESSION['user']['club_id'] ?? 0;
$error_partido = '';
$abrir_modal = false;

/* ELIMINAR PARTIDO */

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


/* GUARDAR NUEVO PARTIDO */

if (isset($_POST['guardar'])) {

    $tipo = $_POST['tipo_partido'] ?? 'local';
    $equipo_club_id = $_POST['equipo_club_id'] ?? 0;
    $rival_id = $_POST['rival'] ?? 0;
    $fecha = $_POST['fecha'] ?? '';
    $resultado = null;

    /* si la fecha ya pasó → permitir resultado */
    if (!empty($fecha) && $fecha < date('Y-m-d')) {
        if (empty($_POST['resultado'])) {
            $error_partido = "Debes ingresar el resultado del partido.";
            $abrir_modal = true;
        } else {
            $resultado = trim($_POST['resultado']);
        }
    }

    if ($tipo == "local") {
        $local_id = $equipo_club_id;
        $visitante_id = $rival_id;
    } else {
        $local_id = $rival_id;
        $visitante_id = $equipo_club_id;
    }

    if (!empty($resultado) && empty($error_partido)) {
        if (!preg_match('/^\d+\-\d+$/', $resultado)) {
            $error_partido = "El resultado debe tener formato 2-1";
            $abrir_modal = true;
        } else {
            $partes = explode('-', $resultado);
            $mis_goles = ($tipo == "local") ? (int)$partes[0] : (int)$partes[1];

            $total_goleadores = 0;
            if (isset($_POST['cantidad_goles']) && isset($_POST['jugador_id'])) {
                foreach ($_POST['cantidad_goles'] as $index => $gol) {
                    if (!empty($_POST['jugador_id'][$index])) {
                        $total_goleadores += (int)$gol;
                    }
                }
            }

            if ($total_goleadores != $mis_goles) {
                $error_partido = "Error: Tu equipo marcó $mis_goles goles, pero asignaste $total_goleadores. Deben coincidir exactamente.";
                $abrir_modal = true;
            }
        }
    }

    if (empty($error_partido) && $local_id > 0 && $visitante_id > 0 && !empty($fecha)) {

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

        $partido_id = $pdo->lastInsertId();

        if (!empty($resultado) && isset($_POST['jugador_id'])) {
            foreach ($_POST['jugador_id'] as $index => $jugador_id) {
                $cant = (int)$_POST['cantidad_goles'][$index];
                if ($jugador_id > 0 && $cant > 0) {
                    $sql_gol = "INSERT INTO goles_partido (partido_id, jugador_id, cantidad_goles) VALUES (:p, :j, :c)";
                    $pdo->prepare($sql_gol)->execute([':p' => $partido_id, ':j' => $jugador_id, ':c' => $cant]);
                }
            }
        }

        $_SESSION['paginaActual'] = 'partidos';

        echo "<script>window.location.href='menu.php';</script>";
        exit();
    }
}


/* ACTUALIZAR RESULTADO */

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


/* EQUIPOS DE MI CLUB */

$stmt_eq = $pdo->prepare("
    SELECT id, nombre
    FROM equipos
    WHERE equipo_id = :club_id
    ORDER BY nombre ASC
");

$stmt_eq->execute([
    ':club_id' => $club_id
]);

$mis_equipos = $stmt_eq->fetchAll(PDO::FETCH_ASSOC);


/* TODOS LOS RIVALES */

$stmt_rivales = $pdo->query("
    SELECT id, nombre
    FROM equipos
    ORDER BY nombre ASC
");

$todos_equipos = $stmt_rivales->fetchAll(PDO::FETCH_ASSOC);


/* PRÓXIMOS PARTIDOS */

$sql_proximos = "
    SELECT p.*,
           el.nombre AS local,
           ev.nombre AS visitante
    FROM partidos p
    INNER JOIN equipos el
        ON p.equipo_local_id = el.id
    INNER JOIN equipos ev
        ON p.equipo_visitante_id = ev.id
    WHERE (el.equipo_id = :club_id OR ev.equipo_id = :club_id)
    AND p.fecha >= CURDATE()
    ORDER BY p.fecha ASC
";

$stmt_proximos = $pdo->prepare($sql_proximos);
$stmt_proximos->execute([
    ':club_id' => $club_id
]);

$proximos = $stmt_proximos->fetchAll(PDO::FETCH_ASSOC);


/* HISTORIAL */

$sql_historial = "
    SELECT p.*,
           el.nombre AS local,
           ev.nombre AS visitante
    FROM partidos p
    INNER JOIN equipos el
        ON p.equipo_local_id = el.id
    INNER JOIN equipos ev
        ON p.equipo_visitante_id = ev.id
    WHERE (el.equipo_id = :club_id OR ev.equipo_id = :club_id)
    AND p.fecha < CURDATE()
    ORDER BY p.fecha DESC
";

$stmt_historial = $pdo->prepare($sql_historial);
$stmt_historial->execute([
    ':club_id' => $club_id
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


<!-- PROXIMOS -->

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

                    <form method="POST" style="margin-top:12px;">
                        <input type="hidden" name="id" value="<?= $fila['id'] ?>">

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

<div id="historial" class="contenido-tab" style="display:none;">

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

                        <input type="hidden" name="id" value="<?= $fila['id'] ?>">

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

        <?php if (!empty($error_partido)): ?>
            <div class="error-form" style="color: #a94442; background-color: #f2dede; border: 1px solid #ebccd1; padding: 15px; margin-bottom: 20px; border-radius: 4px;">
                <?= $error_partido ?>
            </div>
        <?php endif; ?>

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
                <label id="label_equipo">
                    Equipo local
                </label>

                <select
                    name="equipo_club_id"
                    id="equipo_club_id"
                    onchange="filtrarRivalYJugadores()"
                    required>

                    <option value="">
                        Seleccionar equipo de mi club
                    </option>

                    <?php foreach ($mis_equipos as $eq): ?>
                        <option value="<?= $eq['id'] ?>">
                            <?= htmlspecialchars($eq['nombre']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="form-group">
                <label id="label_rival">
                    Equipo visitante (Rival)
                </label>

                <select
                    name="rival"
                    id="rival_select"
                    required>

                    <option value="">
                        Seleccionar rival
                    </option>

                    <?php foreach ($todos_equipos as $eq): ?>
                        <option value="<?= $eq['id'] ?>" class="opcion-rival">
                            <?= htmlspecialchars($eq['nombre']) ?>
                        </option>
                    <?php endforeach; ?>

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
                    readonly>
            </div>

            <!-- ===== GOLEADORES MÚLTIPLES ===== -->
            <div id="contenedor_goleadores" style="display: none;">
                <div class="goleador-item">
                    <div class="form-group">
                        <label>Jugador que marcó</label>
                        <select name="jugador_id[]" class="select-jugador">
                            <option value="">Seleccionar jugador</option>
                            <?php
                            $stmt_jug = $pdo->prepare("SELECT j.id, j.nombre, j.equipo_id FROM jugadores j INNER JOIN equipos e ON j.equipo_id = e.id WHERE e.equipo_id = :club_id");
                            $stmt_jug->execute([':club_id' => $club_id]);
                            foreach ($stmt_jug->fetchAll() as $jug): ?>
                                <option value="<?= $jug['id'] ?>" data-equipo="<?= $jug['equipo_id'] ?>" class="opcion-jugador">
                                    <?= htmlspecialchars($jug['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cantidad de goles</label>
                        <input type="number" name="cantidad_goles[]" min="1" value="1">
                    </div>
                </div>
            </div>

            <button
                id="btn_agregar_goleador"
                type="button"
                class="boton-add"
                style="display: none;"
                onclick="agregarGoleador()">
                + Añadir otro goleador
            </button>

            <button
                type="submit"
                name="guardar"
                class="boton-add">
                Guardar Partido
            </button>

        </form>

    </div>

</div>

<script>
function filtrarRivalYJugadores() {
    let equipoSeleccionado = document.getElementById("equipo_club_id").value;
    
    // 1. Filtrar rivales (no puedes jugar contra ti mismo)
    let opcionesRival = document.querySelectorAll(".opcion-rival");
    opcionesRival.forEach(opt => {
        if (opt.value === equipoSeleccionado) {
            opt.style.display = "none";
        } else {
            opt.style.display = "block";
        }
    });

    // 2. Filtrar jugadores (mostrar solo los del equipo seleccionado)
    let opcionesJugador = document.querySelectorAll(".opcion-jugador");
    opcionesJugador.forEach(opt => {
        if (opt.getAttribute("data-equipo") === equipoSeleccionado) {
            opt.style.display = "block";
        } else {
            opt.style.display = "none";
        }
    });

    // Resetear selecciones si el equipo cambia
    document.getElementById("rival_select").value = "";
    document.querySelectorAll(".select-jugador").forEach(sel => sel.value = "");
}
</script>

<?php if ($abrir_modal): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof abrirModal === "function") abrirModal();
        if (typeof validarResultado === "function") validarResultado();
    });
</script>
<?php endif; ?>

<script src="../assets/js/partidos.js"></script>