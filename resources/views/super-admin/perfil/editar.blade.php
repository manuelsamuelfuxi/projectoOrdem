@extends("layouts.super-admin")
@section("title", "Meu Perfil — ORDEPDITA")
@section("page-title", "Meu Perfil")

@section("content")
<div style="max-width: 680px;">

    {{-- Dados pessoais --}}
    <div style="background:white; border:1px solid #e2e8f0; margin-bottom:20px;">
        <div style="padding:20px 24px; border-bottom:1px solid #f1f5f9;">
            <div style="font-size:14px; font-weight:500; color:#0f172a;">Dados pessoais</div>
            <div style="font-size:12px; color:#64748b; margin-top:2px;">Actualize o seu nome e endereço de email.</div>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('super-admin.perfil.atualizar') }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
                        Nome completo
                    </label>
                    <input type="text" name="name" value="{{ old('name', $utilizador->name) }}"
                        style="width:100%; padding:9px 12px; border-radius:8px; border:1px solid {{ $errors->has('name') ? '#fca5a5' : '#e2e8f0' }}; font-size:13.5px; color:#0f172a; outline:none;"
                        placeholder="Nome completo">
                    @error('name')
                        <div style="font-size:12px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email', $utilizador->email) }}"
                        style="width:100%; padding:9px 12px; border:1px solid {{ $errors->has('email') ? '#fca5a5' : '#e2e8f0' }}; border-radius:8px; font-size:13.5px; color:#0f172a; outline:none;"
                        placeholder="email@ordepdita.ao">
                    @error('email')
                        <div style="font-size:12px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit"
                        style="padding:9px 20px; background:#1d4ed8; color:white; border:none; font-size:13px; font-weight:500; cursor:pointer;">
                        <i class="fas fa-save me-1"></i> Guardar alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Alterar senha --}}
    <div style="background:white; border:1px solid #e2e8f0;">
        <div style="padding:20px 24px; border-bottom:1px solid #f1f5f9;">
            <div style="font-size:14px; font-weight:500; color:#0f172a;">Alterar senha</div>
            <div style="font-size:12px; color:#64748b; margin-top:2px;">Use uma senha segura com pelo menos 8 caracteres.</div>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('super-admin.perfil.senha') }}">
                @csrf
                @method('PUT')

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
                        Senha actual
                    </label>
                    <input type="password" name="senha_actual"
                        style="width:100%; padding:9px 12px; border:1px solid {{ $errors->has('senha_actual') ? '#fca5a5' : '#e2e8f0' }}; border-radius:8px; font-size:13.5px; color:#0f172a; outline:none;"
                        placeholder="••••••••">
                    @error('senha_actual')
                        <div style="font-size:12px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
                        Nova senha
                    </label>
                    <input type="password" name="nova_senha"
                        style="width:100%; padding:9px 12px; border:1px solid {{ $errors->has('nova_senha') ? '#fca5a5' : '#e2e8f0' }}; border-radius:8px; font-size:13.5px; color:#0f172a; outline:none;"
                        placeholder="••••••••">
                    @error('nova_senha')
                        <div style="font-size:12px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom:20px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">
                        Confirmar nova senha
                    </label>
                    <input type="password" name="nova_senha_confirmation"
                        style="width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:13.5px; color:#0f172a; outline:none;"
                        placeholder="••••••••">
                </div>

                <div style="display:flex; justify-content:flex-end;">
                    <button type="submit"
                        style="padding:9px 20px; background:#0f172a; color:white; border:none; font-size:13px; font-weight:500; cursor:pointer;">
                        <i class="fas fa-lock me-1"></i> Alterar senha
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection