<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Funcao extends Model
{
    protected $table = 'funcoes';

    protected $fillable = ['nome'];

    public function pedidos()
    {
        return $this->hasMany(Application::class, 'funcao_id');
    }
}
