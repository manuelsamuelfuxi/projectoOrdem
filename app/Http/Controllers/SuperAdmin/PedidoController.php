<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RejeitarPedidoRequest;
use App\Models\Application;
use App\Services\Publico\PedidoService;
use App\Services\Carteira\EmissaoCarteiraService;

class PedidoController extends Controller
{
    private PedidoService $pedidoService;
    private EmissaoCarteiraService $emissaoCarteiraService;

    public function __construct(PedidoService $pedidoService, EmissaoCarteiraService $emissaoCarteiraService)
    {
        $this->pedidoService = $pedidoService;
        $this->emissaoCarteiraService = $emissaoCarteiraService;
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
            $this->emissaoCarteiraService->emitir($pedido);
        } catch (\DomainException $e) {
            return redirect()
                ->route('super-admin.pedidos.financeiramente-aprovados')
                ->with('warning', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('super-admin.pedidos.financeiramente-aprovados')
                ->with('warning', 'Não foi possível gerar a licença. Verifique os dados do pedido (curso, função, província) e tente novamente.');
        }

        return redirect()
            ->route('super-admin.pedidos.financeiramente-aprovados')
            ->with('success', 'Documento aprovado e emitido com sucesso!');
    }

    /**
     * Emite, em massa, todos os pedidos actualmente em PAGAMENTO_CONFIRMADO.
     * Cada emissão é independente — uma falha isolada não impede as restantes;
     * o resumo final indica quantas foram emitidas e quais falharam.
     */
    public function aprovarEmissaoTodas()
    {
        $pedidos = Application::where('status', 'pagamento_confirmado')->get();

        if ($pedidos->isEmpty()) {
            return redirect()
                ->route('super-admin.pedidos.financeiramente-aprovados')
                ->with('warning', 'Não há pedidos pendentes de emissão neste momento.');
        }

        $emitidos = 0;
        $falhas   = [];

        foreach ($pedidos as $pedido) {
            try {
                $this->emissaoCarteiraService->emitir($pedido);
                $emitidos++;
            } catch (\Throwable $e) {
                report($e);
                $falhas[] = $pedido->process_number;
            }
        }

        if (!empty($falhas)) {
            return redirect()
                ->route('super-admin.pedidos.financeiramente-aprovados')
                ->with('warning', "{$emitidos} documento(s) emitido(s) com sucesso. Falharam: " . implode(', ', $falhas) . '.');
        }

        return redirect()
            ->route('super-admin.pedidos.financeiramente-aprovados')
            ->with('success', "{$emitidos} documento(s) emitido(s) com sucesso!");
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