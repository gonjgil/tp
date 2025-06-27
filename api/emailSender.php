<?php
require_once 'vendor/PHPMailer/src/PHPMailer.php';
require_once 'vendor/PHPMailer/src/SMTP.php';
require_once 'vendor/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

class emailSender
{
    public function send($toEmail, $body)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'codigotrivia@gmail.com';  // tu mail real
            $mail->Password = 'yynquspajbhzwpns';          // app password ver que se genera de otra forma
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('codigotrivia@gmail.com', 'Bienvenido a Codigo Trivia');
            $mail->addAddress($toEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Verificá tu cuenta de Codigo Trivia para poder jugar';
            $mail->Body = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar email: {$mail->ErrorInfo}");
            return false;
        }
    }
}
