@extends("layouts.super-admin")

@section("title", "Dashboard - Super Admin")
@section("page-title", "Dashboard")

@section("content")

{{-- Estilos específicos do dashboard --}}
<style>
    .stat-card {
        background: white;
        padding: 20px 24px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: box-shadow 0.15s;
    }
    .stat-card:hover { box-shadow: 0 4px 16px rgba(15,23,42,0.07); }

    .stat-icon {
        width: 46px; height: 46px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
    .stat-icon.blue   { background: #eff6ff; color: #1d4ed8; }
    .stat-icon.green  { background: #f0fdf4; color: #16a34a; }
    .stat-icon.amber  { background: #fffbeb; color: #d97706; }
    .stat-icon.red    { background: #fef2f2; color: #dc2626; }
    .stat-icon.indigo { background: #eef2ff; color: #4338ca; }
    .stat-icon.slate  { background: #f8fafc; color: #475569; }

    .stat-value { font-size: 26px; font-weight: 600; color: #0f172a; line-height: 1; }
    .stat-label { font-size: 12.5px; color: #64748b; margin-top: 3px; }

    .section-title {
        font-size: 13px;
        font-weight: 500;
        color: #0f172a;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #475569;
        font-size: 11px;
        font-weight: 500;
        padding: 2px 8px;
    }

    .data-card {
        background: white;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }
    .data-card-header {
        padding: 16px 20px;
        border-bottom: 0.5px solid #e2e8f0;
        font-size: 13px;
        font-weight: 500;
        color: #0f172a;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .data-card-body { padding: 0; }

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
        vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #f8fafc; }

    .process-num { font-size: 12px; color: #94a3b8; font-family: monospace; }
    .candidate-name { font-weight: 500; color: #0f172a; }

    .badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }
    .badge-status::before { content: ''; width: 5px; height: 5px; border-radius: 50%; background: currentColor; }

    .bs-secondary { background: #f1f5f9; color: #475569; }
    .bs-warning   { background: #fffbeb; color: #b45309; }
    .bs-info      { background: #eff6ff; color: #1d4ed8; }
    .bs-primary   { background: #eef2ff; color: #4338ca; }
    .bs-success   { background: #f0fdf4; color: #16a34a; }
    .bs-danger    { background: #fef2f2; color: #dc2626; }

    .doc-type-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 500;
    }
    .doc-carteira { background: #eff6ff; color: #1d4ed8; }
    .doc-licenca  { background: #f0fdf4; color: #16a34a; }

    .empty-row td {
        text-align: center;
        padding: 28px 16px;
        color: #94a3b8;
        font-size: 13px;
    }

    .quick-actions { display: flex; flex-wrap: wrap; gap: 10px; }
    .quick-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: opacity 0.15s, box-shadow 0.15s;
        border: none;
        cursor: pointer;
    }
    .quick-btn:hover { opacity: 0.88; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .quick-btn.blue   { background: #1d4ed8; color: white; }
    .quick-btn.green  { background: #16a34a; color: white; }
    .quick-btn.indigo { background: #4338ca; color: white; }

    .progress-bar-wrap {
        background: #f1f5f9;
        height: 6px;
        overflow: hidden;
        margin-top: 6px;
    }
    .progress-bar-fill {
        height: 100%;
        background: #1d4ed8;
        transition: width 0.4s ease;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .stat-grid { grid-template-columns: 1fr 1fr !important; }
        .two-col-grid { grid-template-columns: 1fr !important; }
        .quick-actions { flex-direction: column; }
        .quick-btn { justify-content: center; }
        .data-table thead th:nth-child(3),
        .data-table tbody td:nth-child(3) { display: none; }
    }
    @media (max-width: 480px) {
        .stat-grid { grid-template-columns: 1fr !important; }
    }
</style>

@php
    $statusMap = [
        'nao_pago'             => ['label' => 'Não Pago',               'class' => 'bs-secondary'],
        'aguarda_comprovativo' => ['label' => 'Aguarda Comprovativo',   'class' => 'bs-warning'],
        'pagamento_confirmado' => ['label' => 'Pagamento Confirmado',   'class' => 'bs-info'],
        'em_analise'           => ['label' => 'Em Análise',             'class' => 'bs-primary'],
        'aprovado'             => ['label' => 'Aprovado',               'class' => 'bs-success'],
        'documento_emitido'    => ['label' => 'Documento Emitido',      'class' => 'bs-success'],
        'rejeitado'            => ['label' => 'Rejeitado',              'class' => 'bs-danger'],
    ];

    $totalParaBase = $totalPedidos > 0 ? $totalPedidos : 1;
@endphp

{{-- ===== CARDS DE ESTATÍSTICAS ===== --}}
<div class="stat-grid" style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px;">

    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-value">{{ $totalAdmins }}</div>
            <div class="stat-label">Administradores</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon slate"><i class="fas fa-file-alt"></i></div>
        <div>
            <div class="stat-value">{{ $totalPedidos }}</div>
            <div class="stat-label">Total de Pedidos</div>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-file-pdf"></i></div>
        <div>
            <div class="stat-value">{{ $pedidosComDocumentosEmitidos }}</div>
            <div class="stat-label">Documentos Emitidos</div>
        </div>
    </div>

</div>

{{-- ===== RECEITA + DISTRIBUIÇÃO ===== --}}
<div style="display:grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 24px;" class="two-col-grid">

    {{-- Receita --}}
    <div class="data-card" style="padding: 20px 24px;">
        <div style="font-size:12px; color:#94a3b8; margin-bottom:4px;">Receita Total Confirmada</div>
        <div style="font-size:28px; font-weight:600; color:#0f172a;">KZ {{ number_format($receitaTotal, 2, ',', '.') }}</div>
        <div style="margin-top:16px; display:flex; flex-direction:column; gap:10px;">
            <div>
                <div style="display:flex; justify-content:space-between; font-size:12px; color:#64748b; margin-bottom:4px;">
                    <span>Documentos emitidos</span>
                    <span style="font-weight:500; color:#0f172a;">{{ $pedidosComDocumentosEmitidos }}</span>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width: {{ round(($pedidosComDocumentosEmitidos / $totalParaBase) * 100) }}%; background:#16a34a;"></div>
                </div>
            </div>
            <div>
                <div style="display:flex; justify-content:space-between; font-size:12px; color:#64748b; margin-bottom:4px;">
                    <span>Aprovados (pendentes emissão)</span>
                    <span style="font-weight:500; color:#0f172a;">{{ $pedidosAprovados }}</span>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width: {{ round(($pedidosAprovados / $totalParaBase) * 100) }}%; background:#4338ca;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Últimos Administradores --}}
    <div class="data-card">
        <div class="data-card-header">
            Administradores Recentes
            <a href="{{ route('super-admin.admins.index') }}" style="font-size:12px; color:#1d4ed8; text-decoration:none;">Ver todos →</a>
        </div>
        <div class="data-card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Desde</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($adminsRecentes as $admin)
                    <tr>
                        <td class="candidate-name">{{ $admin->name }}</td>
                        <td style="color:#64748b; font-size:12px;">{{ $admin->email }}</td>
                        <td style="color:#94a3b8; font-size:12px;">{{ $admin->created_at->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr class="empty-row"><td colspan="3">Nenhum administrador registado</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ===== APROVADOS PRONTOS PARA EMISSÃO ===== --}}
<div class="data-card" style="margin-bottom: 24px;">
    <div class="data-card-header">
        <span style="display:flex; align-items:center; gap:8px;">
            <i class="fas fa-check-circle" style="color:#16a34a;"></i>
            Prontos para Emissão de Documento
            @if($pedidosAprovados > 0)
                <span style="background:#fef9c3; color:#a16207; font-size:11px; font-weight:500; padding:2px 8px; border-radius:20px;">
                    {{ $pedidosAprovados }} pendente{{ $pedidosAprovados > 1 ? 's' : '' }}
                </span>
            @endif
        </span>
        <a href="{{ route('super-admin.pedidos.financeiramente-aprovados') }}" style="font-size:12px; color:#1d4ed8; text-decoration:none;">Ver todos →</a>
    </div>
    <div class="data-card-body">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Processo</th>
                    <th>Candidato</th>
                    <th>Tipo</th>
                    <th>Aprovado em</th>
                    <th>Acção</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pedidosParaEmitir as $pedido)
                @php $statusKey = is_object($pedido->status) ? $pedido->status->value : (string)($pedido->status ?? ''); @endphp
                <tr>
                    <td class="process-num">{{ $pedido->process_number }}</td>
                    <td class="candidate-name">{{ $pedido->full_name }}</td>
                    <td>
                        <span class="doc-type-badge {{ $pedido->document_type === 'carteira' ? 'doc-carteira' : 'doc-licenca' }}">
                            {{ $pedido->document_type === 'carteira' ? 'Carteira' : 'Licença' }}
                        </span>
                    </td>
                    <td style="color:#94a3b8; font-size:12px;">
                        {{ $pedido->approved_at ? \Carbon\Carbon::parse($pedido->approved_at)->format('d/m/Y') : '—' }}
                    </td>
                    <td>
                        <a href="{{ route('super-admin.pedidos.emitir', $pedido->id) }}"
                           style="display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:500; background:#16a34a; color:white; padding:5px 12px; border-radius:6px; text-decoration:none;">
                            <i class="fas fa-file-pdf"></i> Emitir
                        </a>
                    </td>
                </tr>
                @empty
                <tr class="empty-row">
                    <td colspan="5">
                        <i class="fas fa-inbox" style="font-size:20px; color:#cbd5e1; display:block; margin-bottom:6px;"></i>
                        Nenhum pedido aprovado aguardando emissão
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- ===== ACÇÕES RÁPIDAS ===== --}}
    <div class="quick-actions">
        <a href="{{ route('super-admin.admins.index') }}" class="quick-btn blue">
            <i class="fas fa-users"></i> Gerir Administradores
        </a>
        <a href="{{ route('super-admin.pedidos.financeiramente-aprovados') }}" class="quick-btn green">
            <i class="fas fa-file-pdf"></i> Emitir Documentos
        </a>
        <a href="{{ route('super-admin.relatorios.completos') }}" class="quick-btn indigo">
            <i class="fas fa-chart-bar"></i> Ver Relatórios
        </a>
    </div>

@endsection