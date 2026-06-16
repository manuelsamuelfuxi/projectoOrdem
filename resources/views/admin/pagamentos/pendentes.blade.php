@extends("layouts.admin")

@section("title", "Pagamentos Pendentes")
@section("page-title", "Pagamentos Pendentes")

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

    .process-num  { font-size: 12px; color: #94a3b8; font-family: monospace; }
    .candidate-name { font-weight: 500; color: #0f172a; }
    .valor-cell   { font-family: monospace; font-size: 13px; color: #0f172a; font-weight: 500; }
    .ref-cell     { font-family: monospace; font-size: 12px; color: #64748b; }
    .date-cell    { font-size: 12px; color: #94a3b8; white-space: nowrap; }

    .empty-row td {
        text-align: center;
        padding: 40px 28px;
        color: #94a3b8;
        font-size: 13px;
    }

    /* Botões de ação inline */
    .act-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 5px 11px;
        font-size: 11px;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid transparent;
        cursor: pointer;
        font-family: inherit;
        transition: opacity 0.15s;
        white-space: nowrap;
    }
    .act-btn:hover { opacity: 0.82; }
    .act-btn-ver      { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
    .act-btn-aprovar  { background: #16a34a; color: white; }
    .act-btn-rejeitar { background: white;   color: #dc2626; border-color: #fecaca; }
    .act-btn-rejeitar:hover { background: #fef2f2; opacity: 1; }

    .actions-cell { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }

    /* Paginação — herda do Bootstrap, apenas ajustamos raio */
    .pagination .page-link { font-size: 12px; color: #475569; border-color: #e2e8f0; }
    .pagination .page-item.active .page-link { background: #1d4ed8; border-color: #1d4ed8; }

    /* Modal */
    .modal-content { border: 1px solid #e2e8f0; border-radius: 0; }
    .modal-header  { padding: 14px 20px; border-bottom: 1px solid #e2e8f0; }
    .modal-title   { font-size: 13px; font-weight: 500; color: #0f172a; }
    .modal-body    { padding: 16px 20px; }
    .modal-footer  { padding: 12px 20px; border-top: 1px solid #e2e8f0; }
    .form-label    { font-size: 11px; font-weight: 500; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
    .form-control  { font-size: 13px; border-color: #e2e8f0; border-radius: 0; color: #334155; }
    .form-control:focus { border-color: #93c5fd; box-shadow: 0 0 0 3px rgba(59,130,246,0.1); }

    @media (max-width: 768px) {
        .data-table thead th:nth-child(4),
        .data-table tbody td:nth-child(4) { display: none; }
    }
    @media (max-width: 576px) {
        .data-table thead th:nth-child(3),
        .data-table tbody td:nth-child(3) { display: none; }
        .actions-cell { flex-direction: column; }
        .act-btn { width: 100%; justify-content: center; }
    }
</style>

{{-- Alertas --}}
@if(session('success'))
<div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; padding:12px 16px; font-size:13px; margin-bottom:16px;">
    <i class="fas fa-check-circle"></i> {{ session('success') }}
</div>
@endif
@if(session('warning'))
<div style="background:#fffbeb; border:1px solid #fde68a; color:#b45309; padding:12px 16px; font-size:13px; margin-bottom:16px;">
    <i class="fas fa-exclamation-circle"></i> {{ session('warning') }}
</div>
@endif

<div class="data-card">
    <div class="data-card-header">
        <span>
            <i class="fas fa-clock" style="color:#d97706; margin-right:6px;"></i>
            Comprovativos Pendentes de Validação
        </span>
        <span style="font-size:12px; color:#94a3b8; font-weight:400;">
            {{ $pagamentos->total() }} {{ $pagamentos->total() === 1 ? 'registo' : 'registos' }}
        </span>
    </div>

    <div style="overflow-x:auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Processo</th>
                    <th>Candidato</th>
                    <th>Valor</th>
                    <th>Data Envio</th>
                    <th>Comprovativo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pagamentos as $pagamento)
                <tr>
                    <td class="process-num">{{ $pagamento->pedido->process_number }}</td>
                    <td class="candidate-name">{{ $pagamento->pedido->full_name }}</td>
                    <td class="valor-cell">KZ {{ number_format($pagamento->amount, 2, ',', '.') }}</td>
                    <td class="date-cell">{{ $pagamento->proof_submitted_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.pagamentos.ver-comprovativo', $pagamento) }}"
                           target="_blank"
                           class="act-btn act-btn-ver">
                            <i class="fas fa-file-alt"></i> Ver
                        </a>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <form action="{{ route('admin.pagamentos.aprovar', $pagamento) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="act-btn act-btn-aprovar"
                                        onclick="return confirm('Aprovar este pagamento?')">
                                    <i class="fas fa-check"></i> Aprovar
                                </button>
                            </form>
                            <button type="button"
                                    class="act-btn act-btn-rejeitar"
                                    data-bs-toggle="modal"
                                    data-bs-target="#rejeitarModal{{ $pagamento->id }}">
                                <i class="fas fa-times"></i> Rejeitar
                            </button>
                        </div>
                    </td>
                </tr>

                {{-- Modal Rejeitar --}}
                <div class="modal fade" id="rejeitarModal{{ $pagamento->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <form action="{{ route('admin.pagamentos.rejeitar', $pagamento) }}" method="POST">
                            @csrf
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fas fa-times-circle" style="color:#dc2626; margin-right:6px;"></i>
                                        Rejeitar — {{ $pagamento->pedido->process_number }}
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <label for="motivo_{{ $pagamento->id }}" class="form-label">
                                        Motivo da Rejeição
                                    </label>
                                    <textarea class="form-control"
                                              id="motivo_{{ $pagamento->id }}"
                                              name="motivo_rejeicao"
                                              rows="3"
                                              placeholder="Descreva o motivo..."
                                              required></textarea>
                                    <div style="font-size:11px; color:#94a3b8; margin-top:6px;">
                                        O candidato será notificado e poderá submeter novo comprovativo.
                                    </div>
                                </div>
                                <div class="modal-footer" style="justify-content:flex-end; gap:8px;">
                                    <button type="button"
                                            class="act-btn"
                                            style="border:1px solid #e2e8f0; color:#475569; background:white;"
                                            data-bs-dismiss="modal">
                                        Cancelar
                                    </button>
                                    <button type="submit" class="act-btn act-btn-rejeitar"
                                            style="border:1px solid #fecaca;">
                                        <i class="fas fa-times"></i> Confirmar Rejeição
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @empty
                <tr class="empty-row">
                    <td colspan="7">
                        <i class="fas fa-check-circle" style="font-size:22px; display:block; margin-bottom:8px; color:#86efac;"></i>
                        Nenhum comprovativo pendente de validação
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pagamentos->hasPages())
    <div style="padding:12px 16px; border-top:1px solid #f1f5f9; display:flex; justify-content:flex-end;">
        {{ $pagamentos->links() }}
    </div>
    @endif
</div>

@endsection