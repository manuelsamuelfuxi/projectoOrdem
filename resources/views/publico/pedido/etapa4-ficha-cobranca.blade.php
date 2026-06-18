@extends("layouts.app")

@section("title", "Pedido de " . ($tipoDocumento === "carteira" ? "Carteira Profissional" : "Licença Profissional") . " — Passo 4 de 4")

@section("content")

@push('styles')
    @vite('resources/css/pedidos/etapa4-ficha-cobranca.css')
@endpush

@php
    /**
     * Todos os valores sensíveis (valor, dados bancários) vêm do controller via BD.
     * Nunca calcular ou definir valores monetários na view.
     *
     * Variáveis esperadas do controller:
     *   - $dadosCandidato   : array (sanitizado no controller)
     *   - $dadosProfissionais : array (sanitizado no controller)
     *   - $documentosEnviados : array
     *   - $tipoDocumento     : string ('carteira'|'licenca')
     *   - $configuracaoPagamento : array com 'valor', 'banco', 'iban', 'beneficiario', 'nif'
     *   - $fotoUrl           : string|null — URL segura gerada no controller via Storage::url()
     */

    $tiposLabels = [
        'bi'                       => 'Bilhete de Identidade',
        'certificado_habilitacoes' => 'Certificado de Habilitações',
        'outro'                    => 'Outro Documento',
    ];

    $labelDocumento = $tipoDocumento === 'carteira'
        ? 'Carteira Profissional'
        : 'Licença Profissional';
@endphp

