<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Enums\EstadoPedido;
use App\Models\Application;
use App\Services\EmissaoLicencaService;

class PedidoController extends Controller
{
    public function index()
    {
        $pedidos = Pedido::orderBy("created_at", "desc")->paginate(20);
        
        return view("admin.pedidos.index", compact("pedidos"));
    }

    public function show(Pedido $pedido)
    {
        return view("admin.pedidos.show", compact("pedido"));
    }

    public function documentos(Pedido $pedido)
    {
        $documentos = $pedido->documentos;
        
        return view("admin.pedidos.documentos", compact("pedido", "documentos"));
    }

}