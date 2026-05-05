<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: menu.php");
    exit;
}

$usuario_id = $_SESSION['user']['id'] ?? 0;
$partido_id = isset($_POST['partido_id']) ? (int)$_POST['partido_id'] : 0;
$goles = isset($_POST['goles']) ? (int)$_POST['goles'] : 0;
$asistencias = isset($_POST['asistencias']) ? (int)$_POST['asistencias'] : 0;
$minutos = isset($_POST['minutos_jugados']) ? (int)$_POST['minutos_jugados'] : 0;
$amarilla = isset($_POST['tarjeta_amarilla']) ? 1 : 0;
$roja = isset($_POST['tarjeta_roja']) ? 1 : 0;

if ($usuario_id <= 0 || $partido_id <= 0) {
    header("Location: menu.php");
    exit;
}

foreach (['goles' => $goles, 'asistencias' => $asistencias, 'minutos_jugados' => $minutos] as $k => $v) {
    if ($v < 0) {
        $_SESSION['flash_error'] = "Valores inválidos.";
        $_SESSION['paginaActual'] = 'partidos';
        header("Location: menu.php");
        exit;
    }
}

$stmtJ = $pdo->prepare("SELECT id, equipo_id FROM jugadores WHERE usuario_id = :uid LIMIT 1");
$stmtJ->execute([':uid' => $usuario_id]);
$jug = $stmtJ->fetch(PDO::FETCH_ASSOC);
$jugador_id = (int)($jug['id'] ?? 0);
$equipo_id  = (int)($jug['equipo_id'] ?? 0);

if ($jugador_id <= 0 || $equipo_id <= 0) {
    header("Location: menu.php");
    exit;
}

// Verificar que el partido pertenezca a su equipo y ya esté jugado (fecha pasada)
$stmtP = $pdo->prepare("
    SELECT id, resultado, equipo_local_id, equipo_visitante_id
    FROM partidos
    WHERE id = :pid
      AND (equipo_local_id = :eid OR equipo_visitante_id = :eid2)
      AND fecha < CURDATE()
    LIMIT 1
");
$stmtP->execute([':pid' => $partido_id, ':eid' => $equipo_id, ':eid2' => $equipo_id]);
$p = $stmtP->fetch(PDO::FETCH_ASSOC);
if (!$p) {
    $_SESSION['flash_error'] = "No puedes editar estadísticas de este partido.";
    $_SESSION['paginaActual'] = 'partidos';
    header("Location: menu.php");
    exit;
}

// Validar que (goles + asistencias) no supere los goles del equipo en ese partido
$resultado = trim((string)($p['resultado'] ?? ''));
if ($resultado !== '' && str_contains($resultado, '-')) {
    [$gl, $gv] = array_map('trim', explode('-', $resultado, 2));
    $gl = (int)$gl;
    $gv = (int)$gv;
    $soyLocal = ((int)$p['equipo_local_id'] === $equipo_id);
    $golesEquipo = $soyLocal ? $gl : $gv;
    if (($goles + $asistencias) > $golesEquipo) {
        $_SESSION['flash_error'] = "La suma de goles + asistencias no puede superar los goles del equipo ($golesEquipo).";
        $_SESSION['paginaActual'] = 'partidos';
        header("Location: menu.php");
        exit;
    }
}

// Upsert
$stmtUp = $pdo->prepare("
    INSERT INTO estadisticas_jugador (jugador_id, partido_id, goles, asistencias, minutos_jugados, tarjetas_amarillas, tarjetas_rojas)
    VALUES (:jid, :pid, :g, :a, :m, :ta, :tr)
    ON DUPLICATE KEY UPDATE
        goles = VALUES(goles),
        asistencias = VALUES(asistencias),
        minutos_jugados = VALUES(minutos_jugados),
        tarjetas_amarillas = VALUES(tarjetas_amarillas),
        tarjetas_rojas = VALUES(tarjetas_rojas)
");
$stmtUp->execute([
    ':jid' => $jugador_id,
    ':pid' => $partido_id,
    ':g' => $goles,
    ':a' => $asistencias,
    ':m' => $minutos,
    ':ta' => $amarilla,
    ':tr' => $roja,
]);

$_SESSION['paginaActual'] = 'partidos';
header("Location: menu.php");
exit;
