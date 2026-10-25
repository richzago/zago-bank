<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'value' => ['required', 'numeric', 'gt:0', 'regex:/^\d{1,6}(?:\.\d{1,2})?$/D'],
            'payer' => ['required', 'uuid', 'exists:users,id'],
            'payee' => ['required', 'uuid', 'exists:users,id', 'different:payer'],
        ];
    }

    public function messages(): array
    {
        return [
            'value.regex' => 'Informe um valor positivo com até duas casas decimais e no máximo 999999.99.',
            'value.gt' => 'O valor deve ser maior que zero.',
            'payee.different' => 'O destinatário deve ser diferente do pagador.',
        ];
    }
}
