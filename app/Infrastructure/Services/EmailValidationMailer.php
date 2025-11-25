<?php

namespace App\Infrastructure\Services;

use App\Domain\Mail\Abstractions\MailerInterface;
use App\Domain\Mail\Consts\MailType;
use App\Mails\ValidateEmail;
use Illuminate\Support\Facades\Mail;

class EmailValidationMailer implements MailerInterface
{
    public function __construct(
        private Mail $mailer
    ) {}

    public function supports(MailType $type): bool
    {
        return $type === MailType::EMAIL_VERIFICATION;
    }

    public function send(string $email, array $data): bool
    {
        if (!isset($data['code'])) {
            return false;
        }

        $this->mailer::to($email)->queue(new ValidateEmail($data['code']));
        return true;
    }
}
