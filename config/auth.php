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
        $stmt->execute([
            ':email' => $email
        ]);

        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario) {
            return false;
        }

        $passwordDb = (string)($usuario['password'] ?? '');

        // Soporte legacy: si la contraseña está en texto plano, permitimos login y migramos a hash.
        $info = password_get_info($passwordDb);
        $esHash = isset($info['algo']) && $info['algo'] !== 0;

        $loginOk = false;
        if ($esHash) {
            $loginOk = password_verify($password, $passwordDb);
        } else {
            $loginOk = hash_equals($passwordDb, $password);
        }

        if (!$loginOk) {
            return false;
        }

        if (!$esHash) {
            $nuevoHash = password_hash($password, PASSWORD_DEFAULT);
            if ($nuevoHash !== false) {
                $stmtUp = $pdo->prepare('UPDATE usuarios SET password = :pw WHERE id = :id');
                $stmtUp->execute([':pw' => $nuevoHash, ':id' => (int)$usuario['id']]);
            }
        }

        // nunca guardar password en sesión
        unset($usuario['password']);

        return $usuario;
    } catch (PDOException $e) {
        die("Error en login: " . $e->getMessage());
    }
}

