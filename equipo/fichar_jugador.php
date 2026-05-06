<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user'])) {
    header('Location: menu.php');
    exit;
}

$club_id    = $_SESSION['club_id'] ?? 0;
$jugador_id = (int)($_POST['jugador_id'] ?? 0);
$equipo_id  = (int)($_POST['equipo_id']  ?? 0);

if ($jugador_id > 0 && $equipo_id > 0 && $club_id > 0) {
    // Verificar que el equipo pertenece al club
    $check_eq = $pdo->prepare("SELECT id FROM equipos WHERE id = :eq AND equipo_id = :club");
    $check_eq->execute([':eq' => $equipo_id, ':club' => $club_id]);

    // Verificar que el jugador está libre (equipo_id IS NULL, igual que admin)
    $check_j = $pdo->prepare("SELECT id FROM jugadores WHERE id = :id  AND equipo_id IS NULL");
    $check_j->execute([':id' => $jugador_id]);

    if ($check_eq->fetch() && $check_j->fetch()) {
        $stmt = $pdo->prepare("UPDATE jugadores SET equipo_id = :equipo_id, eliminado = 0 WHERE id = :id");
        $stmt->execute([':equipo_id' => $equipo_id, ':id' => $jugador_id]);
        $_SESSION['fichaje_msg'] = ['tipo' => 'success', 'texto' => 'Jugador fichado correctamente.'];
    } else {
        $_SESSION['fichaje_msg'] = ['tipo' => 'error', 'texto' => 'El jugador ya no está disponible o el equipo no es válido.'];
    }
} else {
    $_SESSION['fichaje_msg'] = ['tipo' => 'error', 'texto' => 'Datos incorrectos.'];
}

$_SESSION['paginaActual'] = 'fichajes';
header('Location: menu.php');
exit;
