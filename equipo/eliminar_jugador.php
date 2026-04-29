<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // SOFT DELETE: guardamos el equipo anterior y marcamos como eliminado,
    // pero NO borramos el registro. El jugador queda en la BD sin equipo.
    $sql = "UPDATE jugadores
            SET eliminado = 1,
                equipo_anterior_id = equipo_id,
                equipo_id = NULL
            WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    $_SESSION['paginaActual'] = 'jugadores';
    header("Location: menu.php");
    exit;
}
