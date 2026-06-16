@extends("layouts.admin")

@section("title", "Notícias")
@section("page-title", "Notícias")

@section("content")

{{-- Cabeçalho --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:24px;">
    <div>
        <div style="font-size:13px; color:#64748b;">
            {{ $noticias->total() }} notícia(s) registada(s)
        </div>
    </div>
    <a href="{{ route('admin.noticias.create') }}"
       style="display:inline-flex; align-items:center; gap:8px; padding:9px 18px;
              background:#1d4ed8; color:white; font-size:13.5px;
              font-weight:500; text-decoration:none; transition:background 0.15s;"
       onmouseover="this.style.background='#1e40af'"
       onmouseout="this.style.background='#1d4ed8'">
        <i class="fas fa-plus"></i> Nova Notícia
    </a>
</div>

{{-- Tabela --}}
<div style="background:white; border:1px solid #e2e8f0; overflow:hidden;">
    <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
        <thead>
            <tr style="border-bottom:1px solid #e2e8f0; background:#f8fafc;">
                <th style="padding:12px 20px; text-align:left; font-size:11px; font-weight:500;
                           color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Imagem</th>
                <th style="padding:12px 20px; text-align:left; font-size:11px; font-weight:500;
                           color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Título</th>
                <th style="padding:12px 20px; text-align:left; font-size:11px; font-weight:500;
                           color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Status</th>
                <th style="padding:12px 20px; text-align:left; font-size:11px; font-weight:500;
                           color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Data</th>
                <th style="padding:12px 20px; text-align:right; font-size:11px; font-weight:500;
                           color:#64748b; text-transform:uppercase; letter-spacing:0.06em;">Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($noticias as $noticia)
            <tr style="border-bottom:1px solid #f1f5f9; transition:background 0.1s;"
                onmouseover="this.style.background='#f8fafc'"
                onmouseout="this.style.background='white'">

                {{-- Imagem --}}
                <td style="padding:14px 20px;">
                    @if($noticia->image_path)
                        <img src="{{ Storage::url($noticia->image_path) }}"
                             alt="{{ $noticia->title }}"
                             style="width:56px; height:40px; object-fit:cover;
                                    border:1px solid #e2e8f0;">
                    @else
                        <div style="width:56px; height:40px;
                                    background:#f1f5f9; border:0.5px solid #e2e8f0;
                                    display:flex; align-items:center; justify-content:center;
                                    color:#cbd5e1; font-size:16px;">
                            <i class="fas fa-image"></i>
                        </div>
                    @endif
                </td>

                {{-- Título + excerto --}}
                <td style="padding:14px 20px; max-width:360px;">
                    <div style="font-weight:500; color:#0f172a; margin-bottom:3px;
                                white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
                                max-width:340px;">
                        {{ $noticia->title }}
                    </div>
                    <div style="font-size:12px; color:#94a3b8;
                                white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
                                max-width:340px;">
                        {{ Str::limit(strip_tags($noticia->content), 80) }}
                    </div>
                </td>

                {{-- Status --}}
                <td style="padding:14px 20px;">
                    @if($noticia->status === 'published')
                        <span style="display:inline-flex; align-items:center; gap:5px;
                                     padding:3px 10px; font-size:11.5px;
                                     font-weight:500; background:#f0fdf4;
                                     color:#15803d; border:1px solid #bbf7d0;">
                            <i class="fas fa-circle" style="font-size:6px;"></i> Publicado
                        </span>
                    @else
                        <span style="display:inline-flex; align-items:center; gap:5px;
                                     padding:3px 10px; font-size:11.5px;
                                     font-weight:500; background:#f8fafc;
                                     color:#64748b; border:1px solid #e2e8f0;">
                            <i class="fas fa-circle" style="font-size:6px;"></i> Rascunho
                        </span>
                    @endif
                </td>

                {{-- Data --}}
                <td style="padding:14px 20px; color:#64748b; font-size:13px; white-space:nowrap;">
                    {{ $noticia->created_at->format('d/m/Y') }}
                    <div style="font-size:11px; color:#94a3b8;">
                        {{ $noticia->created_at->format('H:i') }}
                    </div>
                </td>

                {{-- Ações --}}
                <td style="padding:14px 20px; text-align:right; white-space:nowrap;">
                    <a href="{{ route('admin.noticias.edit', $noticia) }}"
                       style="display:inline-flex; align-items:center; gap:6px;
                              padding:6px 12px; font-size:12.5px;
                              font-weight:500; color:#1d4ed8; background:#eff6ff;
                              border:1px solid #bfdbfe; text-decoration:none;
                              margin-right:6px; transition:background 0.15s;"
                       onmouseover="this.style.background='#dbeafe'"
                       onmouseout="this.style.background='#eff6ff'">
                        <i class="fas fa-pencil-alt"></i> Editar
                    </a>
                    <button type="button"
                            onclick="confirmarEliminar('{{ route('admin.noticias.destroy', $noticia) }}', '{{ addslashes($noticia->title) }}')"
                            style="display:inline-flex; align-items:center; gap:6px;
                                   padding:6px 12px; font-size:12.5px;
                                   font-weight:500; color:#b91c1c; background:#fef2f2;
                                   border:1px solid #fecaca; cursor:pointer;
                                   transition:background 0.15s;"
                            onmouseover="this.style.background='#fee2e2'"
                            onmouseout="this.style.background='#fef2f2'">
                        <i class="fas fa-trash-alt"></i> Eliminar
                    </button>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding:48px 20px; text-align:center; color:#94a3b8;">
                    <i class="fas fa-newspaper" style="font-size:32px; margin-bottom:12px; display:block; color:#cbd5e1;"></i>
                    Nenhuma notícia registada ainda.
                    <div style="margin-top:12px;">
                        <a href="{{ route('admin.noticias.create') }}"
                           style="font-size:13px; color:#1d4ed8; text-decoration:none;">
                            Criar primeira notícia →
                        </a>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Paginação --}}
    @if($noticias->hasPages())
    <div style="padding:16px 20px; border-top:0.5px solid #f1f5f9; display:flex;
                align-items:center; justify-content:space-between;">
        <div style="font-size:12.5px; color:#64748b;">
            A mostrar {{ $noticias->firstItem() }}–{{ $noticias->lastItem() }}
            de {{ $noticias->total() }} resultados
        </div>
        <div style="display:flex; gap:4px;">
            {{-- Anterior --}}
            @if($noticias->onFirstPage())
                <span style="padding:6px 12px; border-radius:7px; font-size:13px;
                             color:#cbd5e1; border:0.5px solid #e2e8f0; background:#f8fafc;">
                    <i class="fas fa-chevron-left"></i>
                </span>
            @else
                <a href="{{ $noticias->previousPageUrl() }}"
                   style="padding:6px 12px; border-radius:7px; font-size:13px;
                          color:#334155; border:0.5px solid #e2e8f0; background:white;
                          text-decoration:none; transition:background 0.15s;"
                   onmouseover="this.style.background='#f8fafc'"
                   onmouseout="this.style.background='white'">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            {{-- Próximo --}}
            @if($noticias->hasMorePages())
                <a href="{{ $noticias->nextPageUrl() }}"
                   style="padding:6px 12px; border-radius:7px; font-size:13px;
                          color:#334155; border:0.5px solid #e2e8f0; background:white;
                          text-decoration:none; transition:background 0.15s;"
                   onmouseover="this.style.background='#f8fafc'"
                   onmouseout="this.style.background='white'">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span style="padding:6px 12px; border-radius:7px; font-size:13px;
                             color:#cbd5e1; border:0.5px solid #e2e8f0; background:#f8fafc;">
                    <i class="fas fa-chevron-right"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- Modal de confirmação de eliminação --}}
<div class="modal-overlay" id="modalEliminar">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-trash-alt" style="color:#ef4444;"></i>
        </div>
        <div class="modal-title">Eliminar notícia</div>
        <div class="modal-desc" id="modalEliminarDesc">
            Tem a certeza que pretende eliminar esta notícia? Esta ação não pode ser revertida.
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancelar"
                    onclick="document.getElementById('modalEliminar').classList.remove('show')">
                Cancelar
            </button>
            <form method="POST" id="formEliminar">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-confirmar">Eliminar</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push("scripts")
<script>
    function confirmarEliminar(url, titulo) {
        document.getElementById('formEliminar').action = url;
        document.getElementById('modalEliminarDesc').textContent =
            'Tem a certeza que pretende eliminar "' + titulo + '"? Esta ação não pode ser revertida.';
        document.getElementById('modalEliminar').classList.add('show');
    }
</script>
@endpush