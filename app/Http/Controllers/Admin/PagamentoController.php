<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AprovarPagamentoRequest;
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
        $this->pagamentoService->aprovar($pagamento);

        return redirect()
            ->route("admin.pagamentos.pendentes")
            ->with("success", "Pagamento aprovado com sucesso!");
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