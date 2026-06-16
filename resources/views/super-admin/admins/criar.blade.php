@extends("layouts.super-admin")
@section("title", "Novo Administrador — ORDEPDITA")
@section("page-title", "Novo Administrador")

@section("content")
<div style="max-width:560px;">
    <div style="background:white; border:1px solid #e2e8f0;">
        <div style="padding:20px 24px; border-bottom:0.5px solid #f1f5f9;">
            <div style="font-size:14px; font-weight:500; color:#0f172a;">Dados do administrador</div>
            <div style="font-size:12px; color:#64748b; margin-top:2px;">Preencha os dados para criar uma nova conta de administrador.</div>
        </div>
        <div style="padding:24px;">
            <form method="POST" action="{{ route('super-admin.admins.store') }}">
                @csrf

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Nome completo</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        style="width:100%; padding:9px 12px; border:1px solid {{ $errors->has('name') ? '#fca5a5' : '#e2e8f0' }}; border-radius:8px; font-size:13.5px; outline:none;"
                        placeholder="Nome do administrador">
                    @error('name')
                        <div style="font-size:12px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        style="width:100%; padding:9px 12px; border:1px solid {{ $errors->has('email') ? '#fca5a5' : '#e2e8f0' }}; border-radius:8px; font-size:13.5px; outline:none;"
                        placeholder="admin@ordepdita.ao">
                    @error('email')
                        <div style="font-size:12px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom:16px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Senha</label>
                    <input type="password" name="password"
                        style="width:100%; padding:9px 12px; border:1px solid {{ $errors->has('password') ? '#fca5a5' : '#e2e8f0' }}; border-radius:8px; font-size:13.5px; outline:none;"
                        placeholder="••••••••">
                    @error('password')
                        <div style="font-size:12px; color:#ef4444; margin-top:4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom:24px;">
                    <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Confirmar senha</label>
                    <input type="password" name="password_confirmation"
                        style="width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:13.5px; outline:none;"
                        placeholder="••••••••">
                </div>

                <div style="display:flex; gap:10px; justify-content:flex-end;">
                    <a href="{{ route('super-admin.admins.index') }}"
                        style="padding:9px 18px; border:0.5px solid #e2e8f0; border-radius:8px; font-size:13px; color:#374151; text-decoration:none; background:white;">
                        Cancelar
                    </a>
                    <button type="submit"
                        style="padding:9px 20px; background:#1d4ed8; color:white; border:none; border-radius:8px; font-size:13px; font-weight:500; cursor:pointer;">
                        <i class="fas fa-plus me-1"></i> Criar administrador
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection