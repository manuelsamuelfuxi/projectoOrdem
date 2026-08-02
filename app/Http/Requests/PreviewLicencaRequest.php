<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreviewLicencaRequest extends FormRequest
{
    /**
     * Única responsabilidade: confirmar que o pedido está num estado que
     * permite gerar a pré-visualização da licença. Não há input do
     * utilizador para validar — a "entrada" é o próprio Model resolvido
     * pela Route Model Binding.
     */
    public function authorize(): bool
    {
        $pedido = $this->route('pedido');

        return in_array($pedido->status_string, ['pagamento_confirmado', 'aprovado'], true);
    }

    public function rules(): array
    {
        return [];
    }

    protected function failedAuthorization()
    {
        abort(404, 'Este pedido não está disponível para pré-visualização de licença.');
    }
}