<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AprovarPagamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin());
    }

    public function rules(): array
    {
        // A rota já implica a acção (POST /admin/pagamentos/{pagamento}/aprovar).
        // Não há nenhum dado adicional do formulário a validar aqui — só a
        // autorização acima importa. O campo 'acao' anterior nunca era enviado
        // pelo Blade (o form de aprovar só envia @csrf), pelo que a validação
        // falhava sempre com 422 antes de chegar ao Service.
        return [];
    }
}