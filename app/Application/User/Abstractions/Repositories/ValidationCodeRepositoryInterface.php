<?php

namespace App\Application\User\Abstractions\Repositories;

use BMCLibrary\Contracts\GenericRepositoryInterface;

interface ValidationCodeRepositoryInterface extends  GenericRepositoryInterface
{
    public function deactivatePreviousForUser(int $userId, string $type): int;
}
