<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Noticia;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class NoticiaController extends Controller
{
    /**
     * Display a listing of the resource (notícias públicas).
     */
    public function index(): View
    {
        $noticias = Noticia::publicados()
            ->orderByDesc('publicado_em')  // Mais explícito que latest()
            ->paginate(12);

        return view("publico.noticias.index", compact("noticias"));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid): View
    {
        $noticia = Noticia::where("uuid", $uuid)
            ->where("status", "publicado")
            ->firstOrFail();

        // Incrementa o contador de visualizações
        $noticia->increment("visualizacoes");

        // Busca notícias relacionadas (excluindo a atual)
        $noticiasRelacionadas = Noticia::where("status", "publicado")
            ->where("uuid", "!=", $uuid)
            ->orderByDesc('publicado_em')  // Mais explícito
            ->limit(3)
            ->get();

        return view("publico.noticias.show", compact("noticia", "noticiasRelacionadas"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'titulo'            => 'required|string|max:255',
            'conteudo'          => 'required|string|min:50',
            'status'            => 'required|in:rascunho,publicado,arquivado',
            'imagem'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'data_publicacao'   => 'nullable|date',
            'legenda_imagem'    => 'nullable|string|max:255',
            'texto_alternativo' => 'nullable|string|max:255',
            'destacar'          => 'nullable|boolean',
        ]);

        $dados = $validated;

        // Se data_publicacao foi fornecida, usa ela como publicado_em
        if (!empty($dados['data_publicacao'])) {
            $dados['publicado_em'] = $dados['data_publicacao'];
        }
        unset($dados['data_publicacao']);

        // Processa o upload da imagem
        if ($request->hasFile('imagem')) {
            $dados['imagem_path'] = $request->file('imagem')->store('noticias', 'public');
        }

        // Define se a notícia será destaque
        $dados['destacar'] = $request->has('destacar') && $request->input('destacar') === 'on';

        Noticia::create($dados);

        return redirect()
            ->route('admin.noticias.index')
            ->with('success', 'Notícia criada com sucesso!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): \Illuminate\Http\RedirectResponse
    {
        $noticia = Noticia::findOrFail($id);

        $validated = $request->validate([
            'titulo'            => 'required|string|max:255',
            'conteudo'          => 'required|string|min:50',
            'status'            => 'required|in:rascunho,publicado,arquivado',
            'imagem'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'data_publicacao'   => 'nullable|date',
            'legenda_imagem'    => 'nullable|string|max:255',
            'texto_alternativo' => 'nullable|string|max:255',
            'destacar'          => 'nullable|boolean',
        ]);

        $dados = $validated;

        // Se data_publicacao foi fornecida, usa ela como publicado_em
        if (!empty($dados['data_publicacao'])) {
            $dados['publicado_em'] = $dados['data_publicacao'];
        }
        unset($dados['data_publicacao']);

        // Processa o upload da nova imagem
        if ($request->hasFile('imagem')) {
            // Remove a imagem antiga se existir
            if ($noticia->imagem_path && Storage::disk('public')->exists($noticia->imagem_path)) {
                Storage::disk('public')->delete($noticia->imagem_path);
            }
            $dados['imagem_path'] = $request->file('imagem')->store('noticias', 'public');
        }

        // Define se a notícia será destaque
        $dados['destacar'] = $request->has('destacar') && $request->input('destacar') === 'on';

        $noticia->update($dados);

        return redirect()
            ->route('admin.noticias.index')
            ->with('success', 'Notícia atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): \Illuminate\Http\RedirectResponse
    {
        $noticia = Noticia::findOrFail($id);

        // Remove a imagem associada se existir
        if ($noticia->imagem_path && Storage::disk('public')->exists($noticia->imagem_path)) {
            Storage::disk('public')->delete($noticia->imagem_path);
        }

        $noticia->delete();

        return redirect()
            ->route('admin.noticias.index')
            ->with('success', 'Notícia removida com sucesso!');
    }
}