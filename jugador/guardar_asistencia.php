<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: menu.php");
    exit;
}

$usuario_id = $_SESSION['user']['id'] ?? 0;
$entrenamiento_id = isset($_POST['entrenamiento_id']) ? (int)$_POST['entrenamiento_id'] : 0;
$asistio = isset($_POST['asistio']) ? 1 : 0;

if ($usuario_id <= 0 || $entrenamiento_id <= 0) {
    header("Location: menu.php");
    exit;
}

// Jugador asociado al usuario
$stmtJ = $pdo->prepare("SELECT id, equipo_id FROM jugadores WHERE usuario_id = :uid LIMIT 1");
$stmtJ->execute([':uid' => $usuario_id]);
$jug = $stmtJ->fetch(PDO::FETCH_ASSOC);
$jugador_id = (int)($jug['id'] ?? 0);
$equipo_id  = (int)($jug['equipo_id'] ?? 0);

if ($jugador_id <= 0 || $equipo_id <= 0) {
    header("Location: menu.php");
    exit;
}

// Verificar que el entrenamiento sea de su equipo
$stmtE = $pdo->prepare("SELECT id FROM entrenamientos WHERE id = :eid AND equipo_id = :eqid LIMIT 1");
$stmtE->execute([':eid' => $entrenamiento_id, ':eqid' => $equipo_id]);
if (!$stmtE->fetchColumn()) {
    header("Location: menu.php");
    exit;
}

// Evitar duplicados: borramos cualquier registro previo y reinsertamos uno solo
$pdo->beginTransaction();
try {
    $stmtDel = $pdo->prepare("
        DELETE FROM entrenamiento_asistencia
        WHERE entrenamiento_id = :entrenamiento_id AND jugador_id = :jugador_id
    ");
    $stmtDel->execute([
        ':entrenamiento_id' => $entrenamiento_id,
        ':jugador_id' => $jugador_id,
    ]);

    $stmtIns = $pdo->prepare("
        INSERT INTO entrenamiento_asistencia (entrenamiento_id, jugador_id, asistio)
        VALUES (:entrenamiento_id, :jugador_id, :asistio)
    ");
    $stmtIns->execute([
        ':entrenamiento_id' => $entrenamiento_id,
        ':jugador_id' => $jugador_id,
        ':asistio' => $asistio,
    ]);
    $pdo->commit();
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
}

$_SESSION['paginaActual'] = 'entrenamientos';
header("Location: menu.php");
exit;
