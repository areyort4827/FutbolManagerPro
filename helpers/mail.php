<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

function enviarEmail($destino, $asunto, $mensaje){

    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'emersoncruz712@gmail.com';
        $mail->Password = 'vpqj kjmz jycv grtu';

        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Remitente
        $mail->setFrom('emersoncruz712@gmail.com', 'FutbolManager');

        // Destinatario
        $mail->addAddress($destino);

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $mensaje;

        $mail->send();

    } catch (Exception $e) {
        echo "❌ Error al enviar email: " . $mail->ErrorInfo . "<br>";
    }
}