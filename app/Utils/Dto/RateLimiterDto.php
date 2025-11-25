<?php

namespace App\Utils\Dto;

readonly class RateLimiterDto
{
    public function __construct(
        public string $rateLimitKey,
        public int $maxAttempts = 5,
        public int $decayMinutes = 60,
        public string $message = "Demasiados intentos.",
        public bool $autoIncrement = false
    ) {}
}
