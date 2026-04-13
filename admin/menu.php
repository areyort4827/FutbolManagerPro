<?php
session_start();
require_once '../config/auth.php';

// Si viene un POST de la pantalla de jugadores, aseguramos que la página activa sea jugadores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipo'])) {
    $_SESSION['paginaActual'] = 'jugadores';
}

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = 'Admin';
$nombre = htmlspecialchars($user['nombre']);

// Detectar la página activa
$paginaActual = $_SESSION['paginaActual'] ?? 'dashboard';
unset($_SESSION['paginaActual']); // Borrar para la próxima carga

$user = $_SESSION['user'];
$role = 'Admin';
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
    <div class="logo">
        FutbolManager Pro
    </div>

   <div class="menu">
        <a class="page <?= $paginaActual === 'dashboard' ? 'active' : '' ?>" onclick="mostrarPagina('dashboard')">
            <i class="fa-solid fa-gauge"></i> Dashboard
        </a>
        <a class="page <?= $paginaActual === 'jugadores' ? 'active' : '' ?>" onclick="mostrarPagina('jugadores')">
            <i class="fa-solid fa-user"></i> Jugadores
        </a>        
        <a class="page <?= $paginaActual === 'entrenamientos' ? 'active' : '' ?>" onclick="mostrarPagina('entrenamientos')">
            <i class="fa-solid fa-dumbbell"></i> Entrenamientos
        </a>
        <a class="page <?= $paginaActual === 'partidos' ? 'active' : '' ?>" onclick="mostrarPagina('partidos')">
            <i class="fa-solid fa-futbol"></i> Partidos
        </a>
        <a class="page <?= $paginaActual === 'estadisticas' ? 'active' : '' ?>" onclick="mostrarPagina('estadisticas')">
            <i class="fa-solid fa-chart-line"></i> Estadísticas
        </a>
        <a class="page <?= $paginaActual === 'calendario' ? 'active' : '' ?>" onclick="mostrarPagina('calendario')">
            <i class="fa-solid fa-calendar"></i> Calendario
        </a>
    </div>
    <!-- USER BOX - Icono a la izquierda + abajo del todo -->
    <div class="user-box">
        <i class="fa-solid fa-circle-user"></i>
        <div class="user-info">
            <strong><?= strtoupper($nombre) ?></strong>
            <div class="role"><?= strtoupper($role) ?></div>
        </div>
    </div>

    <div class="main">

        <!-- Pantallas  -->
        <!-- Pantalla Principal  -->
        <div id="dashboard" class="page <?= $paginaActual === 'dashboard' ? 'active' : '' ?>">
            <?php include 'dashboard.php' ?>
        </div>
        <!-- Pantalla Jugadores  -->
        <div id="jugadores" class="page <?= $paginaActual === 'jugadores' ? 'active' : '' ?>">
            <?php include 'jugadores.php' ?>
        </div>

        <!-- Pantalla Entrenamientos  -->
        <div id="entrenamientos" class="page <?= $paginaActual === 'entrenamientos' ? 'active' : '' ?>">
            <?php include 'entrenamientos.php' ?>
        </div>

        <!-- Pantalla Partidos  -->
        <div id="partidos" class="page <?= $paginaActual === 'partidos' ? 'active' : '' ?>">
            <?php include 'partidos.php' ?>
        </div>
        <!-- Pantalla Calendario  -->
        <div id="calendario" class="page <?= $paginaActual === 'calendario' ? 'active' : '' ?>">
            <?php include 'calendario.php' ?>
        </div>
        <!-- Pantalla Estadisticas  -->

        <div id="estadisticas" class="page <?= $paginaActual === 'estadisticas' ? 'active' : '' ?>">
            <?php include 'estadisticas.php' ?>
        </div>

    </div>

    <script src="../script.js"></script>

</body>

</html>