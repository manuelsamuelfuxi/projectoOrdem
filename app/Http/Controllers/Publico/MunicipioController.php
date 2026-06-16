<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Services\ProvinciaMunicipioService;

class MunicipioController extends Controller
{
    public function __construct(
        private ProvinciaMunicipioService $provinciaService
    ) {}

    public function porProvincia(int $provinciaId)
    {
        $municipios = $this->provinciaService->municipiosDaProvincia($provinciaId);

        return response()->json(
            $municipios->map(fn($m) => ['id' => $m->id, 'nome' => $m->nome])
        );
    }
}