<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejeitarPedidoRequest extends FormRequest
{
    /**
     * Defesa em profundidade: a rota já está protegida pelo middleware
     * EnsureUserIsSuperAdmin, mas esta verificação garante que, mesmo que
     * a rota seja um dia registada sem esse middleware por engano, a ação
     * continua bloqueada para quem não for super-admin.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isSuperAdmin();
    }

    public function rules(): array
    {
        return [
            'motivo' => ['required', 'string', 'min:10', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'motivo.required' => 'É obrigatório indicar o motivo da rejeição.',
            'motivo.min'       => 'O motivo deve ter pelo menos 10 caracteres — seja específico.',
            'motivo.max'       => 'O motivo não pode exceder 500 caracteres.',
        ];
    }
}