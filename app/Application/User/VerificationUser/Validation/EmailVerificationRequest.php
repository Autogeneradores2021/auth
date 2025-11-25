<?php

namespace App\Application\User\VerificationUser\Validation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class EmailVerificationRequest extends FormRequest
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
                'email:rfc,dns', // Validación RFC + DNS
                'max:255',
                //'exists:users,email', // Verificar que el usuario existe
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', // Formato estricto
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El campo email es obligatorio.',
            'email.string' => 'El email debe ser una cadena de texto.',
            'email.email' => 'Debe proporcionar una dirección de email válida.',
            'email.max' => 'El email no puede tener más de 255 caracteres.',
            //'email.exists' => 'No existe una cuenta asociada a este email.',
            'email.regex' => 'El formato del email no es válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'correo electrónico',
        ];
    }


    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Validación personalizada: verificar si el email ya está verificado
            if (!$validator->errors()->has('email')) {
                $email = $this->input('email');

                if ($this->isEmailAlreadyVerified($email)) {
                    $validator->errors()->add(
                        'email',
                        'Esta dirección de email ya está verificada.'
                    );
                }

                // Validación adicional: verificar dominios permitidos (opcional)
                if ($this->isBlockedDomain($email)) {
                    $validator->errors()->add(
                        'email',
                        'Este dominio de email no está permitido.'
                    );
                }
            }
        });
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors()->toArray();

        // Formatear errores para respuesta consistente con tu API
        $formattedErrors = [];
        foreach ($errors as $field => $messages) {
            $formattedErrors[$field] = $messages[0];
        }

        $response = response()->json([
            'success' => false,
            'message' => 'Los datos proporcionados no son válidos.',
            'errors' => $formattedErrors,
            'error_code' => 'VALIDATION_ERROR'
        ], 422);

        throw new HttpResponseException($response);
    }

    private function isEmailAlreadyVerified(string $email): bool
    {
        $user = \App\Models\User::where('email', $email)->first();
        return $user && $user->hasVerifiedEmail();
    }

    private function isBlockedDomain(string $email): bool
    {
        $blockedDomains = [
            'tempmail.org',
            '10minutemail.com',
            'guerrillamail.com',
            'mailinator.com',
            // Agregar más dominios temporales según necesites
        ];

        $domain = substr(strrchr($email, '@'), 1);

        return in_array(strtolower($domain), $blockedDomains);
    }

    public function validatedEmail(): string
    {
        return $this->validated('email');
    }
}
