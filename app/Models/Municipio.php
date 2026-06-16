<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $table = 'municipios';

    protected $fillable = ['provincia_id', 'nome'];

    public function provincia()
    {
        return $this->belongsTo(Provincia::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Application::class, 'municipio_id');
    }

    public function pedidosTrabalho()
    {
        return $this->hasMany(Application::class, 'municipio_trabalho_id');
    }
}
