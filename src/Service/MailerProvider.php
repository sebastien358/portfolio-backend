<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerProvider
{
    public function __construct(
        private string $mailFrom,
        private MailerInterface $mailer
    ){
    }

    public function sendEmail($to, $subject, $body)
    {
        $email = (new Email())
            ->from($this->mailFrom)
            ->to($to)
            ->subject($subject)
            ->html($body);

        $this->mailer->send($email);
    }
}