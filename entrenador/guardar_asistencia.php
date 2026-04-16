<?php
session_start();
require_once "../config/conexion.php";

if ($_POST) {
    $entrenamiento_id = (int)$_POST['entrenamiento_id'];
    $num_asistentes   = (int)$_POST['num_asistentes'];

    try {
        $sql = "UPDATE entrenamientos SET num_asistentes = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$num_asistentes, $entrenamiento_id]);

        $_SESSION['paginaActual'] = 'entrenamientos';
        header("Location: menu.php");
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}