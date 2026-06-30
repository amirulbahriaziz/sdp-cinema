<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation + authorization for POST /auth/login. Shape-validation only — the
 * credential check itself is an authentication concern resolved in AuthService
 * (a wrong password is a 401, not a 422). Open to guests, so authorize() is true.
 */
class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
