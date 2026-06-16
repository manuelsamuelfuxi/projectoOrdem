@extends("layouts.admin")

@section("title", "Dashboard — Administração")
@section("page-title", "Dashboard")

@section("content")

<style>
    .stat-card {
        background: white;
        border: 1px solid #e2e8f0;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
    }
    .stat-card:hover { box-shadow: 0 2px 12px rgba(15,23,42,0.07); }

    .stat-icon {
        width: 44px; height: 44px;
        display: flex; align-items: center; justify-content: center;
        font-size: 17px; flex-shrink: 0;
    }
    .stat-icon.blue   { background: #eff6ff; color: #1d4ed8; }
    .stat-icon.amber  { background: #fffbeb; color: #d97706; }
    .stat-icon.indigo { background: #eef2ff; color: #4338ca; }
    .stat-icon.green  { background: #f0fdf4; color: #16a34a; }

    .stat-value { font-size: 26px; font-weight: 600; color: #0f172a; line-height: 1; }
    .stat-label { font-size: 12px; color: #64748b; margin-top: 3px; }

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
    }
    .data-table tbody td {
        padding: 10px 16px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #f8fafc; }

    .process-num { font-size: 12px; color: #94a3b8; font-family: monospace; }
    .candidate-name { font-weight: 500; color: #0f172a; }

    .bs { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; font-size: 11px; font-weight: 500; }
    .bs::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }
    .bs-secondary { background: #f1f5f9; color: #475569; }
    .bs-warning   { background: #fffbeb; color: #b45309; }
    .bs-info      { background: #eff6ff; color: #1d4ed8; }
    .bs-primary   { background: #eef2ff; color: #4338ca; }
    .bs-success   { background: #f0fdf4; color: #16a34a; }
    .bs-danger    { background: #fef2f2; color: #dc2626; }

    .doc-type { font-size: 11px; padding: 2px 7px; font-weight: 500; }
    .doc-carteira { background: #eff6ff; color: #1d4ed8; }
    .doc-licenca  { background: #f0fdf4; color: #16a34a; }

    .empty-row td { text-align: center; padding: 28px; color: #94a3b8; font-size: 13px; }

    .quick-btn {
        display: inline-flex; align-items: center; gap: 7px;
        padding: 9px 18px; font-size: 13px; font-weight: 500;
        text-decoration: none; border: none; cursor: pointer;
        transition: opacity 0.15s;
    }
    .quick-btn:hover { opacity: 0.85; }
    .quick-btn.amber  { background: #d97706; color: white; }
    .quick-btn.blue   { background: #1d4ed8; color: white; }
    .quick-btn.slate  { background: #475569; color: white; }
    .quick-btn.teal   { background: #0d9488; color: white; }

    /* Gráfico de barras */
    .chart-wrap { padding: 20px 20px 8px; }
    .bar-chart { display: flex; align-items: flex-end; gap: 8px; height: 160px; }
    .bar-col { display: flex; flex-direction: column; align-items: center; flex: 1; height: 100%; justify-content: flex-end; gap: 4px; }
    .bar-fill { width: 100%; background: #1d4ed8; transition: height 0.4s ease; min-height: 2px; }
    .bar-fill.zero { background: #e2e8f0; }
    .bar-label { font-size: 10px; color: #94a3b8; white-space: nowrap; }
    .bar-value { font-size: 11px; font-weight: 600; color: #0f172a; }

    /* Distribuição por status */
    .status-row {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 20px; border-bottom: 1px solid #f1f5f9;
        font-size: 12.5px;
    }
    .status-row:last-child { border-bottom: none; }
    .status-bar-wrap { flex: 1; background: #f1f5f9; height: 5px; }
    .status-bar-fill { height: 100%; transition: width 0.4s ease; }
    .status-name { width: 150px; color: #334155; flex-shrink: 0; }
    .status-count { width: 32px; text-align: right; font-weight: 600; color: #0f172a; flex-shrink: 0; }

    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr !important; }
        .two-col   { grid-template-columns: 1fr !important; }
        .three-col { grid-template-columns: 1fr 1fr !important; }
        .data-table thead th:nth-child(3),
        .data-table tbody td:nth-child(3) { display: none; }
    }
    @media (max-width: 480px) {
        .stat-grid  { grid-template-columns: 1fr !important; }
        .three-col  { grid-template-columns: 1fr !important; }
    }
</style>

@php
    $statusMap = [
        'nao_pago'             => ['label' => 'Não Pago',             'class' => 'bs-secondary', 'bar' => '#94a3b8'],
        'aguarda_comprovativo' => ['label' => 'Aguarda Comprovativo', 'class' => 'bs-warning',   'bar' => '#d97706'],
        'pagamento_confirmado' => ['label' => 'Pag. Confirmado',      'class' => 'bs-info',      'bar' => '#1d4ed8'],
        'em_analise'           => ['label' => 'Em Análise',           'class' => 'bs-primary',   'bar' => '#4338ca'],
        'aprovado'             => ['label' => 'Aprovado',             'class' => 'bs-success',   'bar' => '#16a34a'],
        'documento_emitido'    => ['label' => 'Doc. Emitido',         'class' => 'bs-success',   'bar' => '#0d9488'],
        'rejeitado'            => ['label' => 'Rejeitado',            'class' => 'bs-danger',    'bar' => '#dc2626'],
    ];

    $totalParaBase = max(array_sum(array_column($pedidosPorStatus, 'total')), 1);
    $maxMes = max(array_column($pedidosPorMes, 'total') ?: [1]);

    $mesesLabels = [
        '01'=>'Jan','02'=>'Fev','03'=>'Mar','04'=>'Abr',
        '05'=>'Mai','06'=>'Jun','07'=>'Jul','08'=>'Ago',
        '09'=>'Set','10'=>'Out','11'=>'Nov','12'=>'Dez',
    ];
@endphp

{{-- Alertas --}}
@if(session('success'))
<div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; font-size:13px; margin-bottom:18px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif

{{-- ===== STATS ===== --}}
<div class="stat-grid" style="display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:18px;">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-file-alt"></i></div>
        <div>
            <div class="stat-value">{{ $totalPedidos }}</div>
            <div class="stat-label">Total de Pedidos</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber"><i class="fas fa-clock"></i></div>
        <div>
            <div class="stat-value">{{ $pedidosPendentes }}</div>
            <div class="stat-label">Pendentes</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon indigo"><i class="fas fa-upload"></i></div>
        <div>
            <div class="stat-value">{{ $pedidosAguardandoComprovativo }}</div>
            <div class="stat-label">Aguardam Comprovativo</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-value">{{ $pedidosAprovados }}</div>
            <div class="stat-label">Aprovados</div>
        </div>
    </div>
</div>

{{-- ===== GRÁFICO + DISTRIBUIÇÃO ===== --}}
<div class="two-col" style="display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:18px;">

    {{-- Gráfico de pedidos por mês --}}
    <div class="data-card">
        <div class="data-card-header">
            <span><i class="fas fa-chart-bar" style="color:#1d4ed8; margin-right:6px;"></i>Pedidos por Mês — {{ now()->year }}</span>
        </div>
        <div class="chart-wrap">
            @if(count($pedidosPorMes) > 0)
            <div class="bar-chart">
                @foreach($pedidosPorMes as $item)
                @php
                    $mes = substr($item['mes'], 5, 2);
                    $pct = $maxMes > 0 ? round(($item['total'] / $maxMes) * 100) : 0;
                @endphp
                <div class="bar-col">
                    <div class="bar-value">{{ $item['total'] }}</div>
                    <div class="bar-fill {{ $item['total'] == 0 ? 'zero' : '' }}"
                         style="height:{{ $pct }}%;"></div>
                    <div class="bar-label">{{ $mesesLabels[$mes] ?? $mes }}</div>
                </div>
                @endforeach
            </div>
            @else
            <div style="text-align:center; padding:40px; color:#94a3b8; font-size:13px;">
                <i class="fas fa-chart-bar" style="font-size:24px; display:block; margin-bottom:8px; color:#cbd5e1;"></i>
                Sem dados para este ano
            </div>
            @endif
        </div>
        {{-- Pagamentos hoje --}}
        <div style="padding:14px 20px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center;">
            <span style="font-size:12px; color:#64748b;">
                <i class="fas fa-calendar-day" style="margin-right:4px;"></i>
                Pagamentos hoje: <strong style="color:#0f172a;">{{ $pagamentosHoje }}</strong>
            </span>
            <span style="font-size:13px; font-weight:600; color:#16a34a;">
                KZ {{ number_format($valorPagoHoje, 2, ',', '.') }}
            </span>
        </div>
    </div>

    {{-- Distribuição por status --}}
    <div class="data-card">
        <div class="data-card-header">
            <span><i class="fas fa-layer-group" style="color:#4338ca; margin-right:6px;"></i>Distribuição por Estado</span>
        </div>
        @foreach($pedidosPorStatus as $item)
        @php
            $pct = round(($item['total'] / $totalParaBase) * 100);
            $cor = $statusMap[$item['status']]['bar'] ?? '#94a3b8';
        @endphp
        <div class="status-row">
            <span class="status-name">{{ $item['label'] }}</span>
            <div class="status-bar-wrap">
                <div class="status-bar-fill" style="width:{{ $pct }}%; background:{{ $cor }};"></div>
            </div>
            <span class="status-count">{{ $item['total'] }}</span>
        </div>
        @endforeach
    </div>

</div>

{{-- ===== ÚLTIMOS PEDIDOS ===== --}}
<div class="data-card" style="margin-bottom:18px;">
    <div class="data-card-header">
        <span><i class="fas fa-list" style="color:#475569; margin-right:6px;"></i>Últimos Pedidos</span>
        <a href="{{ route('admin.pedidos.index') }}" style="font-size:12px; color:#1d4ed8; text-decoration:none;">Ver todos →</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Processo</th>
                <th>Candidato</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            @forelse($ultimosPedidos as $pedido)
            @php
                $statusKey = is_object($pedido->status) ? $pedido->status->value : (string)($pedido->status ?? '');
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
                <td><span class="bs {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span></td>
                <td style="color:#94a3b8; font-size:12px;">{{ $pedido->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="5">Nenhum pedido encontrado</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ===== ACÇÕES RÁPIDAS ===== --}}
<div class="data-card" style="padding:18px 20px;">
    <div style="font-size:13px; font-weight:500; color:#0f172a; margin-bottom:12px;">Acções Rápidas</div>
    <div class="three-col" style="display:grid; grid-template-columns:repeat(4,1fr); gap:10px;">
        <a href="{{ route('admin.pagamentos.pendentes') }}" class="quick-btn amber">
            <i class="fas fa-money-bill"></i> Validar Pagamentos
        </a>
        <a href="{{ route('admin.pedidos.index') }}" class="quick-btn blue">
            <i class="fas fa-list"></i> Ver Pedidos
        </a>
        <a href="{{ route('admin.noticias.index') }}" class="quick-btn teal">
            <i class="fas fa-newspaper"></i> Gerir Notícias
        </a>
        <a href="{{ route('admin.relatorios.pedidos') }}" class="quick-btn slate">
            <i class="fas fa-chart-bar"></i> Relatórios
        </a>
    </div>
</div>

@endsection