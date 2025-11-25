<?php

namespace App\Application\User\ChangePassword;

readonly class ChangePasswordCommand
{
    public function __construct(
        public  string $userId,
        public  string $oldPassword,
        public  string $newPassword
    ) {}
}
