<?php

use App\Database\Migrations\DatabaseSchema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('validation_codes', function (Blueprint $table) {
            $table->id();

            // Identificador (email, teléfono, etc.)
            $table->string('identifier')
                ->nullable()
                ->comment('Email, teléfono u otro identificador del destinatario');

            // Código de validación
            $table->string('code', 10)
                ->comment('Código de verificación (OTP, PIN, etc.)');

            // Tipo de validación
            $table->enum('type', [
                'email_verification',
                'password_reset',
                'two_factor_auth',
                'phone_verification',
                'account_recovery',
                'login_verification'
            ])->index()->comment('Tipo de código de validación');

            // Control de tiempo
            $table->timestamp('expires_at')
                ->index()
                ->comment('Fecha y hora de expiración del código');

            $table->timestamp('verified_at')
                ->nullable()
                ->comment('Fecha y hora cuando fue verificado');

            // Control de estado
            $table->boolean('is_active')
                ->default(true)
                ->index()
                ->comment('Si el código está activo');

            // Control de intentos
            $table->integer('attempts')
                ->default(0)
                ->comment('Número de intentos de verificación realizados');

            $table->integer('max_attempts')
                ->default(3)
                ->comment('Número máximo de intentos permitidos');

            // Metadatos adicionales
            $table->json('metadata')
                ->nullable()
                ->comment('Datos adicionales como IP, user agent, operación, etc.');

            // Timestamps estándar
            $table->timestamps();

            // Índices compuestos para optimizar consultas frecuentes
            $table->index(['code', 'type', 'expires_at'], 'idx_code_type_expires');
            $table->index(['identifier', 'type'], 'idx_identifier_type');
            $table->index(['expires_at', 'is_active'], 'idx_expires_active');

            // Índice único para evitar códigos activos duplicados por usuario y tipo
            $table->unique(['identifier', 'type', 'is_active'], 'uniq_act_code_usr_typ')
                ->where('is_active', true);
        });

        // Comentario de la tabla
        DB::statement("COMMENT ON TABLE validation_codes IS 'Tabla para almacenar códigos de validación temporales (2FA, verificación email, etc.)'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validation_codes');
    }
};
