<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'regex:/^\d{11}$/', 'unique:users,cpf'],
            'password' => ['required', 'string', 'min:8'],
            'type' => ['required', Rule::in(['COMMON', 'SELLER'])],
        ];
    }
}
