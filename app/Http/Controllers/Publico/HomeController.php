<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Noticia;

class HomeController extends Controller
{
    public function index()
    {
        $noticias = Noticia::publicados()
            ->orderBy("publicado_em", "desc")
            ->paginate(6);

        $noticiasEmDestaque = Noticia::destacados()
            ->orderBy("publicado_em", "desc")
            ->limit(3)
            ->get();

        return view("publico.home", compact("noticias", "noticiasEmDestaque"));
    }
}