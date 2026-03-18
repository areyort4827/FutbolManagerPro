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
    
<style>
   
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">FutbolManager Pro</div>
    
    <div style="padding: 15px; text-align: center; border-bottom: 1px solid #334155; color: white;">
        <strong><?= $nombre ?></strong><br><br>
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
        <h1>Dashboard</h1>
        <div class="card">
            Bienvenido, <strong><?= $nombre ?></strong>
            <br><br>
            Contenido del dashboard...
        </div>
    </div>

      <!-- Pantalla Entrenamientos  -->
    <div id="entrenamientos" class="page">
        <h1>Entrenamientos</h1>
        <div class="card">Gestión de entrenamientos...</div>
    </div>

      <!-- Pantalla Partidos  -->
    <div id="partidos" class="page">
        <h1>Partidos</h1>
        <div class="card">Información de partidos...</div>
    </div>

      <!-- Pantalla Estadisticas  -->
    <div id="estadisticas" class="page">
        <h1>Estadísticas</h1>
        <div class="card">Estadisticas...</div>
    </div>
     <!-- Pantalla Calendario  -->
    <div id="calendario" class="page">
        <h1>Calendario</h1>
        <div class="card">Calendario del equipo...</div>
    </div>

</div>

<script src="../script.js"></script>

</body>
</html>