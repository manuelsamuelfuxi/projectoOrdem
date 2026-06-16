<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\RelatorioService; // <-- Nome alterado aqui

class RelatorioController extends Controller
{
    private RelatorioService $relatorioService; // <-- Nome alterado aqui

    public function __construct(RelatorioService $relatorioService) // <-- Nome alterado aqui
    {
        $this->relatorioService = $relatorioService;
    }

    public function index(Request $requisicao)
    {
        // 1. Extrai filtros da requisição
        $filtros = $requisicao->only(['data_inicio', 'data_fim', 'status']);

        // 2. Delega ao Serviço
        $dadosRelatorio = $this->relatorioService->gerarRelatorioPedidos($filtros); // <-- Nome alterado aqui

        // 3. Retorna a view
        return view('admin.relatorios.pedidos', $dadosRelatorio);
    }
}