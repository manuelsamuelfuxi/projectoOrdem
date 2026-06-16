<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    protected $table = 'provincias';

    protected $fillable = ['nome'];

    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }

    public function pedidos()
    {
        return $this->hasMany(Application::class, 'provincia_id');
    }

    public function pedidosTrabalho()
    {
        return $this->hasMany(Application::class, 'provincia_trabalho_id');
    }
}
