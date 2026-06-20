<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield("title", "Admin - Ordem dos Técnicos")</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: #f8fafc;
            color: #334155;
        }

        .sidebar {
            width: 260px;
            background: #0f172a;
            border-right: 0.5px solid rgba(255,255,255,0.08);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 0.5px solid rgba(255,255,255,0.08);
        }

        .brand-badge { display: inline-flex; align-items: center; gap: 10px; }

        .brand-icon {
            width: 36px; height: 36px;
            background: #1d4ed8;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; color: white; font-weight: 500;
        }

        .brand-text { font-size: 14px; font-weight: 500; color: #f1f5f9; line-height: 1.3; }
        .brand-sub  { font-size: 11px; color: #64748b; }

        .sidebar-section {
            padding: 20px 24px 6px;
            font-size: 10px;
            font-weight: 500;
            color: #475569;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .nav-link-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            margin: 1px 8px;
            font-size: 13.5px;
            color: #94a3b8;
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
        }

        .nav-link-item:hover { background: rgba(255,255,255,0.06); color: #e2e8f0; }
        .nav-link-item.active { background: #1e3a8a; color: #fff; }
        .nav-link-item .nav-icon { width: 18px; text-align: center; font-size: 14px; flex-shrink: 0; }

        .nav-badge {
            margin-left: auto;
            background: #ef4444;
            color: white;
            font-size: 10px;
            font-weight: 500;
            padding: 2px 6px;
            border-radius: 10px;
        }

        .sidebar-bottom {
            margin-top: auto;
            padding: 16px 8px;
            border-top: 0.5px solid rgba(255,255,255,0.08);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: 8px;
        }

        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: #1e3a8a;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; font-weight: 500; color: #93c5fd;
            flex-shrink: 0;
        }

        .user-name { font-size: 13px; font-weight: 500; color: #e2e8f0; }
        .user-role { font-size: 11px; color: #64748b; }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            margin: 1px 8px;
            font-size: 13.5px;
            color: #f87171;
            background: none;
            border: none;
            width: calc(100% - 16px);
            cursor: pointer;
            text-align: left;
            transition: background 0.15s;
        }

        .logout-btn:hover { background: rgba(239,68,68,0.1); }

        .main-content {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: white;
            border-bottom: 0.5px solid #e2e8f0;
            padding: 0 32px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title { font-size: 16px; font-weight: 500; color: #0f172a; }

        .topbar-user {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: #334155;
            font-weight: 500;
        }

        .topbar-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: #1e3a8a;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 500; color: #93c5fd;
        }

        .page-body { padding: 28px 32px; flex: 1; }

        .alert { border-radius: 8px; font-size: 13.5px; padding: 12px 16px; margin-bottom: 20px; border: 0.5px solid; }
        .alert-success { background: #f0fdf4; border-color: #bbf7d0; color: #15803d; }
        .alert-warning { background: #fffbeb; border-color: #fde68a; color: #b45309; }
        .alert-danger  { background: #fef2f2; border-color: #fecaca; color: #b91c1c; }

        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(15,23,42,0.5);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show { display: flex; }

        .modal-box {
            background: white;
            padding: 28px;
            width: 380px;
        }

        .modal-icon {
            width: 44px; height: 44px;
            background: #fef2f2;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            margin-bottom: 16px;
        }

        .modal-title { font-size: 15px; font-weight: 500; color: #0f172a; margin-bottom: 6px; }
        .modal-desc  { font-size: 13px; color: #64748b; line-height: 1.6; margin-bottom: 24px; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; }

        .btn-cancelar {
            padding: 8px 18px;
            border: 1px solid #e2e8f0; background: white;
            font-size: 13px; color: #334155; cursor: pointer;
        }

        .btn-cancelar:hover { background: #f8fafc; }

        .btn-confirmar {
            padding: 8px 18px;
            border: none; background: #ef4444;
            font-size: 13px; color: white; cursor: pointer; font-weight: 500;
        }

        .btn-confirmar:hover { background: #dc2626; }
    </style>
    @stack("styles")
</head>
<body>

{{-- ═══════════════════════════════════════════ --}}
{{-- SIDEBAR                                     --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="sidebar">

    <div class="sidebar-brand">
        <div class="brand-badge">
            <div class="brand-icon">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <div class="brand-text">AATDSPA</div>
                <div class="brand-sub">Administrador</div>
            </div>
        </div>
    </div>

    <div class="sidebar-section">Principal</div>
    <a class="nav-link-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
       href="{{ route('admin.dashboard') }}">
        <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
        Dashboard
    </a>

    <div class="sidebar-section">Gestão</div>
    <a class="nav-link-item {{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}"
       href="{{ route('admin.pedidos.index') }}">
        <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
        Pedidos
    </a>
    <a class="nav-link-item {{ request()->routeIs('admin.pagamentos.*') ? 'active' : '' }}"
       href="{{ route('admin.pagamentos.pendentes') }}">
        <span class="nav-icon"><i class="fas fa-money-bill"></i></span>
        Pagamentos
    </a>
    <a class="nav-link-item {{ request()->routeIs('admin.noticias.*') ? 'active' : '' }}"
       href="{{ route('admin.noticias.index') }}">
        <span class="nav-icon"><i class="fas fa-newspaper"></i></span>
        Notícias
    </a>
    <a class="nav-link-item {{ request()->routeIs('admin.relatorios.*') ? 'active' : '' }}"
       href="{{ route('admin.relatorios.pedidos') }}">
        <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
        Relatórios
    </a>

    <div class="sidebar-section">Sistema</div>
    <a class="nav-link-item" href="{{ route('home') }}">
        <span class="nav-icon"><i class="fas fa-globe"></i></span>
        Ver Site
    </a>

    <div class="sidebar-bottom">
        <div class="user-card">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">Administrador</div>
            </div>
        </div>
        <button type="button" class="logout-btn"
                onclick="document.getElementById('modalLogout').classList.add('show')">
            <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
            Terminar sessão
        </button>
    </div>

</div>
{{-- FIM SIDEBAR --}}

{{-- ═══════════════════════════════════════════ --}}
{{-- CONTEÚDO PRINCIPAL                          --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="main-content">

    <div class="topbar">
        <div class="topbar-title">@yield("page-title", "Administração")</div>
        <div class="topbar-user">
            <div class="topbar-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            {{ auth()->user()->name }}
        </div>
    </div>

    <div class="page-body">

        @if(session("success"))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>{{ session("success") }}
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session("warning"))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session("warning") }}
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session("error"))
            <div class="alert alert-danger">
                <i class="fas fa-times-circle me-2"></i>{{ session("error") }}
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield("content")

    </div>

</div>
{{-- FIM CONTEÚDO PRINCIPAL --}}

{{-- ═══════════════════════════════════════════ --}}
{{-- MODAL DE CONFIRMAÇÃO DE LOGOUT              --}}
{{-- ═══════════════════════════════════════════ --}}
<div class="modal-overlay" id="modalLogout">
    <div class="modal-box">
        <div class="modal-icon">
            <i class="fas fa-sign-out-alt" style="color:#ef4444;"></i>
        </div>
        <div class="modal-title">Terminar sessão</div>
        <div class="modal-desc">
            Tem a certeza que pretende terminar a sessão?
        </div>
        <div class="modal-actions">
            <button type="button" class="btn-cancelar"
                    onclick="document.getElementById('modalLogout').classList.remove('show')">
                Cancelar
            </button>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-confirmar">
                    Sim
                </button>
            </form>
        </div>
    </div>
</div>
{{-- FIM MODAL --}}

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack("scripts")
</body>
</html>