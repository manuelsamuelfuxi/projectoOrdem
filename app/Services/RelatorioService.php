<?php

namespace App\Services;

use App\Models\Pedido;

class RelatorioService
{
    private Pedido $modeloPedido;

    public function __construct(Pedido $modeloPedido)
    {
        $this->modeloPedido = $modeloPedido;
    }

    /**
     * Gera o relatório de pedidos delegando a busca e cálculo ao próprio Domínio (Model)
     */
    public function gerarRelatorioPedidos(array $filtros = []): array
    {
        // O Service não sabe COMO o relatório é calculado, 
        // apenas pede ao Model os dados processados.
        return $this->modeloPedido->obterDadosRelatorioPedidos($filtros);
    }
}