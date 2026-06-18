@extends("layouts.app")

@section("title", "Pedido de " . ($tipoDocumento === "carteira" ? "Carteira Profissional" : "Licença Profissional") . " — Passo 1 de 4")

@section("content")

@push('styles')
    @vite('resources/css/pedidos/etapa1-dados-pessoais.css')
@endpush

<div class="pedido-wrap">

    {{-- Cabeçalho --}}
    <div class="pedido-header">
        <div class="pedido-titulo">{{ $titulo ?? 'Solicitação de Documento' }}</div>
        <div class="pedido-subtitulo">Preencha os campos abaixo. Os campos marcados com <span style="color:#ef4444;">*</span> são obrigatórios.</div>
    </div>

    {{-- Barra de progresso --}}
    <div class="progresso-wrap">
        <div class="prog-step ativo">
            <div class="prog-circulo">1</div>
            <div class="prog-label">Dados Pessoais</div>
        </div>
        <div class="prog-step">
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

        {{-- Preview tipo crachá --}}
        <div class="pedido-preview-topo">

            {{-- Foto tipo passe --}}
<div class="foto-passe-wrap">
    <div class="foto-passe-placeholder" id="fotoPlaceholder"
         onclick="document.getElementById('inputFoto').click()">
        <i class="fas fa-camera"></i>
        <span>Clique para<br>adicionar foto</span>
    </div>
    <img id="fotoPreview" class="foto-passe"
         src="" alt="Foto" style="display:none;">
    <div class="foto-btn-trocar" title="Trocar foto"
         onclick="document.getElementById('inputFoto').click()"
         style="display:none;" id="fotoBtnTrocar">
        <i class="fas fa-camera"></i>
    </div>
    
    {{-- 🔥 INPUT CORRIGIDO - opacity 0 em vez de display:none --}}
    <div style="position: relative; width: 100%; height: 0; overflow: visible;">
        <input type="file" id="inputFoto" name="foto" accept="image/*"
               style="position: absolute; top: 0; left: 0; opacity: 0; 
                      width: 100%; height: 100%; cursor: pointer; z-index: 10;"
               onchange="previewFoto(this)">
    </div>
