<?php

namespace App\Services;

use App\Mail\EmergencyScanAlert;
use App\Models\SmartTag;
use PHPMailer\PHPMailer\PHPMailer;

class EmergencyTagMailer
{
    public function send(SmartTag $tag, array $scan, array $recipients): void
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = (string) config('services.tag_mail.host');
        $mail->Port = (int) config('services.tag_mail.port', 465);
        $mail->SMTPAuth = true;
        $mail->Username = (string) config('services.tag_mail.username');
        $mail->Password = (string) config('services.tag_mail.password');
        $mail->SMTPSecure = (string) config('services.tag_mail.encryption', PHPMailer::ENCRYPTION_SMTPS);
        $mail->CharSet = PHPMailer::CHARSET_UTF8;
        $mail->Timeout = 15;
        $mail->setFrom(
            (string) config('services.tag_mail.from_address'),
            (string) config('services.tag_mail.from_name', 'ForjaLab Emergencias')
        );

        foreach ($recipients as $recipient) {
            $mail->addAddress($recipient);
        }

        $mailable = new EmergencyScanAlert($tag, $scan);
        $mail->isHTML(true);
        $mail->Subject = 'Alerta de escaneo QR: '.$tag->display_name.' ('.$tag->tag_code.')';
        $mail->Body = $mailable->render();
        $mail->AltBody = "Se escaneó el QR de {$tag->display_name}. Ubicación: {$scan['mapsUrl']}";
        $mail->send();
    }
}
