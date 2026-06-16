<?php

namespace App\Services;

use App\Models\Provincia;
use App\Models\Municipio;
use Illuminate\Support\Collection;

class ProvinciaMunicipioService
{
    public function todasProvincias(): Collection
    {
        return Provincia::orderBy('nome')->get();
    }

    public function municipiosDaProvincia(int $provinciaId): Collection
    {
        return Municipio::where('provincia_id', $provinciaId)
            ->orderBy('nome')
            ->get();
    }
}
