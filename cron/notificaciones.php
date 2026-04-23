<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../helpers/mail.php';

// Buscar entrenamientos de hoy y mañana
$sql = "
-- ENTRENADORES
SELECT e.titulo, e.fecha, e.hora, u.id as usuario_id, u.email
FROM entrenamientos e
JOIN equipos eq ON e.equipo_id = eq.id
JOIN entrenadores en ON en.equipo_id = eq.id
JOIN usuarios u ON en.usuario_id = u.id
WHERE e.fecha BETWEEN CURDATE() AND CURDATE() + INTERVAL 1 DAY

UNION ALL

-- JUGADORES
SELECT e.titulo, e.fecha, e.hora, u.id as usuario_id, u.email
FROM entrenamientos e
JOIN equipos eq ON e.equipo_id = eq.id
JOIN jugadores j ON j.equipo_id = eq.id
JOIN usuarios u ON j.usuario_id = u.id
WHERE e.fecha BETWEEN CURDATE() AND CURDATE() + INTERVAL 1 DAY
";

$stmt = $pdo->query($sql);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Notificaciones a procesar: " . count($rows) . "<br>";

foreach($rows as $row){

    // Mensaje HTML
    $mensaje = "
    <h3>⚽ Recordatorio de entrenamiento</h3>
    <ul>
        <li><strong>Tipo:</strong> {$row['titulo']}</li>
        <li><strong>Fecha:</strong> {$row['fecha']}</li>
        <li><strong>Hora:</strong> {$row['hora']}</li>
    </ul>
    ";

    // Evitar duplicados
    $check = $pdo->prepare("
        SELECT id FROM notificaciones 
        WHERE usuario_id = :uid AND mensaje = :msg
    ");

    $check->execute([
        ':uid' => $row['usuario_id'],
        ':msg' => strip_tags($mensaje)
    ]);

    if($check->rowCount() == 0){

        // Guardar notificación en BD
        $insert = $pdo->prepare("
            INSERT INTO notificaciones (usuario_id, mensaje)
            VALUES (:uid, :msg)
        ");

        $insert->execute([
            ':uid' => $row['usuario_id'],
            ':msg' => strip_tags($mensaje)
        ]);

        echo "✔ Notificación creada para usuario ID: " . $row['usuario_id'] . "<br>";

        // Enviar email
        enviarEmail(
            $row['email'],
            "⚽ Recordatorio de entrenamiento",
            $mensaje
        );

        echo "📧 Email enviado a: " . $row['email'] . "<br>";

    } else {
        echo "⚠ Ya existe notificación para usuario ID: " . $row['usuario_id'] . "<br>";
    }
}