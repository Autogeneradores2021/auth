<?php

namespace App\Application\User\VerificationCode\Services;

use App\Application\User\Abstractions\Repositories\TokenRepositoryInterface;
use App\Application\User\Abstractions\Repositories\ValidationCodeRepositoryInterface;
use App\Infrastructure\Services\VerificationService;
use App\Models\ValidationCode;
use App\Utils\Abstractions\PipelineRunnerInterface;
use App\Utils\Abstractions\RateLimiterUtilInterface;
use BMCLibrary\UnitOfWork\UnitOfWorkInterface;
use BMCLibrary\Utils\Result;
use Carbon\Carbon;

class CodeVerificationService extends VerificationService
{

    public function __construct(
        private ValidationCodeRepositoryInterface $validationCodeRepository,
        private TokenRepositoryInterface $tokenRepository,
        private UnitOfWorkInterface $unitOfWork,
        private PipelineRunnerInterface $pipelineRunner,
        RateLimiterUtilInterface $rateLimiter
    ) {
        parent::__construct($rateLimiter);
    }

    public function validate(string $type, string $email, string $code): Result
    {
        $this->unitOfWork->beginTransaction();

        try {

            $this->config($type);

            $context = [
                'type' => $type,
                'email' => $email,
                'code' => $code,
                'validationCode' => null,
            ];

            $result = $this->pipelineRunner->run($this->validations(), $context);

            $this->unitOfWork->commit();

            return $result;
        } catch (\Throwable $th) {
            $this->unitOfWork->rollback();
            return Result::fail(
                error: 'Error interno durante la validación'
            );
        }
    }

    private function findActiveValidationCode(string $email, string $code): ValidationCode|null
    {
        return $this->validationCodeRepository->model()
            ->where('identifier', $email)
            ->where('type', $this->type)
            ->where('code', $code)
            ->where('is_active', true)
            ->first();
    }

    private function isCodeExpired($validationCode): bool
    {
        return $validationCode->isExpired();
    }

    private function deactivateValidationCode(ValidationCode $validationCode): void
    {
        $validationCode->delete();
    }

    private function hasExceededMaxAttempts(ValidationCode $validationCode): bool
    {
        return $validationCode->attempts >= $validationCode->max_attempts;
    }

    private function isCodeValid($validationCode, string $providedCode): bool
    {
        return hash_equals($validationCode->code, $providedCode);
    }

    private function incrementCodeAttempts($validationCode): void
    {
        $validationCode->increment('attempts');
    }

    private function generateSecureToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function saveToken(string $email, string $type, string $hashed, Carbon $expiresAt): void
    {
        $this->tokenRepository->create([
            'identifier' => $email,
            'type' => $type,
            'token_hash' => $hashed,
            'scopes' => json_encode(['email_verification']),
            'is_used' => false,
            'expires_at' => $expiresAt,
        ]);
    }

    private function validations()
    {
        return [

            // 1) rate limit
            function (&$ctx) {
                $check = $this->checkRateLimit($ctx['email']);
                return $check instanceof Result ? $check : null;
            },

            // 2) find active code
            function (&$ctx) {
                $ctx['validationCode'] = $this->findActiveValidationCode($ctx['email'], $ctx['code']);
                if (!$ctx['validationCode']) {
                    $this->incrementRateLimit($ctx['email']);
                    return Result::fail(error: 'Código de verificación no encontrado');
                }
                return null;
            },

            // 3) expired
            function (&$ctx) {
                if ($this->isCodeExpired($ctx['validationCode'])) {
                    $this->deactivateValidationCode($ctx['validationCode']);
                    $this->incrementRateLimit($ctx['email']);
                    return Result::fail(error: 'El código de verificación ha expirado');
                }
                return null;
            },

            // 4) max attempts
            function (&$ctx) {
                if ($this->hasExceededMaxAttempts($ctx['validationCode'])) {
                    $this->deactivateValidationCode($ctx['validationCode']);
                    $this->incrementRateLimit($ctx['email']);
                    return Result::fail(error: 'Demasiados intentos fallidos. Solicita un nuevo código');
                }
                return null;
            },

            // 5) validate code
            function (&$ctx) {
                if (!$this->isCodeValid($ctx['validationCode'], $ctx['code'])) {
                    $this->incrementCodeAttempts($ctx['validationCode']);
                    $this->incrementRateLimit($ctx['email']);
                    return Result::fail(error: 'Código de verificación incorrecto');
                }
                return null;
            },

            // 6) success: mark used, create token and return success Result
            function (&$ctx) {

                $this->deactivateValidationCode($ctx['validationCode']);

                $this->clearRateLimit($ctx['email']);

                $rawToken = $this->generateSecureToken();
                $hashed = hash('sha256', $rawToken);
                $expiresAt = Carbon::now()->addMinutes(60);

                $this->saveToken(
                    email: $ctx['email'],
                    type: $ctx['type'],
                    hashed: $hashed,
                    expiresAt: $expiresAt
                );

                return Result::ok(
                    message: 'Código verificado correctamente',
                    data: [
                        'token' => $rawToken,
                        'token_expires_at' => $expiresAt->toISOString(),
                        'identifier' => $ctx['email'],
                        'type' => $ctx['type'],
                    ]
                );
            },
        ];
    }
}
