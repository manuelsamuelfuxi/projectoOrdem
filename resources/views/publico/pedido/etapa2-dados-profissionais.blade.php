@extends("layouts.app")

@section("title", "Pedido de " . ($tipoDocumento === "carteira" ? "Carteira Profissional" : "Licença Profissional") . " — Passo 2 de 4")

@section("content")

@push('styles')
    @vite('resources/css/pedidos/etapa2-dados-profissionais.css')
@endpush

<div class="pedido-wrap">

    {{-- Cabeçalho --}}
    <div class="pedido-header">
        <div class="pedido-titulo">
            {{ $tipoDocumento === 'carteira' ? 'Pedido de Carteira Profissional' : 'Pedido de Licença Profissional' }}
        </div>
        <div class="pedido-subtitulo">
            Preencha os campos abaixo. Os campos marcados com <span style="color:#ef4444;">*</span> são obrigatórios.
        </div>
    </div>

    {{-- Barra de progresso --}}
    <div class="progresso-wrap">
        <div class="prog-step feito">
            <div class="prog-circulo"><i class="fas fa-check" style="font-size:13px;"></i></div>
            <div class="prog-label">Dados Pessoais</div>
        </div>
        <div class="prog-step ativo">
            <div class="prog-circulo">2</div>
            <div class="prog-label">Dados Profissionais</div>
        </div>
        <div class="prog-step">
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

        {{--
            Preview do candidato — resumo dos dados da etapa 1.

            $fotoUrl é gerado no controller (dadosProfissionais) via Storage::disk('public')->exists()
            antes de criar a URL. Nunca chamar Storage::url() directamente na view.
            $dadosEtapa1 é passado pelo controller para exibir nome, BI e email.
        --}}
        <div class="pedido-preview-topo">
            <div class="preview-etapa1-resumo">

                <div class="preview-etapa-icon">
                    @if(!empty($fotoUrl))
                        <img src="{{ $fotoUrl }}"
                             alt="Foto de identificação"
                             class="foto-preview-etapa2"
                             onerror="this.style.display='none';
                                      this.nextElementSibling.style.display='flex';">
                        <i class="fas fa-user-check" style="display:none;" aria-hidden="true"></i>
                    @else
                        <i class="fas fa-user-check" aria-hidden="true"></i>
                    @endif
                </div>

                <div class="preview-etapa-info">
                    <div class="preview-etapa-nome">{{ $dadosEtapa1['nome_completo'] ?? '—' }}</div>
                    <div class="preview-etapa-sub">
                        <span><i class="fas fa-id-card" aria-hidden="true"></i> {{ $dadosEtapa1['numero_bi'] ?? '—' }}</span>
                        <span><i class="fas fa-globe" aria-hidden="true"></i> {{ $dadosEtapa1['nacionalidade'] ?? '—' }}</span>
                        <span><i class="fas fa-envelope" aria-hidden="true"></i> {{ $dadosEtapa1['email'] ?? '—' }}</span>
                    </div>
                </div>
            </div>

            {{-- Preview em tempo real --}}
            <div class="preview-prof-wrap">
                <div class="preview-prof-campo">
                    <div class="preview-campo-label">Curso</div>
                    <div class="preview-campo-valor vazio" id="prevCurso">—</div>
                </div>
                <div class="preview-prof-campo">
                    <div class="preview-campo-label">Instituição de Trabalho</div>
                    <div class="preview-campo-valor vazio" id="prevInstituicao">—</div>
                </div>
                <div class="preview-prof-campo">
                    <div class="preview-campo-label">Função</div>
                    <div class="preview-campo-valor vazio" id="prevFuncao">—</div>
                </div>
            </div>
        </div>

        {{-- FORMULÁRIO --}}
        <form method="POST" action="{{ route('pedido.etapa2.salvar') }}" id="formEtapa2">
            @csrf

            {{-- ── SECÇÃO 1: Dados Académicos (OBRIGATÓRIO) ── --}}
            <div class="form-secao">
                <div class="form-secao-titulo">
                    <i class="fas fa-graduation-cap" aria-hidden="true"></i>
                    Dados Académicos
                </div>

                <div class="form-grid form-grid-2">

                    <div class="campo-wrap" style="grid-column: span 2;">
                        <label class="campo-label" for="instituicao_formacao">
                            Escola / Universidade <span class="campo-obrigatorio">*</span>
                        </label>
                        <input type="text" id="instituicao_formacao" name="instituicao_formacao"
                               class="campo-input {{ $errors->has('instituicao_formacao') ? 'erro' : '' }}"
                               value="{{ old('instituicao_formacao', $dadosAnteriores['instituicao_formacao'] ?? '') }}"
                               placeholder="Ex: Universidade Agostinho Neto"
                               required>
                        @error('instituicao_formacao')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap" style="grid-column: span 2;">
                        <label class="campo-label" for="curso_id">
                            Curso <span class="campo-obrigatorio">*</span>
                        </label>
                        <select id="curso_id" name="curso_id"
                                class="campo-select {{ $errors->has('curso_id') ? 'erro' : '' }}"
                                onchange="atualizarPreview()"
                                required>
                            <option value="">Selecione o curso...</option>
                            @foreach($cursos as $curso)
                                <option value="{{ $curso->id }}"
                                    {{ old('curso_id', $dadosAnteriores['curso_id'] ?? '') == $curso->id ? 'selected' : '' }}>
                                    {{ $curso->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('curso_id')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="nivel">
                            Nível Académico <span class="campo-obrigatorio">*</span>
                        </label>
                        <select id="nivel" name="nivel"
                                class="campo-select {{ $errors->has('nivel') ? 'erro' : '' }}"
                                onchange="atualizarClasse(this.value)"
                                required>
                            <option value="">Selecione o nível...</option>
                            <option value="medio"    {{ old('nivel', $dadosAnteriores['nivel'] ?? '') === 'medio'    ? 'selected' : '' }}>Ensino Médio</option>
                            <option value="superior" {{ old('nivel', $dadosAnteriores['nivel'] ?? '') === 'superior' ? 'selected' : '' }}>Superior</option>
                            <option value="outro"    {{ old('nivel', $dadosAnteriores['nivel'] ?? '') === 'outro'    ? 'selected' : '' }}>Outro</option>
                        </select>
                        @error('nivel')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap" id="classeWrap">
                        <label class="campo-label" for="classe">
                            Classe <span class="campo-obrigatorio">*</span>
                        </label>

                        <select id="classeMedio" name="classe"
                                class="campo-select {{ $errors->has('classe') ? 'erro' : '' }}"
                                style="display:none;">
                            <option value="">Selecione a classe...</option>
                            <option value="10"  {{ old('classe', $dadosAnteriores['classe'] ?? '') === '10'  ? 'selected' : '' }}>10.ª Classe</option>
                            <option value="11"  {{ old('classe', $dadosAnteriores['classe'] ?? '') === '11'  ? 'selected' : '' }}>11.ª Classe</option>
                            <option value="12"  {{ old('classe', $dadosAnteriores['classe'] ?? '') === '12'  ? 'selected' : '' }}>12.ª Classe</option>
                            <option value="13"  {{ old('classe', $dadosAnteriores['classe'] ?? '') === '13'  ? 'selected' : '' }}>13.ª Classe</option>
                        </select>

                        <select id="classeSuperior" name="classe"
                                class="campo-select {{ $errors->has('classe') ? 'erro' : '' }}"
                                style="display:none;">
                            <option value="">Selecione o ano...</option>
                            <option value="1ano" {{ old('classe', $dadosAnteriores['classe'] ?? '') === '1ano' ? 'selected' : '' }}>1.º Ano</option>
                            <option value="2ano" {{ old('classe', $dadosAnteriores['classe'] ?? '') === '2ano' ? 'selected' : '' }}>2.º Ano</option>
                            <option value="3ano" {{ old('classe', $dadosAnteriores['classe'] ?? '') === '3ano' ? 'selected' : '' }}>3.º Ano</option>
                            <option value="4ano" {{ old('classe', $dadosAnteriores['classe'] ?? '') === '4ano' ? 'selected' : '' }}>4.º Ano</option>
                            <option value="5ano" {{ old('classe', $dadosAnteriores['classe'] ?? '') === '5ano' ? 'selected' : '' }}>5.º Ano</option>
                        </select>

                        <input type="text" id="classeOutro" name="classe"
                               class="campo-input {{ $errors->has('classe') ? 'erro' : '' }}"
                               value="{{ old('classe', $dadosAnteriores['classe'] ?? '') }}"
                               placeholder="Descreva a sua classe ou habilitação"
                               style="display:none;">

                        <input type="text" id="classePlaceholder"
                               class="campo-input"
                               placeholder="Selecione primeiro o nível académico"
                               disabled style="background:#f5f5f5; cursor:not-allowed;">

                        @error('classe')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ── SECÇÃO 2: Dados Profissionais (OPCIONAL) ── --}}
            <div class="form-secao">
                <div class="form-secao-titulo">
                    <i class="fas fa-briefcase" aria-hidden="true"></i>
                    Dados Profissionais
                    <span style="font-size:0.8em; color:#888; font-weight:normal;">(opcional)</span>
                </div>

                <div class="form-grid form-grid-2">

                    <div class="campo-wrap" style="grid-column: span 2;">
                        <label class="campo-label" for="nome_instituicao">
                            Nome da Instituição
                        </label>
                        <input type="text" id="nome_instituicao" name="nome_instituicao"
                               class="campo-input {{ $errors->has('nome_instituicao') ? 'erro' : '' }}"
                               value="{{ old('nome_instituicao', $dadosAnteriores['nome_instituicao'] ?? '') }}"
                               placeholder="Ex: Hospital Geral de Luanda"
                               oninput="atualizarPreview()">
                        @error('nome_instituicao')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap" style="grid-column: span 2;">
                        <label class="campo-label" for="funcao_id">
                            Função que Ocupa
                        </label>
                        <select id="funcao_id" name="funcao_id"
                                class="campo-select {{ $errors->has('funcao_id') ? 'erro' : '' }}"
                                onchange="atualizarPreview()">
                            <option value="">Selecione a função...</option>
                            @foreach($funcoes as $funcao)
                                <option value="{{ $funcao->id }}"
                                    {{ old('funcao_id', $dadosAnteriores['funcao_id'] ?? '') == $funcao->id ? 'selected' : '' }}>
                                    {{ $funcao->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('funcao_id')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap" style="grid-column: span 2;">
                        <label class="campo-label" for="sector">
                            Sector
                        </label>
                        <select id="sector" name="sector"
                                class="campo-select {{ $errors->has('sector') ? 'erro' : '' }}">
                            <option value="">Selecione o sector...</option>
                            <option value="publico" {{ old('sector', $dadosAnteriores['sector'] ?? '') === 'publico' ? 'selected' : '' }}>Público</option>
                            <option value="privado" {{ old('sector', $dadosAnteriores['sector'] ?? '') === 'privado' ? 'selected' : '' }}>Privado</option>
                        </select>
                        @error('sector')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="provincia_trabalho_id">
                            Província
                        </label>
                        <select id="provincia_trabalho_id" name="provincia_trabalho_id"
                                class="campo-select {{ $errors->has('provincia_trabalho_id') ? 'erro' : '' }}"
                                onchange="carregarMunicipios(this.value, 'municipio_trabalho_id')">
                            <option value="">Selecione a província...</option>
                            @foreach($provincias as $provincia)
                                <option value="{{ $provincia->id }}"
                                    {{ old('provincia_trabalho_id', $dadosAnteriores['provincia_trabalho_id'] ?? '') == $provincia->id ? 'selected' : '' }}>
                                    {{ $provincia->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('provincia_trabalho_id')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="municipio_trabalho_id">
                            Município
                        </label>
                        <select id="municipio_trabalho_id" name="municipio_trabalho_id"
                                class="campo-select {{ $errors->has('municipio_trabalho_id') ? 'erro' : '' }}">
                            <option value="">Selecione a província primeiro</option>
                        </select>
                        @error('municipio_trabalho_id')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="telefone_trabalho">
                            Telefone
                        </label>
                        <input type="tel" id="telefone_trabalho" name="telefone_trabalho"
                               class="campo-input {{ $errors->has('telefone_trabalho') ? 'erro' : '' }}"
                               value="{{ old('telefone_trabalho', $dadosAnteriores['telefone_trabalho'] ?? '') }}"
                               placeholder="Ex: 923 000 000">
                        @error('telefone_trabalho')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="email_trabalho">
                            E-mail
                        </label>
                        <input type="email" id="email_trabalho" name="email_trabalho"
                               class="campo-input {{ $errors->has('email_trabalho') ? 'erro' : '' }}"
                               value="{{ old('email_trabalho', $dadosAnteriores['email_trabalho'] ?? '') }}"
                               placeholder="Ex: nome@instituicao.ao">
                        @error('email_trabalho')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- Rodapé --}}
            <div class="pedido-rodape">
                <a href="{{ route('pedido.carteira.form') }}" class="btn-cancelar-pedido">
                    <i class="fas fa-arrow-left" aria-hidden="true"></i> Voltar
                </a>
                <button type="submit" class="btn-proximo">
                    Próximo: Upload de Documentos <i class="fas fa-arrow-right" aria-hidden="true"></i>
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

@push("scripts")
<script>
(function () {
    'use strict';

    // ── AJAX: carregar municípios por província ────────────────────────────────
    window.carregarMunicipios = async function (provinciaId, selectId) {
        const select = document.getElementById(selectId);
        if (!select) return;

        select.innerHTML = '<option value="">A carregar...</option>';
        select.disabled  = true;

        if (!provinciaId) {
            select.innerHTML = '<option value="">Selecione a província primeiro</option>';
            select.disabled  = false;
            return;
        }

        try {
            const resp = await fetch(`/municipios/${encodeURIComponent(provinciaId)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!resp.ok) throw new Error('Resposta inválida do servidor.');

            const municipios = await resp.json();

            select.innerHTML = '<option value="">Selecione o município...</option>';
            municipios.forEach(function (m) {
                const opt       = document.createElement('option');
                opt.value       = m.id;
                opt.textContent = m.nome;
                select.appendChild(opt);
            });
        } catch (err) {
            console.error('[etapa2] Erro ao carregar municípios:', err);
            select.innerHTML = '<option value="">Erro ao carregar municípios</option>';
        }

        select.disabled = false;
    };

    // ── Classe dinâmica conforme nível académico ──────────────────────────────
    window.atualizarClasse = function (nivel) {
        const elMedio       = document.getElementById('classeMedio');
        const elSuperior    = document.getElementById('classeSuperior');
        const elOutro       = document.getElementById('classeOutro');
        const elPlaceholder = document.getElementById('classePlaceholder');

        [elMedio, elSuperior, elOutro].forEach(function (el) {
            el.removeAttribute('name');
            el.style.display = 'none';
            el.required      = false;
        });
        elPlaceholder.style.display = 'none';

        if (nivel === 'medio') {
            elMedio.setAttribute('name', 'classe');
            elMedio.style.display = 'block';
            elMedio.required      = true;
        } else if (nivel === 'superior') {
            elSuperior.setAttribute('name', 'classe');
            elSuperior.style.display = 'block';
            elSuperior.required      = true;
        } else if (nivel === 'outro') {
            elOutro.setAttribute('name', 'classe');
            elOutro.style.display = 'block';
            elOutro.required      = true;
        } else {
            elPlaceholder.style.display = 'block';
        }
    };

    // ── Preview em tempo real ─────────────────────────────────────────────────
    window.atualizarPreview = function () {
        atualizarCampo('curso_id',         'prevCurso');
        atualizarCampo('nome_instituicao', 'prevInstituicao');
        atualizarCampo('funcao_id',        'prevFuncao');
    };

    function atualizarCampo(idCampo, idPreview) {
        const el   = document.getElementById(idCampo);
        const prev = document.getElementById(idPreview);
        if (!el || !prev) return;
        const val = el.value.trim();
        if (val) {
            prev.textContent = el.tagName === 'SELECT'
                ? el.options[el.selectedIndex].text
                : val;
            prev.classList.remove('vazio');
        } else {
            prev.textContent = '—';
            prev.classList.add('vazio');
        }
    }

    // ── Inicialização ─────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', async function () {
        // Restaurar nível e classe após erro de validação
        const nivelSalvo = document.getElementById('nivel').value;
        if (nivelSalvo) atualizarClasse(nivelSalvo);

        // Restaurar município de trabalho após erro de validação
        const provTrabalhoId = '{{ old('provincia_trabalho_id', $dadosAnteriores['provincia_trabalho_id'] ?? '') }}';
        const munTrabalhoId  = '{{ old('municipio_trabalho_id', $dadosAnteriores['municipio_trabalho_id'] ?? '') }}';

        if (provTrabalhoId) {
            await carregarMunicipios(provTrabalhoId, 'municipio_trabalho_id');
            const sel = document.getElementById('municipio_trabalho_id');
            if (sel && munTrabalhoId) sel.value = munTrabalhoId;
        }

        atualizarPreview();
    });

}());
</script>
@endpush