<?php

namespace App\Services;

use App\Models\Application;
use Illuminate\Support\Facades\Storage;

class EmissaoLicencaService
{
    public function __construct(
        private readonly LicencaDadosService $dadosService,
        private readonly LicencaPdfService $pdfService,
        private readonly PedidoService $pedidoService,
    ) {}

    /**
     * Única responsabilidade: orquestrar o caso de uso "emitir documento" —
     * gerar o PDF definitivo, persistir o ficheiro, e só depois delegar a
     * transição de estado ao PedidoService (fonte única de verdade sobre
     * regras de transição, via EstadoPedido::podeTransitarPara()).
     *
     * Gera o ficheiro ANTES de mudar o status: se a transição falhar
     * (ex. race condition entre dois super-admins), preferimos um PDF
     * órfão em disco a um pedido marcado como "emitido" sem ficheiro real.
     *
     * @throws \DomainException se o pedido não puder transitar para
     *         'documento_emitido' — propagado do PedidoService.
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