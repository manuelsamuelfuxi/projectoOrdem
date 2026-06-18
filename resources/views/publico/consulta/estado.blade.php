@extends("layouts.app")

@section("title", "Estado da Candidatura - " . ($pedido->process_number ?? ''))

@push('styles')
    @vite('resources/css/consulta/estado.css')
@endpush

@section("content")

@php
    /*
     * SEGURANÇA: todos os valores exibidos vêm do Model $pedido carregado pelo
     * controller via BD. O valor monetário vem do relacionamento $pedido->pagamento
     * — nunca de fallback hardcoded na view.
     *
     * O controller deve eager-load o relacionamento:
     *   $pedido = Application::with('pagamento')->findOrFail($id);
     */

    $statusVal = $pedido->status instanceof \UnitEnum
        ? $pedido->status->value
        : (string) $pedido->status;

    $statusLabels = [
        'nao_pago'             => 'Aguardando Pagamento',
        'aguarda_comprovativo' => 'Aguarda Validação',
        'pagamento_confirmado' => 'Pagamento Confirmado',
        'em_analise'           => 'Em Análise',
        'aprovado'             => 'Aprovado',
        'documento_emitido'    => 'Documento Emitido',
        'rejeitado'            => 'Rejeitado',
        'correcao_solicitada'  => 'Correcção Solicitada',
    ];

    $statusBadgeClass = [
        'nao_pago'             => 'badge-nao-pago',
        'aguarda_comprovativo' => 'badge-aguarda',
        'pagamento_confirmado' => 'badge-validado',
        'em_analise'           => 'badge-validado',
        'aprovado'             => 'badge-aprovado',
        'documento_emitido'    => 'badge-aprovado',
        'rejeitado'            => 'badge-erro',
        'correcao_solicitada'  => 'badge-nao-pago',
    ];

    $passos = [
        ['label' => 'Pedido submetido',     'icon' => 'fa-file-alt'],
        ['label' => 'Aguarda comprovativo', 'icon' => 'fa-upload'],
        ['label' => 'Aguarda validação',    'icon' => 'fa-file-check'],
        ['label' => 'Aprovado',             'icon' => 'fa-award'],
    ];

    $ordemStatus = [
        'nao_pago'             => 0,
        'aguarda_comprovativo' => 2,
        'pagamento_confirmado' => 2,
        'em_analise'           => 2,
        'aprovado'             => 3,
        'documento_emitido'    => 3,
        'rejeitado'            => -1,
        'correcao_solicitada'  => 1,
    ];

    $indiceActual = $ordemStatus[$statusVal] ?? 0;

    $notasMensagem = [
        'nao_pago'             => 'O teu pedido foi submetido. Efectua o pagamento e submete o comprovativo para continuar o processo.',
        'aguarda_comprovativo' => 'O teu comprovativo foi recebido e está a aguardar análise pela secretaria.',
        'pagamento_confirmado' => 'O comprovativo foi recebido e está a ser analisado pela secretaria. Aguarda a validação.',
        'em_analise'           => 'A tua candidatura está em análise pela equipa técnica.',
        'aprovado'             => 'Parabéns! A tua candidatura foi aprovada. O documento será emitido em breve.',
        'documento_emitido'    => 'O teu documento foi emitido. Podes fazer o download abaixo.',
        'rejeitado'            => 'A tua candidatura foi rejeitada. Contacta a secretaria para mais informações.',
        'correcao_solicitada'  => 'Foi solicitada uma correcção à tua candidatura. Verifica os dados e resubmete.',
    ];

    $jaEnviouComprovativo     = $statusVal === 'aguarda_comprovativo';
    $podeSubmeterComprovativo = in_array($statusVal, ['nao_pago', 'aguarda_comprovativo']);

    /*
     * Valor monetário — vem exclusivamente do registo de pagamento na BD.
     * Se o pagamento ainda não existir (situação anómala), mostra '—' em vez
     * de um valor hardcoded incorreto.
     *
     * O controller deve garantir eager-load: Application::with('pagamento')->findOrFail($id)
     */
    $valorPagamento = $pedido->pagamento?->amount ?? null;
@endphp

