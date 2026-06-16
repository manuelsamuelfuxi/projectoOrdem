<?php

namespace App\Enums;

enum EstadoPedido: string
{
    case NAO_PAGO              = 'nao_pago';
    case AGUARDA_COMPROVATIVO  = 'aguarda_comprovativo';  // ADICIONADO
    case PAGAMENTO_CONFIRMADO  = 'pagamento_confirmado';
    case EM_ANALISE            = 'em_analise';
    case APROVADO              = 'aprovado';
    case REJEITADO             = 'rejeitado';
    case PENDENTE_CORRECAO     = 'pendente_correcao';
    case DOCUMENTO_EMITIDO     = 'documento_emitido';
    case CANCELADO             = 'cancelado';

    public function rotulo(): string
    {
        return match($this) {
            self::NAO_PAGO             => 'Aguardando Pagamento',
            self::AGUARDA_COMPROVATIVO => 'Comprovativo Pendente',   // ADICIONADO
            self::PAGAMENTO_CONFIRMADO => 'Pagamento Confirmado',
            self::EM_ANALISE           => 'Em Análise',
            self::APROVADO             => 'Aprovado',
            self::REJEITADO            => 'Rejeitado',
            self::PENDENTE_CORRECAO    => 'Pendente de Correção',
            self::DOCUMENTO_EMITIDO    => 'Documento Emitido',
            self::CANCELADO            => 'Cancelado',
        };
    }

    public function podeTransitarPara(EstadoPedido $destino): bool
    {
        return match($this) {
            self::NAO_PAGO             => in_array($destino, [self::AGUARDA_COMPROVATIVO, self::CANCELADO]),
            self::AGUARDA_COMPROVATIVO => in_array($destino, [self::PAGAMENTO_CONFIRMADO, self::NAO_PAGO, self::CANCELADO]),
            self::PAGAMENTO_CONFIRMADO => in_array($destino, [self::EM_ANALISE]),
            self::EM_ANALISE           => in_array($destino, [self::APROVADO, self::REJEITADO, self::PENDENTE_CORRECAO]),
            self::PENDENTE_CORRECAO    => in_array($destino, [self::EM_ANALISE, self::CANCELADO]),
            self::APROVADO             => in_array($destino, [self::DOCUMENTO_EMITIDO]),
            self::DOCUMENTO_EMITIDO,
            self::REJEITADO,
            self::CANCELADO            => false,
        };
    }
}