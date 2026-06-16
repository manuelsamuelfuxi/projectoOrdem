<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminDashboardService;

class DashboardController extends Controller
{
    public function __construct(private AdminDashboardService $service) {}

    public function index()
{
    return view('admin.dashboard.index', [
        'totalPedidos'                  => $this->service->totalPedidos(),
        'pedidosAguardandoComprovativo' => $this->service->pedidosAguardamComprovativo(),
        'pedidosPendentes'              => $this->service->pedidosPendentes(),
        'pedidosAprovados'              => $this->service->pedidosAprovados(),
        'pagamentosHoje'                => $this->service->pagamentosHoje(),
        'valorPagoHoje'                 => $this->service->valorPagoHoje(),
        'ultimosPedidos'                => $this->service->ultimosPedidos(),
        'pedidosPorStatus'              => $this->service->pedidosPorStatus(),
        'pedidosPorMes'                 => $this->service->pedidosPorMesAno(),
    ]);
}

    public function relatorioPedidos()
    {
        return view('admin.relatorios.pedidos');
    }

    public function relatorioFinanceiro()
    {
        return view('admin.relatorios.financeiro');
    }
}