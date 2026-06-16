<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Enums\EstadoPedido;
use Illuminate\Http\RedirectResponse;

class PagamentoController extends Controller
{
    public function processar(int $id): RedirectResponse
    {
        $pedido = Pedido::findOrFail($id);

        if ($pedido->status !== EstadoPedido::NAO_PAGO) {
            return redirect()
                ->route('consulta.form')
                ->with('error', 'Este pedido não está disponível para pagamento.');
        }

        return redirect()->route('pedido.form-upload', $pedido->id);
    }
}