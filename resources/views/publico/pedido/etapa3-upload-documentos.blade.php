@extends("layouts.app")

@section("title", "Pedido de " . ($tipoDocumento === "carteira" ? "Carteira Profissional" : "Licença Profissional") . " — Passo 3 de 4")

@section("content")

@push('styles')
    @vite('resources/css/pedidos/etapa3-upload-documentos.css')
@endpush

<div class="pedido-wrap">

    {{-- Cabeçalho --}}
    <div class="pedido-header">
        <div class="pedido-titulo">
            {{ $tipoDocumento === 'carteira' ? 'Pedido de Carteira Profissional' : 'Pedido de Licença Profissional' }}
        </div>
        <div class="pedido-subtitulo">
            Envie os documentos abaixo. Os documentos marcados com <span style="color:#ef4444;">*</span> são obrigatórios.
        </div>
    </div>

    {{-- Barra de progresso --}}
    <div class="progresso-wrap">
        <div class="prog-step feito">
            <div class="prog-circulo"><i class="fas fa-check" style="font-size:13px;"></i></div>
            <div class="prog-label">Dados Pessoais</div>
        </div>
        <div class="prog-step feito">
            <div class="prog-circulo"><i class="fas fa-check" style="font-size:13px;"></i></div>
            <div class="prog-label">Dados Profissionais</div>
        </div>
        <div class="prog-step ativo">
            <div class="prog-circulo">3</div>
            <div class="prog-label">Documentos</div>
        </div>
        <div class="prog-step">
            <div class="prog-circulo">4</div>
            <div class="prog-label">Revisão</div>
        </div>
    </div>

    {{-- Card principal --}}
    <div class="pedido-card">

        <div class="aviso-info">
            <i class="fas fa-info-circle" aria-hidden="true"></i>
            <div>
                <p><strong>Documentos obrigatórios:</strong> Bilhete de Identidade (B.I.) e Certificado de Habilitações.</p>
                <p>Todos os documentos obrigatórios devem ser enviados para avançar para a próxima etapa.</p>
            </div>
        </div>

        {{-- ── SECÇÃO 1: Documentos Obrigatórios ── --}}
        <div class="form-secao">
            <div class="form-secao-titulo">
                <i class="fas fa-folder-open" aria-hidden="true"></i>
                Documentos Obrigatórios
            </div>

            <div class="doc-grid">

                <div class="doc-card" id="card-bi">
                    <div class="doc-card-info">
                        <div class="doc-card-icon">
                            <i class="fas fa-id-card" aria-hidden="true"></i>
                        </div>
                        <div class="doc-card-texto">
                            <div class="doc-card-titulo">
                                Bilhete de Identidade (B.I.) <span class="campo-obrigatorio">*</span>
                            </div>
                            <div class="doc-card-formato">JPG, PNG ou PDF até 10MB</div>
                        </div>
                    </div>
                    <div class="doc-card-acoes">
                        <span id="status-bi" class="doc-status pendente">Pendente</span>
                        <button type="button" class="btn-doc btn-doc-primario" onclick="abrirUpload('bi')">
                            <i class="fas fa-upload" aria-hidden="true"></i> Enviar
                        </button>
                    </div>
                </div>

                <div class="doc-card" id="card-certificado_habilitacoes">
                    <div class="doc-card-info">
                        <div class="doc-card-icon">
                            <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                        </div>
                        <div class="doc-card-texto">
                            <div class="doc-card-titulo">
                                Certificado de Habilitações <span class="campo-obrigatorio">*</span>
                            </div>
                            <div class="doc-card-formato">PDF até 10MB</div>
                        </div>
                    </div>
                    <div class="doc-card-acoes">
                        <span id="status-certificado_habilitacoes" class="doc-status pendente">Pendente</span>
                        <button type="button" class="btn-doc btn-doc-primario"
                                onclick="abrirUpload('certificado_habilitacoes')">
                            <i class="fas fa-upload" aria-hidden="true"></i> Enviar
                        </button>
                    </div>
                </div>

            </div>
        </div>

        {{-- ── SECÇÃO 2: Documentos Opcionais ── --}}
        <div class="form-secao">
            <div class="form-secao-titulo">
                <i class="fas fa-paperclip" aria-hidden="true"></i>
                Documentos Opcionais
                <span class="form-secao-sub">— pode adicionar documentos complementares</span>
            </div>

            <div class="doc-card" id="card-outro">
                <div class="doc-card-info">
                    <div class="doc-card-icon">
                        <i class="fas fa-file-circle-plus" aria-hidden="true"></i>
                    </div>
                    <div class="doc-card-texto">
                        <div class="doc-card-titulo">Outros Documentos</div>
                        <div class="doc-card-formato">JPG, PNG ou PDF até 10MB</div>
                    </div>
                </div>
                <div class="doc-card-acoes">
                    <button type="button" class="btn-doc" onclick="abrirUpload('outro')">
                        <i class="fas fa-plus" aria-hidden="true"></i> Adicionar
                    </button>
                </div>
            </div>

            <div id="outros-documentos-lista" class="outros-lista"></div>
        </div>

        {{-- Rodapé --}}
        <div class="pedido-rodape">
            <a href="{{ route('pedido.dados-profissionais', ['tipo' => $tipoDocumento]) }}"
               class="btn-cancelar-pedido">
                <i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar
            </a>
            <button type="button"
                    class="btn-proximo desabilitado"
                    id="btnProximo"
                    onclick="irParaRevisao()"
                    disabled
                    aria-disabled="true">
                Próximo: Ficha de Cobrança <i class="fas fa-arrow-right" aria-hidden="true"></i>
            </button>
        </div>

    </div>
