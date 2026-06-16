<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Pagamento;
use Illuminate\Support\Collection;

class AdminDashboardService
{
    public function totalPedidos(): int
    {
        return Application::count();
    }

    public function pedidosAguardamComprovativo(): int
{
    return Application::whereIn('status', [
        'nao_pago',
        'aguarda_comprovativo',
    ])
    ->whereDoesntHave('pagamento', function ($query) {
        $query->where('status', 'proof_submitted');
    })
    ->count();
}

public function pedidosPendentes(): int
{
    return Pagamento::where('status', 'proof_submitted')->count();
}

public function pedidosAprovados(): int
{
    return Application::whereIn('status', [
        'pagamento_confirmado',
        'aprovado',
        'documento_emitido',
    ])->count();
}

    public function pagamentosHoje(): int
    {
        return Pagamento::whereDate('confirmed_at', today())->count();
    }

    public function valorPagoHoje(): float
    {
        return (float) Pagamento::whereDate('confirmed_at', today())->sum('amount');
    }

    public function ultimosPedidos(): Collection
    {
        return Application::orderBy('created_at', 'desc')->limit(8)->get();
    }

    public function pedidosPorStatus(): array
    {
        $statuses = [
            'nao_pago'             => 'Não Pago',
            'aguarda_comprovativo' => 'Aguarda Comprovativo',
            'pagamento_confirmado' => 'Pag. Confirmado',
            'em_analise'           => 'Em Análise',
            'aprovado'             => 'Aprovado',
            'documento_emitido'    => 'Doc. Emitido',
            'rejeitado'            => 'Rejeitado',
        ];

        $counts = Application::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $result = [];
        foreach ($statuses as $key => $label) {
            $result[] = [
                'status' => $key,
                'label'  => $label,
                'total'  => $counts[$key] ?? 0,
            ];
        }

        return $result;
    }

    public function pedidosPorMesAno(): array
    {
        return Application::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as mes, COUNT(*) as total")
            ->whereYear('created_at', now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->get()
            ->map(fn($item) => ['mes' => $item->mes, 'total' => $item->total])
            ->toArray();
    }
}