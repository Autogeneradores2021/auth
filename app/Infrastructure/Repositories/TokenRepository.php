<?php

namespace App\Infrastructure\Repositories;

use App\Application\User\Abstractions\Repositories\TokenRepositoryInterface;
use App\Models\VerificationToken;
use BMCLibrary\Repository\AutoModelRepository;

class TokenRepository extends AutoModelRepository implements TokenRepositoryInterface
{
    protected string $modelClass = VerificationToken::class;
}
