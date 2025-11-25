<?php

namespace App\Application\User\VerificationUser;

readonly class VerificationUserCommand
{
    public function __construct(
        public string $email
    ) {}
}
