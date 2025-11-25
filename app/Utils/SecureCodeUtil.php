<?php

namespace App\Utils;

use App\Utils\Abstractions\SecureCodeUtilInterface;

class SecureCodeUtil implements SecureCodeUtilInterface
{
    private const CODE_LENGTH = 6;
    private const MAX_ATTEMPTS = 5;
    private const WEAK_PATTERNS = [
        '/^(\d)\1+$/',
        '/^0+$/',
        '/^123456$/',
        '/^654321$/',
        '/^012345$/',
        '/^987654$/',
    ];

    public function generate(): string
    {
        $attempts = 0;

        do {
            $attempts++;
            $code = str_pad(random_int(0, 999999), self::CODE_LENGTH, '0', STR_PAD_LEFT);

            if ($attempts >= self::MAX_ATTEMPTS) {
                break;
            }
        } while ($this->isWeakCode($code));

        return $code;
    }

    private function isWeakCode(string $code): bool
    {
        foreach (self::WEAK_PATTERNS as $pattern) {
            if (preg_match($pattern, $code)) {
                return true;
            }
        }

        return false;
    }
}
