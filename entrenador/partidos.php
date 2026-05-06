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
$error_partido = '';
$abrir_modal = false;

/* ===== VALIDAR RESULTADO + GOLEADORES ===== */

if (isset($_POST['guardar'])) {

    $tipo = $_POST['tipo_partido'] ?? 'local';
    $rival_id = $_POST['rival'] ?? 0;
    $fecha = $_POST['fecha'] ?? '';
    $resultado = null;

    /* Si la fecha es futura -> NO resultado */
    if (!empty($fecha) && $fecha < date('Y-m-d')) {

        if (empty($_POST['resultado'])) {
            $error_partido = "Debes ingresar el resultado del partido.";
             $abrir_modal = true;
        } else {
            $resultado = trim($_POST['resultado']);
        }
    }

    /* Definir local y visitante */
    if ($tipo == "local") {
        $local_id = $mi_equipo_id;
        $visitante_id = $rival_id;
        $mis_goles = 0;
    } else {
        $local_id = $rival_id;
        $visitante_id = $mi_equipo_id;
        $mis_goles = 0;
    }

    /* =====================================
       VALIDAR RESULTADO
    ===================================== */

    if (!empty($resultado) && empty($error_partido)) {

        /* validar formato tipo 2-1 */
        if (!preg_match('/^\d+\-\d+$/', $resultado)) {
            $error_partido = "El resultado debe tener formato 2-1";
            $abrir_modal = true;
        } else {
            $partes = explode('-', $resultado);

            $goles_local = (int)$partes[0];
            $goles_visitante = (int)$partes[1];

            /* Si soy local -> uso primer número */
            if ($tipo == "local") {
                $mis_goles = $goles_local;
            } else {
                $mis_goles = $goles_visitante;
            }

            /* sumar goles registrados */
            $total_goleadores = 0;

            if (isset($_POST['cantidad_goles']) && isset($_POST['jugador_id'])) {
                foreach ($_POST['cantidad_goles'] as $index => $gol) {
                    if (!empty($_POST['jugador_id'][$index])) {
                        $total_goleadores += (int)$gol;
                    }
                }
            }

            /* validar coincidencia exacta */
            if ($total_goleadores != $mis_goles) {
                $error_partido = "Error: Tu equipo marcó $mis_goles goles, pero asignaste $total_goleadores. Deben coincidir exactamente.";
                $abrir_modal = true;
            }
        }
    }

    /* =====================================
       GUARDAR PARTIDO
    ===================================== */

    if (
        empty($error_partido) && $local_id > 0 && $visitante_id > 0 && !empty($fecha)
    ) {

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

        /* =====================================
           GUARDAR GOLEADORES
        ===================================== */

        if (
            !empty($resultado) &&
            isset($_POST['jugador_id']) &&
            isset($_POST['cantidad_goles'])
        ) {

            foreach ($_POST['jugador_id'] as $index => $jugador_id) {

                $jugador_id = (int)$jugador_id;
                $cantidad_goles = (int)$_POST['cantidad_goles'][$index];

                if ($jugador_id > 0 && $cantidad_goles > 0) {

                    $sql_gol = "INSERT INTO goles_partido
                        (partido_id, jugador_id, cantidad_goles)
                        VALUES (:partido_id, :jugador_id, :cantidad_goles)";

                    $stmt_gol = $pdo->prepare($sql_gol);

                    $stmt_gol->execute([
                        ':partido_id' => $partido_id,
                        ':jugador_id' => $jugador_id,
                        ':cantidad_goles' => $cantidad_goles
                    ]);
                }
            }
        }

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

/* ===== GUARDAR ESTADÍSTICAS DE PARTIDO ===== */
if (isset($_POST['guardar_estadisticas'])) {
    $partido_id = (int)($_POST['partido_id'] ?? 0);
    if ($partido_id > 0) {
        // Borrar estadísticas previas de jugadores de este equipo en este partido
        $jugadores_equipo = $pdo->prepare("SELECT id FROM jugadores WHERE equipo_id = :eid");
        $jugadores_equipo->execute([':eid' => $mi_equipo_id]);
        $ids_jugadores = array_column($jugadores_equipo->fetchAll(PDO::FETCH_ASSOC), 'id');

        if (!empty($ids_jugadores)) {
            $in = implode(',', array_map('intval', $ids_jugadores));
            $pdo->exec("DELETE FROM estadisticas_jugador WHERE partido_id = $partido_id AND jugador_id IN ($in)");
            $pdo->exec("DELETE FROM goles_partido WHERE partido_id = $partido_id AND jugador_id IN ($in)");
        }

        // Guardar resultado si viene
        $nuevo_resultado = trim($_POST['resultado_partido'] ?? '');
        if ($nuevo_resultado !== '') {
            $pdo->prepare("UPDATE partidos SET resultado = :r WHERE id = :id")
                ->execute([':r' => $nuevo_resultado, ':id' => $partido_id]);
        }

        // Guardar estadísticas por jugador
        $jugadores_post = $_POST['jugadores'] ?? [];
        foreach ($jugadores_post as $jid => $stats) {
            $jid = (int)$jid;
            $goles       = max(0, (int)($stats['goles'] ?? 0));
            $asistencias = max(0, (int)($stats['asistencias'] ?? 0));
            $amarillas   = max(0, (int)($stats['amarillas'] ?? 0));
            $rojas       = max(0, (int)($stats['rojas'] ?? 0));
            $minutos     = max(0, (int)($stats['minutos'] ?? 0));

            if ($goles + $asistencias + $amarillas + $rojas + $minutos > 0) {
                $pdo->prepare("INSERT INTO estadisticas_jugador (jugador_id, partido_id, goles, asistencias, tarjetas_amarillas, tarjetas_rojas, minutos_jugados)
                    VALUES (:jid, :pid, :g, :a, :am, :ro, :mi)")
                    ->execute([':jid'=>$jid,':pid'=>$partido_id,':g'=>$goles,':a'=>$asistencias,':am'=>$amarillas,':ro'=>$rojas,':mi'=>$minutos]);

                if ($goles > 0) {
                    $pdo->prepare("INSERT INTO goles_partido (partido_id, jugador_id, cantidad_goles) VALUES (:pid, :jid, :g)")
                        ->execute([':pid'=>$partido_id,':jid'=>$jid,':g'=>$goles]);
                }
            }
        }
    }
    $_SESSION['paginaActual'] = 'partidos';
    echo "<script>window.location.href='menu.php?pagina=partidos';</script>";
    exit();
}

/* ===== CARGAR JUGADORES DEL EQUIPO PARA EL MODAL ===== */
$stmt_jug_modal = $pdo->prepare("SELECT id, nombre, posicion FROM jugadores WHERE equipo_id = :eid ORDER BY nombre ASC");
$stmt_jug_modal->execute([':eid' => $mi_equipo_id]);
$jugadores_modal = $stmt_jug_modal->fetchAll(PDO::FETCH_ASSOC);
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

                    <button
                        type="button"
                        class="btn-stats"
                        onclick="abrirModalStats(<?= $fila['id'] ?>, '<?= htmlspecialchars($fila['local'], ENT_QUOTES) ?> vs <?= htmlspecialchars($fila['visitante'], ENT_QUOTES) ?>', '<?= htmlspecialchars($fila['resultado'] ?? '', ENT_QUOTES) ?>')">
                        <i class="fa-solid fa-chart-bar"></i> Estadísticas
                    </button>
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


                <input
                    type="hidden"
                    name="mi_equipo_id"
                    value="<?= $mi_equipo_id ?>">
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
                    readonly>
            </div>

            <!-- ===== GOLEADORES MÚLTIPLES ===== -->

            <div id="contenedor_goleadores" style="display: none;">

                <div class="goleador-item">

                    <div class="form-group">
                        <label>Jugador que marcó</label>

                        <select name="jugador_id[]">

                            <option value="">
                                Seleccionar jugador
                            </option>

                            <?php
                            $sql_jugadores = "
                    SELECT id, nombre
                    FROM jugadores
                    WHERE equipo_id = :mi_id
                    ORDER BY nombre ASC
                ";

                            $stmt_jugadores = $pdo->prepare($sql_jugadores);
                            $stmt_jugadores->execute([
                                ':mi_id' => $mi_equipo_id
                            ]);

                            foreach ($stmt_jugadores as $jugador) {
                                echo "
                        <option value='{$jugador['id']}'>
                            {$jugador['nombre']}
                        </option>
                    ";
                            }
                            ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label>Cantidad de goles</label>

                        <input
                            type="number"
                            name="cantidad_goles[]"
                            min="1"
                            value="1">
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

<?php if ($abrir_modal): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        if (typeof abrirModal === "function") {
            abrirModal();
        }
        if (typeof validarResultado === "function") {
            validarResultado();
        }
    });
</script>
<?php endif; ?>

<script src="../assets/js/partidos.js"></script>

<!-- ===== MODAL ESTADÍSTICAS ===== -->
<div id="modalStats" class="modal-stats-overlay" style="display:none;">
    <div class="modal-stats-contenido">
        <div class="modal-stats-header">
            <div>
                <h2 class="modal-stats-title"><i class="fa-solid fa-chart-bar"></i> Estadísticas del Partido</h2>
                <p class="modal-stats-sub" id="stats-partido-nombre"></p>
            </div>
            <button type="button" class="modal-stats-cerrar" onclick="cerrarModalStats()">×</button>
        </div>

        <form method="POST" id="formStats">
            <input type="hidden" name="guardar_estadisticas" value="1">
            <input type="hidden" name="partido_id" id="stats-partido-id">

            <div class="stats-resultado-row">
                <label class="stats-label">Resultado del partido</label>
                <input type="text" name="resultado_partido" id="stats-resultado" placeholder="Ej: 2-1" class="stats-resultado-input">
            </div>

            <div class="stats-table-wrap">
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>Jugador</th>
                            <th><i class="fa-solid fa-futbol"></i> Goles</th>
                            <th><i class="fa-solid fa-handshake-simple"></i> Asistencias</th>
                            <th><i class="fa-solid fa-square" style="color:#eab308"></i> Amarillas</th>
                            <th><i class="fa-solid fa-square" style="color:#ef4444"></i> Rojas</th>
                            <th><i class="fa-regular fa-clock"></i> Minutos</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jugadores_modal as $jug): ?>
                        <tr>
                            <td class="stats-jugador-nombre">
                                <?= htmlspecialchars($jug['nombre']) ?>
                                <span class="stats-posicion"><?= htmlspecialchars($jug['posicion']) ?></span>
                            </td>
                            <td><input type="number" name="jugadores[<?= $jug['id'] ?>][goles]" min="0" value="0" class="stats-num-input" data-jugador="<?= $jug['id'] ?>"></td>
                            <td><input type="number" name="jugadores[<?= $jug['id'] ?>][asistencias]" min="0" value="0" class="stats-num-input"></td>
                            <td><input type="number" name="jugadores[<?= $jug['id'] ?>][amarillas]" min="0" max="2" value="0" class="stats-num-input stats-amarilla"></td>
                            <td><input type="number" name="jugadores[<?= $jug['id'] ?>][rojas]" min="0" max="1" value="0" class="stats-num-input stats-roja"></td>
                            <td><input type="number" name="jugadores[<?= $jug['id'] ?>][minutos]" min="0" max="120" value="0" class="stats-num-input stats-minutos"></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="stats-footer">
                <button type="button" class="stats-btn-cancelar" onclick="cerrarModalStats()">Cancelar</button>
                <button type="submit" class="stats-btn-guardar"><i class="fa-solid fa-floppy-disk"></i> Guardar Estadísticas</button>
            </div>
        </form>
    </div>
