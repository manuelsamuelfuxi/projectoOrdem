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

        // Processa o upload da imagem
        if ($request->hasFile("imagem")) {
            $caminho = $request->file("imagem")->store("noticias", "public");
            $dados["imagem_path"] = $caminho; // ✅ Usa o campo correto
        }

        $dados["criado_por"] = auth()->id();

        // Se data_publicacao foi enviada, usa ela; senão, define baseado no status
        if (!empty($dados["data_publicacao"])) {
            $dados["publicado_em"] = $dados["data_publicacao"];
        } elseif ($dados["status"] === "publicado") {
            $dados["publicado_em"] = now();
        } else {
            $dados["publicado_em"] = null;
        }

        // Remove campos que não estão no fillable
        unset($dados["data_publicacao"]);

        // LOG DE DEPURAÇÃO
        \Log::info('Admin - Criando notícia:', $dados);

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
        
        // Processa o upload da nova imagem
        if ($request->hasFile("imagem")) {
            // Remove a imagem antiga
            if ($noticia->imagem_path && Storage::disk("public")->exists($noticia->imagem_path)) {
                Storage::disk("public")->delete($noticia->imagem_path);
            }
            
            $caminho = $request->file("imagem")->store("noticias", "public");
            $dados["imagem_path"] = $caminho;
        }
        
        $dados["atualizado_por"] = auth()->id();

        // Se data_publicacao foi enviada, usa ela; senão, define baseado no status
        if (!empty($dados["data_publicacao"])) {
            $dados["publicado_em"] = $dados["data_publicacao"];
        } elseif ($dados["status"] === "publicado" && empty($noticia->publicado_em)) {
            $dados["publicado_em"] = now();
        }

        // Remove campos que não estão no fillable
        unset($dados["data_publicacao"]);

        // LOG DE DEPURAÇÃO
        \Log::info('Admin - Atualizando notícia:', $dados);

        $noticia->update($dados);
        
        return redirect()
            ->route("admin.noticias.index")
            ->with("success", "Notícia atualizada com sucesso!");
    }

    public function destroy(Noticia $noticia)
    {
        // Remove a imagem associada
        if ($noticia->imagem_path && Storage::disk("public")->exists($noticia->imagem_path)) {
            Storage::disk("public")->delete($noticia->imagem_path);
        }
        
        $noticia->delete();
        
        return redirect()
            ->route("admin.noticias.index")
            ->with("success", "Notícia removida com sucesso!");
    }

    public function publicar(Noticia $noticia)
    {
        $noticia->update([
            "status" => "publicado",
            "publicado_em" => now()
        ]);
        
        return redirect()
            ->route("admin.noticias.index")
            ->with("success", "Notícia publicada!");
    }

    public function arquivar(Noticia $noticia)
    {
        $noticia->update([
            "status" => "arquivado"
        ]);
        
        return redirect()
            ->route("admin.noticias.index")
            ->with("success", "Notícia arquivada!");
    }
}