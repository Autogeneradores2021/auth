<?php

namespace App\Domain\ValidationCode\Model;

use Illuminate\Database\Eloquent\Builder;

trait ValidationCodeScope
{

    /**
     * Scope para códigos activos
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope para códigos no expirados
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Scope para códigos no verificados
     */
    public function scopeNotVerified(Builder $query): Builder
    {
        return $query->whereNull('verified_at');
    }

    /**
     * Scope para códigos válidos (activos, no expirados, no verificados)
     */
    public function scopeValid(Builder $query): Builder
    {
        return $query->active()
            ->notExpired()
            ->notVerified();
    }

    /**
     * Scope por tipo de código
     */
    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    /**
     * Scope por código específico
     */
    public function scopeByCode(Builder $query, string $code): Builder
    {
        return $query->where('code', $code);
    }

    /**
     * Scope por identificador (email, teléfono, etc.)
     */
    public function scopeByIdentifier(Builder $query, string $identifier): Builder
    {
        return $query->where('identifier', $identifier);
    }

    /**
     * Scope por usuario
     */
    public function scopeByUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para códigos con intentos disponibles
     */
    public function scopeWithAttemptsAvailable(Builder $query): Builder
    {
        return $query->whereRaw('attempts < max_attempts');
    }

    /**
     * Scope para códigos creados recientemente
     */
    public function scopeRecent(Builder $query, int $minutes = 60): Builder
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }
}
