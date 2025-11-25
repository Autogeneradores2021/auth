<?php

namespace App\Application\User\VerificationUser;

use App\Application\User\VerificationUser\Services\EmailVerificationService;
use App\Domain\ValidationCode\Const\ValidationCodeConst;
use BMCLibrary\Utils\Result;

class VerificationUserCommandHandler
{
    public function __construct(
        private EmailVerificationService $emailVerificationService
    ) {}

    public function handle(VerificationUserCommand $command): Result
    {
        return $this->emailVerificationService->send(
            ValidationCodeConst::TYPE_EMAIL_VERIFICATION,
            $command->email
        );
    }
}
