<?php
session_start();
require_once "../config/conexion.php";

// Recoger datos del formulario
$nombre          = trim($_POST['nombre']);
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$posicion        = $_POST['posicion'];
$equipo_id       = (int)$_POST['equipo_id'];

// Insertar jugador en la BD (sin campo edad; se calcula dinámicamente)
$sql = "INSERT INTO jugadores (nombre, fecha_nacimiento, posicion, equipo_id) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nombre, $fecha_nacimiento, $posicion, $equipo_id]);

// Guardar la sesion para volver a la pantalla de jugadores
$_SESSION['paginaActual'] = 'jugadores';

// Redirigir al menú principal
header("Location: menu.php");
exit;