<div class="pedido-wrap">

    {{-- ── Cabeçalho ── --}}
    <div class="pedido-header">
        <div class="pedido-titulo">Pedido de {{ $labelDocumento }}</div>
        <div class="pedido-subtitulo">Reveja o resumo da sua candidatura antes de submeter.</div>
    </div>

    {{-- ── Barra de progresso ── --}}
    <div class="progresso-wrap">
        @foreach([1 => 'Dados Pessoais', 2 => 'Dados Profissionais', 3 => 'Documentos', 4 => 'Revisão'] as $num => $label)
            <div class="prog-step {{ $num < 4 ? 'feito' : 'ativo' }}">
                <div class="prog-circulo">
                    @if($num < 4)
                        <i class="fas fa-check" style="font-size:13px;"></i>
                    @else
                        {{ $num }}
                    @endif
                </div>
                <div class="prog-label">{{ $label }}</div>
            </div>
        @endforeach
    </div>

    {{-- ── Card principal ── --}}
    <div class="pedido-card">

        {{-- ── Topo: foto + dados chave ── --}}
        <div class="pedido-preview-topo">

            <div class="foto-passe-wrap">
                {{--
                    $fotoUrl é gerado no controller:
                        $fotoUrl = $fotoPath ? Storage::disk('public')->url($fotoPath) : null;
                    Nunca gerar URLs de storage na view — o controller valida existência do ficheiro.
                --}}
                @if(!empty($fotoUrl))
                    <img
                        src="{{ $fotoUrl }}"
                        alt="Foto de identificação do candidato"
                        class="foto-passe"
                        loading="lazy"
                        onerror="this.style.display='none';
                                 document.getElementById('foto-fallback').style.display='flex';"
                    >
                    <div class="foto-passe-placeholder" id="foto-fallback" style="display:none;"
                         aria-label="Foto não disponível">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <span>Sem foto</span>
                    </div>
                @else
                    <div class="foto-passe-placeholder" aria-label="Foto não carregada">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        <span>Sem foto</span>
                    </div>
                @endif
            </div>

            <div class="preview-info">
                <div class="preview-nome">{{ $dadosCandidato['nome_completo'] }}</div>
                <div class="preview-linha"></div>
                <div class="preview-campos">
                    @php
                        $camposPreview = [
                            'Nº do B.I.'          => $dadosCandidato['numero_bi'],
                            'Género'              => ucfirst($dadosCandidato['genero']),
                            'Data de Nascimento'  => \Carbon\Carbon::parse($dadosCandidato['data_nascimento'])->format('d/m/Y'),
                            'Nacionalidade'       => $dadosCandidato['nacionalidade'],
                            'Tipo de Documento'   => $labelDocumento,
                            'Valor a Pagar'       => number_format($configuracaoPagamento['valor'], 2, ',', '.') . ' Kz',
                        ];
                    @endphp
                    @foreach($camposPreview as $label => $valor)
                        <div class="preview-campo">
                            <div class="preview-campo-label">{{ $label }}</div>
                            <div class="preview-campo-valor">{{ $valor }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ── Aviso de confirmação ── --}}
        <div class="aviso-confirmacao" role="alert">
            <i class="fas fa-check-circle" aria-hidden="true"></i>
            <div>
                Verifique todos os dados abaixo antes de confirmar a submissão.
                Após submeter, não será possível alterar as informações.
            </div>
        </div>

        {{-- ── SECÇÃO 1: Dados Pessoais ── --}}
        <div class="form-secao">
            <div class="form-secao-titulo">
                <i class="fas fa-user" aria-hidden="true"></i>
                Dados Pessoais
            </div>
            <div class="resumo-grid">
                <div class="resumo-item span-2">
                    <div class="resumo-label">Nome Completo</div>
                    <div class="resumo-valor">{{ $dadosCandidato['nome_completo'] }}</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Número do B.I.</div>
                    <div class="resumo-valor">{{ $dadosCandidato['numero_bi'] }}</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Género</div>
                    <div class="resumo-valor">{{ ucfirst($dadosCandidato['genero']) }}</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Data de Nascimento</div>
                    <div class="resumo-valor">
                        {{ \Carbon\Carbon::parse($dadosCandidato['data_nascimento'])->format('d/m/Y') }}
                    </div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Nacionalidade</div>
                    <div class="resumo-valor">{{ $dadosCandidato['nacionalidade'] }}</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Email</div>
                    <div class="resumo-valor">{{ $dadosCandidato['email'] }}</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Telefone Principal</div>
                    <div class="resumo-valor">{{ $dadosCandidato['telefone'] }}</div>
                </div>
                @if(!empty($dadosCandidato['telefone_alternativo']))
                    <div class="resumo-item">
                        <div class="resumo-label">Telefone Alternativo</div>
                        <div class="resumo-valor">{{ $dadosCandidato['telefone_alternativo'] }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ── SECÇÃO 2: Morada ── --}}
        <div class="form-secao">
            <div class="form-secao-titulo">
                <i class="fas fa-map-marker-alt" aria-hidden="true"></i>
                Endereço de Morada
            </div>
            <div class="resumo-grid">
                <div class="resumo-item">
                    <div class="resumo-label">Província</div>
                    <div class="resumo-valor">{{ $dadosCandidato['provincia_nome'] ?? '—' }}</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Município</div>
                    <div class="resumo-valor">{{ $dadosCandidato['municipio_nome'] ?? '—' }}</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Bairro</div>
                    <div class="resumo-valor">{{ $dadosCandidato['bairro'] ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- ── SECÇÃO 3: Dados Académicos ── --}}
        <div class="form-secao">
            <div class="form-secao-titulo">
                <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                Dados Académicos
            </div>
            <div class="resumo-grid">
                <div class="resumo-item span-2">
                    <div class="resumo-label">Escola / Universidade</div>
                    <div class="resumo-valor">{{ $dadosProfissionais['instituicao_formacao'] ?? '—' }}</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Curso</div>
                    <div class="resumo-valor">{{ $dadosProfissionais['curso_nome'] ?? '—' }}</div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Nível Académico</div>
                    <div class="resumo-valor">
                        @php
                            $nivelLabel = match($dadosProfissionais['nivel'] ?? '') {
                                'medio'    => 'Ensino Médio',
                                'superior' => 'Superior',
                                'outro'    => 'Outro',
                                default    => '—',
                            };
                        @endphp
                        {{ $nivelLabel }}
                    </div>
                </div>
                <div class="resumo-item">
                    <div class="resumo-label">Classe / Ano</div>
                    <div class="resumo-valor">{{ $dadosProfissionais['classe_label'] ?? '—' }}</div>
                </div>
            </div>
        </div>

        {{-- ── SECÇÃO 4: Dados Profissionais (opcional) ── --}}
        @if(!empty($dadosProfissionais['nome_instituicao']) || !empty($dadosProfissionais['funcao_nome']))
            <div class="form-secao">
                <div class="form-secao-titulo">
                    <i class="fas fa-briefcase" aria-hidden="true"></i>
                    Dados Profissionais
                </div>
                <div class="resumo-grid">
                    @if(!empty($dadosProfissionais['nome_instituicao']))
                        <div class="resumo-item span-2">
                            <div class="resumo-label">Instituição de Trabalho</div>
                            <div class="resumo-valor">{{ $dadosProfissionais['nome_instituicao'] }}</div>
                        </div>
                    @endif
                    @if(!empty($dadosProfissionais['funcao_nome']))
                        <div class="resumo-item">
                            <div class="resumo-label">Função que Ocupa</div>
                            <div class="resumo-valor">{{ $dadosProfissionais['funcao_nome'] }}</div>
                        </div>
                    @endif
                    @if(!empty($dadosProfissionais['sector']))
                        <div class="resumo-item">
                            <div class="resumo-label">Sector</div>
                            <div class="resumo-valor">{{ ucfirst($dadosProfissionais['sector']) }}</div>
                        </div>
                    @endif
                    @if(!empty($dadosProfissionais['provincia_trabalho_nome']))
                        <div class="resumo-item">
                            <div class="resumo-label">Província</div>
                            <div class="resumo-valor">{{ $dadosProfissionais['provincia_trabalho_nome'] }}</div>
                        </div>
                    @endif
                    @if(!empty($dadosProfissionais['municipio_trabalho_nome']))
                        <div class="resumo-item">
                            <div class="resumo-label">Município</div>
                            <div class="resumo-valor">{{ $dadosProfissionais['municipio_trabalho_nome'] }}</div>
                        </div>
                    @endif
                    @if(!empty($dadosProfissionais['telefone_trabalho']))
                        <div class="resumo-item">
                            <div class="resumo-label">Telefone</div>
                            <div class="resumo-valor">{{ $dadosProfissionais['telefone_trabalho'] }}</div>
                        </div>
                    @endif
                    @if(!empty($dadosProfissionais['email_trabalho']))
                        <div class="resumo-item">
                            <div class="resumo-label">E-mail</div>
                            <div class="resumo-valor">{{ $dadosProfissionais['email_trabalho'] }}</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- ── SECÇÃO 5: Documentos Carregados ── --}}
        <div class="form-secao">
            <div class="form-secao-titulo">
                <i class="fas fa-folder-open" aria-hidden="true"></i>
                Documentos Carregados
            </div>
            <div class="docs-lista">
                @forelse($documentosEnviados as $tipo => $info)
                    <div class="doc-item">
                        <div class="doc-item-icon">
                            <i class="fas fa-file-check" aria-hidden="true"></i>
                        </div>
                        <div class="doc-item-info">
                            <div class="doc-item-titulo">
                                {{ $tiposLabels[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo)) }}
                            </div>
                            <div class="doc-item-nome">{{ $info['nome_original'] ?? '' }}</div>
                        </div>
                    </div>
                @empty
                    <p class="resumo-valor">Nenhum documento carregado.</p>
                @endforelse
            </div>
        </div>

        {{-- ── SECÇÃO 6: Informação de Pagamento ── --}}
        {{--
            SEGURANÇA: $configuracaoPagamento vem exclusivamente da BD via controller.
            Nenhum valor monetário ou dado bancário é definido na view.
            O controller re-valida o valor no momento da submissão (nunca confia no front).
        --}}
        <div class="form-secao">
            <div class="form-secao-titulo">
                <i class="fas fa-university" aria-hidden="true"></i>
                Informação de Pagamento
            </div>

            <div class="pagamento-box">
                <div class="pagamento-item">
                    <div class="resumo-label">Documento Solicitado</div>
                    <div class="resumo-valor">{{ $labelDocumento }}</div>
                </div>
                <div class="pagamento-item destaque">
                    <div class="resumo-label">Total a Pagar</div>
                    <div class="pagamento-valor">
                        {{ number_format($configuracaoPagamento['valor'], 2, ',', '.') }} Kz
                    </div>
                </div>
            </div>

            <div class="dados-bancarios">
                <div class="dados-bancarios-titulo">
                    <i class="fas fa-landmark" aria-hidden="true"></i>
                    Dados Bancários para Transferência
                </div>
                <div class="dados-bancarios-grid">
                    <div class="dados-bancarios-item">
                        <div class="dados-bancarios-label">Banco</div>
                        <div class="dados-bancarios-valor">{{ $configuracaoPagamento['banco'] }}</div>
                    </div>
                    <div class="dados-bancarios-item">
                        <div class="dados-bancarios-label">IBAN</div>
                        <div class="dados-bancarios-valor">{{ $configuracaoPagamento['iban'] }}</div>
                    </div>
                    <div class="dados-bancarios-item span-2">
                        <div class="dados-bancarios-label">Beneficiário</div>
                        <div class="dados-bancarios-valor">{{ $configuracaoPagamento['beneficiario'] }}</div>
                    </div>
                    <div class="dados-bancarios-item">
                        <div class="dados-bancarios-label">NIF</div>
                        <div class="dados-bancarios-valor">{{ $configuracaoPagamento['nif'] }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Rodapé de navegação ── --}}
        <div class="pedido-rodape">
            <a href="{{ route('pedido.upload-documentos', ['tipo' => $tipoDocumento]) }}"
               class="btn-cancelar-pedido">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar
            </a>
            <button type="button"
                    class="btn-proximo"
                    onclick="abrirModal()"
                    aria-haspopup="dialog">
                <i class="fas fa-check-circle" aria-hidden="true"></i> Confirmar e Submeter
            </button>
        </div>

    </div>{{-- /pedido-card --}}
