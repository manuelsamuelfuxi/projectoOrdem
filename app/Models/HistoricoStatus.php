<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoricoStatus extends Model
{
    use HasFactory;

    protected $table = 'status_histories';

    protected $fillable = [
        'application_id',
        'from_status',
        'to_status',
        'metadata',
        'ip_address',
        'user_agent',
        'changed_by',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'application_id');
    }

    public function alteradoPor()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}