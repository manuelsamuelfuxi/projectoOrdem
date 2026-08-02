<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\EstadoPedido;
use App\Models\Concerns\GeraDadosSubmissao;

class Application extends Model
{
    use SoftDeletes, GeraDadosSubmissao;

    protected $table = 'applications';

    protected $fillable = [
        'full_name',
        'birth_name',
        'birth_date',
        'birth_place',
        'gender',
        'nationality',
        'bi_number',
        'bi_issue_date',
        'bi_issuing_entity',
        'nif',
        'email',
        'phone',
        'alternative_phone',
        'address',
        'postal_code',
        'city',
        'province',
        'document_type',
        'professional_category',
        'specialization',
        'institution',
        'professional_license_number',
        'professional_license_expiry',
        'status',
        'ip_address',
        'user_agent',
        'reference_uuid',
        'process_number',
        'submitted_at',
        'provincia_id',
        'municipio_id',
        'curso_id',
        'funcao_id',
        'provincia_trabalho_id',
        'municipio_trabalho_id',
    ];

    protected $casts = [
        'birth_date'                  => 'date',
        'bi_issue_date'               => 'date',
        'professional_license_expiry' => 'date',
        'submitted_at'                => 'datetime',
        'status'                      => EstadoPedido::class, // ADICIONADO
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Application $pedido) {
            if (empty($pedido->reference_uuid)) {
                $pedido->reference_uuid = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'application_id');
    }

    public function pagamento()
    {
        return $this->hasOne(Pagamento::class, 'application_id');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id');
    }

    public function funcao()
    {
        return $this->belongsTo(Funcao::class, 'funcao_id');
    }

    public function provincia()
    {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function getClasseLabelAttribute(): ?string
    {
        if (!$this->nivel || !$this->classe) {
            return null;
        }

        return \App\Support\ClasseFormatter::formatar($this->nivel, $this->classe);
    }

    public function getStatusStringAttribute(): string
    {
        return is_object($this->status) ? $this->status->value : (string)($this->status ?? '');
    }
}