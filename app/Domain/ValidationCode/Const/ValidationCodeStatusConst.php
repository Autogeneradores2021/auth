<?php

namespace App\Domain\ValidationCode\Const;

enum ValidationCodeStatusConst: string
{
    case INACTIVE = 'inactive';
    case VERIFIED = 'verified';
    case EXPIRED = 'expired';
    case MAX_ATTEMPTS_EXCEEDED = 'max_attempts_exceeded';
    case VALID = 'valid';

    public function getLabel(): string
    {
        return match ($this) {
            self::INACTIVE => 'Inactivo',
            self::VERIFIED => 'Verificado',
            self::EXPIRED => 'Expirado',
            self::MAX_ATTEMPTS_EXCEEDED => 'Máximo de intentos excedido',
            self::VALID => 'Válido',
        };
    }
}
