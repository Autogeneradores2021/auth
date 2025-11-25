<?php

namespace App\Domain\Mail\Abstractions;

use App\Domain\Mail\Consts\MailType;

interface MailerInterface
{
    public function send(string $email, array $data): bool;
    public function supports(MailType $type): bool;
}
