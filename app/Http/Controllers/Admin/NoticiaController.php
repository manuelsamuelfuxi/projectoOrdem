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
        $noticias = Noticia::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.noticias.index', compact('noticias'));
    }

    public function create()
    {
        return view('admin.noticias.create');
    }

    public function store(GerirNoticiaRequest $request)
    {
        $dados = $request->validated();

        if ($request->hasFile('imagem')) {
            $caminho = $request->file('imagem')->store('noticias', 'public');
            $dados['imagem_path'] = $caminho;
        }

        $dados['criado_por'] = auth()->id();

        if (!empty($dados['data_publicacao'])) {
            $dados['publicado_em'] = $dados['data_publicacao'];
        } elseif ($dados['status'] === 'publicado') {
            $dados['publicado_em'] = now();
        } else {
            $dados['publicado_em'] = null;
        }

        unset($dados['data_publicacao']);

        Noticia::create($dados);

        return redirect()
            ->route('admin.noticias.index')
            ->with('success', 'Notícia criada com sucesso!');
    }

    public function show(Noticia $noticia)
    {
        return view('admin.noticias.show', compact('noticia'));
    }

    public function edit(Noticia $noticia)
    {
        return view('admin.noticias.edit', compact('noticia'));
    }

    public function update(GerirNoticiaRequest $request, Noticia $noticia)
    {
        $dados = $request->validated();

        if ($request->hasFile('imagem')) {
            if ($noticia->imagem_path && Storage::disk('public')->exists($noticia->imagem_path)) {
                Storage::disk('public')->delete($noticia->imagem_path);
            }

            $caminho = $request->file('imagem')->store('noticias', 'public');
            $dados['imagem_path'] = $caminho;
        }

        $dados['atualizado_por'] = auth()->id();

        if (!empty($dados['data_publicacao'])) {
            $dados['publicado_em'] = $dados['data_publicacao'];
        } elseif ($dados['status'] === 'publicado' && empty($noticia->publicado_em)) {
            $dados['publicado_em'] = now();
        }

        unset($dados['data_publicacao']);

        $noticia->update($dados);

        return redirect()
            ->route('admin.noticias.index')
            ->with('success', 'Notícia actualizada com sucesso!');
    }

    public function destroy(Noticia $noticia)
    {
        if ($noticia->imagem_path && Storage::disk('public')->exists($noticia->imagem_path)) {
            Storage::disk('public')->delete($noticia->imagem_path);
        }

        $noticia->delete();

        return redirect()
            ->route('admin.noticias.index')
            ->with('success', 'Notícia removida com sucesso!');
    }

    public function publicar(Noticia $noticia)
    {
        $noticia->update([
            'status'       => 'publicado',
            'publicado_em' => now(),
        ]);

        return redirect()
            ->route('admin.noticias.index')
            ->with('success', 'Notícia publicada!');
    }

    public function arquivar(Noticia $noticia)
    {
        $noticia->update(['status' => 'arquivado']);

        return redirect()
            ->route('admin.noticias.index')
            ->with('success', 'Notícia arquivada!');
    }
}