</div>

<style>
.btn-stats {
    margin-top: 8px;
    width: 100%;
    padding: 8px 12px;
    border-radius: 10px;
    border: none;
    background: rgba(99,102,241,0.12);
    color: #4f46e5;
    font-weight: 700;
    font-size: 13px;
    cursor: pointer;
    transition: 0.15s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
.btn-stats:hover { background: rgba(99,102,241,0.22); transform: translateY(-1px); }

.modal-stats-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,0.55);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.modal-stats-contenido {
    background: white;
    border-radius: 18px;
    width: 100%;
    max-width: 820px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 25px 60px rgba(0,0,0,0.2);
    display: flex;
    flex-direction: column;
}
.modal-stats-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 22px 24px 16px;
    border-bottom: 1px solid #e5e7eb;
    position: sticky;
    top: 0;
    background: white;
    z-index: 1;
    border-radius: 18px 18px 0 0;
}
.modal-stats-title { margin: 0; font-size: 20px; color: #0f172a; display: flex; align-items: center; gap: 10px; }
.modal-stats-title i { color: #4f46e5; }
.modal-stats-sub { margin: 4px 0 0; color: #64748b; font-size: 14px; }
.modal-stats-cerrar {
    background: #f1f5f9; border: none; border-radius: 10px;
    width: 34px; height: 34px; font-size: 20px; cursor: pointer;
    color: #475569; line-height: 34px; text-align: center;
    transition: 0.15s; flex-shrink: 0;
}
.modal-stats-cerrar:hover { background: #e2e8f0; }

.stats-resultado-row {
    padding: 16px 24px;
    display: flex;
    align-items: center;
    gap: 14px;
    border-bottom: 1px solid #f1f5f9;
}
.stats-label { font-weight: 700; color: #0f172a; font-size: 14px; white-space: nowrap; }
.stats-resultado-input {
    padding: 10px 14px; border-radius: 10px; border: 2px solid #e5e7eb;
    font-size: 16px; font-weight: 700; width: 120px;
    transition: 0.2s;
}
.stats-resultado-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(99,102,241,0.15); }

.stats-table-wrap { padding: 0 24px; overflow-x: auto; }
.stats-table { width: 100%; border-collapse: collapse; min-width: 600px; }
.stats-table th {
    padding: 10px 8px; text-align: center; font-size: 12px;
    color: #64748b; text-transform: uppercase; letter-spacing: 0.04em;
    border-bottom: 2px solid #e5e7eb; background: #f8fafc;
}
.stats-table th:first-child { text-align: left; }
.stats-table td { padding: 8px; border-bottom: 1px solid #f1f5f9; text-align: center; }
.stats-table tr:last-child td { border-bottom: none; }
.stats-table tr:hover td { background: #f8fafc; }

.stats-jugador-nombre { text-align: left !important; font-weight: 600; color: #0f172a; font-size: 14px; }
.stats-posicion { display: block; font-size: 11px; color: #94a3b8; font-weight: 400; text-transform: capitalize; }

.stats-num-input {
    width: 58px; padding: 7px 4px; border-radius: 8px;
    border: 2px solid #e5e7eb; text-align: center; font-size: 14px;
    font-weight: 700; transition: 0.15s;
}
.stats-num-input:focus { outline: none; border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(99,102,241,0.12); }
.stats-num-input:not([value="0"]) { border-color: #c7d2fe; background: #eef2ff; }
.stats-amarilla:focus { border-color: #eab308; box-shadow: 0 0 0 3px rgba(234,179,8,0.15); }
.stats-roja:focus { border-color: #ef4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.15); }
.stats-minutos { width: 68px !important; }

.stats-footer {
    display: flex; justify-content: flex-end; gap: 12px;
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
    position: sticky; bottom: 0; background: white;
    border-radius: 0 0 18px 18px;
}
.stats-btn-cancelar {
    padding: 10px 20px; border-radius: 10px; border: 2px solid #e5e7eb;
    background: white; color: #64748b; font-weight: 700; cursor: pointer; transition: 0.15s;
}
.stats-btn-cancelar:hover { background: #f8fafc; }
.stats-btn-guardar {
    padding: 10px 24px; border-radius: 10px; border: none;
    background: linear-gradient(145deg, #6366f1, #4f46e5);
    color: white; font-weight: 800; cursor: pointer;
    display: flex; align-items: center; gap: 8px; transition: 0.15s;
    box-shadow: 0 4px 12px rgba(99,102,241,0.3);
}
.stats-btn-guardar:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(99,102,241,0.35); }
</style>

<script>
function abrirModalStats(partidoId, nombre, resultado) {
    document.getElementById('stats-partido-id').value = partidoId;
    document.getElementById('stats-partido-nombre').textContent = nombre;
    document.getElementById('stats-resultado').value = resultado;

    // Reset all inputs
    document.querySelectorAll('#formStats .stats-num-input').forEach(i => {
        i.value = 0;
        i.style.borderColor = '';
        i.style.background = '';
    });

    // Load existing stats via fetch
    fetch('get_estadisticas.php?partido_id=' + partidoId + '&equipo_id=<?= $mi_equipo_id ?>')
        .then(r => r.json())
        .then(data => {
            data.forEach(row => {
                const jid = row.jugador_id;
                const set = (campo, val) => {
                    const el = document.querySelector(`input[name="jugadores[${jid}][${campo}]"]`);
                    if (el) el.value = val;
                };
                set('goles', row.goles);
                set('asistencias', row.asistencias);
                set('amarillas', row.tarjetas_amarillas);
                set('rojas', row.tarjetas_rojas);
                set('minutos', row.minutos_jugados);
            });
        })
        .catch(() => {});

    document.getElementById('modalStats').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function cerrarModalStats() {
    document.getElementById('modalStats').style.display = 'none';
    document.body.style.overflow = '';
}

document.getElementById('modalStats').addEventListener('click', function(e) {
    if (e.target === this) cerrarModalStats();
});
</script>