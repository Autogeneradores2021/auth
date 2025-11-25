<?php

namespace App\Domain\ValidationCode\Model;

use App\Domain\ValidationCode\Const\ValidationCodeConst;
use App\Domain\ValidationCode\Const\ValidationCodeStatusConst;

trait ValidationCodeAccessor
{

    /**
     * Accessor para obtener el tiempo restante hasta expiración
     */
    public function getTimeUntilExpirationAttribute(): ?int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return $this->expires_at->diffInSeconds(now());
    }

    /**
     * Accessor para obtener información de estado
     */
    public function getStatusAttribute(): string
    {
        return $this->determineStatus()->value;
    }

    /**
     * Accessor para obtener el nombre legible del tipo
     */
    public function getTypeNameAttribute(): string
    {
        return match ($this->type) {
            ValidationCodeConst::TYPE_EMAIL_VERIFICATION => 'Verificación de Email',
            ValidationCodeConst::TYPE_PASSWORD_RESET => 'Recuperación de Contraseña',
            ValidationCodeConst::TYPE_TWO_FACTOR_AUTH => 'Autenticación de Dos Factores',
            ValidationCodeConst::TYPE_PHONE_VERIFICATION => 'Verificación de Teléfono',
            ValidationCodeConst::TYPE_ACCOUNT_RECOVERY => 'Recuperación de Cuenta',
            ValidationCodeConst::TYPE_LOGIN_VERIFICATION => 'Verificación de Inicio de Sesión',
            default => 'Desconocido',
        };
    }

    private function determineStatus(): ValidationCodeStatusConst
    {
        if (!$this->is_active) {
            return ValidationCodeStatusConst::INACTIVE;
        }

        if ($this->isVerified()) {
            return ValidationCodeStatusConst::VERIFIED;
        }

        if ($this->isExpired()) {
            return ValidationCodeStatusConst::EXPIRED;
        }

        return $this->hasAttemptsAvailable()
            ? ValidationCodeStatusConst::VALID
            : ValidationCodeStatusConst::MAX_ATTEMPTS_EXCEEDED;
    }
}