</div>

            {{-- Dados em tempo real --}}
            <div class="preview-info">
                <div class="preview-nome" id="prevNome">Nome Completo</div>
                <div class="preview-linha"></div>
                <div class="preview-campos">
                    <div class="preview-campo">
                        <div class="preview-campo-label">Nº do B.I.</div>
                        <div class="preview-campo-valor vazio" id="prevBI">—</div>
                    </div>
                    <div class="preview-campo">
                        <div class="preview-campo-label">Género</div>
                        <div class="preview-campo-valor vazio" id="prevGenero">—</div>
                    </div>
                    <div class="preview-campo">
                        <div class="preview-campo-label">Data de Nascimento</div>
                        <div class="preview-campo-valor vazio" id="prevNascimento">—</div>
                    </div>
                    <div class="preview-campo">
                        <div class="preview-campo-label">Nacionalidade</div>
                        <div class="preview-campo-valor vazio" id="prevNacionalidade">—</div>
                    </div>
                    <div class="preview-campo">
                        <div class="preview-campo-label">Província</div>
                        <div class="preview-campo-valor vazio" id="prevProvincia">—</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- FORMULÁRIO --}}
        <form method="POST" action="{{ route('pedido.salvar-etapa1') }}"
              id="formPedido" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="tipo_documento" value="{{ $tipoDocumento }}">
            <input type="hidden" name="etapa_atual" value="1">

            {{-- ── SECÇÃO 1: Identificação Pessoal ── --}}
            <div class="form-secao">
                <div class="form-secao-titulo">
                    <i class="fas fa-user"></i>
                    Identificação Pessoal
                </div>

                <div class="form-grid form-grid-2">

                    <div class="campo-wrap" style="grid-column: span 2;">
                        <label class="campo-label" for="nome_completo">
                            Nome Completo <span class="campo-obrigatorio">*</span>
                        </label>
                        <input type="text" id="nome_completo" name="nome_completo"
                               class="campo-input {{ $errors->has('nome_completo') ? 'erro' : '' }}"
                               value="{{ old('nome_completo') }}"
                               placeholder="Insira o nome completo"
                               oninput="atualizarPreview()"
                               required>
                        @error('nome_completo')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="numero_bi">
                            Número do B.I. <span class="campo-obrigatorio">*</span>
                        </label>
                        <input type="text" id="numero_bi" name="numero_bi"
                               class="campo-input {{ $errors->has('numero_bi') ? 'erro' : '' }}"
                               value="{{ old('numero_bi') }}"
                               placeholder="Ex: 000123456LA041"
                               oninput="atualizarPreview()"
                               required>
                        @error('numero_bi')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="genero">
                            Género <span class="campo-obrigatorio">*</span>
                        </label>
                        <select id="genero" name="genero"
                                class="campo-select {{ $errors->has('genero') ? 'erro' : '' }}"
                                onchange="atualizarPreview()"
                                required>
                            <option value="">Selecione...</option>
                            <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="feminino"  {{ old('genero') == 'feminino'  ? 'selected' : '' }}>Feminino</option>
                        </select>
                        @error('genero')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="data_nascimento">
                            Data de Nascimento <span class="campo-obrigatorio">*</span>
                        </label>
                        <input type="date" id="data_nascimento" name="data_nascimento"
                               class="campo-input {{ $errors->has('data_nascimento') ? 'erro' : '' }}"
                               value="{{ old('data_nascimento') }}"
                               onchange="atualizarPreview()"
                               required>
                        @error('data_nascimento')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="nacionalidade">
                            Nacionalidade <span class="campo-obrigatorio">*</span>
                        </label>
                        <select id="nacionalidade" name="nacionalidade"
                                class="campo-select {{ $errors->has('nacionalidade') ? 'erro' : '' }}"
                                onchange="atualizarPreview()"
                                required>
                            <option value="">Selecione o país...</option>
                            @php
                                $nacionalidades = [
                                    'Angolana' => 'Angola',
                                    'Argelina' => 'Argélia',
                                    'Beninense' => 'Benim',
                                    'Botsuanesa' => 'Botsuana',
                                    'Burquinabê' => 'Burquina Faso',
                                    'Burundiana' => 'Burundi',
                                    'Cabo-verdiana' => 'Cabo Verde',
                                    'Camaronesa' => 'Camarões',
                                    'Comorense' => 'Comores',
                                    'Congolesa' => 'Congo (República do)',
                                    'Congolesa (RDC)' => 'Congo (República Democrática)',
                                    'Costa-marfinense' => 'Costa do Marfim',
                                    'Djiboutiana' => 'Djibouti',
                                    'Egípcia' => 'Egipto',
                                    'Eritreia' => 'Eritreia',
                                    'Essuatinesa' => 'Essuatíni',
                                    'Etíope' => 'Etiópia',
                                    'Gabonesa' => 'Gabão',
                                    'Gambiana' => 'Gâmbia',
                                    'Ganesa' => 'Gana',
                                    'Guineense' => 'Guiné',
                                    'Guinéu-equatoriana' => 'Guiné Equatorial',
                                    'Guineense-bissauense' => 'Guiné-Bissau',
                                    'Keniana' => 'Quénia',
                                    'Lesotiana' => 'Lesoto',
                                    'Liberiana' => 'Libéria',
                                    'Líbia' => 'Líbia',
                                    'Malgaxe' => 'Madagáscar',
                                    'Malauiana' => 'Maláui',
                                    'Maliana' => 'Mali',
                                    'Marroquina' => 'Marrocos',
                                    'Mauritana' => 'Mauritânia',
                                    'Mauriciana' => 'Maurícia',
                                    'Moçambicana' => 'Moçambique',
                                    'Namibiana' => 'Namíbia',
                                    'Nigerina' => 'Níger',
                                    'Nigeriana' => 'Nigéria',
                                    'Ruandesa' => 'Ruanda',
                                    'São-tomense' => 'São Tomé e Príncipe',
                                    'Senegalesa' => 'Senegal',
                                    'Serra-leonesa' => 'Serra Leoa',
                                    'Somali' => 'Somália',
                                    'Sul-africana' => 'África do Sul',
                                    'Sul-sudanesa' => 'Sudão do Sul',
                                    'Sudanesa' => 'Sudão',
                                    'Tanzaniana' => 'Tanzânia',
                                    'Togolesa' => 'Togo',
                                    'Tunisina' => 'Tunísia',
                                    'Ugandesa' => 'Uganda',
                                    'Zambiana' => 'Zâmbia',
                                    'Zimbabueana' => 'Zimbabué',
                                ];
                                $nacionalidadeSel = old('nacionalidade', 'Angolana');
                            @endphp
                            @foreach($nacionalidades as $valor => $label)
                                <option value="{{ $valor }}" {{ $nacionalidadeSel === $valor ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('nacionalidade')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ── SECÇÃO 2: Morada ── --}}
            <div class="form-secao">
                <div class="form-secao-titulo">
                    <i class="fas fa-map-marker-alt"></i>
                    Endereço de Morada
                </div>

                <div class="form-grid form-grid-2">

                    <div class="campo-wrap">
                        <label class="campo-label" for="provincia_id">
                            Província <span class="campo-obrigatorio">*</span>
                        </label>
                        <select id="provincia_id" name="provincia_id"
                                class="campo-select {{ $errors->has('provincia_id') ? 'erro' : '' }}"
                                onchange="atualizarPreview(); carregarMunicipios(this.value, 'municipio_id')"
                                required>
                            <option value="">Selecione...</option>
                            @foreach($provincias as $provincia)
                                <option value="{{ $provincia->id }}"
                                    {{ old('provincia_id') == $provincia->id ? 'selected' : '' }}>
                                    {{ $provincia->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('provincia_id')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="municipio_id">
                            Município <span class="campo-obrigatorio">*</span>
                        </label>
                        <select id="municipio_id" name="municipio_id"
                                class="campo-select {{ $errors->has('municipio_id') ? 'erro' : '' }}"
                                required>
                            <option value="">Selecione a província primeiro</option>
                        </select>
                        @error('municipio_id')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="bairro">
                            Bairro <span class="campo-obrigatorio">*</span>
                        </label>
                        <input type="text" id="bairro" name="bairro"
                               class="campo-input {{ $errors->has('bairro') ? 'erro' : '' }}"
                               value="{{ old('bairro') }}"
                               placeholder="Nome do bairro"
                               required>
                        @error('bairro')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- ── SECÇÃO 3: Contactos ── --}}
            <div class="form-secao">
                <div class="form-secao-titulo">
                    <i class="fas fa-phone"></i>
                    Contactos
                </div>

                <div class="form-grid form-grid-2">

                    <div class="campo-wrap" style="grid-column: span 2;">
                        <label class="campo-label" for="email">
                            Email <span class="campo-obrigatorio">*</span>
                        </label>
                        <input type="email" id="email" name="email"
                               class="campo-input {{ $errors->has('email') ? 'erro' : '' }}"
                               value="{{ old('email') }}"
                               placeholder="exemplo@email.com"
                               required>
                        @error('email')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="telefone">
                            Telefone Principal <span class="campo-obrigatorio">*</span>
                        </label>
                        <input type="tel" id="telefone" name="telefone"
                               class="campo-input {{ $errors->has('telefone') ? 'erro' : '' }}"
                               value="{{ old('telefone') }}"
                               placeholder="+244 9XX XXX XXX"
                               required>
                        @error('telefone')
                            <div class="campo-erro"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                        @enderror
                    </div>

                    <div class="campo-wrap">
                        <label class="campo-label" for="telefone_alternativo">
                            Telefone Alternativo
                        </label>
                        <input type="tel" id="telefone_alternativo" name="telefone_alternativo"
                               class="campo-input"
                               value="{{ old('telefone_alternativo') }}"
                               placeholder="+244 9XX XXX XXX">
                    </div>

                </div>
            </div>

            {{-- Rodapé --}}
            <div class="pedido-rodape">
                <a href="{{ route('home') }}" class="btn-cancelar-pedido">
                    <i class="fas fa-times"></i> Cancelar
                </a>
                <button type="submit" class="btn-proximo" id="btnProximo">
                    Próximo: Dados Profissionais <i class="fas fa-arrow-right"></i>
                </button>
            </div>

        </form>
    </div>
