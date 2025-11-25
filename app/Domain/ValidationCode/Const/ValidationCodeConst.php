<?php

namespace App\Domain\ValidationCode\Const;

enum ValidationCodeConst
{
    public const TYPE_EMAIL_VERIFICATION = 'email_verification';
    public const TYPE_PASSWORD_RESET = 'password_reset';
    public const TYPE_TWO_FACTOR_AUTH = 'two_factor_auth';
    public const TYPE_PHONE_VERIFICATION = 'phone_verification';
    public const TYPE_ACCOUNT_RECOVERY = 'account_recovery';
    public const TYPE_LOGIN_VERIFICATION = 'login_verification';

    public const TYPES = [
        self::TYPE_EMAIL_VERIFICATION,
        self::TYPE_PASSWORD_RESET,
        self::TYPE_TWO_FACTOR_AUTH,
        self::TYPE_PHONE_VERIFICATION,
        self::TYPE_ACCOUNT_RECOVERY,
        self::TYPE_LOGIN_VERIFICATION,
    ];

    public const TYPE_CONFIG = [
        self::TYPE_EMAIL_VERIFICATION => [
            'length' => 6,
            'expires_minutes' => 15,
            'max_attempts' => 5,
            'rate_limit_per_hour' => 3,
            'decay_minutes' => 60,
            'message' => 'Demasiados intentos de solicitud de verificación. Inténtalo de nuevo más tarde.',
        ],
        self::TYPE_PASSWORD_RESET => [
            'length' => 6,
            'expires_minutes' => 30,
            'max_attempts' => 5,
            'rate_limit_per_hour' => 3,
            'decay_minutes' => 60,
            'message' => 'Demasiados intentos de solicitud de restablecimiento de contraseña. Inténtalo de nuevo más tarde.',
        ],
        self::TYPE_TWO_FACTOR_AUTH => [
            'length' => 6,
            'expires_minutes' => 10,
            'max_attempts' => 3,
            'rate_limit_per_hour' => 5,
            'decay_minutes' => 60,
            'message' => 'Demasiados intentos de solicitud de autenticación de dos factores. Inténtalo de nuevo más tarde.',
        ],
        self::TYPE_PHONE_VERIFICATION => [
            'length' => 4,
            'expires_minutes' => 10,
            'max_attempts' => 3,
            'rate_limit_per_hour' => 3,
            'decay_minutes' => 60,
            'message' => 'Demasiados intentos de solicitud de verificación telefónica. Inténtalo de nuevo más tarde.',
        ],
        self::TYPE_ACCOUNT_RECOVERY => [
            'length' => 8,
            'expires_minutes' => 60,
            'max_attempts' => 5,
            'rate_limit_per_hour' => 2,
            'decay_minutes' => 120,
            'message' => 'Demasiados intentos de solicitud de recuperación de cuenta. Inténtalo de nuevo más tarde.',
        ],
        self::TYPE_LOGIN_VERIFICATION => [
            'length' => 6,
            'expires_minutes' => 5,
            'max_attempts' => 3,
            'rate_limit_per_hour' => 10,
            'decay_minutes' => 60,
            'message' => 'Demasiados intentos de solicitud de verificación de inicio de sesión. Inténtalo de nuevo más tarde.',
        ],
    ];
}
