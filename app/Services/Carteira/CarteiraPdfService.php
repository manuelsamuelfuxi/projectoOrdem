<?php

namespace App\Services\Carteira;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\QrCode\QrCodeService;

class CarteiraPdfService
{
    private QrCodeService $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }

    /**
     * Única responsabilidade: renderizar o PDF a partir de dados já
     * prontos. Não busca nada na BD e não formata nada — só recebe e desenha.
     */
    public function gerar(array $dados): string
    {
        $dados['qrCodeDataUri'] = $this->qrCodeService->gerarDataUri($dados['urlVerificacao']);

        return Pdf::loadView('pdf.carteira', $dados)
    ->setPaper('a4', 'portrait')
    ->output();
    }
}