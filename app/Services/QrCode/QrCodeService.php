<?php

namespace App\Services\QrCode;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    /**
     * Única responsabilidade: gerar o QR code e devolvê-lo como data URI,
     * pronto para um atributo src="" de <img>.
     */
    public function gerarDataUri(string $url): string
    {
        $qrCode = QrCode::create($url)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
            ->setSize(400)
            ->setMargin(8)
            ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin);

        $png = (new PngWriter())->write($qrCode)->getString();

        return 'data:image/png;base64,' . base64_encode($png);
    }
}