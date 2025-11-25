<?php

namespace App\Domain\ValidationCode\Model;

use App\Domain\ValidationCode\Const\ValidationCodeConst;
use Carbon\Carbon;

trait ValidationCodeUtilitiesStatic
{

    public static function generateCode(string $type): string
    {
        $config = ValidationCodeConst::TYPE_CONFIG[$type] ?? ValidationCodeConst::TYPE_CONFIG[ValidationCodeConst::TYPE_EMAIL_VERIFICATION];
        $length = $config['length'];

        // Generar código numérico o alfanumérico según el tipo
        if ($type === ValidationCodeConst::TYPE_ACCOUNT_RECOVERY) {
            // Código alfanumérico para recuperación de cuenta
            return strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        }

        // Código numérico para otros tipos
        do {
            $code = str_pad(random_int(0, pow(10, $length) - 1), $length, '0', STR_PAD_LEFT);
        } while (
            self::isWeakCode($code) // Evitar códigos débiles
        );

        return $code;
    }

    /**
     * Verificar si un código es débil/predecible
     */
    private static function isWeakCode(string $code): bool
    {
        // Evitar códigos como 000000, 111111, 123456, etc.
        $weakPatterns = [
            '/^(\d)\1+$/',           // Dígitos repetidos (111111)
            '/^0+$/',                // Solo ceros
            '/^1234+/',              // Secuencias ascendentes
            '/^9876+/',              // Secuencias descendentes
            '/^(12|21|34|43|56|65|78|87|90|09)+$/', // Patrones comunes
        ];

        foreach ($weakPatterns as $pattern) {
            if (preg_match($pattern, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Obtener tiempo de expiración según el tipo
     */
    public static function getExpirationTime(string $type): Carbon
    {
        $config = ValidationCodeConst::TYPE_CONFIG[$type] ?? ValidationCodeConst::TYPE_CONFIG[ValidationCodeConst::TYPE_EMAIL_VERIFICATION];
        return now()->addMinutes($config['expires_minutes']);
    }

    /**
     * Obtener configuración de intentos máximos según el tipo
     */
    public static function getMaxAttempts(string $type): int
    {
        $config = ValidationCodeConst::TYPE_CONFIG[$type] ?? ValidationCodeConst::TYPE_CONFIG[ValidationCodeConst::TYPE_EMAIL_VERIFICATION];
        return $config['max_attempts'];
    }

    /**
     * Limpiar códigos expirados
     */
    public static function cleanExpired(): int
    {
        return self::where('expires_at', '<', now())
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    /**
     * Desactivar códigos anteriores del mismo tipo para un usuario
     */
    public static function deactivatePreviousForUser(int $userId, string $type): int
    {
        return self::where('user_id', $userId)
            ->where('type', $type)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    /**
     * Encontrar código válido para verificación
     */
    public static function findValidCode(int $userId, string $type, string $code): ?self
    {
        return self::byUser($userId)
            ->byType($type)
            ->byCode($code)
            ->valid()
            ->withAttemptsAvailable()
            ->first();
    }
}
