<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;

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