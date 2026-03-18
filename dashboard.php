<?php
session_start();
require_once 'config/auth.php';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
$nombre = htmlspecialchars($user['nombre']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Futbol Manager</title>
<link rel="stylesheet" href="assets/css/style.css">
    
<style>
    .role-icono {
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: bold;
        margin-left: 10px;
    }
    .admin-icono   { background: #eab308; color: #000; }
    .equipo-icono  { background: #3b82f6; color: white; }
    .jugador-icono { background: #22c55e; color: white; }

    .page { display: none; }
    .page.active { display: block; }

    .menu a.restricted {
        opacity: 0.5;
        pointer-events: none;
        color: #64748b;
    }
</style>
</head>
<body>

<div class="sidebar">
    <div class="logo">FutbolManager Pro</div>
    
    <div style="padding: 15px; text-align: center; border-bottom: 1px solid #334155; color: white;">
        <strong><?= $nombre ?></strong><br>
        <span class="role-icono 
            <?= $role === 'admin' ? 'admin-icono' : '' ?>
            <?= $role === 'equipo' ? 'equipo-icono' : '' ?>
            <?= $role === 'jugador' ? 'jugador-icono' : '' ?>">
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
        <a href="logout.php" style="color:#ef4444; margin-top: 30px;">Cerrar Sesión</a>
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
        <?php if ($role === 'jugador'): ?>
            <p style="color: #ef4444; font-weight: bold;">
                No tienes permiso para ver la lista completa de jugadores.
            </p>
        <?php else: ?>
            <table border="1">
                <tr>
                    <th>Nombre</th>
                    <th>Edad</th>
                    <th>Posición</th>
                    <th>Equipo</th>
                    <th>Categoria</th>
                </tr>
                <?php
                include "conexion.php";
                $sql = "SELECT jugadores.nombre AS jugador, jugadores.edad, jugadores.posicion, 
                               equipos.nombre AS equipo, equipos.categoria
                        FROM jugadores INNER JOIN equipos ON jugadores.equipo_id = equipos.id";
                $resultado = $conexion->query($sql);
                while($fila = $resultado->fetch_assoc()){
                    echo "<tr><td>".$fila["jugador"]."</td><td>".$fila["edad"]."</td><td>".$fila["posicion"]."</td><td>".$fila["equipo"]."</td><td>".$fila["categoria"]."</td></tr>";
                }
                ?>
            </table>
        <?php endif; ?>
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

<script src="script.js"></script>

</body>
</html>