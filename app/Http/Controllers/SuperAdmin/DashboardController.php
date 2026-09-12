<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Services\SuperAdmin\SuperAdminDashboardService;

class DashboardController extends Controller
{
    public function __construct(
        private readonly SuperAdminDashboardService $dashboard
    ) {}

    public function index()
    {
        return view('super-admin.dashboard.index', [
            'totalAdmins'                  => $this->dashboard->totalAdmins(),
            'totalPedidos'                 => $this->dashboard->totalPedidos(),
            'pedidosAprovados'             => $this->dashboard->pedidosAprovados(),
            'pedidosComDocumentosEmitidos' => $this->dashboard->pedidosComDocumentosEmitidos(),
            'receitaTotal'                 => $this->dashboard->receitaTotal(),
            'pedidosParaEmitir'            => $this->dashboard->pedidosParaEmitir(),
            'adminsRecentes'               => $this->dashboard->adminsRecentes(),
        ]);
    }
}