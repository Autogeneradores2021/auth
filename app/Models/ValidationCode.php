<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\ValidationCode\Model\ValidationCodeScope;
use App\Domain\ValidationCode\Model\ValidationCodeUtilities;
use App\Domain\ValidationCode\Model\ValidationCodeUtilitiesStatic;

class ValidationCode extends Model
{
    use HasFactory,
        ValidationCodeScope,
        ValidationCodeUtilities,
        ValidationCodeUtilitiesStatic;

    protected $fillable = [
        'user_id',
        'code',
        'type',
        'identifier',
        'expires_at',
        'verified_at',
        'is_active',
        'attempts',
        'max_attempts',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'verified_at' => 'datetime',
        'is_active' => 'boolean',
        'attempts' => 'integer',
        'max_attempts' => 'integer',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();

        // Event listeners
        static::creating(function ($validationCode) {
            // Establecer valores por defecto basados en el tipo
            if ($validationCode->type && !$validationCode->max_attempts) {
                $validationCode->max_attempts = self::getMaxAttempts($validationCode->type);
            }

            if ($validationCode->type && !$validationCode->expires_at) {
                $validationCode->expires_at = self::getExpirationTime($validationCode->type);
            }
        });
    }
}
