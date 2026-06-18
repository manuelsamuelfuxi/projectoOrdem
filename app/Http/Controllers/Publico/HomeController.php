<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Noticia;

class HomeController extends Controller
{
    public function index()
    {
        // Busca as últimas 6 notícias publicadas (para a fita horizontal)
        $noticias = Noticia::publicados()
            ->latest('publicado_em')  // Mais elegante que orderBy
            ->limit(6)                // Apenas 6 para a fita
            ->get();

        // Busca notícias em destaque (até 3)
        $noticiasEmDestaque = Noticia::destacados()
            ->latest('publicado_em')
            ->limit(3)
            ->get();

        // Última notícia para o carrossel (a primeira da lista)
        $ultimaNoticia = $noticias->first();

        return view("publico.home", compact("noticias", "noticiasEmDestaque", "ultimaNoticia"));
    }
}