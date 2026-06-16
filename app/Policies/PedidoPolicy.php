<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Pedido;
use App\Enums\EstadoPedido;

class PedidoPolicy
{
    public function aprovarPagamento(User $user, Pedido $pedido): bool
    {
        return $user->isAdmin() && 
               $pedido->status === EstadoPedido::AGUARDA_COMPROVATIVO;
    }

    public function emitirDocumento(User $user, Pedido $pedido): bool
    {
        return $user->isSuperAdmin() && 
               $pedido->status === EstadoPedido::APROVADO;
    }

    public function baixarDocumento(User $user, Pedido $pedido): bool
    {
        // Dono do pedido (público com URL assinada) OU Admin
        return $user->isAdmin() || $user->isSuperAdmin();
    }
}
