<?php
session_start();
require_once __DIR__ . '/config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit;
}

function volverConError(string $mensaje): void {
    $_SESSION['flash_error'] = $mensaje;
    header('Location: registro.php');
    exit;
}

$nombre        = trim($_POST['nombre']   ?? '');
$email         = trim($_POST['email']    ?? '');
$password      = trim($_POST['password'] ?? '');
$password2     = trim($_POST['password2'] ?? '');
$rolSolicitado = trim($_POST['rol']      ?? 'jugador');
$clubId        = isset($_POST['club_id'])   ? (int)$_POST['club_id']   : 0;
$equipoId      = isset($_POST['equipo_id']) ? (int)$_POST['equipo_id'] : 0;
$fechaNacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$posicion        = trim($_POST['posicion'] ?? '');

// Validaciones básicas
if ($nombre === '' || $email === '' || $password === '') {
    volverConError('Rellena todos los campos obligatorios.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    volverConError('El email no es válido.');
}

if ($password !== $password2) {
    volverConError('Las contraseñas no coinciden.');
}

if (strlen($password) < 6) {
    volverConError('La contraseña debe tener al menos 6 caracteres.');
}

$rolesPermitidos = ['jugador', 'entrenador', 'equipo'];
if (!in_array($rolSolicitado, $rolesPermitidos, true)) {
    $rolSolicitado = 'jugador';
}

if (!$clubId) {
    volverConError('Selecciona un club.');
}

// Validar club existe
$stmtClub = $pdo->prepare('SELECT id FROM clubes WHERE id = ? LIMIT 1');
$stmtClub->execute([$clubId]);
if (!$stmtClub->fetchColumn()) {
    volverConError('El club seleccionado no existe.');
}

// Si es entrenador, validar que seleccionó equipo
if ($rolSolicitado === 'entrenador') {
    if (!$equipoId) {
        volverConError('Los entrenadores deben seleccionar un equipo.');
    }
    // Verificar que el equipo pertenece al club
    $stmtEq = $pdo->prepare('SELECT id FROM equipos WHERE id = ? AND equipo_id = ? LIMIT 1');
    $stmtEq->execute([$equipoId, $clubId]);
    if (!$stmtEq->fetchColumn()) {
        volverConError('El equipo seleccionado no pertenece al club.');
    }
}

// Si es jugador, validar que seleccionó equipo y datos de perfil
if ($rolSolicitado === 'jugador') {
    if (!$equipoId) {
        volverConError('Los jugadores deben seleccionar un equipo.');
    }
    // Verificar que el equipo pertenece al club
    $stmtEq = $pdo->prepare('SELECT id FROM equipos WHERE id = ? AND equipo_id = ? LIMIT 1');
    $stmtEq->execute([$equipoId, $clubId]);
    if (!$stmtEq->fetchColumn()) {
        volverConError('El equipo seleccionado no pertenece al club.');
    }

    if ($fechaNacimiento === '') {
        volverConError('Indica tu fecha de nacimiento.');
    }
    $dt = DateTime::createFromFormat('Y-m-d', $fechaNacimiento);
    if (!$dt || $dt->format('Y-m-d') !== $fechaNacimiento) {
        volverConError('La fecha de nacimiento no es válida.');
    }

    $posicionesPermitidas = ['delantero', 'mediocentro', 'defensa', 'portero'];
    if (!in_array($posicion, $posicionesPermitidas, true)) {
        volverConError('Selecciona una posición válida.');
    }
}

try {
    // Comprobar email duplicado
    $stmtExiste = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
    $stmtExiste->execute([':email' => $email]);
    if ($stmtExiste->fetchColumn()) {
        volverConError('Ya existe una cuenta con ese email.');
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    if ($passwordHash === false) {
        volverConError('No se pudo procesar la contraseña.');
    }

    $pdo->beginTransaction();

    // Insertar usuario
    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nombre, email, password, rol, club_id)
         VALUES (:nombre, :email, :password, :rol, :club_id)'
    );
    $stmt->execute([
        ':nombre'   => $nombre,
        ':email'    => $email,
        ':password' => $passwordHash,
        ':rol'      => $rolSolicitado,
        ':club_id'  => $clubId,
    ]);
    $nuevoUsuarioId = (int)$pdo->lastInsertId();

    // Si es entrenador, crear también el registro en la tabla entrenadores
    if ($rolSolicitado === 'entrenador') {
        $stmtEnt = $pdo->prepare(
            'INSERT INTO entrenadores (nombre, experiencia, equipo_id, usuario_id)
             VALUES (:nombre, 0, :equipo_id, :usuario_id)'
        );
        $stmtEnt->execute([
            ':nombre'     => $nombre,
            ':equipo_id'  => $equipoId,
            ':usuario_id' => $nuevoUsuarioId,
        ]);
    }

    // Si es jugador, crear también el registro en la tabla jugadores
    if ($rolSolicitado === 'jugador') {
        $stmtJug = $pdo->prepare(
            'INSERT INTO jugadores (nombre, fecha_nacimiento, posicion, equipo_id, usuario_id)
             VALUES (:nombre, :fecha_nacimiento, :posicion, :equipo_id, :usuario_id)'
        );
        $stmtJug->execute([
            ':nombre'           => $nombre,
            ':fecha_nacimiento' => $fechaNacimiento,
            ':posicion'         => $posicion,
            ':equipo_id'        => $equipoId,
            ':usuario_id'       => $nuevoUsuarioId,
        ]);
    }

    $pdo->commit();

    $_SESSION['flash_success'] = 'Registro completado. Ya puedes iniciar sesión.';
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    volverConError('Error al registrar el usuario. Inténtalo de nuevo.');
}
