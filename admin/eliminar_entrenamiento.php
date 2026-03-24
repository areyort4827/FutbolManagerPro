<?php
session_start();
require_once "../config/conexion.php";

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    try {
        $sql = "DELETE FROM entrenamientos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['paginaActual'] = 'entrenamientos';
    } catch (PDOException $e) {
       
    }
}

header("Location: menu.php");
exit;
?>   