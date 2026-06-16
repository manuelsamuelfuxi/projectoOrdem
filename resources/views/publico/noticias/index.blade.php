@extends("layouts.app")

@section("title", "ORDEPDITA — Ordem dos Técnicos de Angola")

@section("content")

{{-- ═══════════════════════════════════════════ --}}
{{-- ESTILOS GLOBAIS DA PARTE PÚBLICA            --}}
{{-- ═══════════════════════════════════════════ --}}
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
        --sombra-h:   0 8px 40px rgba(12,45,107,0.18);
    }

    body {
        font-family: 'Source Sans 3', sans-serif;
        background: var(--branco);
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

    .pub-nav-sub {
        font-size: 10.5px;
        color: rgba(255,255,255,0.55);
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .pub-nav-links {
        display: flex;
        align-items: center;
        gap: 4px;
        list-style: none;
    }

    .pub-nav-links a {
        padding: 7px 14px;
        border-radius: 7px;
        font-size: 13.5px;
        font-weight: 500;
        color: rgba(255,255,255,0.80);
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }

    .pub-nav-links a:hover,
    .pub-nav-links a.ativo {
        background: rgba(255,255,255,0.12);
        color: var(--branco);
    }

    .pub-nav-cta {
        padding: 8px 18px !important;
        background: var(--ouro) !important;
        color: var(--azul) !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        transition: background 0.15s !important;
    }

    .pub-nav-cta:hover {
        background: var(--ouro-claro) !important;
    }

    /* ─── HERO ─────────────────────────────────── */
    .hero {
        background: linear-gradient(135deg, var(--azul) 0%, var(--azul-med) 60%, #1e5fcc 100%);
        padding: 80px 24px 72px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    .hero-linha {
        width: 48px; height: 3px;
        background: var(--ouro);
        border-radius: 2px;
        margin: 0 auto 20px;
    }

    .hero h1 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(28px, 5vw, 48px);
        font-weight: 700;
        color: var(--branco);
        line-height: 1.2;
        margin-bottom: 16px;
        position: relative;
    }

    .hero p {
        font-size: 17px;
        color: rgba(255,255,255,0.75);
        max-width: 560px;
        margin: 0 auto 32px;
        line-height: 1.7;
        position: relative;
    }

    .hero-btns {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        position: relative;
    }

    .btn-hero-prim {
        padding: 13px 28px;
        background: var(--ouro);
        color: var(--azul);
        border-radius: 10px;
        font-size: 14.5px;
        font-weight: 700;
        text-decoration: none;
        transition: background 0.15s, transform 0.15s;
        letter-spacing: 0.01em;
    }

    .btn-hero-prim:hover { background: var(--ouro-claro); transform: translateY(-1px); }

    .btn-hero-sec {
        padding: 13px 28px;
        background: rgba(255,255,255,0.12);
        color: var(--branco);
        border-radius: 10px;
        font-size: 14.5px;
        font-weight: 600;
        text-decoration: none;
        border: 1px solid rgba(255,255,255,0.25);
        transition: background 0.15s;
    }

    .btn-hero-sec:hover { background: rgba(255,255,255,0.20); }

    /* ─── SECÇÕES ─────────────────────────────── */
    .secao {
        max-width: 1180px;
        margin: 0 auto;
        padding: 64px 24px;
    }

    .secao-topo {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        margin-bottom: 36px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .secao-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--ouro);
        text-transform: uppercase;
        letter-spacing: 0.1em;
        margin-bottom: 6px;
    }

    .secao-titulo {
        font-family: 'Playfair Display', serif;
        font-size: clamp(22px, 3vw, 30px);
        font-weight: 700;
        color: var(--azul);
        line-height: 1.2;
    }

    .link-ver-todas {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--azul-claro);
        text-decoration: none;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: gap 0.15s;
    }

    .link-ver-todas:hover { gap: 10px; }

    /* ─── CARD DESTAQUE (grande) ──────────────── */
    .grid-destaques {
        display: grid;
        grid-template-columns: 1fr 1fr;
        grid-template-rows: auto auto;
        gap: 20px;
    }

    .card-destaque-grande {
        grid-row: span 2;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: var(--sombra);
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        background: var(--branco);
        border: 0.5px solid var(--cinza-b);
        transition: box-shadow 0.2s, transform 0.2s;
    }

    .card-destaque-grande:hover {
        box-shadow: var(--sombra-h);
        transform: translateY(-3px);
    }

    .card-destaque-grande .card-img {
        height: 300px;
        object-fit: cover;
        width: 100%;
    }

    .card-destaque-grande .card-img-placeholder {
        height: 300px;
        background: linear-gradient(135deg, var(--cinza-b), var(--cinza-f));
        display: flex; align-items: center; justify-content: center;
        color: var(--cinza-b); font-size: 48px;
    }

    .card-destaque-grande .card-corpo {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    /* ─── CARD DESTAQUE (pequeno) ─────────────── */
    .card-destaque-pequeno {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--sombra);
        text-decoration: none;
        color: inherit;
        display: flex;
        background: var(--branco);
        border: 0.5px solid var(--cinza-b);
        transition: box-shadow 0.2s, transform 0.2s;
    }

    .card-destaque-pequeno:hover {
        box-shadow: var(--sombra-h);
        transform: translateY(-2px);
    }

    .card-destaque-pequeno .card-img {
        width: 130px;
        min-width: 130px;
        object-fit: cover;
    }

    .card-destaque-pequeno .card-img-placeholder {
        width: 130px;
        min-width: 130px;
        background: linear-gradient(135deg, var(--cinza-b), var(--cinza-f));
        display: flex; align-items: center; justify-content: center;
        color: #c8d3e8; font-size: 28px;
    }

    .card-destaque-pequeno .card-corpo {
        padding: 16px 18px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* ─── BADGE STATUS ───────────────────────── */
    .badge-destaque {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        background: #fef3c7;
        color: #92400e;
        border: 0.5px solid #fde68a;
        margin-bottom: 10px;
        width: fit-content;
    }

    /* ─── TÍTULOS E EXCERTOS DOS CARDS ──────── */
    .card-titulo-grande {
        font-family: 'Playfair Display', serif;
        font-size: 20px;
        font-weight: 700;
        color: var(--azul);
        line-height: 1.35;
        margin-bottom: 10px;
    }

    .card-titulo-pequeno {
        font-family: 'Playfair Display', serif;
        font-size: 15px;
        font-weight: 700;
        color: var(--azul);
        line-height: 1.35;
        margin-bottom: 6px;
    }

    .card-excerto {
        font-size: 13.5px;
        color: var(--muted);
        line-height: 1.6;
        flex: 1;
        margin-bottom: 14px;
    }

    .card-meta {
        font-size: 12px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .card-meta i { color: var(--ouro); }

    /* ─── SEPARADOR ──────────────────────────── */
    .separador {
        background: var(--cinza-f);
        border-top: 0.5px solid var(--cinza-b);
        border-bottom: 0.5px solid var(--cinza-b);
    }

    /* ─── GRID DE NOTÍCIAS ───────────────────── */
    .grid-noticias {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
    }

    .card-noticia {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: var(--sombra);
        background: var(--branco);
        border: 0.5px solid var(--cinza-b);
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        transition: box-shadow 0.2s, transform 0.2s;
    }

    .card-noticia:hover {
        box-shadow: var(--sombra-h);
        transform: translateY(-3px);
    }

    .card-noticia .card-img {
        height: 180px;
        object-fit: cover;
        width: 100%;
    }

    .card-noticia .card-img-placeholder {
        height: 180px;
        background: linear-gradient(135deg, var(--cinza-b), var(--cinza-f));
        display: flex; align-items: center; justify-content: center;
        color: #c8d3e8; font-size: 36px;
    }

    .card-noticia .card-corpo {
        padding: 18px 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    /* ─── PAGINAÇÃO ──────────────────────────── */
    .paginacao {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .pag-btn {
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13.5px;
        font-weight: 500;
        color: var(--azul);
        background: var(--branco);
        border: 0.5px solid var(--cinza-b);
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }

    .pag-btn:hover { background: var(--cinza-f); }
    .pag-btn.ativo { background: var(--azul); color: var(--branco); border-color: var(--azul); }
    .pag-btn.desativo { color: #c8d3e8; pointer-events: none; }

    /* ─── BANNER INSTITUCIONAL ───────────────── */
    .banner-inst {
        background: linear-gradient(135deg, var(--azul) 0%, var(--azul-med) 100%);
        padding: 56px 24px;
        text-align: center;
    }

    .banner-inst h2 {
        font-family: 'Playfair Display', serif;
        font-size: clamp(22px, 3vw, 32px);
        font-weight: 700;
        color: var(--branco);
        margin-bottom: 12px;
    }

    .banner-inst p {
        font-size: 15px;
        color: rgba(255,255,255,0.72);
        max-width: 540px;
        margin: 0 auto 28px;
        line-height: 1.7;
    }

    /* ─── FOOTER ─────────────────────────────── */
    .pub-footer {
        background: #07193a;
        color: rgba(255,255,255,0.75);
        padding: 56px 24px 0;
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

    .footer-links {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 9px;
    }

    .footer-links a {
        font-size: 13.5px;
        color: rgba(255,255,255,0.60);
        text-decoration: none;
        transition: color 0.15s;
    }

    .footer-links a:hover { color: var(--branco); }

    .footer-contacto {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .footer-contacto-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13.5px;
        color: rgba(255,255,255,0.60);
    }

    .footer-contacto-item i {
        color: var(--ouro);
        margin-top: 2px;
        flex-shrink: 0;
        width: 14px;
    }

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

    .footer-bottom p {
        font-size: 12.5px;
        color: rgba(255,255,255,0.35);
    }

    .footer-bottom a {
        color: rgba(255,255,255,0.45);
        text-decoration: none;
        font-size: 12.5px;
        transition: color 0.15s;
    }

    .footer-bottom a:hover { color: rgba(255,255,255,0.80); }

    /* ─── RESPONSIVO ─────────────────────────── */
    @media (max-width: 900px) {
        .grid-destaques { grid-template-columns: 1fr; }
        .card-destaque-grande { grid-row: auto; }
        .grid-noticias { grid-template-columns: repeat(2, 1fr); }
        .pub-footer-inner { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 600px) {
        .pub-nav-links { display: none; }
        .grid-noticias { grid-template-columns: 1fr; }
        .pub-footer-inner { grid-template-columns: 1fr; }
        .footer-bottom { flex-direction: column; text-align: center; }
    }
</style>

{{-- ═══════════════════════════════════════════ --}}
{{-- NAVBAR                                      --}}
{{-- ═══════════════════════════════════════════ --}}
<nav class="pub-nav">
    <div class="pub-nav-inner">
        <a href="{{ route('home') }}" class="pub-nav-brand">
            <div class="pub-nav-logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div>
                <div class="pub-nav-nome">ORDEPDITA</div>
                <div class="pub-nav-sub">Ordem dos Técnicos</div>
            </div>
        </a>

        <ul class="pub-nav-links">
            <li><a href="{{ route('home') }}" class="ativo">Início</a></li>
            <li><a href="{{ route('noticias.index') }}">Notícias</a></li>
            <li><a href="#">Sobre</a></li>
            <li><a href="#">Serviços</a></li>
            <li><a href="#">Contacto</a></li>
            @auth
                <li>
                    @if(auth()->user()->isAdmin() || auth()->user()->isSuperAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="pub-nav-cta">
                            <i class="fas fa-tachometer-alt"></i> Painel
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="pub-nav-cta">
                            <i class="fas fa-user"></i> Área pessoal
                        </a>
                    @endif
                </li>
            @else
                <li><a href="{{ route('login') }}" class="pub-nav-cta">
                    <i class="fas fa-sign-in-alt"></i> Entrar
                </a></li>
            @endauth
        </ul>
    </div>
</nav>

{{-- ═══════════════════════════════════════════ --}}
{{-- HERO                                        --}}
{{-- ═══════════════════════════════════════════ --}}
<section class="hero">
    <div class="hero-linha"></div>
    <h1>Ordem dos Técnicos de Angola</h1>
    <p>Regulação, valorização e defesa da classe técnica angolana. Consulte as nossas notícias e fique a par de todas as novidades institucionais.</p>
    <div class="hero-btns">
        <a href="{{ route('noticias.index') }}" class="btn-hero-prim">
            <i class="fas fa-newspaper"></i> Ver todas as notícias
        </a>
        <a href="{{ route('login') }}" class="btn-hero-sec">
            <i class="fas fa-user"></i> Área de membro
        </a>
    </div>
</section>

{{-- ═══════════════════════════════════════════ --}}
{{-- NOTÍCIAS EM DESTAQUE                        --}}
{{-- ═══════════════════════════════════════════ --}}
@if($noticiasEmDestaque->count() > 0)
<section class="secao">
    <div class="secao-topo">
        <div>
            <div class="secao-label"><i class="fas fa-star"></i> Em destaque</div>
            <div class="secao-titulo">Notícias de Destaque</div>
        </div>
        <a href="{{ route('noticias.index') }}" class="link-ver-todas">
            Ver todas <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    <div class="grid-destaques">
        {{-- Card grande (1.ª notícia) --}}
        @php $primeira = $noticiasEmDestaque->first(); @endphp
        <a href="{{ route('noticias.show', $primeira->uuid ?? $primeira->id) }}" class="card-destaque-grande">
            @if($primeira->image_path)
                <img src="{{ Storage::url($primeira->image_path) }}"
                     alt="{{ $primeira->texto_alternativo ?? $primeira->titulo }}"
                     class="card-img">
            @else
                <div class="card-img-placeholder">
                    <i class="fas fa-newspaper"></i>
                </div>
            @endif
            <div class="card-corpo">
                <div class="badge-destaque">
                    <i class="fas fa-star"></i> Destaque
                </div>
                <div class="card-titulo-grande">{{ $primeira->titulo }}</div>
                <div class="card-excerto">
                    {{ Str::limit(strip_tags($primeira->conteudo), 160) }}
                </div>
                <div class="card-meta">
                    <span><i class="fas fa-calendar-alt"></i>
                        {{ $primeira->publicado_em?->format('d/m/Y') ?? '—' }}
                    </span>
                    <span><i class="fas fa-eye"></i>
                        {{ number_format($primeira->visualizacoes ?? 0) }} visualizações
                    </span>
                </div>
            </div>
        </a>

        {{-- Cards pequenos (restantes destaques) --}}
        @foreach($noticiasEmDestaque->skip(1) as $noticia)
        <a href="{{ route('noticias.show', $noticia->uuid ?? $noticia->id) }}" class="card-destaque-pequeno">
            @if($noticia->image_path)
                <img src="{{ Storage::url($noticia->image_path) }}"
                     alt="{{ $noticia->texto_alternativo ?? $noticia->titulo }}"
                     class="card-img">
            @else
                <div class="card-img-placeholder">
                    <i class="fas fa-newspaper"></i>
                </div>
            @endif
            <div class="card-corpo">
                <div class="card-titulo-pequeno">{{ $noticia->titulo }}</div>
                <div class="card-excerto" style="font-size:12.5px; margin-bottom:8px;">
                    {{ Str::limit(strip_tags($noticia->conteudo), 80) }}
                </div>
                <div class="card-meta">
                    <span><i class="fas fa-calendar-alt"></i>
                        {{ $noticia->publicado_em?->format('d/m/Y') ?? '—' }}
                    </span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════ --}}
{{-- TODAS AS NOTÍCIAS                           --}}
{{-- ═══════════════════════════════════════════ --}}
<section class="separador">
    <div class="secao">
        <div class="secao-topo">
            <div>
                <div class="secao-label"><i class="fas fa-newspaper"></i> Publicações</div>
                <div class="secao-titulo">Últimas Notícias</div>
            </div>
            <span style="font-size:13px; color:var(--muted);">
                {{ $noticias->total() }} notícia(s) publicada(s)
            </span>
        </div>

        @forelse($noticias as $noticia)
        {{-- Primeira iteração: linha de 3 cards --}}
        @if($loop->first)
        <div class="grid-noticias">
        @endif

        <a href="{{ route('noticias.show', $noticia->uuid ?? $noticia->id) }}" class="card-noticia">
            @if($noticia->image_path)
                <img src="{{ Storage::url($noticia->image_path) }}"
                     alt="{{ $noticia->texto_alternativo ?? $noticia->titulo }}"
                     class="card-img">
            @else
                <div class="card-img-placeholder">
                    <i class="fas fa-newspaper"></i>
                </div>
            @endif
            <div class="card-corpo">
                @if($noticia->destacar)
                    <div class="badge-destaque" style="margin-bottom:8px;">
                        <i class="fas fa-star"></i> Destaque
                    </div>
                @endif
                <div class="card-titulo-pequeno" style="font-size:16px; margin-bottom:8px;">
                    {{ $noticia->titulo }}
                </div>
                <div class="card-excerto">
                    {{ Str::limit(strip_tags($noticia->conteudo), 110) }}
                </div>
                <div class="card-meta">
                    <span><i class="fas fa-calendar-alt"></i>
                        {{ $noticia->publicado_em?->format('d/m/Y') ?? '—' }}
                    </span>
                    <span><i class="fas fa-eye"></i>
                        {{ number_format($noticia->visualizacoes ?? 0) }}
                    </span>
                </div>
            </div>
        </a>

        @if($loop->last)
        </div>
        @endif

        @empty
        <div style="text-align:center; padding:48px 0; color:var(--muted);">
            <i class="fas fa-newspaper" style="font-size:40px; margin-bottom:16px; display:block; color:var(--cinza-b);"></i>
            Nenhuma notícia disponível no momento.
        </div>
        @endforelse

        {{-- Paginação --}}
        @if($noticias->hasPages())
        <div class="paginacao">
            @if($noticias->onFirstPage())
                <span class="pag-btn desativo"><i class="fas fa-chevron-left"></i></span>
            @else
                <a href="{{ $noticias->previousPageUrl() }}" class="pag-btn">
                    <i class="fas fa-chevron-left"></i>
                </a>
            @endif

            @foreach($noticias->getUrlRange(1, $noticias->lastPage()) as $page => $url)
                @if($page == $noticias->currentPage())
                    <span class="pag-btn ativo">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                @endif
            @endforeach

            @if($noticias->hasMorePages())
                <a href="{{ $noticias->nextPageUrl() }}" class="pag-btn">
                    <i class="fas fa-chevron-right"></i>
                </a>
            @else
                <span class="pag-btn desativo"><i class="fas fa-chevron-right"></i></span>
            @endif
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════ --}}
{{-- BANNER CTA                                   --}}
{{-- ═══════════════════════════════════════════ --}}
<section class="banner-inst">
    <h2>Faça parte da nossa comunidade</h2>
    <p>Aceda à área de membros, acompanhe os seus pedidos e beneficie de todos os serviços da Ordem.</p>
    <div style="display:flex; align-items:center; justify-content:center; gap:12px; flex-wrap:wrap;">
        <a href="{{ route('login') }}" class="btn-hero-prim">
            <i class="fas fa-sign-in-alt"></i> Entrar na plataforma
        </a>
        <a href="#" class="btn-hero-sec">
            <i class="fas fa-info-circle"></i> Saber mais
        </a>
    </div>
</section>

{{-- ═══════════════════════════════════════════ --}}
{{-- FOOTER                                      --}}
{{-- ═══════════════════════════════════════════ --}}
<footer class="pub-footer">
    <div class="pub-footer-inner">

        {{-- Coluna 1: Marca --}}
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
            <div style="display:flex; gap:10px; margin-top:16px;">
                <a href="#" style="width:34px; height:34px; border-radius:8px;
                                   background:rgba(255,255,255,0.08); display:flex;
                                   align-items:center; justify-content:center;
                                   color:rgba(255,255,255,0.60); text-decoration:none;
                                   font-size:14px; transition:background 0.15s;"
                   onmouseover="this.style.background='rgba(255,255,255,0.16)'"
                   onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <i class="fab fa-facebook-f"></i>
                </a>
                <a href="#" style="width:34px; height:34px; border-radius:8px;
                                   background:rgba(255,255,255,0.08); display:flex;
                                   align-items:center; justify-content:center;
                                   color:rgba(255,255,255,0.60); text-decoration:none;
                                   font-size:14px; transition:background 0.15s;"
                   onmouseover="this.style.background='rgba(255,255,255,0.16)'"
                   onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <i class="fab fa-linkedin-in"></i>
                </a>
                <a href="#" style="width:34px; height:34px; border-radius:8px;
                                   background:rgba(255,255,255,0.08); display:flex;
                                   align-items:center; justify-content:center;
                                   color:rgba(255,255,255,0.60); text-decoration:none;
                                   font-size:14px; transition:background 0.15s;"
                   onmouseover="this.style.background='rgba(255,255,255,0.16)'"
                   onmouseout="this.style.background='rgba(255,255,255,0.08)'">
                    <i class="fab fa-youtube"></i>
                </a>
            </div>
        </div>

        {{-- Coluna 2: Links --}}
        <div>
            <div class="footer-titulo">Navegação</div>
            <ul class="footer-links">
                <li><a href="{{ route('home') }}">Início</a></li>
                <li><a href="{{ route('noticias.index') }}">Notícias</a></li>
                <li><a href="#">Sobre a Ordem</a></li>
                <li><a href="#">Serviços</a></li>
                <li><a href="#">Legislação</a></li>
            </ul>
        </div>

        {{-- Coluna 3: Área de membro --}}
        <div>
            <div class="footer-titulo">Área de Membro</div>
            <ul class="footer-links">
                <li><a href="{{ route('login') }}">Entrar</a></li>
                <li><a href="#">Registar</a></li>
                <li><a href="#">Os meus pedidos</a></li>
                <li><a href="#">Certificados</a></li>
                <li><a href="#">Suporte</a></li>
            </ul>
        </div>

        {{-- Coluna 4: Contacto --}}
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
                <div class="footer-contacto-item">
                    <i class="fas fa-clock"></i>
                    <span>Seg–Sex, 08h–17h</span>
                </div>
            </div>
        </div>

    </div>

    <div class="pub-footer-inner footer-bottom">
        <p>© {{ date('Y') }} ORDEPDITA — Todos os direitos reservados.</p>
        <div style="display:flex; gap:20px;">
            <a href="#">Política de Privacidade</a>
            <a href="#">Termos de Uso</a>
            <a href="#">Acessibilidade</a>
        </div>
    </div>
</footer>

@endsection