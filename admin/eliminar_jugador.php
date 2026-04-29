<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id         = (int)$_GET['id'];
    $definitivo = isset($_GET['definitivo']) && $_GET['definitivo'] == '1';

    if ($definitivo) {
        // BORRADO DEFINITIVO: el jugador ya estaba sin equipo
        $stmt = $pdo->prepare("DELETE FROM jugadores WHERE id = ? AND eliminado = 1");
        $stmt->execute([$id]);
    } else {
        // SOFT DELETE: quitar del equipo pero conservar en BD
        $stmt = $pdo->prepare("
            UPDATE jugadores
            SET eliminado = 1,
                equipo_anterior_id = equipo_id,
                equipo_id = NULL
            WHERE id = ?
        ");
        $stmt->execute([$id]);
    }

    $_SESSION['paginaActual'] = 'jugadores';
    header("Location: menu.php");
    exit;
}