</div>{{-- /pedido-wrap --}}


{{-- ── MODAL DE CONFIRMAÇÃO ── --}}
<div class="modal-overlay"
     id="modalConfirmacao"
     role="dialog"
     aria-modal="true"
     aria-labelledby="modalTitulo"
     aria-describedby="modalDescricao"
     style="display:none;">
    <div class="modal-box">

        <div class="modal-cabecalho">
            <div class="modal-cabecalho-icon">
                <i class="fas fa-paper-plane" aria-hidden="true"></i>
            </div>
            <div>
                <div class="modal-titulo" id="modalTitulo">Confirmar Submissão</div>
                <div class="modal-subtitulo">Esta acção não pode ser revertida</div>
            </div>
        </div>

        <div class="modal-corpo">
            <p class="modal-texto" id="modalDescricao">
                Está prestes a submeter o seu pedido de
                <strong>{{ $labelDocumento }}</strong>.
                Após a submissão, receberá as instruções de pagamento e poderá
                acompanhar o estado do seu pedido no portal.
            </p>

            <div class="modal-declaracao">
                <input type="checkbox"
                       id="chkDeclaracao"
                       onchange="toggleBtnConfirmar(this)">
                <label for="chkDeclaracao">
                    Declaro que todas as informações fornecidas são verdadeiras e
                    estou ciente das implicações legais em caso de declarações falsas.
                </label>
            </div>

            <div class="modal-aviso" role="alert">
                <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                <div>
                    O pedido só será processado após a confirmação do pagamento.
                    Envie o comprovativo através do portal após efectuar a transferência.
                </div>
            </div>
        </div>

        <div class="modal-rodape">
            <button type="button"
                    class="btn-modal-cancelar"
                    onclick="fecharModal()"
                    aria-label="Cancelar e fechar modal">
                <i class="fas fa-times" aria-hidden="true"></i> Cancelar
            </button>

            {{--
                SEGURANÇA:
                - @csrf : token Laravel no form (mitigação CSRF)
                - O controller re-verifica o valor e dados bancários da BD — nunca do front
                - O controller verifica se o pedido já foi submetido (idempotência)
                - O botão fica desactivado até ao checkbox e é re-desactivado após submit (JS)
            --}}
            <form method="POST"
                  action="{{ route('pedido.submeter') }}"
                  id="formSubmeter"
                  novalidate>
                @csrf
                <button type="submit"
                        class="btn-modal-confirmar"
                        id="btnConfirmar"
                        disabled
                        aria-disabled="true">
                    <i class="fas fa-check" aria-hidden="true"></i> Submeter Pedido
                </button>
            </form>
        </div>

    </div>{{-- /modal-box --}}
