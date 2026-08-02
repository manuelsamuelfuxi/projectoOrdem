<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AprovarPagamentoRequest;
use App\Http\Requests\RejeitarPagamentoRequest;
use App\Models\Pagamento;
use App\Services\PagamentoService;

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
        } catch (\DomainException | \InvalidArgumentException $e) {
            return redirect()
                ->route("admin.pagamentos.pendentes")
                ->with("warning", $e->getMessage());
        }

        return redirect()
            ->route("admin.pagamentos.pendentes")
            ->with("success", "Pagamento aprovado com sucesso!");
    }

    public function rejeitar(RejeitarPagamentoRequest $request, Pagamento $pagamento)
    {
        try {
            $this->pagamentoService->rejeitar($pagamento, $request->motivo_rejeicao);
        } catch (\DomainException | \InvalidArgumentException $e) {
            return redirect()
                ->route("admin.pagamentos.pendentes")
                ->with("warning", $e->getMessage());
        }

        return redirect()
            ->route("admin.pagamentos.pendentes")
            ->with("warning", "Pagamento rejeitado.");
    }

    public function verComprovativo(Pagamento $pagamento)
    {
        return $this->pagamentoService->obterComprovativo($pagamento);
    }
}