</div>

@endsection

@push("scripts")
<script>
    // ── AJAX: carregar municípios por província ────────────────────────────────
    async function carregarMunicipios(provinciaId, selectId) {
        const select = document.getElementById(selectId);
        select.innerHTML = '<option value="">A carregar...</option>';
        select.disabled = true;

        if (!provinciaId) {
            select.innerHTML = '<option value="">Selecione a província primeiro</option>';
            select.disabled = false;
            return;
        }

        try {
            const resp = await fetch(`/municipios/${provinciaId}`);
            const municipios = await resp.json();

            select.innerHTML = '<option value="">Selecione o município...</option>';
            municipios.forEach(m => {
                const opt = document.createElement('option');
                opt.value = m.id;
                opt.textContent = m.nome;
                select.appendChild(opt);
            });
        } catch (e) {
            select.innerHTML = '<option value="">Erro ao carregar municípios</option>';
        }

        select.disabled = false;
    }

    // ── Preview em tempo real ─────────────────────────────────────────────────
    function atualizarPreview() {
        const nome = document.getElementById('nome_completo').value.trim();
        const elNome = document.getElementById('prevNome');
        elNome.textContent = nome || 'Nome Completo';
        elNome.style.opacity = nome ? '1' : '0.4';

        atualizar('numero_bi',     'prevBI');
        atualizar('genero',        'prevGenero');
        atualizar('nacionalidade', 'prevNacionalidade');
        atualizar('provincia_id',  'prevProvincia');

        const dataNasc = document.getElementById('data_nascimento').value;
        const elDataNasc = document.getElementById('prevNascimento');
        if (dataNasc) {
            const [y, m, d] = dataNasc.split('-');
            elDataNasc.textContent = `${d}/${m}/${y}`;
            elDataNasc.classList.remove('vazio');
        } else {
            elDataNasc.textContent = '—';
            elDataNasc.classList.add('vazio');
        }
    }

    function atualizar(idCampo, idPreview) {
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

    // ── Preview da foto ───────────────────────────────────────────────────────
    function previewFoto(input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            const img         = document.getElementById('fotoPreview');
            const placeholder = document.getElementById('fotoPlaceholder');
            const btn         = document.getElementById('fotoBtnTrocar');
            img.src = e.target.result;
            img.style.display = 'block';
            placeholder.style.display = 'none';
            btn.style.display = 'flex';
        };
        reader.readAsDataURL(input.files[0]);
    }

    // ── Inicialização ─────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', async () => {
        atualizarPreview();

        // Restaurar município após erro de validação
        @if(old('provincia_id'))
            await carregarMunicipios('{{ old('provincia_id') }}', 'municipio_id');
            document.getElementById('municipio_id').value = '{{ old('municipio_id') }}';
        @endif

        // Validação da foto
        const inputFoto = document.getElementById('inputFoto');
        if (inputFoto) {
            inputFoto.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (!file) return;
                if (file.size > 2 * 1024 * 1024) {
                    alert('A foto deve ter no máximo 2MB. Por favor, reduza o tamanho da imagem.');
                    this.value = '';
                    return;
                }
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Formato inválido. Use apenas JPG, JPEG ou PNG.');
                    this.value = '';
                }
            });
        }
    });
</script>
@endpush