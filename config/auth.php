<?php

$usuarios = [
    // ADMIN
    [
        'username' => 'admin',
        'password' => 'admin123',     // En producción hay que usar password_hash()
        'role'     => 'admin',
        'nombre'   => 'Administrador'
    ],
    // EQUIPO
    [
        'username' => 'entrenador',
        'password' => 'equipo123',
        'role'     => 'equipo',
        'nombre'   => 'Pep Guardiola'
    ],
    // JUGADOR
    [
        'username' => 'jugador',
        'password' => 'jugador123',
        'role'     => 'jugador',
        'nombre'   => 'Cristiano Ronaldo'
    ]
];

// Función para verificar login
function verificarLogin($username, $password) {
    global $usuarios;
    
    foreach ($usuarios as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            return $user;
        }
    }
    return false;
}
?>