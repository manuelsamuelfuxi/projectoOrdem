<?php

namespace App\Events;

use App\Models\Pedido;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentoEmitido
{
    use Dispatchable, SerializesModels;

    public Pedido $pedido;
    public string $tipoDocumento;

    public function __construct(Pedido $pedido, string $tipoDocumento)
    {
        $this->pedido = $pedido;
        $this->tipoDocumento = $tipoDocumento;
    }
}
