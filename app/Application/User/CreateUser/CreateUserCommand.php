<?php

namespace App\Application\User\CreateUser;

readonly class CreateUserCommand
{
    public function __construct(
        public readonly string $name,
        public readonly string $lastName,
        public readonly string $email,
        public readonly string $password,
    ) {}
}
