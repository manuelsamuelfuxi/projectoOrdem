<?php

namespace App\Listeners;

use App\Events\PagamentoConfirmado;
use Illuminate\Support\Facades\Log;

class RegistrarLogPagamentoConfirmado
{
    public function __construct()
    {
        //
    }

    public function handle(PagamentoConfirmado $event): void
    {
        Log::info('Pagamento confirmado', [
            'pedido_id' => $event->pedido->id,
            'valor' => $event->pedido->pagamento->amount ?? null,
            'confirmado_por' => auth()->id(),
            'data_confirmacao' => now()
        ]);
    }
}
