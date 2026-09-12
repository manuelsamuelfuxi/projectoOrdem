@extends("layouts.super-admin")

@section("title", "Carteiras Emitidas")
@section("page-title", "Licenças Emitidas")

@section("content")

<style>
    .data-card { background: white; border: 1px solid #e2e8f0; overflow: hidden; }
    .data-card-header {
        padding: 16px 20px; border-bottom: 0.5px solid #e2e8f0;
        font-size: 13px; font-weight: 500; color: #0f172a;
        display: flex; align-items: center; justify-content: space-between;
    }
    .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-table thead th {
        padding: 10px 16px; font-size: 11px; font-weight: 500; color: #94a3b8;
        text-transform: uppercase; letter-spacing: 0.05em;
        background: #f8fafc; border-bottom: 0.5px solid #e2e8f0; text-align: left;
    }
    .data-table tbody td {
        padding: 12px 16px; color: #334155;
        border-bottom: 0.5px solid #f1f5f9; vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #f8fafc; }
    .process-num { font-size: 12px; color: #94a3b8; font-family: monospace; }
    .candidate-name { font-weight: 500; color: #0f172a; }
    .doc-type-badge { font-size: 11px; padding: 2px 8px; border-radius: 4px; font-weight: 500; }
    .doc-carteira { background: #eff6ff; color: #1d4ed8; }
    .doc-licenca  { background: #f0fdf4; color: #16a34a; }
    .btn-ver-pdf {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 500;
        background: white; color: #1d4ed8;
        padding: 6px 14px; border: 1px solid #bfdbfe;
        text-decoration: none; transition: background 0.15s;
    }
    .btn-ver-pdf:hover { background: #eff6ff; }
    .empty-state { text-align: center; padding: 48px 16px; color: #94a3b8; font-size: 13px; }
    .empty-state i { font-size: 28px; color: #cbd5e1; display: block; margin-bottom: 10px; }
</style>

@if(session('success'))
    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; border-radius:8px; font-size:13px; margin-bottom:20px;">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif
@if(session('warning'))
    <div style="background:#fffbeb; border:1px solid #fde68a; color:#b45309; padding:12px 16px; border-radius:8px; font-size:13px; margin-bottom:20px;">
        <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
    </div>
@endif

<div class="data-card">
    <div class="data-card-header">
        <span style="display:flex; align-items:center; gap:8px;">
            <i class="fas fa-folder-open" style="color:#1d4ed8;"></i>
            Licenças / Carteiras Emitidas
            <span style="background:#eff6ff; color:#1d4ed8; font-size:11px; font-weight:500; padding:2px 8px; border-radius:20px;">
                {{ $pedidos->total() }} registo{{ $pedidos->total() !== 1 ? 's' : '' }}
            </span>
        </span>
        <a href="{{ route('super-admin.pedidos.financeiramente-aprovados') }}" style="font-size:12px; color:#64748b; text-decoration:none;">
            ← Voltar
        </a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Processo</th>
                <th>Candidato</th>
                <th>Tipo de Documento</th>
                <th>Emitido em</th>
                <th>Acções</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $pedido)
            <tr>
                <td class="process-num">{{ $pedido->process_number }}</td>
                <td class="candidate-name">{{ $pedido->full_name }}</td>
                <td>
                    <span class="doc-type-badge {{ $pedido->document_type === 'carteira' ? 'doc-carteira' : 'doc-licenca' }}">
                        {{ $pedido->document_type === 'carteira' ? 'Carteira Profissional' : 'Licença Profissional' }}
                    </span>
                </td>
                <td style="color:#94a3b8; font-size:12px;">
                    {{ $pedido->document_issued_at ? \Carbon\Carbon::parse($pedido->document_issued_at)->format('d/m/Y H:i') : '—' }}
                </td>
                <td>
                    <a href="{{ route('super-admin.licencas.documento', $pedido) }}" target="_blank" rel="noopener" class="btn-ver-pdf">
                        <i class="fas fa-file-pdf"></i> Ver PDF
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <i class="fas fa-folder-open"></i>
                        Ainda não há licenças/carteiras emitidas.
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($pedidos->hasPages())
    <div style="padding: 14px 16px; border-top: 0.5px solid #e2e8f0; font-size:13px;">
        {{ $pedidos->links() }}
    </div>
    @endif
</div>

@endsection