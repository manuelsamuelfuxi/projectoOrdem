<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// REMOVER: use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\TipoDocumento;

class Documento extends Model
{
    use HasFactory;
    // REMOVER: use SoftDeletes;

    protected $table = 'documents';

    protected $fillable = [
        'application_id',
        'document_uuid',
        'type',
        'original_name',
        'stored_path',
        'hash_sha256',
        'mime_type',
        'file_size',
        'metadata',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_notes',
    ];

    protected $casts = [
        'type' => TipoDocumento::class,
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'metadata' => 'array',
        'file_size' => 'integer',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'application_id');
    }

    public function verificadoPor()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}