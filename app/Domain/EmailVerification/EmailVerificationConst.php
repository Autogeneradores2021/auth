<?php

namespace App\Domain\EmailVerification;

enum EmailVerificationConst
{
    private const CACHE_PREFIX = 'email_verification_rate_limit:';
    private const MAX_REQUESTS_PER_HOUR = 3;
    private const CODE_LENGTH = 6;
    private const CODE_EXPIRY_MINUTES = 15;
    private const MAX_ATTEMPTS = 5;
}
