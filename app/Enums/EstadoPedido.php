<?php

namespace App\Enums;

enum EstadoPedido: string
{
    case NAO_PAGO              = 'nao_pago';
    case AGUARDA_COMPROVATIVO  = 'aguarda_comprovativo';
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
            self::AGUARDA_COMPROVATIVO => 'Comprovativo Pendente',
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
            // ALTERADO: o fluxo real não usa a fase 'em_analise' — o admin
            // aprova o pagamento e o super-admin decide directamente entre
            // gerar a licença ou rejeitar o pedido (decisão de negócio
            // confirmada — 19/07/2026). 'em_analise' fica disponível para
            // uso futuro, caso se queira introduzir uma revisão intermédia.
            self::PAGAMENTO_CONFIRMADO => in_array($destino, [self::EM_ANALISE, self::DOCUMENTO_EMITIDO, self::REJEITADO]),
            self::EM_ANALISE           => in_array($destino, [self::APROVADO, self::REJEITADO, self::PENDENTE_CORRECAO]),
            self::PENDENTE_CORRECAO    => in_array($destino, [self::EM_ANALISE, self::CANCELADO]),
            // ALTERADO: um pedido aprovado ainda pode ser rejeitado pelo
            // super-admin como última verificação antes da emissão do
            // documento (decisão de negócio confirmada — 19/07/2026).
            self::APROVADO             => in_array($destino, [self::DOCUMENTO_EMITIDO, self::REJEITADO]),
            self::DOCUMENTO_EMITIDO,
            self::REJEITADO,
            self::CANCELADO            => false,
        };
    }
}