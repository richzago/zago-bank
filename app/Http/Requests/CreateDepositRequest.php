<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateDepositRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (string) auth()->id() === (string) $this->route('id');
    }

    public function rules(): array
    {
        return [
            'value' => ['required', 'numeric', 'gt:0', 'regex:/^\d{1,6}(?:\.\d{1,2})?$/D'],
        ];
    }

    public function messages(): array
    {
        return [
            'value.regex' => 'Informe um valor positivo com até duas casas decimais e no máximo 999999.99.',
            'value.gt' => 'O valor deve ser maior que zero.',
        ];
    }
}
