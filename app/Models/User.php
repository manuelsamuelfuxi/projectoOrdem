<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\PapelUtilizador;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'public_id',
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'role' => PapelUtilizador::class,
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (!$user->public_id) {
                $user->public_id = (string) str()->uuid();
            }
        });
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === PapelUtilizador::SUPER_ADMIN;
    }

    public function isAdmin(): bool
{
    return in_array($this->role, [
        PapelUtilizador::ADMIN,
        PapelUtilizador::SUPER_ADMIN,
    ]);
}

    // Relacionamentos
    public function pedidosVerificados()
    {
        return $this->hasMany(Pedido::class, 'verificado_por');
    }

    public function pagamentosConfirmados()
    {
        return $this->hasMany(Pagamento::class, 'confirmado_por');
    }

    public function noticiasCriadas()
    {
        return $this->hasMany(Noticia::class, 'criado_por');
    }

    public function noticiasAtualizadas()
    {
        return $this->hasMany(Noticia::class, 'atualizado_por');
    }

    public function historicosStatus()
    {
        return $this->hasMany(HistoricoStatus::class, 'alterado_por');
    }
}