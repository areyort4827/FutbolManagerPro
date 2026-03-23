<?php
require_once 'conexion.php';
/**
 * Verifica el login del usuario
 * @param string $username
 * @param string $password
 * @return array|false
 */
function verificarLogin($username, $password)
{
    global $pdo;

    try {
        // Buscar usuario por nombre
        $sql = "SELECT id, nombre, email, password, rol
                FROM usuarios
                WHERE nombre = :username
                LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ':username' => $username
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    
        if ($usuario && $password === $usuario['password']) {

            // nunca guardar password en sesión
            unset($usuario['password']);

            return $usuario;
        }

        return false;

    } catch (PDOException $e) {
        die("Error en login: " . $e->getMessage());
    }
}
/*
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
        'role'     => 'entrenador',
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

?>*/