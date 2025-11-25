<?php

namespace App\Application\User\VerificationToken;

use App\Application\User\Abstractions\Repositories\TokenRepositoryInterface;
use BMCLibrary\Utils\HttpStatus;
use BMCLibrary\Utils\Result;
use Carbon\Carbon;

class VerificationTokenCommandHandler
{
    public function __construct(
        private TokenRepositoryInterface $tokenRepository
    ) {}

    public function handle(VerificationTokenCommand $command): Result
    {

        $hashed = hash('sha256', $command->token);

        $tokenModel = $this->tokenRepository->model()
            ->where('token_hash', $hashed)
            ->where('type', $command->type)
            ->first();

        if (!$tokenModel) {
            return Result::fail(error: 'Token inválido o expirado', status: HttpStatus::UNAUTHORIZED);
        }

        $tokenModel->delete();

        return Result::ok(
            message: 'Token válido',
            data: []
        );
    }
}
