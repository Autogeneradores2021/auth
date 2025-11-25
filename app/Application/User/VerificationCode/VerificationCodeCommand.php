<?php

namespace App\Application\User\VerificationCode;

readonly class VerificationCodeCommand
{
    public function __construct(
        public string $email,
        public string $code
    ) {}
}
