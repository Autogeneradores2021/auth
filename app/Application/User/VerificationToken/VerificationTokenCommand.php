<?php

namespace App\Application\User\VerificationToken;

readonly class VerificationTokenCommand
{
    public function __construct(
        public readonly string $token,
        public readonly ?string $type = 'email_verification'
    ) {}
}
