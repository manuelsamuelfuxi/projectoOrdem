@extends("layouts.admin")

@section("title", "Editar Notícia")
@section("page-title", "Editar Notícia")

@section("content")
<div style="max-width:780px;">
    <div style="background:white; border-radius:12px; border:0.5px solid #e2e8f0; overflow:hidden;">

        <div style="padding:20px 24px; border-bottom:0.5px solid #f1f5f9;">
            <div style="font-size:14px; font-weight:500; color:#0f172a;">Dados da notícia</div>
            <div style="font-size:12.5px; color:#94a3b8; margin-top:2px;">Actualize os campos que pretende alterar.</div>
        </div>

        <form method="POST" action="{{ route('admin.noticias.update', $noticia) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div style="padding:24px; display:flex; flex-direction:column; gap:20px;">

                {{-- Título --}}
                <div>
                    <label for="titulo" style="display:block; font-size:13px; font-weight:500;
                                               color:#334155; margin-bottom:6px;">
                        Título <span style="color:#ef4444;">*</span>
                    </label>
                    <input type="text" id="titulo" name="titulo"
                           value="{{ old('titulo', $noticia->titulo) }}"
                           placeholder="Título da notícia"
                           style="width:100%; padding:9px 12px; border-radius:8px; font-size:13.5px;
                                  border:0.5px solid {{ $errors->has('titulo') ? '#fca5a5' : '#e2e8f0' }};
                                  background:{{ $errors->has('titulo') ? '#fef2f2' : 'white' }};
                                  color:#0f172a; outline:none;"
                           onfocus="this.style.borderColor='#93c5fd'"
                           onblur="this.style.borderColor='{{ $errors->has('titulo') ? '#fca5a5' : '#e2e8f0' }}'">
                    @error('titulo')
                        <div style="font-size:12px; color:#b91c1c; margin-top:5px;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Conteúdo --}}
                <div>
                    <label for="conteudo" style="display:block; font-size:13px; font-weight:500;
                                                  color:#334155; margin-bottom:6px;">
                        Conteúdo <span style="color:#ef4444;">*</span>
                        <span style="font-weight:400; color:#94a3b8;">(mín. 50 caracteres)</span>
                    </label>
                    <textarea id="conteudo" name="conteudo" rows="10"
                              placeholder="Escreva o conteúdo completo da notícia..."
                              style="width:100%; padding:9px 12px; border-radius:8px; font-size:13.5px;
                                     border:0.5px solid {{ $errors->has('conteudo') ? '#fca5a5' : '#e2e8f0' }};
                                     background:{{ $errors->has('conteudo') ? '#fef2f2' : 'white' }};
                                     color:#0f172a; outline:none; resize:vertical; line-height:1.6;"
                              onfocus="this.style.borderColor='#93c5fd'"
                              onblur="this.style.borderColor='{{ $errors->has('conteudo') ? '#fca5a5' : '#e2e8f0' }}'">{{ old('conteudo', $noticia->conteudo) }}</textarea>
                    @error('conteudo')
                        <div style="font-size:12px; color:#b91c1c; margin-top:5px;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Imagem actual + novo upload --}}
                <div>
                    <label style="display:block; font-size:13px; font-weight:500;
                                  color:#334155; margin-bottom:6px;">
                        Imagem
                        <span style="font-weight:400; color:#94a3b8;">(deixe em branco para manter a actual)</span>
                    </label>

                    {{-- Imagem actual --}}
                    @if($noticia->imagem_path)
                        <div style="margin-bottom:12px;">
                            <div style="font-size:12px; color:#64748b; margin-bottom:6px;">Imagem actual:</div>
                            <img src="{{ Storage::url($noticia->imagem_path) }}"
                                 style="max-width:100%; max-height:220px; border-radius:8px;
                                        border:0.5px solid #e2e8f0; object-fit:cover;">
                        </div>
                    @endif

                    <input type="file" id="imagem" name="imagem" accept="image/*"
                           onchange="previewImagem(this)"
                           style="width:100%; padding:9px 12px; border-radius:8px; font-size:13.5px;
                                  border:0.5px solid {{ $errors->has('imagem') ? '#fca5a5' : '#e2e8f0' }};
                                  background:{{ $errors->has('imagem') ? '#fef2f2' : '#f8fafc' }};
                                  color:#334155; outline:none; cursor:pointer;">
                    @error('imagem')
                        <div style="font-size:12px; color:#b91c1c; margin-top:5px;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror

                    {{-- Preview nova imagem --}}
                    <div id="previewBox" style="display:none; margin-top:12px;">
                        <div style="font-size:12px; color:#64748b; margin-bottom:6px;">Nova imagem:</div>
                        <img id="previewImg" src=""
                             style="max-width:100%; max-height:220px; border-radius:8px;
                                    border:0.5px solid #e2e8f0; object-fit:cover;">
                    </div>
                </div>

                {{-- Legenda e texto alternativo --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label for="legenda_imagem" style="display:block; font-size:13px; font-weight:500;
                                                            color:#334155; margin-bottom:6px;">
                            Legenda da imagem
                        </label>
                        <input type="text" id="legenda_imagem" name="legenda_imagem"
                               value="{{ old('legenda_imagem', $noticia->legenda_imagem) }}"
                               placeholder="Breve descrição da imagem"
                               style="width:100%; padding:9px 12px; border-radius:8px; font-size:13.5px;
                                      border:0.5px solid #e2e8f0; color:#0f172a; outline:none;"
                               onfocus="this.style.borderColor='#93c5fd'"
                               onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                    <div>
                        <label for="texto_alternativo" style="display:block; font-size:13px; font-weight:500;
                                                               color:#334155; margin-bottom:6px;">
                            Texto alternativo (acessibilidade)
                        </label>
                        <input type="text" id="texto_alternativo" name="texto_alternativo"
                               value="{{ old('texto_alternativo', $noticia->texto_alternativo) }}"
                               placeholder="alt text da imagem"
                               style="width:100%; padding:9px 12px; border-radius:8px; font-size:13.5px;
                                      border:0.5px solid #e2e8f0; color:#0f172a; outline:none;"
                               onfocus="this.style.borderColor='#93c5fd'"
                               onblur="this.style.borderColor='#e2e8f0'">
                    </div>
                </div>

                {{-- Status + Data publicação --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label for="status" style="display:block; font-size:13px; font-weight:500;
                                                    color:#334155; margin-bottom:6px;">
                            Status <span style="color:#ef4444;">*</span>
                        </label>
                        <select id="status" name="status"
                                onchange="toggleDataPublicacao(this.value)"
                                style="width:100%; padding:9px 12px; border-radius:8px; font-size:13.5px;
                                       border:0.5px solid #e2e8f0; color:#0f172a; outline:none;
                                       background:white; cursor:pointer;">
                            <option value="rascunho"  {{ old('status', $noticia->status) === 'rascunho'  ? 'selected' : '' }}>Rascunho</option>
                            <option value="publicado" {{ old('status', $noticia->status) === 'publicado' ? 'selected' : '' }}>Publicado</option>
                            <option value="arquivado" {{ old('status', $noticia->status) === 'arquivado' ? 'selected' : '' }}>Arquivado</option>
                        </select>
                    </div>
                    <div id="campoDataPublicacao" style="display:none;">
                        <label for="data_publicacao" style="display:block; font-size:13px; font-weight:500;
                                                             color:#334155; margin-bottom:6px;">
                            Data de publicação
                        </label>
                        <input type="date" id="data_publicacao" name="data_publicacao"
                               value="{{ old('data_publicacao', $noticia->publicado_em?->format('Y-m-d')) }}"
                               style="width:100%; padding:9px 12px; border-radius:8px; font-size:13.5px;
                                      border:0.5px solid #e2e8f0; color:#0f172a; outline:none;">
                    </div>
                </div>

                {{-- Destacar --}}
                <div style="display:flex; align-items:center; gap:10px; padding:14px 16px;
                            background:#f8fafc; border-radius:8px; border:0.5px solid #e2e8f0;">
                    <input type="hidden" name="destacar" value="0">
                    <input type="checkbox" id="destacar" name="destacar" value="1"
                           {{ old('destacar', $noticia->destacar) ? 'checked' : '' }}
                           style="width:16px; height:16px; cursor:pointer; accent-color:#1d4ed8;">
                    <div>
                        <label for="destacar" style="font-size:13.5px; font-weight:500;
                                                      color:#0f172a; cursor:pointer;">
                            Destacar notícia
                        </label>
                        <div style="font-size:12px; color:#94a3b8;">
                            Notícias destacadas aparecem em posição privilegiada no site público.
                        </div>
                    </div>
                </div>

            </div>

            {{-- Rodapé com ações --}}
            <div style="padding:16px 24px; border-top:0.5px solid #f1f5f9; background:#f8fafc;
                        display:flex; align-items:center; justify-content:space-between;">
                {{-- Apagar (lado esquerdo) --}}
                <button type="button"
                        onclick="document.getElementById('modalApagar').classList.add('show')"
                        style="padding:9px 18px; border-radius:8px; font-size:13.5px; font-weight:500;
                               color:#b91c1c; background:white; border:0.5px solid #fca5a5;
                               cursor:pointer; transition:background 0.15s;"
                        onmouseover="this.style.background='#fef2f2'"
                        onmouseout="this.style.background='white'">
                    <i class="fas fa-trash" style="margin-right:6px;"></i> Apagar
                </button>

                <div style="display:flex; gap:10px;">
                    <a href="{{ route('admin.noticias.index') }}"
                       style="padding:9px 18px; border-radius:8px; font-size:13.5px; font-weight:500;
                              color:#334155; background:white; border:0.5px solid #e2e8f0;
                              text-decoration:none; transition:background 0.15s;"
                       onmouseover="this.style.background='#f1f5f9'"
                       onmouseout="this.style.background='white'">
                        Cancelar
                    </a>
                    <button type="submit"
                            style="padding:9px 20px; border-radius:8px; font-size:13.5px; font-weight:500;
                                   color:white; background:#1d4ed8; border:none; cursor:pointer;
                                   transition:background 0.15s;"
                            onmouseover="this.style.background='#1e40af'"
                            onmouseout="this.style.background='#1d4ed8'">
                        <i class="fas fa-save" style="margin-right:6px;"></i> Guardar alterações
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>

{{-- Modal de confirmação de apagar --}}
<div class="modal-overlay" id="modalApagar">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-trash" style="color:#ef4444;"></i>
        </div>
        <div class="modal-title">Apagar notícia</div>
        <div class="modal-desc">
            Tem a certeza que pretende apagar a notícia <strong>{{ $noticia->titulo }}</strong>?
            Esta acção é irreversível.
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancelar"
                    onclick="document.getElementById('modalApagar').classList.remove('show')">
                Cancelar
            </button>
            <form method="POST" action="{{ route('admin.noticias.destroy', $noticia) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirmar">Apagar</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push("scripts")
<script>
    function previewImagem(input) {
        const box = document.getElementById('previewBox');
        const img = document.getElementById('previewImg');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = e => { img.src = e.target.result; box.style.display = 'block'; };
            reader.readAsDataURL(input.files[0]);
        } else {
            box.style.display = 'none';
        }
    }

    function toggleDataPublicacao(valor) {
        const campo = document.getElementById('campoDataPublicacao');
        campo.style.display = valor === 'rascunho' ? 'none' : 'block';
    }

    // Restaurar estado no carregamento
    toggleDataPublicacao(document.getElementById('status').value);
</script>
@endpush