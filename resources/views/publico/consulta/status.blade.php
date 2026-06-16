@extends("layouts.app")

@section("title", "Estado do Pedido - " . ($pedido->process_number ?? ''))

@push('styles')
@vite('resources/css/consulta/status.css')
@endpush

@section("content")
<div class="status-wrap">
    <!-- Documento / Ficha de Cobrança -->
    <div class="ficha-cobranca">
        
        <!-- Cabeçalho do Documento -->
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
            <div class="doc-titulo-area">
                <h2>NOTA DE COBRANÇA</h2>
                <div class="doc-ref">
                    <span><strong>Processo:</strong> {{ $pedido->process_number }}</span>
                    <span><strong>Data:</strong> {{ $pedido->submitted_at ? $pedido->submitted_at->format('d/m/Y') : now()->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <div class="doc-corpo">
            <!-- Dados Pessoais -->
            <div class="doc-secao">
                <div class="doc-secao-titulo">
                    Dados Pessoais
                </div>
                <div class="doc-grid doc-grid-2">
                    <div class="doc-campo">
                        <span class="doc-label">Nome Completo</span>
                        <span class="doc-valor">{{ $pedido->full_name ?? '---' }}</span>
                    </div>
                    <div class="doc-campo">
                        <span class="doc-label">Nº do Bilhete</span>
                        <span class="doc-valor">{{ $pedido->bi_number ?? '---' }}</span>
                    </div>
                    <div class="doc-campo">
                        <span class="doc-label">Email</span>
                        <span class="doc-valor">{{ $pedido->email ?? '---' }}</span>
                    </div>
                    <div class="doc-campo">
                        <span class="doc-label">Telefone</span>
                        <span class="doc-valor">{{ $pedido->phone ?? '---' }}</span>
                    </div>
                </div>
            </div>

            <!-- Dados da Candidatura -->
            <div class="doc-secao">
                <div class="doc-secao-titulo">
                     Dados da Candidatura
                </div>
                <div class="doc-grid doc-grid-2">
                    <div class="doc-campo">
                        <span class="doc-label">Profissão</span>
                        <span class="doc-valor">{{ $pedido->profession ?? $pedido->specialty ?? '---' }}</span>
                    </div>
                    <div class="doc-campo">
                        <span class="doc-label">Habilitações</span>
                        <span class="doc-valor">{{ $pedido->qualification ?? '---' }}</span>
                    </div>
                    <div class="doc-campo">
                        <span class="doc-label">Curso</span>
                        <span class="doc-valor">{{ $pedido->course ?? '---' }}</span>
                    </div>
                    <div class="doc-campo">
                        <span class="doc-label">Província</span>
                        <span class="doc-valor">{{ $pedido->province ?? '---' }}</span>
                    </div>
                </div>
            </div>

            <!-- Valor e Estado -->
            <div class="doc-secao destaque-financeiro">
                <div class="doc-grid doc-grid-2 align-center">
                    <div class="doc-campo">
                        <span class="doc-label">Estado do Processo</span>
                        <span class="doc-status-badge {{ $pedido->status }}">
                            @php
                                $statusLabels = [
    "nao_pago"             => "Aguardando Pagamento",
    "aguarda_comprovativo" => "Comprovativo Pendente",  // ADICIONADO
    "pagamento_confirmado" => "Pagamento Confirmado",
    "em_analise"           => "Em Análise",
    "aprovado"             => "Aprovado",
    "documento_emitido"    => "Documento Emitido",
    "rejeitado"            => "Rejeitado",
    "correcao_solicitada"  => "Correcção Solicitada",
];
                                echo $statusLabels[$pedido->status instanceof \UnitEnum ? $pedido->status->value : (string) $pedido->status] ?? '---';
                            @endphp
                        </span>
                    </div>
                    <div class="doc-campo text-right">
                        <span class="doc-label">Total a Pagar</span>
                        <span class="doc-valor-grande">{{ number_format($pedido->amount ?? 25000, 2, ',', '.') }} Kz</span>
                    </div>
                </div>
            </div>

            <!-- Instruções / Observações -->
            <div class="doc-secao">
                <div class="doc-secao-titulo">
                    <i class="fas fa-exclamation-triangle"></i> Observações
                </div>
                <div class="doc-notas">
                    <ol>
                        <li>A emissão do certificado só será realizada após a confirmação do pagamento.</li>
                        <li>O pagamento deve ser feito obrigatoriamente através da conta bancária indicada abaixo.</li>
                        <li>Após o pagamento, envie o comprovativo através do sistema para validação.</li>
                        <li>Guarde esta nota de cobrança como comprovativo da sua candidatura.</li>
                        <li>Para qualquer esclarecimento, contacte a secretaria da ORDEPDITA.</li>
                    </ol>
                </div>
            </div>

            <!-- Dados Bancários -->
            <div class="doc-secao destaque-bancario">
                <div class="doc-secao-titulo">
                    <i class="fas fa-university"></i> Dados para Pagamento (Transferência Bancária)
                </div>
                <div class="doc-grid doc-grid-2">
                    <div class="doc-campo">
                        <span class="doc-label">Banco</span>
                        <span class="doc-valor">Banco de Fomento de Angola (BFA)</span>
                    </div>
                    <div class="doc-campo">
                        <span class="doc-label">IBAN</span>
                        <span class="doc-valor iban">AO06 0044 0000 0123 4567 8901</span>
                    </div>
                    <div class="doc-campo">
                        <span class="doc-label">Beneficiário</span>
                        <span class="doc-valor">ORDEPDITA</span>
                    </div>
                    <div class="doc-campo">
                        <span class="doc-label">NIF</span>
                        <span class="doc-valor">5417256890</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rodapé do Documento -->
        <div class="doc-rodape">
            <p>Documento gerado automaticamente pelo sistema da ORDEPDITA.</p>
            <p>Luanda - Angola | www.ordepdita.ao</p>
        </div>
    </div>

    <!-- Botões de Ação - Todos na parte inferior -->
    @php
    $status = $pedido->status instanceof \UnitEnum 
        ? $pedido->status->value 
        : (string) $pedido->status;
@endphp

<div class="document-actions">
    <a href="{{ route('consulta.form') }}" class="btn-acao btn-voltar-inferior">
        <i class="fas fa-arrow-left"></i> Nova Consulta
    </a>

    @if(in_array($status, ['nao_pago', 'aguarda_comprovativo']))
        <a href="{{ route('pedido.form-upload', $pedido->reference_uuid) }}" class="btn-acao btn-pagar">
            <i class="fas fa-upload"></i> Submeter Comprovativo
        </a>
    @endif

    <a href="{{ route('consulta.baixar-ficha-cobranca', $pedido->reference_uuid) }}" class="btn-acao btn-download">
        <i class="fas fa-file-pdf"></i> Baixar PDF
    </a>
</div>
</div>
@endsection