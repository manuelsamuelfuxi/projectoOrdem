<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejeitarPedidoRequest;
use App\Models\Application;
use App\Services\PedidoService;
use App\Services\EmissaoLicencaService;

class PedidoController extends Controller
{
    private PedidoService $pedidoService;
    private EmissaoLicencaService $emissaoLicencaService;

    public function __construct(PedidoService $pedidoService, EmissaoLicencaService $emissaoLicencaService)
    {
        $this->pedidoService = $pedidoService;
        $this->emissaoLicencaService = $emissaoLicencaService;
    }

    public function aprovadosFinanceiramente()
    {
        $pedidos = Application::where('status', 'pagamento_confirmado')
            ->with('pagamento')
            ->orderByDesc('updated_at')
            ->paginate(20);

        return view('super-admin.pedidos.aprovados-financeiramente', compact('pedidos'));
    }

    public function aprovarEmissao(Application $pedido)
    {
        try {
            $this->emissaoLicencaService->emitir($pedido);
        } catch (\DomainException $e) {
            return redirect()
                ->route('super-admin.pedidos.financeiramente-aprovados')
                ->with('warning', $e->getMessage());
        }

        return redirect()
            ->route('super-admin.pedidos.financeiramente-aprovados')
            ->with('success', 'Documento aprovado e emitido com sucesso!');
    }

    public function rejeitar(Application $pedido, RejeitarPedidoRequest $request)
    {
        try {
            $this->pedidoService->rejeitar($pedido, $request->validated('motivo'));
        } catch (\DomainException $e) {
            return redirect()
                ->route('super-admin.pedidos.financeiramente-aprovados')
                ->with('warning', $e->getMessage());
        }

        return redirect()
            ->route('super-admin.pedidos.financeiramente-aprovados')
            ->with('warning', 'Pedido rejeitado.');
    }
}