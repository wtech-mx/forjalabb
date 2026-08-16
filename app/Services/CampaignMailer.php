<?php

namespace App\Services;

use App\Models\EmailCampaignRecipient;
use PHPMailer\PHPMailer\PHPMailer;

class CampaignMailer
{
    public function send(EmailCampaignRecipient $recipient, string $subject, string $html): void
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
        $mail->Timeout = 20;
        $mail->setFrom((string) config('services.tag_mail.from_address'), 'ForjaLab');
        $mail->addAddress($recipient->email, $recipient->name ?: 'Cliente ForjaLab');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;
        $mail->AltBody = trim(html_entity_decode(strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html))));
        $mail->send();
    }
}
