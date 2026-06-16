<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// REMOVER: use Illuminate\Database\Eloquent\SoftDeletes;

class Pagamento extends Model
{
    use HasFactory;
    // REMOVER: use SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'application_id',
        'payment_uuid',
        'payment_reference',
        'amount',
        'currency',
        'status',
        'proof_path',
        'proof_hash',
        'proof_submitted_at',
        'confirmed_at',
        'confirmed_by',
        'rejection_reason',
        'bank_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'proof_submitted_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'bank_details' => 'array',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'application_id');
    }

    public function confirmadoPor()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}