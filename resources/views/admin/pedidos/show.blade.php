@extends("layouts.admin")

@section("title", "Detalhes do Pedido")
@section("page-title", "Pedido — " . $pedido->process_number)

@section("content")

<style>
    .data-card {
        background: white;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        margin-bottom: 12px;
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
    background: #f8fafc;
}
    .data-card-body { padding: 20px; }

    .field-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .field-item {}
    .field-label {
        font-size: 11px;
        font-weight: 500;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 3px;
    }
    .field-value {
        font-size: 13px;
        color: #0f172a;
    }
    .field-value.mono {
        font-family: monospace;
        color: #475569;
    }

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

    .btn-voltar {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 500;
        text-decoration: none;
        border: 1px solid #e2e8f0;
        background: white;
        color: #475569;
        transition: background 0.15s;
    }
    .btn-voltar:hover { background: #f8fafc; color: #475569; }

    .divider {
        border: none;
        border-top: 1px solid #f1f5f9;
        margin: 16px 0;
    }

    @media (max-width: 768px) {
        .two-col { grid-template-columns: 1fr !important; }
        .field-grid { grid-template-columns: 1fr !important; }
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

    $statusKey  = is_object($pedido->status) ? $pedido->status->value : (string)($pedido->status ?? '');
    $statusInfo = $statusMap[$statusKey] ?? ['label' => ucfirst($statusKey), 'class' => 'bs-secondary'];
@endphp

{{-- Cabeçalho do pedido --}}
<div class="data-card">
    <div class="data-card-header">
        <span>
            <i class="fas fa-file-alt" style="color:#475569; margin-right:6px;"></i>
            Processo <span style="font-family:monospace; color:#94a3b8;">{{ $pedido->process_number }}</span>
        </span>
        <div style="display:flex; align-items:center; gap:12px;">
            <span class="bs {{ $statusInfo['class'] }}">{{ $statusInfo['label'] }}</span>
            <span style="font-size:12px; color:#94a3b8;">
                {{ $pedido->created_at->format('d/m/Y \à\s H:i') }}
            </span>
        </div>
    </div>
    <div class="data-card-body">
        <div class="field-grid" style="grid-template-columns: repeat(3, 1fr);">
            <div class="field-item">
                <div class="field-label">Tipo de Documento</div>
                <div class="field-value">
                    <span class="doc-type {{ $pedido->document_type === 'carteira' ? 'doc-carteira' : 'doc-licenca' }}">
                        {{ $pedido->document_type === 'carteira' ? 'Carteira Profissional' : 'Licença de Exercício' }}
                    </span>
                </div>
            </div>
            <div class="field-item">
                <div class="field-label">Referência UUID</div>
                <div class="field-value mono">{{ $pedido->reference_uuid }}</div>
            </div>
            <div class="field-item">
                <div class="field-label">Submetido em</div>
                <div class="field-value">{{ $pedido->submitted_at?->format('d/m/Y H:i') ?? '—' }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Dados pessoais + profissionais --}}
<div class="two-col" style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">

    <div class="data-card">
        <div class="data-card-header">
            <span><i class="fas fa-user" style="color:#475569; margin-right:6px;"></i>Dados Pessoais</span>
        </div>
        <div class="data-card-body">
            <div class="field-grid">
                <div class="field-item">
                    <div class="field-label">Nome Completo</div>
                    <div class="field-value" style="font-weight:500;">{{ $pedido->full_name }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">Nome de Nascimento</div>
                    <div class="field-value">{{ $pedido->birth_name ?? '—' }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">Nº do BI</div>
                    <div class="field-value mono">{{ $pedido->bi_number }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">NIF</div>
                    <div class="field-value mono">{{ $pedido->nif ?? '—' }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">Data de Nascimento</div>
                    <div class="field-value">{{ $pedido->birth_date?->format('d/m/Y') ?? '—' }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">Naturalidade</div>
                    <div class="field-value">{{ $pedido->birth_place ?? '—' }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">Género</div>
                    <div class="field-value">{{ ucfirst($pedido->gender ?? '—') }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">Nacionalidade</div>
                    <div class="field-value">{{ $pedido->nationality ?? '—' }}</div>
                </div>
            </div>

            <hr class="divider">

            <div class="field-grid">
                <div class="field-item">
                    <div class="field-label">Email</div>
                    <div class="field-value">{{ $pedido->email }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">Telefone</div>
                    <div class="field-value mono">{{ $pedido->phone }}</div>
                </div>
                @if($pedido->alternative_phone)
                <div class="field-item">
                    <div class="field-label">Telefone Alternativo</div>
                    <div class="field-value mono">{{ $pedido->alternative_phone }}</div>
                </div>
                @endif
                <div class="field-item">
                    <div class="field-label">Endereço</div>
                    <div class="field-value">{{ $pedido->address }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">Cidade / Província</div>
                    <div class="field-value">{{ $pedido->city }} / {{ $pedido->province }}</div>
                </div>
                @if($pedido->postal_code)
                <div class="field-item">
                    <div class="field-label">Código Postal</div>
                    <div class="field-value mono">{{ $pedido->postal_code }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="data-card-header">
            <span><i class="fas fa-briefcase" style="color:#475569; margin-right:6px;"></i>Dados Profissionais</span>
        </div>
        <div class="data-card-body">
            <div class="field-grid">
                <div class="field-item">
                    <div class="field-label">Categoria Profissional</div>
                    <div class="field-value" style="font-weight:500;">{{ $pedido->professional_category ?? '—' }}</div>
                </div>
                <div class="field-item">
                    <div class="field-label">Instituição</div>
                    <div class="field-value">{{ $pedido->institution ?? '—' }}</div>
                </div>
                @if($pedido->specialization)
                <div class="field-item">
                    <div class="field-label">Especialização</div>
                    <div class="field-value">{{ $pedido->specialization }}</div>
                </div>
                @endif
                @if($pedido->professional_license_number)
                <div class="field-item">
                    <div class="field-label">Nº Cédula Profissional</div>
                    <div class="field-value mono">{{ $pedido->professional_license_number }}</div>
                </div>
                @endif
                @if($pedido->professional_license_expiry)
                <div class="field-item">
                    <div class="field-label">Validade da Cédula</div>
                    <div class="field-value">{{ $pedido->professional_license_expiry->format('d/m/Y') }}</div>
                </div>
                @endif
            </div>

            @if($pedido->pagamento)
            <hr class="divider">
            <div style="font-size:11px; font-weight:500; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:12px;">
                Pagamento
            </div>
            <div class="field-grid">
                <div class="field-item">
                    <div class="field-label">Valor</div>
                    <div class="field-value" style="font-weight:600; color:#0f172a;">
                        KZ {{ number_format($pedido->pagamento->amount, 2, ',', '.') }}
                    </div>
                </div>
                <div class="field-item">
                    <div class="field-label">Estado do Pagamento</div>
                    <div class="field-value">{{ $pedido->pagamento->status }}</div>
                </div>
                @if($pedido->pagamento->payment_reference)
                <div class="field-item">
                    <div class="field-label">Referência</div>
                    <div class="field-value mono">{{ $pedido->pagamento->payment_reference }}</div>
                </div>
                @endif
                @if($pedido->pagamento->proof_submitted_at)
                <div class="field-item">
                    <div class="field-label">Comprovativo Enviado</div>
                    <div class="field-value">{{ $pedido->pagamento->proof_submitted_at->format('d/m/Y H:i') }}</div>
                </div>
                @endif
                @if($pedido->pagamento->confirmed_at)
                <div class="field-item">
                    <div class="field-label">Confirmado em</div>
                    <div class="field-value">{{ $pedido->pagamento->confirmed_at->format('d/m/Y H:i') }}</div>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>

</div>

{{-- Documentos --}}
@if($pedido->documentos->isNotEmpty())
<div class="data-card">
    <div class="data-card-header">
        <span><i class="fas fa-paperclip" style="color:#475569; margin-right:6px;"></i>Documentos Anexados</span>
        <span style="font-size:12px; color:#94a3b8; font-weight:400;">
            {{ $pedido->documentos->count() }} {{ $pedido->documentos->count() === 1 ? 'ficheiro' : 'ficheiros' }}
        </span>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Nome Original</th>
                <th>Data de Upload</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pedido->documentos as $documento)
            <tr>
                <td>
                    <span style="font-size:11px; padding:2px 7px; font-weight:500; background:#f1f5f9; color:#475569;">
                        {{ $documento->type }}
                    </span>
                </td>
                <td style="font-size:13px; color:#334155;">{{ $documento->original_name }}</td>
                <td style="font-size:12px; color:#94a3b8; white-space:nowrap;">
                    {{ $documento->created_at->format('d/m/Y H:i') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Rodapé --}}
<div style="display:flex; justify-content:space-between; align-items:center; margin-top:4px;">
    <a href="{{ route('admin.pedidos.index') }}" class="btn-voltar">
        <i class="fas fa-arrow-left"></i> Voltar à Lista
    </a>
    <span style="font-size:11px; color:#cbd5e1;">
        IP: {{ $pedido->ip_address ?? '—' }}
    </span>
</div>

@endsection