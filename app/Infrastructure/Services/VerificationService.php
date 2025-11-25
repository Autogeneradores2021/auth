<?php

namespace App\Infrastructure\Services;

use App\Domain\ValidationCode\Const\ValidationCodeConst;
use App\Utils\Abstractions\RateLimiterUtilInterface;
use App\Utils\Dto\RateLimiterDto;
use BMCLibrary\Utils\Result;
use PhpParser\Node\Stmt\TryCatch;

abstract class VerificationService
{
    public string $type = "";
    public array $config;
    private RateLimiterUtilInterface $rateLimiter;

    public function __construct(RateLimiterUtilInterface $rateLimiter)
    {
        $this->rateLimiter = $rateLimiter;
    }

    public function config(string $type): void
    {
        $this->type = $type;
        $this->config = ValidationCodeConst::TYPE_CONFIG[$this->type];
    }

    public function checkRateLimit(string $email): ?Result
    {
        try {
            return $this->rateLimiter->check(new RateLimiterDto(
                rateLimitKey: $this->type . $email,
                maxAttempts: $this->config['max_attempts'],
                decayMinutes: $this->config['decay_minutes'],
                message: $this->config['message'],
                autoIncrement: false
            ));
        } catch (\Throwable $th) {
            \Log::channel('rate_limiter_errors')
                ->error('Error al verificar el limitador de tasa', [
                    'exception' => $th->getMessage(),
                    'line' => $th->getLine(),
                    'trace' => $th->getTraceAsString()
                ]);
            return null;
        }
    }

    public function clearRateLimit(string $email): void
    {
        $this->rateLimiter->clear($this->type . $email);
    }

    public function incrementRateLimit(string $email): void
    {
        $rateLimitKey =  $this->type . $email;
        $this->rateLimiter->hit($rateLimitKey, 60);
    }
}