</div>

{{-- ── MODAL DE UPLOAD ── --}}
<div class="modal-overlay"
     id="uploadModal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="uploadModalTitulo"
     style="display:none;">
    <div class="modal-box">
        <div class="modal-cabecalho">
            <div class="modal-titulo" id="uploadModalTitulo">Enviar Documento</div>
            <button type="button" class="modal-fechar" onclick="fecharUpload()"
                    aria-label="Fechar modal">&times;</button>
        </div>
        <div class="modal-corpo">
            <form id="uploadForm" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="tipo_documento_upload" id="tipo_documento_upload">
                <label class="campo-label" for="arquivo">Selecione o ficheiro</label>
                <input type="file" id="arquivo" name="arquivo" required
                       accept=".jpg,.jpeg,.png,.pdf">
                <div class="modal-ajuda">
                    Formatos permitidos: JPG, PNG ou PDF. Tamanho máximo: 10MB.
                </div>
                <div id="uploadProgress" class="modal-progresso" aria-hidden="true">
                    <div class="modal-progresso-barra"></div>
                </div>
            </form>
        </div>
        <div class="modal-rodape">
            <button type="button" class="btn-doc" onclick="fecharUpload()">Cancelar</button>
            <button type="button" class="btn-doc btn-doc-primario" id="btnEnviarDoc"
                    onclick="enviarDocumento()">
                <i class="fas fa-upload" aria-hidden="true"></i> Enviar
            </button>
        </div>
    </div>
</div>

@endsection

