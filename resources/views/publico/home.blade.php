@extends("layouts.app")

@section("title", "Associação do Ordem dos Técnicos de Diagnóstico e Terapeutas de Angola")

@section("content")

@push('styles')
    @vite('resources/css/publico/home.css')
@endpush

{{-- ═══════════════════════════════════════════ --}}
{{-- CARROSSEL HERO                              --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="hero-carousel" id="heroCarousel">
    <div class="carousel-slides" id="carouselSlides">

        <div class="carousel-slide">
            <img src="{{ asset('images/carrossel/7.png') }}" alt="Slide 1" class="carousel-img-bg">
            <div class="carousel-overlay"></div>
        </div>

        <div class="carousel-slide">
            <img src="{{ asset('images/carrossel/8.png') }}" alt="Slide 2" class="carousel-img-bg">
            <div class="carousel-overlay"></div>
        </div>

        <div class="carousel-slide">
            <img src="{{ asset('images/carrossel/9.png') }}" alt="Slide 3" class="carousel-img-bg">
            <div class="carousel-overlay"></div>
        </div>

    </div>

    <button class="carousel-prev" onclick="moveCarousel(-1)" aria-label="Anterior"><i class="fas fa-chevron-left"></i></button>
    <button class="carousel-next" onclick="moveCarousel(1)" aria-label="Seguinte"><i class="fas fa-chevron-right"></i></button>

    <div class="carousel-dots">
        <button class="carousel-dot ativo" onclick="goToSlide(0)"></button>
        <button class="carousel-dot" onclick="goToSlide(1)"></button>
        <button class="carousel-dot" onclick="goToSlide(2)"></button>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- SERVIÇOS                                    --}}
{{-- ═══════════════════════════════════════════ --}}
<section class="secao-servicos">
    <div class="secao-servicos-inner">
        <div style="text-align:center; margin-bottom:8px;">
            <div class="secao-label"><i class="fas fa-cogs"></i> O que fazemos</div>
            <div class="secao-titulo">Os nossos Serviços</div>
            <p style="font-size:15px; color:var(--muted); max-width:560px; margin:12px auto 0; line-height:1.7;">
                A AATDSPA disponibiliza um conjunto de serviços para os profissionais técnicos angolanos.
            </p>
        </div>

        <div class="servicos-grid">

            <a href="{{ route('pedido.licenca.form') }}" class="servico-card">
                <div class="servico-icone" style="background:linear-gradient(135deg,#c8922a,#e8b84b);">
                    <i class="fas fa-certificate"></i>
                </div>
                <div class="servico-titulo">Licença Profissional</div>
                <div class="servico-desc">Licença anual obrigatória para o exercício da actividade técnica de diagnóstico e terapêutica.</div>
                <div class="servico-link">Solicitar <i class="fas fa-arrow-right" style="font-size:11px;"></i></div>
            </a>

            <a href="{{ route('consulta.form') }}" class="servico-card">
                <div class="servico-icone" style="background:linear-gradient(135deg,#0c4a8b,#1a6acc);">
                    <i class="fas fa-search"></i>
                </div>
                <div class="servico-titulo">Consultar Estado</div>
                <div class="servico-desc">Acompanhe o estado do seu pedido em tempo real, de forma simples e transparente.</div>
                <div class="servico-link">Consultar <i class="fas fa-arrow-right" style="font-size:11px;"></i></div>
            </a>

            <a href="{{ route('login') }}" class="servico-card">
                <div class="servico-icone" style="background:linear-gradient(135deg,#15803d,#16a34a);">
                    <i class="fas fa-user-check"></i>
                </div>
                <div class="servico-titulo">Área de Membro</div>
                <div class="servico-desc">Aceda à sua área pessoal, gira os seus pedidos e documentos em qualquer lugar.</div>
                <div class="servico-link">Entrar <i class="fas fa-arrow-right" style="font-size:11px;"></i></div>
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════ --}}
{{-- ESTATÍSTICAS                                --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="faixa-stats">
    <div class="faixa-stats-inner">
        <div class="stat-item">
            <div class="stat-numero" data-target="2500">0</div>
            <div class="stat-label">Membros Registados</div>
        </div>
        <div class="stat-item">
            <div class="stat-numero" data-target="1800">0</div>
            <div class="stat-label">Carteiras Emitidas</div>
        </div>
        <div class="stat-item">
            <div class="stat-numero" data-target="12">0</div>
            <div class="stat-label">Províncias</div>
        </div>
        <div class="stat-item">
            <div class="stat-numero" data-target="15">0</div>
            <div class="stat-label">Anos de Actividade</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════ --}}
{{-- NOTÍCIAS — FITA HORIZONTAL ANIMADA          --}}
{{-- ═══════════════════════════════════════════ --}}
<section class="secao-noticias">
    <div class="secao-noticias-header">
        <div>
            <div class="secao-label"><i class="fas fa-newspaper"></i> Publicações</div>
            <div class="secao-titulo">Últimas Notícias</div>
        </div>
        <a href="{{ route('noticias.index') }}" class="link-ver-todas">
            Ver todas <i class="fas fa-arrow-right"></i>
        </a>
    </div>

    @if(isset($noticias) && $noticias->count() > 0)
    <div class="noticias-fita-wrap">
        <div class="noticias-fita" id="noticiasFita">
            {{-- Loop original --}}
            @foreach($noticias as $noticia)
            <a href="{{ route('noticias.show', $noticia->uuid ?? $noticia->id) }}"
               class="noticia-fita-card">
                @if($noticia->hasImagem())
                    <img src="{{ $noticia->imagem_url }}"
                         alt="{{ $noticia->texto_alternativo ?? $noticia->titulo }}"
                         class="noticia-fita-img"
                         loading="lazy">
                @else
                    <div class="noticia-fita-img-placeholder">
                        <i class="fas fa-newspaper"></i>
                    </div>
                @endif
                <div class="noticia-fita-corpo">
                    <div class="noticia-fita-data">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $noticia->data_publicacao_amigavel }}
                    </div>
                    <div class="noticia-fita-titulo">{{ $noticia->titulo_limitado }}</div>
                    <div class="noticia-fita-excerto">{{ $noticia->resumo }}</div>
                    <div class="noticia-fita-ler">Ler mais <i class="fas fa-arrow-right" style="font-size:10px;"></i></div>
                </div>
            </a>
            @endforeach

            {{-- Duplicado para o loop infinito (só se houver mais de 1) --}}
            @if($noticias->count() > 1)
                @foreach($noticias as $noticia)
                <a href="{{ route('noticias.show', $noticia->uuid ?? $noticia->id) }}"
                   class="noticia-fita-card">
                    @if($noticia->hasImagem())
                        <img src="{{ $noticia->imagem_url }}"
                             alt="{{ $noticia->texto_alternativo ?? $noticia->titulo }}"
                             class="noticia-fita-img"
                             loading="lazy">
                    @else
                        <div class="noticia-fita-img-placeholder">
                            <i class="fas fa-newspaper"></i>
                        </div>
                    @endif
                    <div class="noticia-fita-corpo">
                        <div class="noticia-fita-data">
                            <i class="fas fa-calendar-alt"></i>
                            {{ $noticia->data_publicacao_amigavel }}
                        </div>
                        <div class="noticia-fita-titulo">{{ $noticia->titulo_limitado }}</div>
                        <div class="noticia-fita-excerto">{{ $noticia->resumo }}</div>
                        <div class="noticia-fita-ler">Ler mais <i class="fas fa-arrow-right" style="font-size:10px;"></i></div>
                    </div>
                </a>
                @endforeach
            @endif
        </div>
    </div>
    @else
    <div style="text-align:center; padding:48px 24px; color:var(--muted);">
        <i class="fas fa-newspaper" style="font-size:40px; margin-bottom:16px; display:block; color:var(--cinza-b);"></i>
        Nenhuma notícia publicada ainda.
    </div>
    @endif
