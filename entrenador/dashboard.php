<?php
session_start();
require_once '../config/auth.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = 'Entrenador';
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
        <span class="role-icono equipo-icono">
            <?= strtoupper($role) ?>
        </span>
    </div>

    <div class="menu">
        <a class="active" onclick="mostrarPagina('dashboard')">Dashboard</a>
        
        <a onclick="mostrarPagina('jugadores')" 
           class="<?= ($role === 'jugador') ? 'restricted' : '' ?>">
            Jugadores
        </a>
        
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

             <!-- Pantalla Jugadores  -->
    <div id="jugadores" class="page">
            <h1>Jugadores</h1>
                <div id="jugadores-grid" class="page">
    <?php
    include "../conexion.php";

    $sql = "SELECT jugadores.nombre AS jugador, jugadores.edad, jugadores.posicion, 
                equipos.nombre AS equipo, equipos.categoria
            FROM jugadores 
            INNER JOIN equipos ON jugadores.equipo_id = equipos.id";

    $resultado = $conexion->query($sql);

    while($fila = $resultado->fetch_assoc()){
    ?>
        <div id="jugador-card">
            <div class="jugador-top">

                    <div class="avatar">
                        <img src="../assets/img/player.png" alt="Jugador">
                    </div>

                    <div class="nombre-info">
                        <h3><?= htmlspecialchars($fila["jugador"]) ?></h3>
                        <span class="posicion"><?= strtoupper($fila["posicion"]) ?></span>
                    </div>

                </div>

            <div id="jugador-info">
                <p><?= $fila["edad"] ?> años</p>
                <p><?= $fila["equipo"] ?></p>
                <span id="categoria"><?= $fila["categoria"] ?></span>
            </div>

        </div>
    <?php } ?>
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