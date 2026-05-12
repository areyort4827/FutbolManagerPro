<?php
session_start();
require_once '../config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user'])) {
    header('Location: menu.php');
    exit;
}

$usuario_id = $_SESSION['user']['id'] ?? 0;
$jugador_id = (int)($_POST['jugador_id'] ?? 0);

// Obtener equipo del entrenador
$stmt = $pdo->prepare("SELECT equipo_id FROM entrenadores WHERE usuario_id = :id");
$stmt->execute([':id' => $usuario_id]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);
$equipo_id = $datos['equipo_id'] ?? 0;

if ($jugador_id > 0 && $equipo_id > 0) {
    // Verificar que el jugador está libre (equipo_id IS NULL, igual que admin)
    $check = $pdo->prepare("SELECT id FROM jugadores WHERE id = :id AND equipo_id IS NULL");
    $check->execute([':id' => $jugador_id]);
    if ($check->fetch()) {
        $stmt_upd = $pdo->prepare("UPDATE jugadores SET equipo_id = :equipo_id, eliminado = 0 WHERE id = :id");
        $stmt_upd->execute([':equipo_id' => $equipo_id, ':id' => $jugador_id]);
        $_SESSION['fichaje_msg'] = ['tipo' => 'success', 'texto' => 'Jugador fichado correctamente.'];
    } else {
        $_SESSION['fichaje_msg'] = ['tipo' => 'error', 'texto' => 'El jugador ya no está disponible.'];
    }
} else {
    $_SESSION['fichaje_msg'] = ['tipo' => 'error', 'texto' => 'Datos incorrectos.'];
}

$_SESSION['paginaActual'] = 'fichajes';
header('Location: menu.php');
exit;
