<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Application;
use App\Models\Pagamento;

class DashboardController extends Controller
{
    public function index()
    {
        // Administradores
        $totalAdmins = User::where('role', 'admin')->count();

        // Pedidos por status
        $totalPedidos = Application::whereIn('status', ['aprovado', 'documento_emitido'])->count();
        $pedidosAprovados               = Application::where('status', 'aprovado')->count();
        $pedidosComDocumentosEmitidos   = Application::where('status', 'documento_emitido')->count();

        // Receita total confirmada
        $receitaTotal = Pagamento::where('status', 'confirmed')->sum('amount') ?? 0;

        // Pedidos aprovados prontos para emissão de documento
        $pedidosParaEmitir = Application::where('status', 'aprovado')
            ->orderBy('approved_at', 'asc')
            ->limit(10)
            ->get();

        // Últimos administradores criados
        $adminsRecentes = User::where('role', 'admin')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('super-admin.dashboard.index', compact(
            'totalAdmins',
            'totalPedidos',
            'pedidosAprovados',
            'pedidosComDocumentosEmitidos',
            'receitaTotal',
            'pedidosParaEmitir',
            'adminsRecentes'
        ));
    }
}