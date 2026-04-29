<?php
session_start();
require_once '../config/conexion.php';

$club_id = $_SESSION['club_id'] ?? 0;
if ($club_id == 0 || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: menu.php');
    exit;
}

$jugador_id = (int)$_POST['jugador_id'];
$equipo_id  = (int)$_POST['equipo_id'];

if ($jugador_id > 0 && $equipo_id > 0) {
    // Verificar que el equipo pertenece al club
    $check = $pdo->prepare("SELECT id FROM equipos WHERE id = :eq AND equipo_id = :club");
    $check->execute([':eq' => $equipo_id, ':club' => $club_id]);
    if ($check->fetch()) {
        $stmt = $pdo->prepare("
            UPDATE jugadores
            SET equipo_id = :equipo_id,
                eliminado = 0,
                equipo_anterior_id = NULL
            WHERE id = :id
        ");
        $stmt->execute([':equipo_id' => $equipo_id, ':id' => $jugador_id]);
    }
}

$_SESSION['paginaActual'] = 'jugadores';
header('Location: menu.php');
exit;
