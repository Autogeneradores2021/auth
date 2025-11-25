<?php

namespace App\Utils\Abstractions;

use App\Utils\Dto\RateLimiterDto;
use BMCLibrary\Utils\Result;

interface RateLimiterUtilInterface
{
    public function check(RateLimiterDto $config): ?Result;
    public function clear(string $rateLimitKey): void;
    public function hit(string $rateLimitKey, int $decayMinutes = 60): int;
}