</div>{{-- /modal-overlay --}}

@endsection


@push("scripts")
<script>
(function () {
    'use strict';

    const overlay  = document.getElementById('modalConfirmacao');
    const chk      = document.getElementById('chkDeclaracao');
    const btnConf  = document.getElementById('btnConfirmar');
    const form     = document.getElementById('formSubmeter');

    if (!overlay || !chk || !btnConf || !form) {
        console.error('[etapa4] Elementos do modal não encontrados. Verifique os IDs.');
        return;
    }

    /** Abre o modal e redefine estado */
    window.abrirModal = function () {
        chk.checked        = false;
        btnConf.disabled   = true;
        btnConf.setAttribute('aria-disabled', 'true');
        overlay.style.display = 'flex';
        overlay.removeAttribute('aria-hidden');
        // Foco acessível no modal
        btnConf.closest('.modal-box')?.querySelector('button, [href], input')?.focus();
    };

    /** Fecha o modal */
    window.fecharModal = function () {
        overlay.style.display = 'none';
        overlay.setAttribute('aria-hidden', 'true');
    };

    /** Activa/desactiva botão conforme checkbox */
    window.toggleBtnConfirmar = function (el) {
        btnConf.disabled = !el.checked;
        btnConf.setAttribute('aria-disabled', String(!el.checked));
    };

    /** Fechar ao clicar fora da caixa (no overlay) */
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) fecharModal();
    });

    /** Fechar com tecla Escape */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.style.display === 'flex') {
            fecharModal();
        }
    });

    /** Prevenir duplo submit — protecção no front (o controller também rejeita duplicados) */
    let _submitted = false;
    form.addEventListener('submit', function (e) {
        if (_submitted) {
            e.preventDefault();
            return;
        }
        // Validação final: checkbox deve estar marcado
        if (!chk.checked) {
            e.preventDefault();
            chk.focus();
            return;
        }
        _submitted     = true;
        btnConf.disabled = true;
        btnConf.innerHTML = '<i class="fas fa-spinner fa-spin" aria-hidden="true"></i> A submeter…';
    });

}());
</script>
@endpush