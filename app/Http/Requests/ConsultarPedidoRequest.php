<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsultarPedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bi_number' => 'required|string|min:5|max:20'
        ];
    }

    public function messages(): array
    {
        return [
            'bi_number.required' => 'O número do BI é obrigatório',
            'bi_number.min' => 'O BI deve ter no mínimo 5 caracteres',
            'bi_number.max' => 'O BI deve ter no máximo 20 caracteres'
        ];
    }
}