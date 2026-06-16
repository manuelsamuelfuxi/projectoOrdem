<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Noticia;

class NoticiaController extends Controller
{
    public function index()
    {
        $noticias = Noticia::where("status", "published")
            ->where("published_at", "<=", now())
            ->orderBy("published_at", "desc")
            ->paginate(12);

        return view("publico.noticias.index", compact("noticias"));
    }

    public function show($id)
    {
        $noticia = Noticia::where("id", $id)
            ->where("status", "published")
            ->firstOrFail();

        // Incrementar visualizações
        $noticia->increment("views");

        $noticiasRelacionadas = Noticia::where("status", "published")
            ->where("id", "!=", $id)
            ->limit(3)
            ->get();

        return view("publico.noticias.show", compact("noticia", "noticiasRelacionadas"));
    }
}