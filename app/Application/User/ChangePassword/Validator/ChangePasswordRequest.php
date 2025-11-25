<?php

namespace App\Application\User\ChangePassword\Validator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    private const MAX_PASSWORD_RULE = 'max:255';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'old_password' => [
                'required',
                'string',
                self::MAX_PASSWORD_RULE,
            ],
            'new_password' => [
                'required',
                'string',
                'min:8',
                self::MAX_PASSWORD_RULE,
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
            'new_password_confirmation' => [
                'required',
                'string',
                self::MAX_PASSWORD_RULE,
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'old_password.required' => 'La contraseña actual es obligatoria.',
            'old_password.string' => 'La contraseña actual debe ser una cadena de texto.',
            'old_password.max' => 'La contraseña actual no puede exceder 255 caracteres.',

            'new_password.required' => 'La nueva contraseña es obligatoria.',
            'new_password.string' => 'La nueva contraseña debe ser una cadena de texto.',
            'new_password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'new_password.max' => 'La nueva contraseña no puede exceder 255 caracteres.',
            'new_password.confirmed' => 'La confirmación de la nueva contraseña no coincide.',

            'new_password_confirmation.required' => 'La confirmación de contraseña es obligatoria.',
            'new_password_confirmation.string' => 'La confirmación de contraseña debe ser una cadena de texto.',
            'new_password_confirmation.max' => 'La confirmación de contraseña no puede exceder 255 caracteres.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'old_password' => 'contraseña actual',
            'new_password' => 'nueva contraseña',
            'new_password_confirmation' => 'confirmación de contraseña',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Limpiar espacios en blanco de las contraseñas
        $this->merge([
            'old_password' => trim($this->old_password ?? ''),
            'new_password' => trim($this->new_password ?? ''),
            'new_password_confirmation' => trim($this->new_password_confirmation ?? ''),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validación personalizada: nueva contraseña diferente a la actual
            if ($this->old_password === $this->new_password) {
                $validator->errors()->add(
                    'new_password',
                    'La nueva contraseña debe ser diferente a la contraseña actual.'
                );
            }

            // Validación de longitud para bcrypt (máximo 72 bytes)
            if (strlen($this->new_password) > 72) {
                $validator->errors()->add(
                    'new_password',
                    'La contraseña es demasiado larga.'
                );
            }
        });
    }
}
