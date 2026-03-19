<?php
session_start();
require_once '../config/auth.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = "Jugador";
$nombre = htmlspecialchars($user['nombre']);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futbol Manager</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <style>

    </style>
</head>

<body>

    <div class="sidebar">
        <div class="logo">FutbolManager Pro</div>

        <div style="padding: 15px; text-align: center; border-bottom: 1px solid #334155; color: white;">
            <div style="font-size: 20px; margin-bottom: 10px;">
                <i class="fa-solid fa-user"></i>
            </div>
            <strong><?= strtoupper($nombre) ?></strong><br><br>
            <span class="role-icono jugador-icono">
                <?= strtoupper($role) ?>
            </span>
        </div>

        <div class="menu">
            <a class="active" onclick="mostrarPagina('dashboard')">Dashboard</a>
            <a onclick="mostrarPagina('entrenamientos')">Entrenamientos</a>
            <a onclick="mostrarPagina('partidos')">Partidos</a>
            <a onclick="mostrarPagina('estadisticas')">Estadísticas</a>
            <a onclick="mostrarPagina('calendario')">Calendario</a>
            <a href="../logout.php" style="color:#ef4444; margin-top: 30px;">Cerrar Sesión</a>
        </div>
    </div>

    <div class="main">

        <!-- Pantallas  -->
        <!-- Pantalla Principal  -->
        <div id="dashboard" class="page active">
            <?php include 'dashboard.php' ?>

        </div>

        <!-- Pantalla Entrenamientos  -->
        <div id="entrenamientos" class="page">
            <?php include 'entrenamientos.php' ?>
        </div>

        <!-- Pantalla Partidos  -->
        <div id="partidos" class="page">
            <?php include 'partidos.php' ?>

        </div>
        <!-- Pantalla Calendario  -->
        <div id="calendario" class="page">
            <?php include 'calendario.php' ?>

        </div>
        <!-- Pantalla Estadisticas  -->
        <div id="estadisticas" class="page">
            <?php include 'estadisticas.php' ?>

        </div>


    </div>

    <script src="../script.js"></script>

</body>

</html>