<?php

namespace App\Listeners;

use App\Events\DocumentoEmitido;
use Illuminate\Support\Facades\Log;

class RegistrarLogDocumentoEmitido
{
    public function __construct()
    {
        //
    }

    public function handle(DocumentoEmitido $event): void
    {
        Log::info('Documento final emitido', [
            'pedido_id' => $event->pedido->id,
            'tipo_documento' => $event->tipoDocumento,
            'emitido_por' => auth()->id(),
            'data_emissao' => now()
        ]);
    }
}
