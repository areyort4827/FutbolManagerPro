<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/config/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode([]);
    exit;
}

$partido_id = (int)($_GET['partido_id'] ?? 0);
if ($partido_id <= 0) {
    echo json_encode([]);
    exit;
}

$rol = $_SESSION['user']['rol'] ?? '';

// Filtrar jugadores según el rol
if ($rol === 'entrenador') {
    $equipo_id = (int)($_GET['equipo_id'] ?? 0);
    $stmt = $pdo->prepare("
        SELECT ej.*
        FROM estadisticas_jugador ej
        INNER JOIN jugadores j ON ej.jugador_id = j.id
        WHERE ej.partido_id = :pid AND j.equipo_id = :eid
    ");
    $stmt->execute([':pid' => $partido_id, ':eid' => $equipo_id]);

} elseif ($rol === 'equipo') {
    $club_id = (int)($_GET['club_id'] ?? 0);
    $stmt = $pdo->prepare("
        SELECT ej.*
        FROM estadisticas_jugador ej
        INNER JOIN jugadores j ON ej.jugador_id = j.id
        INNER JOIN equipos e ON j.equipo_id = e.id
        WHERE ej.partido_id = :pid AND e.equipo_id = :cid
    ");
    $stmt->execute([':pid' => $partido_id, ':cid' => $club_id]);

} else {
    echo json_encode([]);
    exit;
}

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));