</section>

{{-- ═══════════════════════════════════════════ --}}
{{-- DESTAQUES                                   --}}
{{-- ═══════════════════════════════════════════ --}}
@if(isset($noticiasEmDestaque) && $noticiasEmDestaque->count() > 0)
<section class="secao-destaques">
    <div class="secao-destaques-inner">
        <div style="display:flex; align-items:flex-end; justify-content:space-between; margin-bottom:36px; flex-wrap:wrap; gap:16px;">
            <div>
                <div class="secao-label"><i class="fas fa-star"></i> Em destaque</div>
                <div class="secao-titulo">Notícias em Destaque</div>
            </div>
            <a href="{{ route('noticias.index') }}" class="link-ver-todas">
                Ver todas <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid-destaques">
            @php $primeira = $noticiasEmDestaque->first(); @endphp

            <a href="{{ route('noticias.show', $primeira->uuid ?? $primeira->id) }}" class="card-destaque-grande">
                @if($primeira->hasImagem())
                    <img src="{{ $primeira->imagem_url }}"
                         alt="{{ $primeira->texto_alternativo ?? $primeira->titulo }}"
                         class="card-img"
                         loading="lazy">
                @else
                    <div class="card-img-placeholder"><i class="fas fa-newspaper"></i></div>
                @endif
                <div class="card-corpo">
                    <div class="badge-destaque"><i class="fas fa-star"></i> Destaque</div>
                    <div class="card-titulo-grande">{{ $primeira->titulo }}</div>
                    <div class="card-excerto">{{ Str::limit(strip_tags($primeira->conteudo), 160) }}</div>
                    <div class="card-meta">
                        <span><i class="fas fa-calendar-alt"></i> {{ $primeira->data_publicacao_formatada }}</span>
                        <span><i class="fas fa-eye"></i> {{ number_format($primeira->visualizacoes ?? 0) }} visualizações</span>
                    </div>
                </div>
            </a>

            @foreach($noticiasEmDestaque->skip(1) as $noticia)
            <a href="{{ route('noticias.show', $noticia->uuid ?? $noticia->id) }}" class="card-destaque-pequeno">
                @if($noticia->hasImagem())
                    <img src="{{ $noticia->imagem_url }}"
                         alt="{{ $noticia->texto_alternativo ?? $noticia->titulo }}"
                         class="card-img"
                         loading="lazy">
                @else
                    <div class="card-img-placeholder"><i class="fas fa-newspaper"></i></div>
                @endif
                <div class="card-corpo">
                    <div class="card-titulo-pequeno">{{ $noticia->titulo_limitado }}</div>
                    <div class="card-excerto" style="font-size:12.5px; margin-bottom:8px;">
                        {{ Str::limit(strip_tags($noticia->conteudo), 80) }}
                    </div>
                    <div class="card-meta">
                        <span><i class="fas fa-calendar-alt"></i> {{ $noticia->data_publicacao_formatada }}</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════ --}}
{{-- BANNER CTA                                  --}}
{{-- ═══════════════════════════════════════════ --}}
<section class="banner-cta">
    <h2>Pronto para fazer parte da Ordem?</h2>
    <p>Aceda à área de membros, requeira os seus documentos e acompanhe todos os processos em tempo real.</p>
    <div class="banner-cta-btns">
        <a href="{{ route('login') }}" class="btn-slide-prim">
            <i class="fas fa-sign-in-alt"></i> Entrar na plataforma
        </a>
        <a href="{{ route('sobre') }}" class="btn-slide-sec">
            <i class="fas fa-info-circle"></i> Saber mais
        </a>
    </div>
