<?php

namespace App\Application\User\VerificationUser\Services;

use App\Application\User\Abstractions\Repositories\ValidationCodeRepositoryInterface;
use App\Utils\Abstractions\SecureCodeUtilInterface;
use App\Domain\Mail\Abstractions\EmailServiceInterface;
use App\Domain\Mail\Consts\MailType;
use App\Infrastructure\Services\VerificationService;
use App\Utils\Abstractions\RateLimiterUtilInterface;
use BMCLibrary\UnitOfWork\UnitOfWorkInterface;
use BMCLibrary\Utils\Result;
use Illuminate\Support\Facades\Log;

class EmailVerificationService extends VerificationService
{
    public function __construct(
        private ValidationCodeRepositoryInterface $validationCodeRepository,
        private UnitOfWorkInterface $unitOfWork,
        private SecureCodeUtilInterface $secureCode,
        private EmailServiceInterface $emailService,
        RateLimiterUtilInterface $rateLimiter
    ) {
        parent::__construct($rateLimiter);
    }

    public function send(string $type, string $email): Result
    {

        $this->unitOfWork->beginTransaction();

        try {

            $this->config($type);

            $rateLimitCheck = $this->checkRateLimit($email);

            if ($rateLimitCheck !== null) {
                return $rateLimitCheck;
            }

            $code = $this->secureCode->generate();

            $this->deactivatePreviousForIdentifier($email, $this->type);

            $this->validationCodeCreate($email, $code);

            $this->emailSend($email, $code);

            $this->clearRateLimit($email);

            $this->unitOfWork->commit();
        } catch (\Throwable $th) {

            Log::channel('email_errors')
                ->error('Error al enviar el código de verificación', [
                    'exception' => $th->getMessage(),
                    'line' => $th->getLine(),
                    'trace' => $th->getTraceAsString()
                ]);

            $this->unitOfWork->rollback();

            return Result::fail(
                error: 'Error al enviar el código de verificación'
            );
        }

        return Result::ok("", 'Código de verificación enviado correctamente');
    }

    private function deactivatePreviousForIdentifier(string $identifier, string $type): int
    {
        return $this->validationCodeRepository->model()
            ->where('identifier', $identifier)
            ->where('type', $type)
            ->where('is_active', true)
            ->delete();
    }

    private function validationCodeCreate(string $email, string $code): void
    {
        $this->validationCodeRepository->create(
            [
                'identifier' => $email,
                'type' => $this->type,
                'code' => $code,
                'expires_at' => now()->addMinutes($this->config['expires_minutes']),
                'max_attempts' => $this->config['max_attempts']
            ]
        );
    }

    private function emailSend(string $email, string $code): void
    {
        $this->emailService->send(
            type: MailType::EMAIL_VERIFICATION,
            email: $email,
            data: [
                'code' => $code,
                'expires_in' => $this->config['expires_minutes'] . ' minutos',
            ]
        );
    }
}
