<?php

namespace App\Application\User\VerificationToken\Validator;

use Illuminate\Foundation\Http\FormRequest;

class VerificationTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'size:64', 'regex:/^[a-f0-9]{64}$/i'],
        ];
    }

    public function messages(): array
    {
        return [
            'token.required' => 'El token es obligatorio.',
            'token.size' => 'El token debe tener 64 caracteres.',
            'token.regex' => 'El token debe ser hexadecimal.'
        ];
    }
}
