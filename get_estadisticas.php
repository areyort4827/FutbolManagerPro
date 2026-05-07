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

/* ===== ENTRENADOR ===== */

if ($rol === 'entrenador') {

    $equipo_id = (int)($_GET['equipo_id'] ?? 0);

    $stmt = $pdo->prepare("
        SELECT
            j.id AS jugador_id,
            COALESCE(ej.goles, gp.cantidad_goles, 0) AS goles,
            COALESCE(ej.asistencias, 0) AS asistencias,
            COALESCE(ej.tarjetas_amarillas, 0) AS tarjetas_amarillas,
            COALESCE(ej.tarjetas_rojas, 0) AS tarjetas_rojas,
            COALESCE(ej.minutos_jugados, 0) AS minutos_jugados
        FROM jugadores j
        LEFT JOIN estadisticas_jugador ej
            ON j.id = ej.jugador_id
            AND ej.partido_id = :pid
        LEFT JOIN goles_partido gp
            ON j.id = gp.jugador_id
            AND gp.partido_id = :pid
        WHERE j.equipo_id = :eid
        ORDER BY j.nombre ASC
    ");

    $stmt->execute([
        ':pid' => $partido_id,
        ':eid' => $equipo_id
    ]);

/* ===== EQUIPOS ===== */

} elseif ($rol === 'equipo') {

    $equipo_id = (int)($_GET['equipo_id'] ?? 0);

    $stmt = $pdo->prepare("
        SELECT
            j.id AS jugador_id,
            COALESCE(ej.goles, gp.cantidad_goles, 0) AS goles,
            COALESCE(ej.asistencias, 0) AS asistencias,
            COALESCE(ej.tarjetas_amarillas, 0) AS tarjetas_amarillas,
            COALESCE(ej.tarjetas_rojas, 0) AS tarjetas_rojas,
            COALESCE(ej.minutos_jugados, 0) AS minutos_jugados
        FROM jugadores j
        INNER JOIN equipos e
            ON j.equipo_id = e.id
        LEFT JOIN estadisticas_jugador ej
            ON j.id = ej.jugador_id
            AND ej.partido_id = :pid
        LEFT JOIN goles_partido gp
            ON j.id = gp.jugador_id
            AND gp.partido_id = :pid
      WHERE j.equipo_id = :eid
        ORDER BY e.nombre ASC, j.nombre ASC
    ");

   $stmt->execute([
    ':pid' => $partido_id,
    ':eid' => $equipo_id
]);

} else {
    echo json_encode([]);
    exit;
}

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
exit;