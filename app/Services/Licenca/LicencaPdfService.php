<?php

namespace App\Services\Licenca;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\QrCode\QrCodeService;
use App\Services\Barcode\BarcodeService;

class LicencaPdfService
{
    public function __construct(
        private readonly QrCodeService $qrCodeService,
        private readonly BarcodeService $barcodeService,
    ) {}

    /**
     * Única responsabilidade: renderizar o PDF a partir de dados já
     * prontos. Não busca nada na BD e não formata nada — só recebe e desenha.
     */
    public function gerar(array $dados): string
    {
        $dados['qrCodeDataUri']       = $this->qrCodeService->gerarDataUri($dados['urlVerificacao']);
        $dados['codigoBarrasDataUri'] = $this->barcodeService->gerarDataUri($dados['numeroProcesso']);

        return Pdf::loadView('pdf.licenca', $dados)
            ->setPaper('a4', 'portrait')
            ->output();
    }
}