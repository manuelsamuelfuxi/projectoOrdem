<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Services\Licenca\LicencaDadosService;
use App\Services\Licenca\LicencaPdfService;
use Illuminate\Http\Request;

class LicencaController extends Controller
{
    public function __construct(
        private readonly LicencaDadosService $dadosService,
        private readonly LicencaPdfService $pdfService,
    ) {}

    /**
     * Rota provisória de preview, usada apenas durante a construção do
     * documento. Aceita ?pedido=ID na query string para testar com um
     * Application específico; sem parâmetro, usa o mais recente.
     *
     * TODO: remover esta rota/método quando o fluxo real de emissão
     * (EmissaoLicencaService, via super-admin.pedidos) estiver ligado.
     */
    public function preview(Application $pedido)
{
    $dados      = $this->dadosService->prepararDados($pedido);
    $pdfBinario = $this->pdfService->gerar($dados);

    return response($pdfBinario, 200, [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'inline; filename="licenca-preview.pdf"',
    ]);
}
}