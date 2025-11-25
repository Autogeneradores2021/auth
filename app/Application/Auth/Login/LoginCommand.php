<?php

namespace App\Application\Auth\Login;

readonly class LoginCommand
{
    public function __construct(
        public string $email,
        public string $password
    ) {}
}
