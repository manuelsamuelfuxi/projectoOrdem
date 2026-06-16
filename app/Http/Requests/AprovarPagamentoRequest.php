<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AprovarPagamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin());
    }

    public function rules(): array
    {
        return [
            'acao' => ['required', Rule::in(['aprovar', 'rejeitar'])],
            'motivo_rejeicao' => 'required_if:acao,rejeitar|string|max:500',
            'observacoes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'acao.required' => 'É necessário aprovar ou rejeitar o pagamento.',
            'acao.in' => 'Acção inválida.',
            'motivo_rejeicao.required_if' => 'É necessário fornecer uma justificativa para a rejeição.',
        ];
    }
}