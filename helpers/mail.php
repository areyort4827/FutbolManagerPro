<?php

require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmail($destino, $asunto, $mensaje){

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'TU_CORREO@gmail.com';
        $mail->Password = 'TU_PASSWORD_APP';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('TU_CORREO@gmail.com', 'FutbolManager');
        $mail->addAddress($destino);

        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $mensaje;

        $mail->send();

    } catch (Exception $e) {
        // ignoramos errores por ahora
    }
}