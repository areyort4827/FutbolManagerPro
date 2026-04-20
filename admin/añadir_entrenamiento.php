<?php
session_start();
require_once "../config/conexion.php";

if ($_POST) {
    $club_id     = $_SESSION['club_id'];
    $titulo      = $_POST['titulo'];
    $equipo_id   = (int)$_POST['equipo_id'];
    $fecha       = $_POST['fecha'];
    $hora        = $_POST['hora'];
    $duracion    = (int)$_POST['duracion'];
    $lugar       = $_POST['lugar'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    if (empty($club_id) && $equipo_id > 0) {
        $stmtClub = $pdo->prepare("SELECT equipo_id FROM equipos WHERE id = ?");
        $stmtClub->execute([$equipo_id]);
        $club_id = $stmtClub->fetchColumn();
    }

    try {
        $sql = "INSERT INTO entrenamientos 
                (club_id, titulo, descripcion, fecha, hora, duracion, lugar, equipo_id, num_asistentes) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$club_id, $titulo, $descripcion, $fecha, $hora, $duracion, $lugar, $equipo_id]);

        $_SESSION['paginaActual'] = 'entrenamientos';
        header("Location: menu.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al guardar: " . $e->getMessage();
    }
}
?>
