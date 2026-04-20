<?php
require_once 'conexion.php';
/**
 * Verifica el login del usuario
 * @param string $email
 * @param string $password
 * @return array|false
 */
function verificarLogin($email, $password)
{
    global $pdo;
    try {
      
        $sql = "SELECT id, nombre, email, password, rol, club_id
                FROM usuarios
                WHERE email = :email
                LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

       
        if ($usuario && $password === $usuario['password']) {
            unset($usuario['password']);
            return $usuario; 
        }
        return false;
    } catch (PDOException $e) {
        die("Error en login: " . $e->getMessage());
    }
}
