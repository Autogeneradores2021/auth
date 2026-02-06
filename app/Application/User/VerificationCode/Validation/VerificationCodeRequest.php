<?php

namespace App\Application\User\VerificationCode\Validation;

use Illuminate\Foundation\Http\FormRequest;
use App\Application\User\VerificationCode\VerificationCodeCommand;

class VerificationCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'string',
                'email:rfc',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'min:6',
                'max:8',
                'regex:/^[0-9]+$/',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El email es obligatorio.',
            'email.email' => 'El formato del email no es válido.',
            'email.max' => 'El email no puede tener más de 255 caracteres.',
            'code.required' => 'El código de verificación es obligatorio.',
            'code.min' => 'El código debe tener al menos :min caracteres.',
            'code.max' => 'El código no puede tener más de :max caracteres.',
            'code.regex' => 'El código solo puede contener números.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'dirección de correo electrónico',
            'code' => 'código de verificación',
        ];
    }

    /**
     * Convierte el request validado en el comando correspondiente.
     */
    public function toCommand(): VerificationCodeCommand
    {
        return new VerificationCodeCommand(
            email: $this->input('email'),
            code: (string) $this->input('code')
        );
    }
}
