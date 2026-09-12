<?php

namespace App\Services\Licenca;

use App\Models\Application;
use Illuminate\Support\Facades\Storage;
use App\Services\Publico\PedidoService;

class EmissaoLicencaService
{
    public function __construct(
        private readonly LicencaDadosService $dadosService,
        private readonly LicencaPdfService $pdfService,
        private readonly PedidoService $pedidoService,
    ) {}

    /**
     * Única responsabilidade: orquestrar o caso de uso "emitir licença" —
     * gerar o PDF definitivo, persistir o ficheiro, e só depois delegar a
     * transição de estado ao PedidoService.
     */
    public function emitir(Application $pedido): void
    {
        $dados      = $this->dadosService->prepararDados($pedido);
        $pdfBinario = $this->pdfService->gerar($dados);

        $this->guardarFicheiro($pedido, $pdfBinario);

        $this->pedidoService->aprovarEmissao($pedido);
    }

    private function guardarFicheiro(Application $pedido, string $pdfBinario): void
    {
        Storage::disk('local')->put("documents/{$pedido->id}.pdf", $pdfBinario);
    }
}