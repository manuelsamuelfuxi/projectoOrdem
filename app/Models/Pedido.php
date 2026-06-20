<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\EstadoPedido;
use App\Events\PagamentoConfirmado;
use App\Events\PedidoAprovado;
use App\Events\DocumentoEmitido;

class Pedido extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'applications';

    protected $fillable = [
        'process_number',
        'full_name',
        'birth_date',
        'gender',
        'nationality',
        'bi_number',
        'email',
        'phone',
        'alternative_phone',
        // Morada — FK
        'provincia_id',
        'municipio_id',
        'bairro',
        // Académicos
        'institution',
        'curso_id',
        'nivel',
        'classe',
        // Profissionais — FK
        'funcao_id',
        'nome_instituicao_trabalho',
        'sector',
        'provincia_trabalho_id',
        'municipio_trabalho_id',
        'telefone_trabalho',
        'email_trabalho',
        // Controlo
        'document_type',
        'status',
        'admin_notes',
        'correction_feedback',
        'submitted_at',
        'payment_confirmed_at',
        'approved_at',
        'document_issued_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'birth_date'                  => 'date',
        'professional_license_expiry' => 'date',
        'submitted_at'                => 'datetime',
        'payment_confirmed_at'        => 'datetime',
        'approved_at'                 => 'datetime',
        'document_issued_at'          => 'datetime',
        'status'                      => EstadoPedido::class,
    ];

    // ── Boot ──────────────────────────────────────────────────────────────────

    protected static function booted(): void
    {
        static::creating(function ($pedido) {
            $pedido->process_number = $pedido->process_number ?? self::gerarNumeroProcesso();
            $pedido->submitted_at   = $pedido->submitted_at   ?? now();
        });
    }

    protected static function gerarNumeroProcesso(): string
    {
        $ano    = now()->year;
        $ultimo = self::whereYear('created_at', $ano)->max('process_number');
        $numero = $ultimo ? (int) explode('/', $ultimo)[1] + 1 : 1;

        return sprintf('%d/%05d', $ano, $numero);
    }

    // ── Relacionamentos — Normalização ────────────────────────────────────────

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function provinciaTrabalho()
    {
        return $this->belongsTo(Provincia::class, 'provincia_trabalho_id');
    }

    public function municipioTrabalho()
    {
        return $this->belongsTo(Municipio::class, 'municipio_trabalho_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function funcao()
    {
        return $this->belongsTo(Funcao::class, 'funcao_id');
    }

    // ── Relacionamentos — Core ────────────────────────────────────────────────

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'application_id');
    }

    public function pagamento()
    {
        return $this->hasOne(Pagamento::class, 'application_id');
    }

    public function historicosStatus()
    {
        return $this->hasMany(HistoricoStatus::class);
    }

    // ── Lógica de negócio — transição de status ───────────────────────────────

    public function atualizarStatus(EstadoPedido $novoStatus, ?User $utilizador = null, array $metadados = []): void
    {
        $statusAntigo = $this->status;

        if (!$statusAntigo->podeTransitarPara($novoStatus)) {
            throw new \InvalidArgumentException(
                "Transição inválida de {$statusAntigo->value} para {$novoStatus->value}"
            );
        }

        $this->status = $novoStatus;
        $this->save();

        $this->historicosStatus()->create([
            'from_status' => $statusAntigo->value,
            'to_status'   => $novoStatus->value,
            'metadata'    => $metadados,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
            'changed_by'  => $utilizador?->id,
        ]);

        match($novoStatus) {
            EstadoPedido::PAGAMENTO_CONFIRMADO => event(new PagamentoConfirmado($this)),
            EstadoPedido::APROVADO             => event(new PedidoAprovado($this)),
            EstadoPedido::DOCUMENTO_EMITIDO    => event(new DocumentoEmitido($this, $utilizador)),
            default                            => null,
        };
    }

    // ── Relatórios ────────────────────────────────────────────────────────────

    public function obterDadosRelatorioPedidos(array $filtros = []): array
    {
        $query = self::with('pagamento')
            ->when($filtros['data_inicio'] ?? null, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filtros['data_fim']    ?? null, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->when($filtros['status']      ?? null, fn($q, $v) => $q->where('status', $v))
            ->latest();

        $pedidos = $query->get();

        $estatisticas = [
            'total'                   => $pedidos->count(),
            'aguardando_pagamento'    => $pedidos->filter(fn($p) => $p->status->value === 'nao_pago')->count(),
            'aguardando_comprovativo' => $pedidos->filter(fn($p) => $p->status->value === 'aguarda_comprovativo')->count(),
            'aprovados'               => $pedidos->filter(fn($p) => $p->status->value === 'aprovado')->count(),
        ];

        return compact('pedidos', 'estatisticas');
    }
}