<div class="status-wrap">
    <div class="row">
        <div class="col-md-9 mx-auto">

            @if(session('success'))
                <div class="alert-sucesso" role="alert">
                    <i class="fas fa-check-circle" aria-hidden="true"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-erro" role="alert">
                    <i class="fas fa-times-circle" aria-hidden="true"></i>
                    {{ session('error') }}
                </div>
            @endif

            <div class="ficha-cobranca">

                {{-- Cabeçalho --}}
                <div class="doc-header">
                    <div class="doc-logo-area">
                        <div class="doc-emblema">
                            <img src="{{ asset('images/logo.jpg') }}" alt="Logotipo ORDEPDITA">
                        </div>
                        <div class="doc-instituicao">
                            <h1>ORDEPDITA</h1>
                            <p>Ordem dos Profissionais de Diagnóstico e Terapêutica de Angola</p>
                        </div>
                    </div>
                    <div class="doc-ref">
                        <span><strong>Processo:</strong> {{ $pedido->process_number }}</span>
                        <span><strong>BI:</strong> {{ $pedido->bi_number }}</span>
                        <span>
                            <strong>Submetido:</strong>
                            {{ $pedido->submitted_at
                                ? $pedido->submitted_at->format('d/m/Y')
                                : $pedido->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>

                <div class="doc-corpo">

                    {{-- Dados Pessoais --}}
                    <div class="secao-titulo">Dados pessoais</div>
                    <div class="doc-grid doc-grid-2">
                        <div class="doc-campo">
                            <span class="doc-label">Nome completo</span>
                            <span class="doc-valor">{{ $pedido->full_name ?? '—' }}</span>
                        </div>
                        <div class="doc-campo">
                            <span class="doc-label">Nº do bilhete de identidade</span>
                            <span class="doc-valor">{{ $pedido->bi_number ?? '—' }}</span>
                        </div>
                        <div class="doc-campo">
                            <span class="doc-label">Email</span>
                            <span class="doc-valor">{{ $pedido->email ?? '—' }}</span>
                        </div>
                        <div class="doc-campo">
                            <span class="doc-label">Telefone</span>
                            <span class="doc-valor">{{ $pedido->phone ?? '—' }}</span>
                        </div>
                    </div>

                    {{-- Dados da Candidatura --}}
                    <div class="secao-titulo">Dados da candidatura</div>
                    <div class="doc-grid doc-grid-2">
                        <div class="doc-campo">
                            <span class="doc-label">Tipo de pedido</span>
                            <span class="doc-valor">
                                {{ ($pedido->document_type ?? '') === 'carteira'
                                    ? 'Carteira Profissional'
                                    : 'Licença Profissional' }}
                            </span>
                        </div>
                        <div class="doc-campo">
                            <span class="doc-label">Profissão</span>
                            <span class="doc-valor">{{ $pedido->profession ?? $pedido->specialty ?? '—' }}</span>
                        </div>
                        <div class="doc-campo">
                            <span class="doc-label">Habilitações</span>
                            <span class="doc-valor">{{ $pedido->qualification ?? '—' }}</span>
                        </div>
                        <div class="doc-campo">
                            <span class="doc-label">Província</span>
                            <span class="doc-valor">{{ $pedido->province ?? '—' }}</span>
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div class="secao-titulo">Estado da candidatura</div>

                    <div class="estado-linha">
                        <span class="status-badge {{ $statusBadgeClass[$statusVal] ?? 'badge-aguarda' }}">
                            <i class="fas fa-circle" style="font-size: 0.45rem;" aria-hidden="true"></i>
                            {{ $statusLabels[$statusVal] ?? '—' }}
                        </span>
                        <span class="estado-data">
                            Última actualização: {{ $pedido->updated_at->format('d/m/Y \à\s H:i') }}
                        </span>
                    </div>

                    {{-- Timeline --}}
                    <div class="timeline">
                        @foreach($passos as $i => $passo)
                            @php
                                if ($statusVal === 'rejeitado') {
                                    $stepClass = $i === 0 ? 'done' : 'erro';
                                } elseif ($i < $indiceActual) {
                                    $stepClass = 'done';
                                } elseif ($i === $indiceActual) {
                                    $stepClass = 'active';
                                } else {
                                    $stepClass = 'pending';
                                }
                            @endphp
                            <div class="step {{ $stepClass }}">
                                <div class="step-circle">
                                    @if($stepClass === 'done')
                                        <i class="fas fa-check" aria-hidden="true"></i>
                                    @elseif($stepClass === 'erro')
                                        <i class="fas fa-times" aria-hidden="true"></i>
                                    @else
                                        <i class="fas {{ $passo['icon'] }}" aria-hidden="true"></i>
                                    @endif
                                </div>
                                <div class="step-label">{{ $passo['label'] }}</div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Nota informativa --}}
                    <div class="nota {{ $statusVal === 'rejeitado' ? 'nota-erro' : '' }}" role="status">
                        <i class="fas {{ $statusVal === 'rejeitado' ? 'fa-times-circle' : 'fa-info-circle' }}"
                           aria-hidden="true"></i>
                        <span>{{ $notasMensagem[$statusVal] ?? '' }}</span>
                    </div>

                    {{--
                        Valor total — vem do registo de pagamento na BD ($pedido->pagamento->amount).
                        Se o registo de pagamento não existir (situação anómala), mostra '—'
                        em vez de um valor hardcoded errado.
                        O controller deve fazer eager-load: Application::with('pagamento')
                    --}}
                    <div class="valor-total">
                        <span class="valor-label">Total a pagar</span>
                        <span class="valor-montante">
                            @if($valorPagamento !== null)
                                {{ number_format($valorPagamento, 2, ',', '.') }} Kz
                            @else
                                <span style="color:#999; font-size:0.9em;">—</span>
                            @endif
                        </span>
                    </div>

                </div>

                {{-- Botões de acção --}}
                <div class="document-actions">
                    @if($podeSubmeterComprovativo)
                        <button type="button" class="btn-acao btn-pagar" id="btn-abrir-modal">
                            <i class="fas fa-upload" aria-hidden="true"></i> Submeter Comprovativo
                        </button>
                    @endif

                    @if(in_array($statusVal, ['aprovado', 'documento_emitido']))
                        <a href="#" class="btn-acao btn-download">
                            <i class="fas fa-download" aria-hidden="true"></i> Baixar Documento
                        </a>
                    @endif

                    <a href="{{ route('consulta.baixar-ficha-cobranca', $pedido->id) }}"
                       class="btn-acao btn-download">
                        <i class="fas fa-file-pdf" aria-hidden="true"></i> Baixar Ficha de Cobrança
                    </a>

                    <a href="{{ route('consulta.form') }}" class="btn-acao btn-voltar-inferior">
                        <i class="fas fa-arrow-left" aria-hidden="true"></i> Nova Consulta
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal: já enviou comprovativo anteriormente --}}
@if($jaEnviouComprovativo)
<div class="modal fade" id="modalSubstituir" tabindex="-1"
     aria-labelledby="modalSubstituirLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalSubstituirLabel">
                    <i class="fas fa-exclamation-triangle text-warning" aria-hidden="true"></i>
                    Comprovativo já enviado
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <p>Já submeteste um comprovativo de pagamento para este processo.</p>
                <p><strong>Desejas substituir o comprovativo anterior?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times" aria-hidden="true"></i> Cancelar
                </button>
                <a href="{{ route('pedido.form-upload', $pedido->id) }}" class="btn btn-warning">
                    <i class="fas fa-sync-alt" aria-hidden="true"></i> Sim, substituir
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
(function () {
    'use strict';

    const btnAbrirModal = document.getElementById('btn-abrir-modal');
    const jaEnviou      = {{ $jaEnviouComprovativo ? 'true' : 'false' }};

    if (btnAbrirModal) {
        btnAbrirModal.addEventListener('click', function () {
            if (jaEnviou) {
                const modalEl = document.getElementById('modalSubstituir');
                if (modalEl && typeof bootstrap !== 'undefined') {
                    new bootstrap.Modal(modalEl).show();
                }
            } else {
                window.location.href = '{{ route('pedido.form-upload', $pedido->id) }}';
            }
        });
    }

}());
</script>
@endpush

@endsection