<?php

namespace App\Listeners;

use App\Events\DocumentoEmitido;
use App\Jobs\GerarDocumentoFinalJob;

class GerarDocumentoFinal
{
    public function __construct()
    {
        //
    }

    public function handle(DocumentoEmitido $event): void
    {
        dispatch(new GerarDocumentoFinalJob($event->pedido, $event->tipoDocumento));
    }
}
