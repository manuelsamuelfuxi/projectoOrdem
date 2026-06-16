<?php

namespace App\Listeners;

use App\Events\PedidoSubmetido;
use Illuminate\Support\Facades\Log;

class RegistrarLogPedidoSubmetido
{
    public function __construct()
    {
        //
    }

    public function handle(PedidoSubmetido $event): void
    {
        Log::info('Novo pedido submetido', [
            'pedido_id' => $event->pedido->id,
            'referencia' => $event->pedido->reference_uuid,
            'processo' => $event->pedido->process_number,
            'ip' => request()->ip()
        ]);
    }
}
