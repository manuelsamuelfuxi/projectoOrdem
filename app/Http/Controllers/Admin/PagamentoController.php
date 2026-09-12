<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AprovarPagamentoRequest;
use App\Models\Pagamento;
use App\Services\Admin\PagamentoService;

class PagamentoController extends Controller
{
    public function __construct(private PagamentoService $pagamentoService) {}

    public function pendentes()
    {
        $pagamentos = $this->pagamentoService->listarPendentes();

        return view("admin.pagamentos.pendentes", compact("pagamentos"));
    }

   public function aprovar(AprovarPagamentoRequest $request, Pagamento $pagamento)
{
    try {
        $this->pagamentoService->aprovar($pagamento);
    } catch (\DomainException $e) {
        return redirect()
            ->route("admin.pagamentos.pendentes")
            ->with("error", $e->getMessage());
    }

    return redirect()
        ->route("admin.pagamentos.pendentes")
        ->with("success", "Pagamento aprovado com sucesso! O pedido segue agora para revisão do super-admin.");
}

    public function rejeitar(AprovarPagamentoRequest $request, Pagamento $pagamento)
    {
        $this->pagamentoService->rejeitar($pagamento, $request->motivo_rejeicao);

        return redirect()
            ->route("admin.pagamentos.pendentes")
            ->with("warning", "Pagamento rejeitado.");
    }

    public function verComprovativo(Pagamento $pagamento)
    {
        return $this->pagamentoService->obterComprovativo($pagamento);
    }
}