<?php

namespace App\Infrastructure\Services;

use App\Domain\Mail\Abstractions\EmailServiceInterface;
use App\Domain\Mail\Abstractions\MailerInterface;
use App\Domain\Mail\Consts\MailType;

class EmailService implements EmailServiceInterface
{
    public function __construct(
        private array $mailers = []
    ) {}


    public function send(MailType $type, string $email, array $data = []): bool
    {
        $mailer = $this->findMailerForType($type);
        if (!$mailer) {
            return false;
        }
        return $mailer->send($email, $data);
    }


    public function addMailer(MailerInterface $mailer): void
    {
        $this->mailers[] = $mailer;
    }


    private function findMailerForType(MailType $type): ?MailerInterface
    {
        foreach ($this->mailers as $mailer) {
            if ($mailer->supports($type)) {
                return $mailer;
            }
        }
        return null;
    }
}
