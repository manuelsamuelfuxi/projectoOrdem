<?php

namespace App\Listeners;

use App\Events\PedidoAprovado;
use Illuminate\Support\Facades\Log;

class RegistrarLogPedidoAprovado
{
    public function __construct()
    {
        //
    }

    public function handle(PedidoAprovado $event): void
    {
        Log::info('Pedido aprovado pelo Super Admin', [
            'pedido_id' => $event->pedido->id,
            'processo' => $event->pedido->process_number,
            'aprovado_por' => auth()->id(),
            'data_aprovacao' => now()
        ]);
    }
}
