@extends("layouts.app")

@section("title", "Pedido de " . ($tipoDocumento === "carteira" ? "Carteira Profissional" : "Licença Profissional") . " — Passo 3 de 4")

@section("content")

@push('styles')
    @vite('resources/css/pedidos/etapa3-upload-documentos.css')
@endpush

<div class="pedido-wrap">

    {{-- Cabeçalho --}}
    <div class="pedido-header">
        <div class="pedido-titulo">{{ $tipoDocumento === 'carteira' ? 'Pedido de Carteira Profissional' : 'Pedido de Licença Profissional' }}</div>
        <div class="pedido-subtitulo">Envie os documentos abaixo. Os documentos marcados com <span style="color:#ef4444;">*</span> são obrigatórios.</div>
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

        {{-- Aviso informativo --}}
        <div class="aviso-info">
            <i class="fas fa-info-circle"></i>
            <div>
                <p><strong>Documentos obrigatórios:</strong> Bilhete de Identidade (B.I.) e Certificado de Habilitações. Os restantes documentos são opcionais e podem complementar o seu pedido.</p>
                <p>Todos os documentos obrigatórios devem ser enviados para avançar para a próxima etapa.</p>
            </div>
        </div>

        {{-- ── SECÇÃO 1: Documentos Obrigatórios ── --}}
        <div class="form-secao">
            <div class="form-secao-titulo">
                <i class="fas fa-folder-open"></i>
                Documentos Obrigatórios
            </div>

            <div class="doc-grid">

                {{-- BI --}}
                <div class="doc-card" id="card-bi">
                    <div class="doc-card-info">
                        <div class="doc-card-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="doc-card-texto">
                            <div class="doc-card-titulo">Bilhete de Identidade (B.I.) <span class="campo-obrigatorio">*</span></div>
                            <div class="doc-card-formato">JPG, PNG ou PDF até 10MB</div>
                        </div>
                    </div>
                    <div class="doc-card-acoes">
                        <span id="status-bi" class="doc-status pendente">Pendente</span>
                        <button type="button" class="btn-doc btn-doc-primario" onclick="abrirUpload('bi')">
                            <i class="fas fa-upload"></i> Enviar
                        </button>
                    </div>
                </div>

                {{-- Certificado de Habilitações --}}
                <div class="doc-card" id="card-certificado_habilitacoes">
                    <div class="doc-card-info">
                        <div class="doc-card-icon">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                        <div class="doc-card-texto">
                            <div class="doc-card-titulo">Certificado de Habilitações <span class="campo-obrigatorio">*</span></div>
                            <div class="doc-card-formato">PDF até 10MB</div>
                        </div>
                    </div>
                    <div class="doc-card-acoes">
                        <span id="status-certificado_habilitacoes" class="doc-status pendente">Pendente</span>
                        <button type="button" class="btn-doc btn-doc-primario" onclick="abrirUpload('certificado_habilitacoes')">
                            <i class="fas fa-upload"></i> Enviar
                        </button>
                    </div>
                </div>

            </div>

        </div>

        {{-- ── SECÇÃO 2: Documentos Opcionais ── --}}
        <div class="form-secao">
            <div class="form-secao-titulo">
                <i class="fas fa-paperclip"></i>
                Documentos Opcionais
                <span class="form-secao-sub">— pode adicionar documentos complementares</span>
            </div>

            <div class="doc-card" id="card-outro">
                <div class="doc-card-info">
                    <div class="doc-card-icon">
                        <i class="fas fa-file-circle-plus"></i>
                    </div>
                    <div class="doc-card-texto">
                        <div class="doc-card-titulo">Outros Documentos</div>
                        <div class="doc-card-formato">JPG, PNG ou PDF até 10MB</div>
                    </div>
                </div>
                <div class="doc-card-acoes">
                    <button type="button" class="btn-doc" onclick="abrirUpload('outro')">
                        <i class="fas fa-plus"></i> Adicionar
                    </button>
                </div>
            </div>

            <div id="outros-documentos-lista" class="outros-lista"></div>
        </div>

        {{-- Rodapé --}}
        <form method="POST" action="{{ route('pedido.submeter') }}" id="formSubmeter">
            @csrf
        </form>
        <div class="pedido-rodape">
            <a href="{{ route('pedido.dados-profissionais', ['tipo' => $tipoDocumento]) }}" class="btn-cancelar-pedido">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
            <button type="button" class="btn-proximo desabilitado" id="btnProximo" onclick="submeterPedido()" disabled>
                Próximo: Ficha de Cobrança <i class="fas fa-arrow-right"></i>
            </button>
        </div>

    </div>
</div>

