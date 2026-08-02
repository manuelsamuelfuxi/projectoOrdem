<?php

namespace App\Enums;

enum TipoDocumento: string
{
    case BI = 'bi';
    case CERTIFICADO_HABILITACOES = 'certificado_habilitacoes';
    case FOTO_IDENTIFICACAO = 'foto_identificacao';
    case OUTRO = 'outro';

    public function rotulo(): string
    {
        return match($this) {
            self::BI => 'Bilhete de Identidade',
            self::CERTIFICADO_HABILITACOES => 'Certificado de Habilitações',
            self::FOTO_IDENTIFICACAO => 'Fotografia de Identificação',
            self::OUTRO => 'Outro Documento',
        };
    }

    public function isObrigatorio(): bool
    {
        return match($this) {
            self::BI, self::CERTIFICADO_HABILITACOES => true,
            self::FOTO_IDENTIFICACAO, self::OUTRO => false,
        };
    }

    public function tamanhoMaximoMB(): int
    {
        return match($this) {
            self::BI => 5,
            self::CERTIFICADO_HABILITACOES => 10,
            self::FOTO_IDENTIFICACAO => 5,
            self::OUTRO => 10,
        };
    }

    public function formatosPermitidos(): array
    {
        return match($this) {
            self::BI => ['jpg', 'jpeg', 'png', 'pdf'],
            self::CERTIFICADO_HABILITACOES => ['pdf'],
            self::FOTO_IDENTIFICACAO => ['jpg', 'jpeg', 'png'],
            self::OUTRO => ['jpg', 'jpeg', 'png', 'pdf'],
        };
    }

    /**
     * Fonte única de verdade para os tipos obrigatórios.
     * Elimina a duplicação que existia em PedidoService (3 sítios distintos
     * tinham esta mesma lista hardcoded, dessincronizada entre si).
     *
     * @return list<string>
     */
    public static function obrigatorios(): array
    {
        return array_map(
            fn (self $tipo) => $tipo->value,
            array_filter(self::cases(), fn (self $tipo) => $tipo->isObrigatorio())
        );
    }
}