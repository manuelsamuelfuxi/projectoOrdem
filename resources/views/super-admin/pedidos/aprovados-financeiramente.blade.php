@extends("layouts.super-admin")

@section("title", "Pedidos Aprovados — Emissão de Documentos")
@section("page-title", "Emissão de Documentos")

@section("content")

<style>
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
        padding: 12px 16px;
        color: #334155;
        border-bottom: 0.5px solid #f1f5f9;
        vertical-align: middle;
    }
    .data-table tbody tr:last-child td { border-bottom: none; }
    .data-table tbody tr:hover td { background: #f8fafc; }

    .process-num { font-size: 12px; color: #94a3b8; font-family: monospace; }
    .candidate-name { font-weight: 500; color: #0f172a; }

    .doc-type-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: 500;
    }
    .doc-carteira { background: #eff6ff; color: #1d4ed8; }
    .doc-licenca  { background: #f0fdf4; color: #16a34a; }
    .doc-cartao-membro { background: #fdf4ff; color: #a21caf; }

    .btn-emitir {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 500;
        background: #16a34a; color: white;
        padding: 6px 14px;
        border: none; cursor: pointer;
        text-decoration: none;
        transition: opacity 0.15s;
    }
    .btn-emitir:hover { opacity: 0.85; color: white; }

    .btn-rejeitar {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 500;
        background: white; color: #dc2626;
        padding: 6px 14px;
        border: 1px solid #fecaca; cursor: pointer;
        transition: background 0.15s;
    }
    .btn-rejeitar:hover { background: #fef2f2; }

    .empty-state {
        text-align: center;
        padding: 48px 16px;
        color: #94a3b8;
        font-size: 13px;
    }
    .empty-state i { font-size: 28px; color: #cbd5e1; display: block; margin-bottom: 10px; }

    /* Modal */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(15,23,42,0.45); z-index: 999;
        align-items: center; justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: white;
        padding: 28px; width: 420px; max-width: 90vw;
    }
    .modal-title { font-size: 15px; font-weight: 500; color: #0f172a; margin-bottom: 6px; }
    .modal-desc  { font-size: 13px; color: #64748b; margin-bottom: 18px; }
    .modal-label { font-size: 12px; font-weight: 500; color: #334155; margin-bottom: 6px; display: block; }
    .modal-textarea {
        width: 100%; border: 1px solid #e2e8f0;
        padding: 10px 12px; font-size: 13px; color: #0f172a;
        resize: vertical; min-height: 90px; outline: none;
        font-family: inherit;
    }
    .modal-textarea:focus { border-color: #1d4ed8; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 18px; }
    .btn-cancelar {
        padding: 8px 18px;
        border: 1px solid #e2e8f0; background: white;
        font-size: 13px; color: #334155; cursor: pointer;
    }
    .btn-confirmar-rejeitar {
        padding: 8px 18px;
        border: none; background: #dc2626;
        font-size: 13px; color: white; cursor: pointer; font-weight: 500;
    }
    .btn-confirmar-rejeitar:hover { background: #b91c1c; }
    .btn-confirmar-gerar {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 8px 18px;
        border: none; background: #16a34a;
        font-size: 13px; color: white; cursor: pointer; font-weight: 500;
    }
    .btn-confirmar-gerar:hover { background: #15803d; }

    .btn-ver-pdf {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 500;
    background: white; color: #1d4ed8;
    padding: 6px 14px;
    border: 1px solid #bfdbfe; cursor: pointer;
    text-decoration: none;
    transition: background 0.15s;
}
.btn-ver-pdf:hover { background: #eff6ff; }

    @media (max-width: 768px) {
        .data-table thead th:nth-child(3),
        .data-table tbody td:nth-child(3) { display: none; }
        .actions-cell { display: flex; flex-direction: column; gap: 6px; }
    }
</style>

{{-- Alertas --}}
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
            <i class="fas fa-file-pdf" style="color:#16a34a;"></i>
            Pedidos com Pagamento Aprovado — Prontos para Emissão
            <span style="background:#f0fdf4; color:#16a34a; font-size:11px; font-weight:500; padding:2px 8px; border-radius:20px;">
                {{ $pedidos->total() }} registo{{ $pedidos->total() !== 1 ? 's' : '' }}
            </span>
        </span>
        <a href="{{ route('super-admin.dashboard') }}" style="font-size:12px; color:#64748b; text-decoration:none;">
            ← Voltar ao Dashboard
        </a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Processo</th>
                <th>Candidato</th>
                <th>Tipo de Documento</th>
                <th>Pagamento Confirmado em</th>
                <th>Acções</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $pedido)
            @php
                $classeBadge = match($pedido->document_type) {
                    'carteira'      => 'doc-carteira',
                    'licenca'       => 'doc-licenca',
                    'cartao_membro' => 'doc-cartao-membro',
                    default         => 'doc-licenca',
                };

                $rotaPreview = match($pedido->document_type) {
                    'licenca' => route('super-admin.licenca.preview', $pedido),
                    default   => route('super-admin.carteira.preview', $pedido),
                };
            @endphp
            <tr>
                <td class="process-num">{{ $pedido->process_number }}</td>
                <td class="candidate-name">{{ $pedido->full_name }}</td>
                <td>
                    <span class="doc-type-badge {{ $classeBadge }}">
                        {{ match($pedido->document_type) {
                            'carteira' => 'Carteira Profissional',
                            'licenca' => 'Licença Profissional',
                            'cartao_membro' => 'Cartão de Membro',
                            default => $pedido->document_type,
                        } }}
                    </span>
                </td>
                <td style="color:#94a3b8; font-size:12px;">
                    {{ $pedido->pagamento?->confirmed_at ? \Carbon\Carbon::parse($pedido->pagamento->confirmed_at)->format('d/m/Y H:i') : '—' }}
                </td>
                <td class="actions-cell">
                    {{-- Gerar Licença — abre modal de confirmação; só ao confirmar é que
                        o pedido de facto transita para DOCUMENTO_EMITIDO. --}}
                    <button type="button" class="btn-emitir"
                        data-url="{{ route('super-admin.pedidos.aprovar-emissao', $pedido) }}"
                        onclick="abrirModalGerar(this.dataset.url, '{{ addslashes($pedido->full_name) }}')">
                        <i class="fas fa-file-pdf"></i> Gerar Licença
                    </button>

                    {{-- Ver PDF — pré-visualização apenas, não altera o estado do pedido. --}}
                    <a href="{{ $rotaPreview }}"
                    target="_blank"
                    rel="noopener"
                    class="btn-ver-pdf">
                        <i class="fas fa-eye"></i> Ver PDF
                    </a>

                    {{-- Botão Rejeitar --}}
                    <button type="button" class="btn-rejeitar"
                        data-url="{{ route('super-admin.pedidos.rejeitar', $pedido) }}"
                        onclick="abrirModalRejeitar(this.dataset.url, '{{ addslashes($pedido->full_name) }}')">
                        <i class="fas fa-times"></i> Rejeitar
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        Nenhum pedido com pagamento aprovado a aguardar emissão de documento
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

</div> {{-- fim do .data-card --}}

<div style="display:flex; gap:12px; margin-top:20px; justify-content:flex-end;">
    <form method="POST"
          action="{{ route('super-admin.pedidos.aprovar-emissao-todas') }}"
          onsubmit="return confirm('Tem a certeza que deseja gerar as licenças de TODOS os pedidos pendentes de emissão? Esta acção não pode ser desfeita.');">
        @csrf
        <button type="submit" class="btn-confirmar-gerar">
            <i class="fas fa-file-pdf"></i> Gerar Todas
        </button>
    </form>

    <a href="{{ route('super-admin.carteira.visualizar-todas') }}"
   target="_blank"
   rel="noopener"
   class="btn-cancelar"
   style="text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
    <i class="fas fa-eye"></i> Visualizar Todas
</a>
</div>

{{-- ===== MODAL GERAR LICENÇA ===== --}}
<div class="modal-overlay" id="modalGerar">
    <div class="modal-box">
        <div class="modal-title">Gerar Licença</div>
        <div class="modal-desc" id="modalGerarDesc">Tem a certeza desta ação?</div>
        <form id="formGerar" method="POST">
            @csrf
            <div class="modal-actions">
                <button type="button" class="btn-cancelar" onclick="fecharModalGerar()">Cancelar</button>
                <button type="submit" class="btn-confirmar-gerar">
                    <i class="fas fa-file-pdf"></i> Confirmar Geração
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL REJEITAR ===== --}}
<div class="modal-overlay" id="modalRejeitar">
    <div class="modal-box">
        <div class="modal-title">Rejeitar Pedido</div>
        <div class="modal-desc" id="modalRejeitarDesc">Indica o motivo da rejeição.</div>
        <form id="formRejeitar" method="POST">
            @csrf
            <label class="modal-label">Motivo da Rejeição *</label>
            <textarea name="motivo" class="modal-textarea" placeholder="Descreve o motivo da rejeição..." required minlength="10" maxlength="500"></textarea>
            <div class="modal-actions">
                <button type="button" class="btn-cancelar" onclick="fecharModalRejeitar()">Cancelar</button>
                <button type="submit" class="btn-confirmar-rejeitar">
                    <i class="fas fa-times"></i> Confirmar Rejeição
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // ── Modal: Gerar Licença ────────────────────────────────────────────
    function abrirModalGerar(url, nome) {
        document.getElementById('modalGerarDesc').textContent =
            'Tem a certeza que deseja gerar a licença para ' + nome + '? Esta ação não pode ser desfeita.';
        document.getElementById('formGerar').action = url;
        document.getElementById('modalGerar').classList.add('show');
    }
    function fecharModalGerar() {
        document.getElementById('modalGerar').classList.remove('show');
    }
    document.getElementById('modalGerar').addEventListener('click', function (e) {
        if (e.target === this) fecharModalGerar();
    });

    // ── Modal: Rejeitar ──────────────────────────────────────────────────
    function abrirModalRejeitar(url, nome) {
        document.getElementById('modalRejeitarDesc').textContent = 'Indica o motivo da rejeição para o pedido de ' + nome + '.';
        document.getElementById('formRejeitar').action = url;
        document.getElementById('modalRejeitar').classList.add('show');
    }
    function fecharModalRejeitar() {
        document.getElementById('modalRejeitar').classList.remove('show');
    }
    document.getElementById('modalRejeitar').addEventListener('click', function (e) {
        if (e.target === this) fecharModalRejeitar();
    });
</script>

@endsection