{{-- ── MODAL DE UPLOAD ── --}}
<div class="modal-overlay" id="uploadModal">
    <div class="modal-box">
        <div class="modal-cabecalho">
            <div class="modal-titulo">Enviar Documento</div>
            <button type="button" class="modal-fechar" onclick="fecharUpload()">&times;</button>
        </div>
        <div class="modal-corpo">
            <form id="uploadForm" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="tipo_documento_upload" id="tipo_documento_upload">
                <label class="campo-label" for="arquivo">Selecione o ficheiro</label>
                <input type="file" id="arquivo" name="arquivo" required>
                <div class="modal-ajuda">Formatos permitidos: JPG, PNG ou PDF. Tamanho máximo: 10MB.</div>
                <div id="uploadProgress" class="modal-progresso">
                    <div class="modal-progresso-barra"></div>
                </div>
            </form>
        </div>
        <div class="modal-rodape">
            <button type="button" class="btn-doc" onclick="fecharUpload()">Cancelar</button>
            <button type="button" class="btn-doc btn-doc-primario" onclick="enviarDocumento()">
                <i class="fas fa-upload"></i> Enviar
            </button>
        </div>
    </div>
</div>

@endsection

@push("scripts")
<script>
let tipoDocumentoAtual = "";
let documentosEnviados = {!! json_encode($documentosEnviados) !!};

document.addEventListener("DOMContentLoaded", function () {
    for (let tipo in documentosEnviados) {
        if (documentosEnviados.hasOwnProperty(tipo)) {
            atualizarStatusDocumento(tipo, true);
        }
    }
    verificarDocumentos();
});

// ── Modal ────────────────────────────────────────────────────────────────────
function abrirUpload(tipo) {
    tipoDocumentoAtual = tipo;
    document.getElementById("tipo_documento_upload").value = tipo;
    document.getElementById("arquivo").value = "";
    document.getElementById("uploadModal").classList.add("aberto");
}

function fecharUpload() {
    document.getElementById("uploadModal").classList.remove("aberto");
}

// Fechar modal ao clicar fora da caixa
document.getElementById("uploadModal")?.addEventListener("click", function (e) {
    if (e.target === this) fecharUpload();
});

// ── Envio de documento ─────────────────────────────────────────────────────
function enviarDocumento() {
    const form = document.getElementById("uploadForm");
    const formData = new FormData(form);
    const arquivo = document.getElementById("arquivo").files[0];

    if (!arquivo) {
        alert("Selecione um ficheiro.");
        return;
    }

    const progress = document.getElementById("uploadProgress");
    progress.classList.add("ativo");

    fetch("{{ route('pedido.etapa3.salvar') }}", {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            documentosEnviados[tipoDocumentoAtual] = true;
            atualizarStatusDocumento(tipoDocumentoAtual, true);
            verificarDocumentos();
            fecharUpload();
        } else {
            alert("Erro: " + data.error);
        }
    })
    .catch(error => {
        alert("Erro ao enviar: " + error);
    })
    .finally(() => {
        progress.classList.remove("ativo");
    });
}

// ── Remover documento ─────────────────────────────────────────────────────
function removerDocumento(tipo) {
    if (!confirm("Deseja remover este documento?")) return;

    fetch("{{ route('pedido.etapa3.remover') }}", {
        method: "DELETE",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").content
        },
        body: JSON.stringify({ tipo: tipo })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            delete documentosEnviados[tipo];
            atualizarStatusDocumento(tipo, false);
            verificarDocumentos();
        }
    });
}

// ── Estado visual dos cartões obrigatórios ──────────────────────────────────
function atualizarStatusDocumento(tipo, enviado) {
    const statusEl = document.getElementById(`status-${tipo}`);
    const card     = document.getElementById(`card-${tipo}`);

    if (statusEl) {
        if (enviado) {
            statusEl.innerHTML = '<i class="fas fa-check-circle"></i> Enviado';
            statusEl.className = "doc-status enviado";
            if (card) card.classList.add("enviado");
        } else {
            statusEl.textContent = "Pendente";
            statusEl.className = "doc-status pendente";
            if (card) card.classList.remove("enviado");
        }
    }
}

// ── Validar se pode avançar ─────────────────────────────────────────────────
function verificarDocumentos() {
    const documentosObrigatorios = ["bi", "certificado_habilitacoes"];

    const todosEnviados = documentosObrigatorios.every(tipo => documentosEnviados[tipo]);

    const btnProximo = document.getElementById("btnProximo");
    btnProximo.disabled = !todosEnviados;
    btnProximo.classList.toggle("desabilitado", !todosEnviados);
}

function submeterPedido() {
    const btn = document.getElementById('btnProximo');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> A processar...';
    document.getElementById('formSubmeter').submit();
}
</script>
@endpush