@extends("layouts.super-admin")
@section("title", "Administradores — ORDEPDITA")
@section("page-title", "Administradores")

@section("content")
<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <div style="font-size:13px; color:#64748b;">
        {{ $admins->total() }} administrador(es) registado(s)
    </div>
    <a href="{{ route('super-admin.admins.create') }}"
        style="padding:9px 18px; background:#1d4ed8; color:white; font-size:13px; font-weight:500; text-decoration:none;">
        <i class="fas fa-plus me-1"></i> Novo Administrador
    </a>
</div>

<div style="background:white; border:1px solid #e2e8f0; overflow:hidden;">
    <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
        <thead>
            <tr style="border-bottom:0.5px solid #f1f5f9;">
                <th style="padding:12px 20px; text-align:left; font-size:11px; font-weight:500; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Nome</th>
                <th style="padding:12px 20px; text-align:left; font-size:11px; font-weight:500; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Email</th>
                <th style="padding:12px 20px; text-align:left; font-size:11px; font-weight:500; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Estado</th>
                <th style="padding:12px 20px; text-align:left; font-size:11px; font-weight:500; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Criado em</th>
                <th style="padding:12px 20px; text-align:right; font-size:11px; font-weight:500; color:#64748b; text-transform:uppercase; letter-spacing:0.05em;">Acções</th>
            </tr>
        </thead>
        <tbody>
            @forelse($admins as $admin)
            <tr style="border-bottom:0.5px solid #f8fafc;">
                <td style="padding:14px 20px; color:#0f172a; font-weight:500;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <div style="width:32px; height:32px; border-radius:50%; background:#eff6ff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:500; color:#1d4ed8; flex-shrink:0;">
                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                        </div>
                        {{ $admin->name }}
                    </div>
                </td>
                <td style="padding:14px 20px; color:#64748b;">{{ $admin->email }}</td>
                <td style="padding:14px 20px;">
                    @if($admin->is_active)
                        <span style="display:inline-flex; padding:3px 10px; background:#f0fdf4; color:#15803d; border-radius:6px; font-size:11px; font-weight:500;">
                            Activo
                        </span>
                    @else
                        <span style="display:inline-flex; padding:3px 10px; background:#fef2f2; color:#b91c1c; border-radius:6px; font-size:11px; font-weight:500;">
                            Inactivo
                        </span>
                    @endif
                </td>
                <td style="padding:14px 20px; color:#64748b;">{{ $admin->created_at->format('d/m/Y') }}</td>
                <td style="padding:14px 20px; text-align:right;">
                    <div style="display:flex; gap:6px; justify-content:flex-end;">
                        <a href="{{ route('super-admin.admins.edit', $admin) }}"
                            style="padding:6px 12px; border:0.5px solid #e2e8f0; border-radius:6px; font-size:12px; color:#374151; text-decoration:none; background:white;">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <form method="POST" action="{{ route('super-admin.admins.destroy', $admin) }}"
                              onsubmit="return confirm('Tem a certeza que pretende remover este administrador?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                style="padding:6px 12px; border:1px solid #fecaca; border-radius:6px; font-size:12px; color:#ef4444; background:white; cursor:pointer;">
                                <i class="fas fa-trash"></i> Remover
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="padding:40px; text-align:center; color:#94a3b8; font-size:13px;">
                    <i class="fas fa-users" style="font-size:24px; display:block; margin-bottom:8px;"></i>
                    Nenhum administrador registado
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($admins->hasPages())
    <div style="padding:14px 20px; border-top:0.5px solid #f1f5f9;">
        {{ $admins->links() }}
    </div>
    @endif
</div>
@endsection