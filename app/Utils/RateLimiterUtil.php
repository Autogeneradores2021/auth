<?php

namespace App\Utils;

use App\Utils\Abstractions\RateLimiterUtilInterface;
use App\Utils\Dto\RateLimiterDto;
use BMCLibrary\Utils\Result;
use Illuminate\Support\Facades\RateLimiter;

class RateLimiterUtil implements RateLimiterUtilInterface
{

    public function check(RateLimiterDto $config): ?Result
    {
        if (RateLimiter::tooManyAttempts($config->rateLimitKey, $config->maxAttempts)) {

            $seconds = RateLimiter::availableIn($config->rateLimitKey);
            $resetTime = now()->addSeconds($seconds);

            return Result::fail(
                $config->message . " Inténtalo de nuevo en {$seconds} segundos.",
                [
                    'retry_after' => $seconds,
                    'reset_at' => $resetTime->toISOString(),
                    'max_attempts' => $config->maxAttempts,
                    'rate_limit_key' => $config->rateLimitKey,
                    'type' => 'rate_limit_exceeded'
                ],
                429
            );
        }

        if ($config->autoIncrement) {
            self::hit($config->rateLimitKey, $config->decayMinutes);
        }

        return null;
    }

    public  function clear(string $rateLimitKey): void
    {
        RateLimiter::clear($rateLimitKey);
    }

    public  function hit(string $rateLimitKey, int $decayMinutes = 60): int
    {
        return RateLimiter::hit($rateLimitKey, $decayMinutes * 60);
    }
}
