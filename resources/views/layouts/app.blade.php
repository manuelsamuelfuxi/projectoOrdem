<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("title", "Associação do Ordem dos Técnicos de Diagnóstico e Terapeutas de Angola")</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
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

        html, body {
            font-family: 'Source Sans 3', sans-serif;
            background: var(--branco);
            color: var(--texto);
            line-height: 1.6;
        }

        /* ══════════════════════════════════════════
           TOP BAR
        ══════════════════════════════════════════ */
        .top-bar {
            background: #07193a;
            padding: 7px 0;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }

        .top-bar-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .top-bar-info {
            display: flex;
            align-items: center;
            gap: 20px;
            font-size: 12px;
            color: rgba(255,255,255,0.50);
        }

        .top-bar-info span { display: flex; align-items: center; gap: 6px; }
        .top-bar-info i { color: var(--ouro); font-size: 11px; }

        .btn-acesso {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 18px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            text-decoration: none;
            background: linear-gradient(90deg, #c8922a 0%, #e8b84b 50%, #2563eb 100%);
            color: #07193a;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.2s;
            white-space: nowrap;
        }

        .btn-acesso:hover { opacity: 0.88; transform: translateY(-1px); color: #07193a; }

        /* Versão compacta só com cadeado para mobile */
        .btn-acesso-icon {
            display: none;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #c8922a, #2563eb);
            color: #07193a;
            border: none;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            transition: opacity 0.2s;
        }

        .btn-acesso-icon:hover { opacity: 0.85; color: #07193a; }

        .top-user-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 600;
            color: rgba(255,255,255,0.85);
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.14);
            cursor: pointer;
            transition: background 0.15s;
        }

        .top-user-btn:hover { background: rgba(255,255,255,0.14); }

        .top-dropdown { position: relative; display: inline-block; }

        .top-dropdown-menu {
            display: none;
            position: absolute;
            right: 0;
            top: calc(100% + 8px);
            background: #07193a;
            border: 0.5px solid rgba(255,255,255,0.12);
            min-width: 200px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
            overflow: hidden;
            z-index: 9999;
        }

        .top-dropdown:hover .top-dropdown-menu,
        .top-dropdown-menu:hover { display: block; }

        .top-dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            font-size: 13px;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            transition: background 0.12s;
            border: none;
            background: none;
            width: 100%;
            cursor: pointer;
            text-align: left;
        }

        .top-dropdown-item:hover { background: rgba(255,255,255,0.07); color: #fff; }
        .top-dropdown-item i { color: var(--ouro); width: 14px; }
        .top-dropdown-divider { border-top: 0.5px solid rgba(255,255,255,0.08); margin: 4px 0; }

        /* ══════════════════════════════════════════
           NAVBAR PRINCIPAL
        ══════════════════════════════════════════ */
        .pub-nav {
            background: var(--azul);
            position: sticky;
            top: 0;
            z-index: 500;
            box-shadow: 0 2px 20px rgba(0,0,0,0.22);
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
            width: 48px; height: 48px;
            overflow: hidden;
            flex-shrink: 0;
        }

        .pub-nav-logo img { width: 100%; height: 100%; object-fit: contain; }

        .pub-nav-nome {
            font-family: 'Playfair Display', serif;
            font-size: 17px;
            font-weight: 700;
            color: var(--branco);
            line-height: 1.2;
        }

        .pub-nav-sub {
            font-size: 10px;
            color: rgba(255,255,255,0.50);
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .pub-nav-links {
            display: flex;
            align-items: center;
            gap: 2px;
            list-style: none;
            margin: 0; padding: 0;
        }

        .pub-nav-links > li > a,
        .pub-nav-links > li > .nav-drop-btn {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 8px 14px;
            font-size: 13.5px;
            font-weight: 500;
            color: rgba(255,255,255,0.78);
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            white-space: nowrap;
        }

        .pub-nav-links > li > a:hover,
        .pub-nav-links > li > a.ativo,
        .pub-nav-links > li > .nav-drop-btn:hover { background: rgba(255,255,255,0.10); color: #fff; }

        .nav-drop { position: relative; }

        .nav-drop-menu {
            display: none;
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            background: #0f2a5e;
            border: 0.5px solid rgba(255,255,255,0.12);
            min-width: 210px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.35);
            overflow: hidden;
            z-index: 9999;
        }

        .nav-drop:hover .nav-drop-menu { display: block; }

        .nav-drop-menu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            font-size: 13px;
            color: rgba(255,255,255,0.72);
            text-decoration: none;
            transition: background 0.12s;
        }

        .nav-drop-menu a:hover { background: rgba(255,255,255,0.07); color: #fff; }
        .nav-drop-menu a i { color: var(--ouro); width: 14px; }
        .nav-drop-divider { border-top: 0.5px solid rgba(255,255,255,0.08); margin: 4px 0; }

        /* ══════════════════════════════════════════
           MENU HAMBÚRGUER
        ══════════════════════════════════════════ */
        .nav-hamburguer {
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 42px;
            height: 42px;
            background: rgba(255,255,255,0.10);
            border: none;
            cursor: pointer;
            gap: 5px;
            transition: background 0.15s;
            flex-shrink: 0;
        }

        .nav-hamburguer:hover { background: rgba(255,255,255,0.18); }

        .nav-hamburguer span {
            display: block;
            width: 22px;
            height: 2px;
            background: #fff;
            transition: transform 0.3s, opacity 0.3s;
        }

        /* Quando aberto: vira X */
        .nav-hamburguer.aberto span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .nav-hamburguer.aberto span:nth-child(2) { opacity: 0; }
        .nav-hamburguer.aberto span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* Overlay escuro */
        .nav-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 498;
        }

        .nav-overlay.visivel { display: block; }

        /* Drawer — começa logo abaixo da navbar (68px) */
        .nav-drawer {
            position: fixed;
            top: 68px; /* altura exacta da navbar */
            left: 0;
            width: 100%;
            max-height: calc(100vh - 68px);
            background: #07193a;
            z-index: 499;
            transform: translateY(-110%);
            transition: transform 0.32s cubic-bezier(0.4,0,0.2,1);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            box-shadow: 0 8px 32px rgba(0,0,0,0.4);
        }

        .nav-drawer.aberto { transform: translateY(0); }

        .nav-drawer-links {
            list-style: none;
            padding: 8px 0;
            flex: 1;
        }

        .nav-drawer-links li a,
        .nav-drawer-links li button {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 15px 28px;
            font-size: 15px;
            font-weight: 500;
            color: rgba(255,255,255,0.82);
            text-decoration: none;
            background: none;
            border: none;
            width: 100%;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            border-bottom: 0.5px solid rgba(255,255,255,0.05);
        }

        .nav-drawer-links li a:hover,
        .nav-drawer-links li button:hover,
        .nav-drawer-links li a.ativo {
            background: rgba(255,255,255,0.07);
            color: #fff;
        }

        .nav-drawer-links li a i,
        .nav-drawer-links li button i { color: var(--ouro); width: 18px; }

        .nav-drawer-divider { border-top: 0.5px solid rgba(255,255,255,0.08); margin: 4px 0; }

        /* Sub-menu Pedidos no drawer */
        .nav-drawer-submenu {
            list-style: none;
            background: rgba(0,0,0,0.25);
            display: none;
        }

        .nav-drawer-submenu.aberto { display: block; }

        .nav-drawer-submenu li a {
            padding: 12px 28px 12px 60px;
            font-size: 14px;
            border-bottom: 0.5px solid rgba(255,255,255,0.04);
        }

        .nav-drawer-toggle-icon {
            margin-left: auto;
            font-size: 11px;
            transition: transform 0.25s;
            color: rgba(255,255,255,0.45) !important;
            width: auto !important;
        }

        .nav-drawer-toggle-icon.aberto { transform: rotate(180deg); }

        /* ══════════════════════════════════════════
           ALERTAS FLASH
        ══════════════════════════════════════════ */
        .flash-wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 20px 24px 0;
        }

        .flash-alert {
            font-size: 13.5px;
            padding: 12px 16px;
            margin-bottom: 12px;
            border: 0.5px solid;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .flash-alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
        .flash-alert-warning { background: #fffbeb; border-color: #fde68a; color: #b45309; }
        .flash-alert-danger  { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }

        /* ══════════════════════════════════════════
           UTILITÁRIOS PÚBLICOS
        ══════════════════════════════════════════ */
        .secao { max-width: 1180px; margin: 0 auto; padding: 64px 24px; }

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

        .separador {
            background: var(--cinza-f);
            border-top: 0.5px solid var(--cinza-b);
            border-bottom: 0.5px solid var(--cinza-b);
        }

        .grid-destaques {
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto;
            gap: 20px;
        }

        .card-destaque-grande {
            grid-row: span 2;
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

        .card-destaque-grande:hover { box-shadow: var(--sombra-h); transform: translateY(-3px); }
        .card-destaque-grande .card-img { height: 300px; object-fit: cover; width: 100%; display: block; }
        .card-destaque-grande .card-img-placeholder {
            height: 300px;
            background: linear-gradient(135deg, var(--cinza-b), var(--cinza-f));
            display: flex; align-items: center; justify-content: center;
            color: #c8d3e8; font-size: 48px;
        }
        .card-destaque-grande .card-corpo { padding: 24px; flex: 1; display: flex; flex-direction: column; }

        .card-destaque-pequeno {
            overflow: hidden;
            box-shadow: var(--sombra);
            text-decoration: none;
            color: inherit;
            display: flex;
            background: var(--branco);
            border: 0.5px solid var(--cinza-b);
            transition: box-shadow 0.2s, transform 0.2s;
        }

        .card-destaque-pequeno:hover { box-shadow: var(--sombra-h); transform: translateY(-2px); }
        .card-destaque-pequeno .card-img { width: 130px; min-width: 130px; object-fit: cover; display: block; }
        .card-destaque-pequeno .card-img-placeholder {
            width: 130px; min-width: 130px;
            background: linear-gradient(135deg, var(--cinza-b), var(--cinza-f));
            display: flex; align-items: center; justify-content: center;
            color: #c8d3e8; font-size: 28px;
        }
        .card-destaque-pequeno .card-corpo { padding: 16px 18px; display: flex; flex-direction: column; justify-content: center; }

        .badge-destaque {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 3px 10px;
            font-size: 11px;
            font-weight: 600;
            background: #fef3c7;
            color: #92400e;
            border: 0.5px solid #fde68a;
            margin-bottom: 10px;
            width: fit-content;
        }

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

        .grid-noticias { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }

        .card-noticia {
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

        .card-noticia:hover { box-shadow: var(--sombra-h); transform: translateY(-3px); }
        .card-noticia .card-img { height: 180px; object-fit: cover; width: 100%; display: block; }
        .card-noticia .card-img-placeholder {
            height: 180px;
            background: linear-gradient(135deg, var(--cinza-b), var(--cinza-f));
            display: flex; align-items: center; justify-content: center;
            color: #c8d3e8; font-size: 36px;
        }
        .card-noticia .card-corpo { padding: 18px 20px; display: flex; flex-direction: column; flex: 1; }

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
            font-size: 13.5px;
            font-weight: 500;
            color: var(--azul);
            background: var(--branco);
            border: 0.5px solid var(--cinza-b);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }

        .pag-btn:hover { background: var(--cinza-f); }
        .pag-btn.ativo { background: var(--azul); color: #fff; border-color: var(--azul); }
        .pag-btn.desativo { color: #c8d3e8; pointer-events: none; }

        .banner-inst {
            background: linear-gradient(135deg, var(--azul) 0%, var(--azul-med) 100%);
            padding: 56px 24px;
            text-align: center;
        }

        .banner-inst h2 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(22px, 3vw, 32px);
            font-weight: 700;
            color: #fff;
            margin-bottom: 12px;
        }

        .banner-inst p {
            font-size: 15px;
            color: rgba(255,255,255,0.72);
            max-width: 540px;
            margin: 0 auto 28px;
            line-height: 1.7;
        }

        .btn-hero-prim {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            background: var(--ouro);
            color: var(--azul);
            font-size: 14.5px;
            font-weight: 700;
            text-decoration: none;
            transition: background 0.15s, transform 0.15s;
        }

        .btn-hero-prim:hover { background: var(--ouro-claro); transform: translateY(-1px); color: var(--azul); }

        .btn-hero-sec {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 28px;
            background: rgba(255,255,255,0.12);
            color: #fff;
            font-size: 14.5px;
            font-weight: 600;
            text-decoration: none;
            border: 1px solid rgba(255,255,255,0.25);
            transition: background 0.15s;
        }

        .btn-hero-sec:hover { background: rgba(255,255,255,0.20); }

        /* ══════════════════════════════════════════
           FOOTER
        ══════════════════════════════════════════ */
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

        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 9px; }

        .footer-links a {
            font-size: 13.5px;
            color: rgba(255,255,255,0.60);
            text-decoration: none;
            transition: color 0.15s;
        }

        .footer-links a:hover { color: #fff; }

        .footer-contacto { display: flex; flex-direction: column; gap: 10px; }

        .footer-contacto-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 13.5px;
            color: rgba(255,255,255,0.60);
        }

        .footer-contacto-item i { color: var(--ouro); margin-top: 2px; flex-shrink: 0; width: 14px; }

        .footer-social a {
            display: inline-flex;
            width: 34px; height: 34px;
            background: rgba(255,255,255,0.08);
            align-items: center; justify-content: center;
            color: rgba(255,255,255,0.60);
            text-decoration: none;
            font-size: 14px;
            transition: background 0.15s, color 0.15s;
            margin-right: 6px;
        }

        .footer-social a:hover { background: rgba(255,255,255,0.16); color: #fff; }

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
        .footer-bottom-links { display: flex; gap: 20px; }

        .footer-bottom-links a {
            font-size: 12.5px;
            color: rgba(255,255,255,0.40);
            text-decoration: none;
            transition: color 0.15s;
        }

        .footer-bottom-links a:hover { color: rgba(255,255,255,0.80); }

        /* ══════════════════════════════════════════
           RESPONSIVO
        ══════════════════════════════════════════ */
        @media (max-width: 960px) {
            .grid-destaques { grid-template-columns: 1fr; }
            .card-destaque-grande { grid-row: auto; }
            .grid-noticias { grid-template-columns: repeat(2, 1fr); }
            .pub-footer-inner { grid-template-columns: 1fr 1fr; }
            .top-bar-info { display: none; }
        }

        @media (max-width: 768px) {
            .pub-nav-links { display: none; }
            .nav-hamburguer { display: flex; }
            .btn-acesso { display: none; }
            .btn-acesso-icon { display: inline-flex; }
        }

        @media (max-width: 640px) {
            .grid-noticias { grid-template-columns: 1fr; }
            .pub-footer-inner { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .footer-bottom-links { justify-content: center; flex-wrap: wrap; }
            .pub-nav-nome { font-size: 14px; }
            .pub-nav-sub { display: none; }
        }
    </style>

    @stack("styles")
</head>
<body>

{{-- ══════════════════════════════════════════ --}}
{{-- OVERLAY                                    --}}
{{-- ══════════════════════════════════════════ --}}
<div class="nav-overlay" id="navOverlay" onclick="fecharDrawer()"></div>

{{-- ══════════════════════════════════════════ --}}
{{-- DRAWER MÓVEL (sem header, sem logo)        --}}
{{-- ══════════════════════════════════════════ --}}
<div class="nav-drawer" id="navDrawer">
    <ul class="nav-drawer-links">
        <li>
            <a href="{{ route('home') }}"
               class="{{ request()->routeIs('home') ? 'ativo' : '' }}"
               onclick="fecharDrawer()">
                <i class="fas fa-home"></i> Início
            </a>
        </li>
        <li>
            <a href="{{ route('noticias.index') }}"
               class="{{ request()->routeIs('noticias.*') ? 'ativo' : '' }}"
               onclick="fecharDrawer()">
                <i class="fas fa-newspaper"></i> Notícias
            </a>
        </li>
        <li>
            <button onclick="toggleSubmenuDrawer(this)">
                <i class="fas fa-file-alt"></i> Pedidos
                <i class="fas fa-chevron-down nav-drawer-toggle-icon"></i>
            </button>
            <ul class="nav-drawer-submenu" id="drawerSubmenuPedidos">
                <li>
                    <a href="#" onclick="fecharDrawer()">
                        <i class="fas fa-id-card"></i> Carteira Profissional
                    </a>
                </li>
                <li>
                    <a href="{{ route('pedido.licenca.form') }}" onclick="fecharDrawer()">
                        <i class="fas fa-certificate"></i> Licença Profissional
                    </a>
                </li>
                <li>
                    <a href="{{ route('consulta.form') }}" onclick="fecharDrawer()">
                        <i class="fas fa-search"></i> Consultar Estado
                    </a>
                </li>
            </ul>
        </li>
        <li>
            <a href="{{ route('sobre') }}"
               class="{{ request()->routeIs('sobre') ? 'ativo' : '' }}"
               onclick="fecharDrawer()">
                <i class="fas fa-info-circle"></i> Sobre
            </a>
        </li>
        <li>
            <a href="{{ route('contactos') }}"
               class="{{ request()->routeIs('contactos') ? 'ativo' : '' }}"
               onclick="fecharDrawer()">
                <i class="fas fa-envelope"></i> Contactos
            </a>
        </li>

        @auth
            <div class="nav-drawer-divider"></div>
            @if(auth()->user()->isSuperAdmin())
                <li>
                    <a href="{{ route('super-admin.dashboard') }}" onclick="fecharDrawer()">
                        <i class="fas fa-tachometer-alt"></i> Dashboard Super Admin
                    </a>
                </li>
            @elseif(auth()->user()->isAdmin())
                <li>
                    <a href="{{ route('admin.dashboard') }}" onclick="fecharDrawer()">
                        <i class="fas fa-tachometer-alt"></i> Dashboard Admin
                    </a>
                </li>
            @endif
            <div class="nav-drawer-divider"></div>
            <li>
                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf
                    <button type="submit" style="color:#f87171;">
                        <i class="fas fa-sign-out-alt" style="color:#f87171;"></i> Sair da conta
                    </button>
                </form>
            </li>
        @endauth
    </ul>
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- TOP BAR                                    --}}
{{-- ══════════════════════════════════════════ --}}
<div class="top-bar">
    <div class="top-bar-inner">
        <div class="top-bar-info">
            <span><i class="fas fa-phone"></i> +244 948 607 983</span>
            <span><i class="fas fa-envelope"></i> www.aatdspa.ao@gmail.com</span>
            <span><i class="fas fa-clock"></i> Seg–Sex, 08h–17h</span>
        </div>

        @auth
            <div class="top-dropdown">
                <button class="top-user-btn">
                    <i class="fas fa-user-shield"></i>
                    {{ auth()->user()->name }}
                    <i class="fas fa-chevron-down" style="font-size:10px; opacity:0.6;"></i>
                </button>
                <div class="top-dropdown-menu">
                    @if(auth()->user()->isSuperAdmin())
                        <a href="{{ route('super-admin.dashboard') }}" class="top-dropdown-item">
                            <i class="fas fa-tachometer-alt"></i> Dashboard Super Admin
                        </a>
                    @elseif(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="top-dropdown-item">
                            <i class="fas fa-tachometer-alt"></i> Dashboard Admin
                        </a>
                    @endif
                    <div class="top-dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="top-dropdown-item" style="color:#f87171;">
                            <i class="fas fa-sign-out-alt" style="color:#f87171;"></i> Sair
                        </button>
                    </form>
                </div>
            </div>
        @else
            {{-- Desktop: botão completo --}}
            <a href="{{ route('login') }}" class="btn-acesso">
                <i class="fas fa-lock"></i> ACESSO
            </a>
            {{-- Mobile: só cadeado --}}
            <a href="{{ route('login') }}" class="btn-acesso-icon">
                <i class="fas fa-lock"></i>
            </a>
        @endauth
    </div>
</div>

{{-- ══════════════════════════════════════════ --}}
{{-- NAVBAR PRINCIPAL                           --}}
{{-- ══════════════════════════════════════════ --}}
<nav class="pub-nav">
    <div class="pub-nav-inner">

        <button class="nav-hamburguer" id="navHamburguer" onclick="toggleDrawer()" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <a href="{{ route('home') }}" class="pub-nav-brand">
            <div class="pub-nav-logo">
                <img src="{{ asset('images/logo.png') }}" alt="Logotipo AATDSPA">
            </div>
            <div>
                <div class="pub-nav-nome">AATDSPA</div>
                <div class="pub-nav-sub">Associação da Ordem dos Técnicos</div>
            </div>
        </a>

        <ul class="pub-nav-links">
            <li>
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'ativo' : '' }}">
                    <i class="fas fa-home"></i> Início
                </a>
            </li>
            <li>
                <a href="{{ route('noticias.index') }}" class="{{ request()->routeIs('noticias.*') ? 'ativo' : '' }}">
                    <i class="fas fa-newspaper"></i> Notícias
                </a>
            </li>
            <li class="nav-drop">
                <button class="nav-drop-btn">
                    <i class="fas fa-file-alt"></i> Pedidos
                    <i class="fas fa-chevron-down" style="font-size:10px; opacity:0.6;"></i>
                </button>
                <div class="nav-drop-menu">
                    <a href="#">
                        <i class="fas fa-id-card"></i> Carteira Profissional
                    </a>
                    <a href="{{ route('pedido.licenca.form') }}">
                        <i class="fas fa-certificate"></i> Licença Profissional
                    </a>
                    <div class="nav-drop-divider"></div>
                    <a href="{{ route('consulta.form') }}">
                        <i class="fas fa-search"></i> Consultar Estado
                    </a>
                </div>
            </li>
            <li>
                <a href="{{ route('sobre') }}" class="{{ request()->routeIs('sobre') ? 'ativo' : '' }}">
                    <i class="fas fa-info-circle"></i> Sobre
                </a>
            </li>
            <li>
                <a href="{{ route('contactos') }}" class="{{ request()->routeIs('contactos') ? 'ativo' : '' }}">
                    <i class="fas fa-envelope"></i> Contactos
                </a>
            </li>
        </ul>
    </div>
</nav>

{{-- ══════════════════════════════════════════ --}}
{{-- FLASH MESSAGES                             --}}
{{-- ══════════════════════════════════════════ --}}
@if(session('success') || session('warning') || session('error'))
<div class="flash-wrap">
    @if(session('success'))
        <div class="flash-alert flash-alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('warning'))
        <div class="flash-alert flash-alert-warning">
            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
        </div>
    @endif
    @if(session('error'))
        <div class="flash-alert flash-alert-danger">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
        </div>
    @endif
</div>
@endif

@yield("content")

{{-- ══════════════════════════════════════════ --}}
{{-- FOOTER                                     --}}
{{-- ══════════════════════════════════════════ --}}
<footer class="pub-footer">
    <div class="pub-footer-inner">

        <div class="footer-marca">
            <div style="display:flex; align-items:center; gap:10px;">
                <div style="width:44px; height:44px; overflow:hidden; flex-shrink:0;">
                    <img src="{{ asset('images/logo.png') }}" alt="Logotipo AATDSPA"
                         style="width:100%; height:100%; object-fit:contain;">
                </div>
                <div style="font-family:'Playfair Display',serif; font-size:15px;
                            font-weight:700; color:#fff; line-height:1.2;">AATDSPA</div>
            </div>
            <p>Associação da Ordem dos Técnicos de Diagnóstico e Terapeutas de Angola — regulação, valorização e defesa da classe técnica nacional.</p>
            <div class="footer-social" style="margin-top:16px;">
                <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                <a href="#" title="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                <a href="#" title="YouTube"><i class="fab fa-youtube"></i></a>
                <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>

        <div>
            <div class="footer-titulo">Navegação</div>
            <ul class="footer-links">
                <li><a href="{{ route('home') }}"><i class="fas fa-chevron-right" style="font-size:10px; margin-right:4px;"></i> Início</a></li>
                <li><a href="{{ route('noticias.index') }}"><i class="fas fa-chevron-right" style="font-size:10px; margin-right:4px;"></i> Notícias</a></li>
                <li><a href="{{ route('sobre') }}"><i class="fas fa-chevron-right" style="font-size:10px; margin-right:4px;"></i> Sobre a Ordem</a></li>
                <li><a href="{{ route('contactos') }}"><i class="fas fa-chevron-right" style="font-size:10px; margin-right:4px;"></i> Contactos</a></li>
                <li><a href="#"><i class="fas fa-chevron-right" style="font-size:10px; margin-right:4px;"></i> Legislação</a></li>
            </ul>
        </div>

        <div>
            <div class="footer-titulo">Serviços</div>
            <ul class="footer-links">
                <li><a href="#"><i class="fas fa-chevron-right" style="font-size:10px; margin-right:4px;"></i> Carteira Profissional</a></li>
                <li><a href="{{ route('pedido.licenca.form') }}"><i class="fas fa-chevron-right" style="font-size:10px; margin-right:4px;"></i> Licença Profissional</a></li>
                <li><a href="{{ route('consulta.form') }}"><i class="fas fa-chevron-right" style="font-size:10px; margin-right:4px;"></i> Consultar Estado</a></li>
                <li><a href="{{ route('login') }}"><i class="fas fa-chevron-right" style="font-size:10px; margin-right:4px;"></i> Área de Membro</a></li>
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
                    <span>+244 948 607 983</span>
                </div>
                <div class="footer-contacto-item">
                    <i class="fas fa-envelope"></i>
                    <span>www.aatdspa.ao@gmail.com</span>
                </div>
                <div class="footer-contacto-item">
                    <i class="fas fa-clock"></i>
                    <span>Segunda a Sexta, 08h–17h</span>
                </div>
            </div>
        </div>

    </div>

    <div class="pub-footer-inner footer-bottom">
        <p>© {{ date('Y') }} Associação da ORDEPDITA — Todos os direitos reservados.</p>
        <div class="footer-bottom-links">
            <a href="#">Política de Privacidade</a>
            <a href="#">Termos de Uso</a>
            <a href="#">Acessibilidade</a>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    function toggleDrawer() {
        const drawer = document.getElementById('navDrawer');
        const overlay = document.getElementById('navOverlay');
        const btn = document.getElementById('navHamburguer');
        const aberto = drawer.classList.toggle('aberto');
        overlay.classList.toggle('visivel', aberto);
        btn.classList.toggle('aberto', aberto);
        document.body.style.overflow = aberto ? 'hidden' : '';
    }

    function fecharDrawer() {
        document.getElementById('navDrawer').classList.remove('aberto');
        document.getElementById('navOverlay').classList.remove('visivel');
        document.getElementById('navHamburguer').classList.remove('aberto');
        document.body.style.overflow = '';
    }

    function toggleSubmenuDrawer(btn) {
        const submenu = btn.nextElementSibling;
        const icon = btn.querySelector('.nav-drawer-toggle-icon');
        const aberto = submenu.classList.toggle('aberto');
        icon.classList.toggle('aberto', aberto);
    }

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') fecharDrawer();
    });
</script>

@stack("scripts")
</body>
</html>