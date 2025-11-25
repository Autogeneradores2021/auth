<?php

namespace App\Application\User\VerificationCode;

use App\Application\User\VerificationCode\Services\CodeVerificationService;
use App\Domain\ValidationCode\Const\ValidationCodeConst;
use BMCLibrary\Utils\Result;

class VerificationCodeCommandHandler
{
    public function __construct(private CodeVerificationService $codeVerificationService) {}

    public function handle(VerificationCodeCommand $command): Result
    {
        return $this->codeVerificationService->validate(
            type: ValidationCodeConst::TYPE_EMAIL_VERIFICATION,
            email: $command->email,
            code: $command->code
        );
    }
}
