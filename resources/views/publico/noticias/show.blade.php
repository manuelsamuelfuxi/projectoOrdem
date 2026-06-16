@extends("layouts.app")

@section("title", $noticia->titulo . " — ORDEPDITA")

@section("content")

<style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
        --azul:       #0c2d6b;
        --azul-med:   #1a4499;
        --azul-claro: #2563eb;
        --ouro:       #c8922a;
        --ouro-claro: #e8b84b;
        --cinza-f:    #f4f6fa;
        --cinza-b:    #e8ecf3;
        --texto:      #1e2a3a;
        --muted:      #64748b;
        --branco:     #ffffff;
        --sombra:     0 4px 24px rgba(12,45,107,0.10);
    }

    body {
        font-family: 'Source Sans 3', sans-serif;
        background: var(--cinza-f);
        color: var(--texto);
        line-height: 1.6;
    }

    /* ─── NAVBAR ─────────────────────────────────── */
    .pub-nav {
        background: var(--azul);
        position: sticky;
        top: 0;
        z-index: 200;
        box-shadow: 0 2px 16px rgba(0,0,0,0.18);
    }

    .pub-nav-inner {
        max-width: 1180px;
        margin: 0 auto;
        padding: 0 24px;
        height: 68px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
    }

    .pub-nav-brand {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        flex-shrink: 0;
    }

    .pub-nav-logo {
        width: 40px; height: 40px;
        background: var(--ouro);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; color: var(--azul); font-weight: 700;
    }

    .pub-nav-nome {
        font-family: 'Playfair Display', serif;
        font-size: 17px;
        font-weight: 700;
        color: var(--branco);
        line-height: 1.2;
    }

    .pub-nav-sub { font-size: 10.5px; color: rgba(255,255,255,0.55); letter-spacing: 0.04em; text-transform: uppercase; }

    .pub-nav-links { display: flex; align-items: center; gap: 4px; list-style: none; }

    .pub-nav-links a {
        padding: 7px 14px;
        border-radius: 7px;
        font-size: 13.5px;
        font-weight: 500;
        color: rgba(255,255,255,0.80);
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }

    .pub-nav-links a:hover { background: rgba(255,255,255,0.12); color: var(--branco); }

    .pub-nav-cta {
        padding: 8px 18px !important;
        background: var(--ouro) !important;
        color: var(--azul) !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
    }

    /* ─── BREADCRUMB ─────────────────────────────── */
    .breadcrumb-bar {
        background: var(--branco);
        border-bottom: 0.5px solid var(--cinza-b);
        padding: 0 24px;
    }

    .breadcrumb-inner {
        max-width: 860px;
        margin: 0 auto;
        padding: 14px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--muted);
        flex-wrap: wrap;
    }

    .breadcrumb-inner a {
        color: var(--azul-claro);
        text-decoration: none;
    }

    .breadcrumb-inner a:hover { text-decoration: underline; }
    .breadcrumb-sep { color: #cbd5e1; }

    /* ─── ARTIGO ─────────────────────────────────── */
    .artigo-wrap {
        max-width: 860px;
        margin: 40px auto;
        padding: 0 24px;
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 32px;
        align-items: start;
    }

    .artigo-principal {
        background: var(--branco);
        border-radius: 14px;
        overflow: hidden;
        box-shadow: var(--sombra);
        border: 0.5px solid var(--cinza-b);
    }

    .artigo-imagem {
        width: 100%;
        max-height: 420px;
        object-fit: cover;
        display: block;
    }

    .artigo-imagem-placeholder {
        height: 280px;
        background: linear-gradient(135deg, var(--cinza-b), var(--cinza-f));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 56px;
        color: #c8d3e8;
    }

    .artigo-legenda {
        font-size: 12px;
        color: var(--muted);
        text-align: center;
        padding: 10px 24px;
        background: var(--cinza-f);
        border-bottom: 0.5px solid var(--cinza-b);
        font-style: italic;
    }

    .artigo-corpo {
        padding: 36px 40px;
    }

    .artigo-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11.5px;
        font-weight: 600;
        background: #fef3c7;
        color: #92400e;
        border: 0.5px solid #fde68a;
        margin-bottom: 16px;
    }

    .artigo-titulo {
        font-family: 'Playfair Display', serif;
        font-size: clamp(22px, 4vw, 32px);
        font-weight: 700;
        color: var(--azul);
        line-height: 1.25;
        margin-bottom: 16px;
    }

    .artigo-meta {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        padding: 14px 0;
        border-top: 0.5px solid var(--cinza-b);
        border-bottom: 0.5px solid var(--cinza-b);
        margin-bottom: 28px;
        font-size: 13px;
        color: var(--muted);
    }

    .artigo-meta i { color: var(--ouro); margin-right: 4px; }

    .artigo-conteudo {
        font-size: 15.5px;
        line-height: 1.85;
        color: #2d3748;
    }

    .artigo-conteudo p { margin-bottom: 16px; }

    .artigo-rodape {
        padding: 20px 40px;
        border-top: 0.5px solid var(--cinza-b);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .btn-voltar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--azul);
        background: var(--cinza-f);
        border: 0.5px solid var(--cinza-b);
        text-decoration: none;
        transition: background 0.15s;
    }

    .btn-voltar:hover { background: var(--cinza-b); }

    /* ─── SIDEBAR ────────────────────────────────── */
    .artigo-sidebar {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .sidebar-card {
        background: var(--branco);
        border-radius: 12px;
        border: 0.5px solid var(--cinza-b);
        box-shadow: var(--sombra);
        overflow: hidden;
    }

    .sidebar-card-titulo {
        padding: 14px 18px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--ouro);
        border-bottom: 0.5px solid var(--cinza-b);
        background: var(--cinza-f);
    }

    .rel-item {
        display: flex;
        gap: 12px;
        padding: 14px 18px;
        border-bottom: 0.5px solid var(--cinza-f);
        text-decoration: none;
        color: inherit;
        transition: background 0.15s;
    }

    .rel-item:last-child { border-bottom: none; }
    .rel-item:hover { background: var(--cinza-f); }

    .rel-img {
        width: 60px;
        min-width: 60px;
        height: 50px;
        object-fit: cover;
        border-radius: 7px;
    }

    .rel-img-placeholder {
        width: 60px;
        min-width: 60px;
        height: 50px;
        border-radius: 7px;
        background: var(--cinza-b);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #c8d3e8;
        font-size: 18px;
    }

    .rel-titulo {
        font-size: 13px;
        font-weight: 600;
        color: var(--azul);
        line-height: 1.35;
        margin-bottom: 4px;
    }

    .rel-data {
        font-size: 11.5px;
        color: var(--muted);
    }

    /* ─── FOOTER ─────────────────────────────────── */
    .pub-footer {
        background: #07193a;
        color: rgba(255,255,255,0.75);
        padding: 48px 24px 0;
        margin-top: 64px;
    }

    .pub-footer-inner {
        max-width: 1180px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 2fr 1fr 1fr 1fr;
        gap: 40px;
    }

    .footer-marca p {
        font-size: 13.5px;
        line-height: 1.7;
        color: rgba(255,255,255,0.55);
        margin-top: 14px;
        max-width: 280px;
    }

    .footer-titulo {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--ouro);
        margin-bottom: 16px;
    }

    .footer-links { list-style: none; display: flex; flex-direction: column; gap: 9px; }

    .footer-links a {
        font-size: 13.5px;
        color: rgba(255,255,255,0.60);
        text-decoration: none;
        transition: color 0.15s;
    }

    .footer-links a:hover { color: var(--branco); }

    .footer-contacto { display: flex; flex-direction: column; gap: 10px; }

    .footer-contacto-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13.5px;
        color: rgba(255,255,255,0.60);
    }

    .footer-contacto-item i { color: var(--ouro); margin-top: 2px; flex-shrink: 0; width: 14px; }

    .footer-bottom {
        max-width: 1180px;
        margin: 40px auto 0;
        padding: 20px 0;
        border-top: 0.5px solid rgba(255,255,255,0.08);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    .footer-bottom p { font-size: 12.5px; color: rgba(255,255,255,0.35); }

    .footer-bottom a {
        color: rgba(255,255,255,0.45);
        text-decoration: none;
        font-size: 12.5px;
        transition: color 0.15s;
    }

    .footer-bottom a:hover { color: rgba(255,255,255,0.80); }

    @media (max-width: 860px) {
        .artigo-wrap { grid-template-columns: 1fr; }
        .artigo-sidebar { display: none; }
        .pub-footer-inner { grid-template-columns: 1fr 1fr; }
        .pub-nav-links { display: none; }
    }

    @media (max-width: 600px) {
        .artigo-corpo { padding: 24px 20px; }
        .artigo-rodape { padding: 16px 20px; }
        .pub-footer-inner { grid-template-columns: 1fr; }
    }
</style>

{{-- NAVBAR --}}
<nav class="pub-nav">
    <div class="pub-nav-inner">
        <a href="{{ route('home') }}" class="pub-nav-brand">
            <div class="pub-nav-logo"><i class="fas fa-shield-alt"></i></div>
            <div>
                <div class="pub-nav-nome">ORDEPDITA</div>
                <div class="pub-nav-sub">Ordem dos Técnicos</div>
            </div>
        </a>
        <ul class="pub-nav-links">
            <li><a href="{{ route('home') }}">Início</a></li>
            <li><a href="{{ route('noticias.index') }}">Notícias</a></li>
            <li><a href="#">Sobre</a></li>
            <li><a href="#">Serviços</a></li>
            <li><a href="#">Contacto</a></li>
            @auth
                <li><a href="{{ route('admin.dashboard') }}" class="pub-nav-cta">
                    <i class="fas fa-tachometer-alt"></i> Painel
                </a></li>
            @else
                <li><a href="{{ route('login') }}" class="pub-nav-cta">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </a></li>
            @endauth
        </ul>
    </div>
</nav>

{{-- BREADCRUMB --}}
<div class="breadcrumb-bar">
    <div class="breadcrumb-inner">
        <a href="{{ route('home') }}"><i class="fas fa-home"></i> Início</a>
        <span class="breadcrumb-sep"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
        <a href="{{ route('noticias.index') }}">Notícias</a>
        <span class="breadcrumb-sep"><i class="fas fa-chevron-right" style="font-size:10px;"></i></span>
        <span style="color:#334155;">{{ Str::limit($noticia->titulo, 60) }}</span>
    </div>
</div>

{{-- ARTIGO --}}
<div class="artigo-wrap">

    {{-- Coluna principal --}}
    <article class="artigo-principal">

        {{-- Imagem --}}
        @if($noticia->image_path)
            <img src="{{ Storage::url($noticia->image_path) }}"
                 alt="{{ $noticia->texto_alternativo ?? $noticia->titulo }}"
                 class="artigo-imagem">
            @if($noticia->legenda_imagem)
                <div class="artigo-legenda">
                    <i class="fas fa-camera"></i> {{ $noticia->legenda_imagem }}
                </div>
            @endif
        @else
            <div class="artigo-imagem-placeholder">
                <i class="fas fa-newspaper"></i>
            </div>
        @endif

        <div class="artigo-corpo">

            @if($noticia->destacar)
                <div class="artigo-badge">
                    <i class="fas fa-star"></i> Notícia em Destaque
                </div>
            @endif

            <h1 class="artigo-titulo">{{ $noticia->titulo }}</h1>

            <div class="artigo-meta">
                <span>
                    <i class="fas fa-calendar-alt"></i>
                    {{ $noticia->publicado_em?->format('d \d\e F \d\e Y') ?? '—' }}
                </span>
                <span>
                    <i class="fas fa-eye"></i>
                    {{ number_format($noticia->visualizacoes ?? 0) }} visualizações
                </span>
                <span style="margin-left:auto; padding:3px 10px; border-radius:20px;
                             font-size:11px; font-weight:600;
                             background:#f0fdf4; color:#15803d; border:0.5px solid #bbf7d0;">
                    <i class="fas fa-check-circle"></i> Publicado
                </span>
            </div>

            <div class="artigo-conteudo">
                {!! nl2br(e($noticia->conteudo)) !!}
            </div>
        </div>

        <div class="artigo-rodape">
            <a href="{{ route('noticias.index') }}" class="btn-voltar">
                <i class="fas fa-arrow-left"></i> Voltar às Notícias
            </a>
            <div style="font-size:12.5px; color:#94a3b8;">
                Publicado em {{ $noticia->publicado_em?->format('d/m/Y \à\s H:i') ?? '—' }}
            </div>
        </div>
    </article>

    {{-- Sidebar --}}
    <aside class="artigo-sidebar">

        @if(isset($noticiasRelacionadas) && $noticiasRelacionadas->count() > 0)
        <div class="sidebar-card">
            <div class="sidebar-card-titulo">
                <i class="fas fa-link"></i> Notícias Relacionadas
            </div>
            @foreach($noticiasRelacionadas as $rel)
            <a href="{{ route('noticias.show', $rel->uuid ?? $rel->id) }}" class="rel-item">
                @if($rel->image_path)
                    <img src="{{ Storage::url($rel->image_path) }}"
                         alt="{{ $rel->titulo }}"
                         class="rel-img">
                @else
                    <div class="rel-img-placeholder">
                        <i class="fas fa-newspaper"></i>
                    </div>
                @endif
                <div>
                    <div class="rel-titulo">{{ Str::limit($rel->titulo, 60) }}</div>
                    <div class="rel-data">
                        <i class="fas fa-calendar-alt" style="color:var(--ouro);"></i>
                        {{ $rel->publicado_em?->format('d/m/Y') ?? '—' }}
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @endif

        {{-- Card informativo --}}
        <div class="sidebar-card">
            <div class="sidebar-card-titulo">
                <i class="fas fa-info-circle"></i> Sobre a Ordem
            </div>
            <div style="padding:18px;">
                <p style="font-size:13.5px; color:var(--muted); line-height:1.7; margin-bottom:16px;">
                    A ORDEPDITA é a entidade responsável pela regulação e valorização da classe técnica em Angola.
                </p>
                <a href="{{ route('login') }}"
                   style="display:block; padding:10px; text-align:center; border-radius:8px;
                          background:var(--azul); color:white; font-size:13.5px; font-weight:600;
                          text-decoration:none; transition:background 0.15s;"
                   onmouseover="this.style.background='var(--azul-med)'"
                   onmouseout="this.style.background='var(--azul)'">
                    <i class="fas fa-sign-in-alt"></i> Área de Membro
                </a>
            </div>
        </div>

    </aside>
</div>

{{-- FOOTER --}}
<footer class="pub-footer">
    <div class="pub-footer-inner">
        <div class="footer-marca">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:36px; height:36px; background:var(--ouro); border-radius:8px;
                            display:flex; align-items:center; justify-content:center;
                            font-size:16px; color:var(--azul);">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div style="font-family:'Playfair Display',serif; font-size:15px;
                            font-weight:700; color:var(--branco);">ORDEPDITA</div>
            </div>
            <p>Ordem dos Técnicos de Angola — regulação, valorização e defesa da classe técnica nacional.</p>
        </div>
        <div>
            <div class="footer-titulo">Navegação</div>
            <ul class="footer-links">
                <li><a href="{{ route('home') }}">Início</a></li>
                <li><a href="{{ route('noticias.index') }}">Notícias</a></li>
                <li><a href="#">Sobre a Ordem</a></li>
                <li><a href="#">Serviços</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-titulo">Área de Membro</div>
            <ul class="footer-links">
                <li><a href="{{ route('login') }}">Entrar</a></li>
                <li><a href="#">Registar</a></li>
                <li><a href="#">Os meus pedidos</a></li>
                <li><a href="#">Suporte</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-titulo">Contacto</div>
            <div class="footer-contacto">
                <div class="footer-contacto-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>Luanda, República de Angola</span>
                </div>
                <div class="footer-contacto-item">
                    <i class="fas fa-phone"></i>
                    <span>+244 900 000 000</span>
                </div>
                <div class="footer-contacto-item">
                    <i class="fas fa-envelope"></i>
                    <span>geral@ordepdita.ao</span>
                </div>
            </div>
        </div>
    </div>
    <div class="pub-footer-inner footer-bottom">
        <p>© {{ date('Y') }} ORDEPDITA — Todos os direitos reservados.</p>
        <div style="display:flex; gap:20px;">
            <a href="#">Política de Privacidade</a>
            <a href="#">Termos de Uso</a>
        </div>
    </div>
</footer>

@endsection