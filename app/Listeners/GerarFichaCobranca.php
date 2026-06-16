<?php

namespace App\Listeners;

use App\Events\PedidoSubmetido;
use App\Jobs\GerarFichaCobrancaJob;

class GerarFichaCobranca
{
    public function __construct()
    {
        //
    }

    public function handle(PedidoSubmetido $event): void
    {
        dispatch(new GerarFichaCobrancaJob($event->pedido));
    }
}
