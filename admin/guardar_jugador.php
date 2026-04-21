<?php
session_start();
require_once "../config/conexion.php";

// Recoger datos del formulario
$nombre = $_POST['nombre'];
$edad = $_POST['edad'];
$posicion = $_POST['posicion'];
$equipo_id = $_POST['equipo_id'];

// Insertar jugador en la BD
$sql = "INSERT INTO jugadores (nombre, edad, posicion, equipo_id) VALUES (?, ?, ?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$nombre, $edad, $posicion, $equipo_id]);

// Guardar la sesion para volver a la pantalla de jugadores
$_SESSION['paginaActual'] = 'jugadores';

// Redirigir al menú principal
header("Location: menu.php");
exit;