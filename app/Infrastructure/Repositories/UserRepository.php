<?php

namespace App\Infrastructure\Repositories;

use App\Application\User\Abstractions\Repositories\UserRepositoryInterface;
use App\Models\User;
use BMCLibrary\Repository\AutoModelRepository;

class UserRepository extends AutoModelRepository implements UserRepositoryInterface
{
    protected string $modelClass = User::class;
}
