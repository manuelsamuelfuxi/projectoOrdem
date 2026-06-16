<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\EstadoPedido;

class Application extends Model
{
    use SoftDeletes;

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
    ];

    protected $casts = [
        'birth_date'                  => 'date',
        'bi_issue_date'               => 'date',
        'professional_license_expiry' => 'date',
        'submitted_at'                => 'datetime',
        'status'                      => EstadoPedido::class, // ADICIONADO
    ];

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'application_id');
    }

    public function pagamento()
    {
        return $this->hasOne(Pagamento::class, 'application_id');
    }

    public function getStatusStringAttribute(): string
    {
        return is_object($this->status) ? $this->status->value : (string)($this->status ?? '');
    }
}