<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GerirNoticiaRequest;
use App\Models\Noticia;
use Illuminate\Support\Facades\Storage;

class NoticiaController extends Controller
{
    public function index()
    {
        $noticias = Noticia::orderBy("created_at", "desc")->paginate(15);
        
        return view("admin.noticias.index", compact("noticias"));
    }

    public function create()
    {
        return view("admin.noticias.create");
    }

   public function store(GerirNoticiaRequest $request)
{
    $dados = $request->validated();

    // NoticiaController — método store()
if ($request->hasFile("imagem")) {
    // Guarda em storage/app/public/noticias/
    $caminho = $request->file("imagem")->store("noticias", "public");
    // $caminho fica como: "noticias/nome_ficheiro.jpg"
    $dados["image_path"] = $caminho;
}

    $dados["criado_por"] = auth()->id();  // era created_by

    // Só define data_publicacao se não vier do formulário
    if (empty($dados["data_publicacao"])) {
        $dados["data_publicacao"] = $dados["status"] === "publicado" ? now() : null;
    }

    Noticia::create($dados);

    return redirect()
        ->route("admin.noticias.index")
        ->with("success", "Notícia criada com sucesso!");
}

    public function edit(Noticia $noticia)
    {
        return view("admin.noticias.edit", compact("noticia"));
    }

    public function update(GerirNoticiaRequest $request, Noticia $noticia)
    {
        $dados = $request->validated();
        
        if ($request->hasFile("imagem")) {
            if ($noticia->image_path && Storage::disk("public")->exists($noticia->image_path)) {
                Storage::disk("public")->delete($noticia->image_path);
            }
            
            $caminho = $request->file("imagem")->store("noticias", "public");
            $dados["image_path"] = $caminho;
        }
        
        $dados["updated_by"] = auth()->id();
        
        $noticia->update($dados);
        
        return redirect()
            ->route("admin.noticias.index")
            ->with("success", "Notícia atualizada com sucesso!");
    }

    public function destroy(Noticia $noticia)
    {
        if ($noticia->image_path && Storage::disk("public")->exists($noticia->image_path)) {
            Storage::disk("public")->delete($noticia->image_path);
        }
        
        $noticia->delete();
        
        return redirect()
            ->route("admin.noticias.index")
            ->with("success", "Notícia removida com sucesso!");
    }

    public function publicar(Noticia $noticia)
    {
        $noticia->update([
            "status" => "published",
            "published_at" => now()
        ]);
        
        return redirect()
            ->route("admin.noticias.index")
            ->with("success", "Notícia publicada!");
    }

    public function arquivar(Noticia $noticia)
    {
        $noticia->update(["status" => "archived"]);
        
        return redirect()
            ->route("admin.noticias.index")
            ->with("success", "Notícia arquivada!");
    }
}