</section>

@endsection

@push("scripts")
@php
$imagensCarrossel = [
    asset('images/carrossel/1.jpg'),
    asset('images/carrossel/2.jpg'),
    asset('images/carrossel/3.png'),
    asset('images/carrossel/4.jpg'),
    asset('images/carrossel/5.jpg'),
    asset('images/carrossel/6.jpg'),
    asset('images/carrossel/7.png'),
];
@endphp
<script>
    // ── Carrossel ─────────────────────────────
    const imagensCarrossel = @json($imagensCarrossel);
    let slideAtual = 0;
    const totalSlides = 3;
    let autoplayTimer;
    let imagemPorSlot = [0, 2, 4];

    function atualizarCarrossel() {
        const wrap = document.getElementById('carouselSlides');
        if (!wrap) return;
        wrap.style.transform = `translateX(-${slideAtual * 100}%)`;
        document.querySelectorAll('.carousel-dot').forEach((d, i) => d.classList.toggle('ativo', i === slideAtual));
        document.querySelectorAll('.carousel-content-inner').forEach((c, i) => {
            c.style.animation = 'none';
            if (i === slideAtual) requestAnimationFrame(() => { c.style.animation = 'fadeSlideUp 0.9s ease both'; });
        });
    }

    function moveCarousel(dir) {
        const slides = document.querySelectorAll('.carousel-slide');
        if (dir === 1) {
            const sai = slideAtual;
            slideAtual = (slideAtual + 1) % totalSlides;
            imagemPorSlot[sai] = (imagemPorSlot[sai] + totalSlides) % imagensCarrossel.length;
            const imgEl = slides[sai]?.querySelector('.carousel-img-bg');
            if (imgEl) imgEl.src = imagensCarrossel[imagemPorSlot[sai]];
        } else {
            slideAtual = (slideAtual - 1 + totalSlides) % totalSlides;
        }
        atualizarCarrossel();
        resetAutoplay();
    }

    function goToSlide(n) { slideAtual = n; atualizarCarrossel(); resetAutoplay(); }

    function resetAutoplay() {
        clearInterval(autoplayTimer);
        autoplayTimer = setInterval(() => moveCarousel(1), 5500);
    }

    resetAutoplay();

    const carEl = document.getElementById('heroCarousel');
    if (carEl) {
        let tx = 0;
        carEl.addEventListener('touchstart', e => { tx = e.touches[0].clientX; });
        carEl.addEventListener('touchend', e => {
            const d = tx - e.changedTouches[0].clientX;
            if (Math.abs(d) > 40) moveCarousel(d > 0 ? 1 : -1);
        });
    }

    // ── Contador animado das estatísticas ─────
    function animarContadores() {
        document.querySelectorAll('.stat-numero[data-target]').forEach(el => {
            const target = parseInt(el.dataset.target);
            const dur = 1800;
            const start = performance.now();
            function step(now) {
                const p = Math.min((now - start) / dur, 1);
                const ease = p < 0.5 ? 2*p*p : -1+(4-2*p)*p;
                el.textContent = Math.floor(ease * target).toLocaleString('pt-PT');
                if (p < 1) requestAnimationFrame(step);
                else el.textContent = target.toLocaleString('pt-PT') + '+';
            }
            requestAnimationFrame(step);
        });
    }

    const statsEl = document.querySelector('.faixa-stats');
    if (statsEl) {
        const obs = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting) { animarContadores(); obs.disconnect(); }
        }, { threshold: 0.3 });
        obs.observe(statsEl);
    }
</script>
@endpush