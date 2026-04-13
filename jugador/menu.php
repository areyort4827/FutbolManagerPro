<?php
session_start();
require_once '../config/auth.php';

// Si viene un POST de la pantalla de jugadores
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['equipo'])) {
    $_SESSION['paginaActual'] = 'jugadores';
}

$paginaActual = $_SESSION['paginaActual'] ?? 'dashboard';

if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit;
}

$club_id = $_SESSION['club_id'];

$stmt = $pdo->prepare("SELECT nombre FROM clubes WHERE id = ?");
$stmt->execute([$club_id]);
$club = $stmt->fetchColumn() ?? 'Mi Club';

$user = $_SESSION['user'];
$role = 'Jugador';
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
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            display: flex;
        }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: #0f172a;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
        }

        .logo {
            font-size: 18px;
            font-weight: bold;
            padding: 20px;
            color: #22c55e;
            border-bottom: 1px solid #1f2937;
        }

        /* MENÚ */
        .menu {
            display: flex;
            flex-direction: column;
            padding: 10px;
            gap: 5px;
            flex: 1;
        }

        .menu a {
            text-decoration: none;
            color: #cbd5e1;
            padding: 12px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: 0.2s;
        }

        .menu a:hover {
            background: #1e293b;
            color: white;
        }

        .menu a.active {
            background: #22c55e;
            color: #0f172a;
            font-weight: bold;
        }

        /* USER BOX - Abajo del todo, con icono a la izquierda */
        .user-box {
            margin-top: auto;
            padding: 16px 20px;
            border-top: 1px solid #1f2937;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-box i {
            font-size: 38px;
            color: #22c55e;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
        }

        .user-info strong {
            display: block;
            font-size: 15px;
            margin-bottom: 3px;
        }

        .role {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            background: #16a34a;
            font-weight: 600;
        }

        /* CERRAR SESIÓN */
        .logout {
            padding: 16px 20px;
            color: #ef4444 !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 12px;
            border-top: 1px solid #1f2937;
            transition: 0.2s;
        }

        .logout:hover {
            background: #1e293b;
        }

        /* MAIN */
        .main {
            margin-left: 260px;
            padding: 20px;
            width: calc(100% - 260px);
        }

        .page {
            display: none;
        }

        .page.active {
            display: block;
        }
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

    <div class="user-box">
        <i class="fa-solid fa-circle-user"></i>
        <div class="user-info">
            <strong><?= strtoupper($nombre) ?></strong>
            <div class="role"><?= strtoupper($role) ?></div>
        </div>
    </div>

    <!-- Cerrar Sesión -->
    <a href="../logout.php" class="logout">
        <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
    </a>
</div>

    <div class="main">
        <div id="dashboard" class="page <?= $paginaActual === 'dashboard' ? 'active' : '' ?>">
            <?php include 'dashboard.php' ?>
        </div>
        <div id="jugadores" class="page <?= $paginaActual === 'jugadores' ? 'active' : '' ?>">
            <?php include 'jugadores.php' ?>
        </div>
        <div id="entrenamientos" class="page <?= $paginaActual === 'entrenamientos' ? 'active' : '' ?>">
            <?php include 'entrenamientos.php' ?>
        </div>
        <div id="partidos" class="page <?= $paginaActual === 'partidos' ? 'active' : '' ?>">
            <?php include 'partidos.php' ?>
        </div>
        <div id="calendario" class="page <?= $paginaActual === 'calendario' ? 'active' : '' ?>">
            <?php include 'calendario.php' ?>
        </div>
        <div id="estadisticas" class="page <?= $paginaActual === 'estadisticas' ? 'active' : '' ?>">
            <?php include 'estadisticas.php' ?>
        </div>
    </div>

    <script src="../script.js"></script>

</body>
</html>