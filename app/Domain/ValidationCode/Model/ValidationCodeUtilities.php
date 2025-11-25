<?php

namespace App\Domain\ValidationCode\Model;

trait ValidationCodeUtilities
{

    /**
     * Verificar si el código está expirado
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    /**
     * Verificar si el código ya fue usado
     */
    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }

    /**
     * Verificar si el código es válido para usar
     */
    public function isValid(): bool
    {
        return $this->is_active &&
            !$this->isExpired() &&
            !$this->isVerified() &&
            $this->hasAttemptsAvailable();
    }

    /**
     * Verificar si tiene intentos disponibles
     */
    public function hasAttemptsAvailable(): bool
    {
        return $this->attempts < $this->max_attempts;
    }

    /**
     * Obtener intentos restantes
     */
    public function getRemainingAttempts(): int
    {
        return max(0, $this->max_attempts - $this->attempts);
    }

    /**
     * Marcar código como verificado
     */
    public function markAsVerified(): bool
    {
        return $this->update([
            'verified_at' => now(),
            'is_active' => false,
        ]);
    }

    /**
     * Incrementar contador de intentos
     */
    public function incrementAttempts(): bool
    {
        $this->increment('attempts');

        // Desactivar si se exceden los intentos
        if ($this->attempts >= $this->max_attempts) {
            $this->update(['is_active' => false]);
        }

        return true;
    }

    /**
     * Desactivar código
     */
    public function deactivate(): bool
    {
        return $this->update(['is_active' => false]);
    }

    /**
     * Verificar si el código coincide (comparación segura)
     */
    public function matches(string $inputCode): bool
    {
        return hash_equals($this->code, $inputCode);
    }
}
