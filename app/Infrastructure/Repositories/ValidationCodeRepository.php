<?php

namespace App\Infrastructure\Repositories;

use App\Application\User\Abstractions\Repositories\ValidationCodeRepositoryInterface;
use App\Models\ValidationCode;
use BMCLibrary\Repository\AutoModelRepository;

class ValidationCodeRepository extends AutoModelRepository implements ValidationCodeRepositoryInterface
{
    protected string $modelClass = ValidationCode::class;

    public function deactivatePreviousForUser(int $userId, string $type): int
    {
        return $this->model()->deactivatePreviousForUser($userId, $type);
    }
}
