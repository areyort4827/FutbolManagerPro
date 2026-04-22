<?php
session_start();

require_once __DIR__ . '/config/conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: registro.php');
    exit;
}

function volverConError(string $mensaje): void
{
    $_SESSION['flash_error'] = $mensaje;
    header('Location: registro.php');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$apellidos = trim($_POST['apellidos'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$rolSolicitado = trim($_POST['rol'] ?? 'jugador');
$clubId = isset($_POST['club_id']) ? (int)$_POST['club_id'] : 0;

if ($nombre === '' || $apellidos === '' || $email === '' || $password === '') {
    volverConError('Rellena todos los campos obligatorios.');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    volverConError('El email no es válido.');
}

$rolesPermitidos = ['jugador', 'entrenador', 'equipo'];
if (!in_array($rolSolicitado, $rolesPermitidos, true)) {
    $rolSolicitado = 'jugador';
}

$rolFinal = $rolSolicitado;
$clubIdFinal = $clubId ?: null;

if (!$clubIdFinal) {
    volverConError('Selecciona un club.');
}

$stmtClub = $pdo->prepare('SELECT id FROM clubes WHERE id = ? LIMIT 1');
$stmtClub->execute([$clubIdFinal]);
if (!$stmtClub->fetchColumn()) {
    volverConError('El club seleccionado no existe.');
}

try {
    $stmtExiste = $pdo->prepare('SELECT id FROM usuarios WHERE email = :email LIMIT 1');
    $stmtExiste->execute([':email' => $email]);
    if ($stmtExiste->fetchColumn()) {
        volverConError('Ya existe una cuenta con ese email.');
    }

    $nombreCompleto = trim($nombre . ' ' . $apellidos);
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    if ($passwordHash === false) {
        volverConError('No se pudo procesar la contraseña.');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO usuarios (nombre, email, password, rol, club_id)
         VALUES (:nombre, :email, :password, :rol, :club_id)'
    );

    $stmt->execute([
        ':nombre' => $nombreCompleto,
        ':email' => $email,
        ':password' => $passwordHash,
        ':rol' => $rolFinal,
        ':club_id' => $clubIdFinal,
    ]);

    $_SESSION['flash_success'] = 'Registro completado. Ya puedes iniciar sesión.';
    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    volverConError('Error al registrar el usuario.');
}
