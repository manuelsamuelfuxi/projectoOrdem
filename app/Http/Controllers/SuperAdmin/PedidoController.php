<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\PedidoService;
use Illuminate\Http\Request;

class PedidoController extends Controller
{
    private PedidoService $pedidoService;

    public function __construct(PedidoService $pedidoService)
    {
        $this->pedidoService = $pedidoService;
    }

    public function aprovadosFinanceiramente()
    {
        $pedidos = Application::where('status', 'aprovado')
            ->orderBy('approved_at', 'desc')
            ->paginate(20);

        return view('super-admin.pedidos.aprovados-financeiramente', compact('pedidos'));
    }

    public function aprovarEmissao(Application $pedido)
    {
        $this->pedidoService->aprovarEmissao($pedido);

        return redirect()
            ->route('super-admin.pedidos.financeiramente-aprovados')
            ->with('success', 'Documento aprovado e emitido com sucesso!');
    }

    public function rejeitar(Application $pedido, Request $request)
    {
        $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $this->pedidoService->rejeitar($pedido, $request->motivo);

        return redirect()
            ->route('super-admin.pedidos.financeiramente-aprovados')
            ->with('warning', 'Pedido rejeitado.');
    }

    public function visualizarDocumento(Application $pedido)
    {
        if ($pedido->status !== 'documento_emitido') {
            abort(404, 'Documento ainda não emitido.');
        }

        // TODO: return response()->file(storage_path("app/documents/{$pedido->id}.pdf"));
        return response()->json(['message' => 'Documento disponível em breve'], 200);
    }
}