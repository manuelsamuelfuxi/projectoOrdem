<?php

namespace App\Enums;

enum TipoDocumento: string
{
    case BI = 'bi';
    case CERTIFICADO_HABILITACOES = 'certificado_habilitacoes';
    case OUTRO = 'outro';

    public function rotulo(): string
    {
        return match($this) {
            self::BI => 'Bilhete de Identidade',
            self::CERTIFICADO_HABILITACOES => 'Certificado de Habilitações',
            self::OUTRO => 'Outro Documento',
        };
    }
    
    public function isObrigatorio(): bool
    {
        return match($this) {
            self::BI, self::CERTIFICADO_HABILITACOES => true,
            self::OUTRO => false,
        };
    }
    
    public function tamanhoMaximoMB(): int
    {
        return match($this) {
            self::BI => 5,
            self::CERTIFICADO_HABILITACOES => 10,
            self::OUTRO => 10,
        };
    }
    
    public function formatosPermitidos(): array
    {
        return match($this) {
            self::BI => ['jpg', 'jpeg', 'png', 'pdf'],
            self::CERTIFICADO_HABILITACOES => ['pdf'],
            self::OUTRO => ['jpg', 'jpeg', 'png', 'pdf'],
        };
    }
}