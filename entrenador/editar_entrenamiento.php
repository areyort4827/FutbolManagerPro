<?php
session_start();
require_once "../config/conexion.php";

if ($_POST) {
    $id          = (int)$_POST['id'];
    $titulo      = $_POST['titulo'];
    $equipo_id   = (int)$_POST['equipo_id'];
    $fecha       = $_POST['fecha'];
    $hora        = $_POST['hora'];
    $duracion    = (int)$_POST['duracion'];
    $lugar       = $_POST['lugar'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';

    try {
        $sql = "UPDATE entrenamientos SET 
                titulo = ?, equipo_id = ?, fecha = ?, hora = ?, 
                duracion = ?, lugar = ?, descripcion = ? 
                WHERE id = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$titulo, $equipo_id, $fecha, $hora, $duracion, $lugar, $descripcion, $id]);

        $_SESSION['paginaActual'] = 'entrenamientos';
        header("Location: menu.php");
        exit;
    } catch (PDOException $e) {
        echo "Error al actualizar: " . $e->getMessage();
    }
}