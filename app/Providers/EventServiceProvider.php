<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PedidoSubmetido;
use App\Events\PagamentoConfirmado;
use App\Events\PedidoAprovado;
use App\Events\DocumentoEmitido;
use App\Listeners\GerarFichaCobranca;
use App\Listeners\RegistrarLogPedidoSubmetido;
use App\Listeners\RegistrarLogPagamentoConfirmado;
use App\Listeners\RegistrarLogPedidoAprovado;
use App\Listeners\GerarDocumentoFinal;
use App\Listeners\RegistrarLogDocumentoEmitido;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        PedidoSubmetido::class => [
            GerarFichaCobranca::class,
            RegistrarLogPedidoSubmetido::class,
        ],
        PagamentoConfirmado::class => [
            RegistrarLogPagamentoConfirmado::class,
        ],
        PedidoAprovado::class => [
            RegistrarLogPedidoAprovado::class,
        ],
        DocumentoEmitido::class => [
            GerarDocumentoFinal::class,
            RegistrarLogDocumentoEmitido::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}