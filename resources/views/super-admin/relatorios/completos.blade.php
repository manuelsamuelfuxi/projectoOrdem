@extends("layouts.super-admin")

@section("title", "Relatórios Completos")
@section("page-title", "Relatórios Completos")

@section("content")

<style>
    .data-card {
        background: white;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 20px;
    }
    .data-card-header {
        padding: 16px 20px;
        border-bottom: 0.5px solid #e2e8f0;
        font-size: 13px;
        font-weight: 500;
        color: #0f172a;
    }
    .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-table thead th {
        padding: 10px 16px;
        font-size: 11px;
        font-weight: 500;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f8fafc;
        border-bottom: 0.5px solid #e2e8f0;
        text-align: left;
    }
    .data-table tbody td {
        padding: 11px 16px;
        color: #334155;
        border-bottom: 0.5px solid #f1f5f9;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #f8fafc; }
    .empty-row td { text-align: center; padding: 28px 16px; color: #94a3b8; }

    .btn-voltar {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px; border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: white;
        border-radius: 0px;
        font-size: 13px; color: #334155; text-decoration: none;
        transition: background 0.15s;
    }
    .btn-voltar:hover { background: #f8fafc; color: #334155; }

    @media (max-width: 768px) {
        .two-col { grid-template-columns: 1fr !important; }
    }
</style>

<div class="two-col" style="display:grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">

    {{-- Pedidos por Mês --}}
    <div class="data-card">
        <div class="data-card-header">
            <i class="fas fa-calendar-alt" style="color:#1d4ed8; margin-right:6px;"></i>
            Pedidos por Mês
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mês</th>
                    <th>Total Pedidos</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidosPorMes as $item)
                <tr>
                    <td>{{ $item->mes }}</td>
                    <td style="font-weight:500; color:#0f172a;">{{ $item->total }}</td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="2">Nenhum dado encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Receita por Mês --}}
    <div class="data-card">
        <div class="data-card-header">
            <i class="fas fa-chart-line" style="color:#16a34a; margin-right:6px;"></i>
            Receita por Mês
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mês</th>
                    <th>Receita Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($receitaPorMes as $item)
                <tr>
                    <td>{{ $item->mes }}</td>
                    <td style="font-weight:500; color:#16a34a;">KZ {{ number_format($item->total, 2, ',', '.') }}</td>
                </tr>
                @empty
                <tr class="empty-row"><td colspan="2">Nenhum dado encontrado</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

<a href="{{ route('super-admin.dashboard') }}" class="btn-voltar">
    <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
</a>

@endsection