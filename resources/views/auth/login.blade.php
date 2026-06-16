@extends('layouts.app')

@push('styles')
<style>
    :root {
        --azul: #0c2d6b;
        --azul-med: #1e3a8a;
        --azul-claro: #2563eb;
        --azul-fundo: #eff6ff;
        --ouro-claro: #fbbf24;
        --cinza-f: #f1f5f9;
        --cinza-b: #e2e8f0;
        --texto: #1e293b;
        --muted: #64748b;
        --erro: #ef4444;
    }

    .login-wrap {
        min-height: 100vh;
        background: linear-gradient(135deg, var(--azul-fundo) 0%, #ffffff 100%);
        display: flex;
        align-items: center;
        padding: 2rem 0;
    }

    .login-card {
        background: white;
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        width: 100%;
        max-width: 440px;
        margin: 0 auto;
    }

    .login-header {
        background: linear-gradient(135deg, var(--azul) 0%, var(--azul-med) 100%);
        padding: 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .login-header::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -15%;
        width: 220px;
        height: 220px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }

    .login-logo {
        width: 72px;
        height: 72px;
        object-fit: contain;
        display: block;
        margin: 0 auto 1rem;
        position: relative;
        z-index: 1;
    }

    .login-header h1 {
        color: white;
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        position: relative;
        z-index: 1;
    }

    .login-header p {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.8rem;
        margin: 4px 0 0;
        position: relative;
        z-index: 1;
    }

    .login-body {
        padding: 2rem;
    }

    .login-label {
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--texto);
        margin-bottom: 6px;
        display: block;
    }

    .login-input {
        width: 100%;
        border: 1px solid var(--cinza-b);
        border-radius: 10px;
        padding: 11px 14px;
        font-size: 0.9rem;
        color: var(--texto);
        background: white;
        transition: all 0.2s ease;
        outline: none;
    }

    .login-input:hover {
        border-color: var(--azul-claro);
        background: #fafcff;
    }

    .login-input:focus {
        border-color: var(--azul-claro);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }

    .login-input.is-invalid {
        border-color: var(--erro);
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    .invalid-feedback {
        font-size: 0.78rem;
        color: var(--erro);
        margin-top: 5px;
        display: block;
    }

    .campo-group {
        margin-bottom: 1.25rem;
    }

    .remember-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.85rem;
        color: var(--muted);
        cursor: pointer;
    }

    .remember-label input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: var(--azul-claro);
        cursor: pointer;
    }

    .btn-login {
        width: 100%;
        padding: 12px;
        background: linear-gradient(135deg, var(--azul-claro) 0%, var(--azul-med) 100%);
        color: white;
        border: none;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.25s ease;
        margin-top: 1.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.35);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .forgot-link {
        display: block;
        text-align: center;
        margin-top: 1rem;
        font-size: 0.82rem;
        color: var(--azul-claro);
        text-decoration: none;
        transition: color 0.2s;
    }

    .forgot-link:hover {
        color: var(--azul-med);
        text-decoration: underline;
    }

    .login-footer {
        background: var(--cinza-f);
        padding: 0.85rem 2rem;
        text-align: center;
        border-top: 1px solid var(--cinza-b);
    }

    .login-footer p {
        margin: 0;
        font-size: 0.75rem;
        color: var(--muted);
    }
</style>
@endpush

@section('content')
<div class="login-wrap">
    <div class="container">
        <div class="login-card">

            <div class="login-header">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logotipo ORDEPDITA" class="login-logo">
                <h1>ORDEPDITA</h1>
                <p>Área reservada — acesso restrito</p>
            </div>

            <div class="login-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="campo-group">
                        <label for="email" class="login-label">Endereço de email</label>
                        <input id="email"
                               type="email"
                               class="login-input @error('email') is-invalid @enderror"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               autocomplete="email"
                               autofocus
                               placeholder="exemplo@ordepdita.ao">
                        @error('email')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="campo-group">
                        <label for="password" class="login-label">Palavra-passe</label>
                        <input id="password"
                               type="password"
                               class="login-input @error('password') is-invalid @enderror"
                               name="password"
                               required
                               autocomplete="current-password"
                               placeholder="••••••••">
                        @error('password')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="campo-group">
                        <label class="remember-label">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            Manter sessão iniciada
                        </label>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>

                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            Esqueceu a palavra-passe?
                        </a>
                    @endif

                </form>
            </div>

            <div class="login-footer">
                <p>Ordem dos Profissionais de Diagnóstico e Terapêutica de Angola</p>
            </div>

        </div>
    </div>
</div>
@endsection