@push("scripts")
<script>
(function () {
    'use strict';

    // ── Estado local ──────────────────────────────────────────────────────────
    // JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP — escapa todos os
    // caracteres especiais HTML, eliminando qualquer vector XSS nos nomes de ficheiro.
    const documentosEnviados = JSON.parse('{{ json_encode(
        array_map(fn($v) => ['nome_original' => $v['nome_original'] ?? ''],
        $documentosEnviados),
        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
    ) }}');

    let tipoDocumentoAtual = '';

    // ── Inicialização ─────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {
        Object.keys(documentosEnviados).forEach(function (tipo) {
            atualizarStatusDocumento(tipo, true);
        });
        verificarDocumentos();
    });

    // ── Modal ─────────────────────────────────────────────────────────────────
    window.abrirUpload = function (tipo) {
        tipoDocumentoAtual = tipo;
        document.getElementById('tipo_documento_upload').value = tipo;
        document.getElementById('arquivo').value               = '';
        document.getElementById('uploadModal').style.display   = 'flex';
        document.getElementById('uploadModal').removeAttribute('aria-hidden');
    };

    window.fecharUpload = function () {
        document.getElementById('uploadModal').style.display = 'none';
        document.getElementById('uploadModal').setAttribute('aria-hidden', 'true');
    };

    // Fechar ao clicar fora da caixa
    document.getElementById('uploadModal').addEventListener('click', function (e) {
        if (e.target === this) fecharUpload();
    });

    // Fechar com Escape
    document.addEventListener('keydown', function (e) {
        const modal = document.getElementById('uploadModal');
        if (e.key === 'Escape' && modal.style.display === 'flex') fecharUpload();
    });

    // ── Envio de documento ────────────────────────────────────────────────────
    window.enviarDocumento = function () {
        const arquivo  = document.getElementById('arquivo').files[0];
        const btnEnviar = document.getElementById('btnEnviarDoc');

        if (!arquivo) {
            alert('Selecione um ficheiro antes de enviar.');
            return;
        }

        // Validação client-side (o servidor também valida — esta é apenas UX)
        const tiposPermitidos = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!tiposPermitidos.includes(arquivo.type)) {
            alert('Formato inválido. Use apenas JPG, PNG ou PDF.');
            return;
        }
        if (arquivo.size > 10 * 1024 * 1024) {
            alert('O ficheiro excede o limite de 10MB.');
            return;
        }

        const progress  = document.getElementById('uploadProgress');
        const form      = document.getElementById('uploadForm');
        const formData  = new FormData(form);

        progress.classList.add('ativo');
        btnEnviar.disabled = true;

        fetch('{{ route('pedido.etapa3.salvar') }}', {
            method:  'POST',
            body:    formData,
            headers: {
                'X-CSRF-TOKEN':    document.querySelector('meta[name=csrf-token]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
        })
        .then(function (resp) {
            if (!resp.ok) {
                return resp.json().then(function (err) {
                    throw new Error(err.error || 'Erro no servidor.');
                });
            }
            return resp.json();
        })
        .then(function (data) {
            if (data.success) {
                documentosEnviados[tipoDocumentoAtual] = true;
                atualizarStatusDocumento(tipoDocumentoAtual, true);
                verificarDocumentos();
                fecharUpload();
            } else {
                alert('Erro: ' + (data.error || 'Resposta inesperada.'));
            }
        })
        .catch(function (err) {
            console.error('[etapa3] Erro ao enviar documento:', err);
            alert('Não foi possível enviar o documento. Por favor tente novamente.');
        })
        .finally(function () {
            progress.classList.remove('ativo');
            btnEnviar.disabled = false;
        });
    };

    // ── Remover documento ─────────────────────────────────────────────────────
    window.removerDocumento = function (tipo) {
        if (!confirm('Deseja remover este documento?')) return;

        fetch('{{ route('pedido.etapa3.remover') }}', {
            method:  'DELETE',
            headers: {
                'Content-Type':    'application/json',
                'X-CSRF-TOKEN':    document.querySelector('meta[name=csrf-token]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({ tipo: tipo }),
        })
        .then(function (resp) {
            if (!resp.ok) throw new Error('Erro ao remover.');
            return resp.json();
        })
        .then(function (data) {
            if (data.success) {
                delete documentosEnviados[tipo];
                atualizarStatusDocumento(tipo, false);
                verificarDocumentos();
            }
        })
        .catch(function (err) {
            console.error('[etapa3] Erro ao remover documento:', err);
            alert('Não foi possível remover o documento. Por favor tente novamente.');
        });
    };

    // ── Estado visual dos cartões ─────────────────────────────────────────────
    function atualizarStatusDocumento(tipo, enviado) {
        const statusEl = document.getElementById('status-' + tipo);
        const card     = document.getElementById('card-' + tipo);

        if (!statusEl) return;

        if (enviado) {
            statusEl.innerHTML  = '<i class="fas fa-check-circle" aria-hidden="true"></i> Enviado';
            statusEl.className  = 'doc-status enviado';
            if (card) card.classList.add('enviado');
        } else {
            statusEl.textContent = 'Pendente';
            statusEl.className   = 'doc-status pendente';
            if (card) card.classList.remove('enviado');
        }
    }

    // ── Validar se pode avançar ───────────────────────────────────────────────
    function verificarDocumentos() {
        const obrigatorios  = ['bi', 'certificado_habilitacoes'];
        const todosEnviados = obrigatorios.every(function (tipo) {
            return !!documentosEnviados[tipo];
        });

        const btn = document.getElementById('btnProximo');
        btn.disabled = !todosEnviados;
        btn.setAttribute('aria-disabled', String(!todosEnviados));
        btn.classList.toggle('desabilitado', !todosEnviados);
    }

    window.irParaRevisao = function () {
        window.location.href = '{{ route('pedido.ficha-cobranca', ['tipo' => $tipoDocumento]) }}';
    };

}());
</script>
@endpush