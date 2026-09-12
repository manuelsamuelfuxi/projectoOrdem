<?php

namespace App\Services\Barcode;

use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeService
{
    /**
     * Única responsabilidade: gerar o código de barras e devolvê-lo como
     * data URI, pronto para um atributo src="" de <img>.
     */
    public function gerarDataUri(string $conteudo): string
    {
        $generator = new BarcodeGeneratorPNG();

        $png = $generator->getBarcode($conteudo, $generator::TYPE_CODE_128, 2, 60);

        return 'data:image/png;base64,' . base64_encode($png);
    }
}