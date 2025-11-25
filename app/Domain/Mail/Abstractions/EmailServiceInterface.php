<?php

namespace App\Domain\Mail\Abstractions;

use App\Domain\Mail\Consts\MailType;

interface EmailServiceInterface
{
    public function send(MailType $type, string $email, array $data = []): bool;
}
