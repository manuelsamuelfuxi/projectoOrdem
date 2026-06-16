@extends("layouts.admin")

@section("title", "Gestão de Pedidos")
@section("page-title", "Gestão de Pedidos")

@section("content")

<style>
    .data-card {
        background: white;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .data-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid #e2e8f0;
        font-size: 13px;
        font-weight: 500;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-table thead th {
        padding: 9px 16px;
        font-size: 11px;
        font-weight: 500;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
        white-space: nowrap;
    }
    .data-table tbody td {
        padding: 10px 16px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #f8fafc; }

    .process-num    { font-size: 12px; color: #94a3b8; font-family: monospace; }
    .candidate-name { font-weight: 500; color: #0f172a; }
    .date-cell      { font-size: 12px; color: #94a3b8; white-space: nowrap; }

    .doc-type { font-size: 11px; padding: 2px 7px; font-weight: 500; }
    .doc-carteira { background: #eff6ff; color: #1d4ed8; }
    .doc-licenca  { background: #f0fdf4; color: #16a34a; }

    .bs { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 11px; font-weight: 500; }
    .bs::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
    .bs-secondary { background: #f1f5f9; color: #475569; }
    .bs-warning   { background: #fffbeb; color: #b45309; }
    .bs-info      { background: #eff6ff; color: #1d4ed8; }
    .bs-primary   { background: #eef2ff; color: #4338ca; }
    .bs-success   { background: #f0fdf4; color: #16a34a; }
    .bs-danger    { background: #fef2f2; color: #dc2626; }

    .btn-ver {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        font-size: 11px;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid #bfdbfe;
        background: #eff6ff;
        color: #1d4ed8;
        transition: opacity 0.15s;
        white-space: nowrap;
    }
    .btn-ver:hover { opacity: 0.8; color: #1d4ed8; }

    .empty-row td {
        text-align: center;
        padding: 40px 16px;
        color: #94a3b8;
        font-size: 13px;
    }

    .pagination .page-link { font-size: 12px; color: #475569; border-color: #e2e8f0; }
    .pagination .page-item.active .page-link { background: #1d4ed8; border-color: #1d4ed8; }

    @media (max-width: 768px) {
        .data-table thead th:nth-child(3),
        .data-table tbody td:nth-child(3) { display: none; }
    }
    @media (max-width: 576px) {
        .data-table thead th:nth-child(5),
        .data-table tbody td:nth-child(5) { display: none; }
    }
</style>

@php
    $statusMap = [
        'nao_pago'             => ['label' => 'Não Pago',             'class' => 'bs-secondary'],
        'aguarda_comprovativo' => ['label' => 'Aguarda Comprovativo', 'class' => 'bs-warning'],
        'pagamento_confirmado' => ['label' => 'Pag. Confirmado',      'class' => 'bs-info'],
        'em_analise'           => ['label' => 'Em Análise',           'class' => 'bs-primary'],
        'aprovado'             => ['label' => 'Aprovado',             'class' => 'bs-success'],
        'documento_emitido'    => ['label' => 'Doc. Emitido',         'class' => 'bs-success'],
        'rejeitado'            => ['label' => 'Rejeitado',            'class' => 'bs-danger'],
    ];
@endphp

@if(session('success'))
<div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; font-size:13px; margin-bottom:16px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

<div class="data-card">
    <div class="data-card-header">
        <span>
            <i class="fas fa-list" style="color:#475569; margin-right:6px;"></i>
            Todos os Pedidos
        </span>
        <span style="font-size:12px; color:#94a3b8; font-weight:400;">
            {{ $pedidos->total() }} {{ $pedidos->total() === 1 ? 'registo' : 'registos' }}
        </span>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Processo</th>
                    <th>Candidato</th>
                    <th>Documento</th>
                    <th>Estado</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidos as $pedido)
                @php
                    $statusKey  = is_object($pedido->status) ? $pedido->status->value : (string)($pedido->status ?? '');
                    $statusInfo = $statusMap[$statusKey] ?? ['label' => ucfirst($statusKey), 'class' => 'bs-secondary'];
                @endphp
                <tr>
                    <td class="process-num">{{ $pedido->process_number }}</td>
                    <td class="candidate-name">{{ $pedido->full_name }}</td>
                    <td>
                        <span class="doc-type {{ $pedido->document_type === 'carteira' ? 'doc-carteira' : 'doc-licenca' }}">
                            {{ $pedido->document_type === 'carteira' ? 'Carteira' : 'Licença' }}
                        </span>
                    </td>
                    <td>
                        <span class="bs {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
                    </td>
                    <td class="date-cell">{{ $pedido->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.pedidos.show', $pedido) }}" class="btn-ver">
                            <i class="fas fa-eye"></i> Ver
                        </a>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="6">
                        <i class="fas fa-inbox" style="font-size:22px; display:block; margin-bottom:8px; color:#cbd5e1;"></i>
                        Nenhum pedido encontrado
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pedidos->hasPages())
    <div style="padding:12px 16px; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end;">
        {{ $pedidos->links() }}
    </div>
    @endif
</div>

@endsection