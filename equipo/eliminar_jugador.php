<?php
session_start();
require_once "../config/conexion.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id = $_GET['id'];
    
    $sql = "DELETE FROM jugadores WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);

    // Guardar página activa para volver a jugadores
    $_SESSION['paginaActual'] = 'jugadores';

    // Redirigir de vuelta al menú
    header("Location: menu.php");
    exit;
}