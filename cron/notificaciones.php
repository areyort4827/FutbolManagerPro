<?php

require_once __DIR__ . '/../config/conexion.php';
require_once __DIR__ . '/../helpers/mail.php';

// Buscar entrenamientos de mañana
$sql = "
SELECT e.titulo, u.id as usuario_id, u.email
FROM entrenamientos e
JOIN equipos eq ON e.equipo_id = eq.id
JOIN entrenadores en ON en.equipo_id = eq.id
JOIN usuarios u ON en.usuario_id = u.id
WHERE e.fecha = CURDATE() + INTERVAL 1 DAY
";

$stmt = $pdo->query($sql);

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){

    $mensaje = "Tienes entrenamiento mañana: " . $row['titulo'];

    // Guardar notificación
    $insert = $pdo->prepare("
        INSERT INTO notificaciones (usuario_id, mensaje)
        VALUES (:uid, :msg)
    ");

    $insert->execute([
        ':uid' => $row['usuario_id'],
        ':msg' => $mensaje
    ]);

    // Enviar email
    enviarEmail($row['email'], "Recordatorio de entrenamiento", $mensaje);
}