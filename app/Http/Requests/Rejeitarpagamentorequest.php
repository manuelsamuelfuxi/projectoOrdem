<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejeitarPagamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'motivo_rejeicao' => ['required', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo_rejeicao.required' => 'É necessário fornecer uma justificativa para a rejeição.',
        ];
    }
}