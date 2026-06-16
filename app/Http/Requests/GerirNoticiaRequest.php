<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class GerirNoticiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isSuperAdmin());
    }

    public function rules(): array
    {
        $atualizacao = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'titulo'            => 'required|string|max:200',
            'conteudo'          => 'required|string|min:50',
            'imagem'            => [
                $atualizacao ? 'nullable' : 'required',
                File::image()->max(2 * 1024),
            ],
            'legenda_imagem'    => 'nullable|string|max:255',
            'texto_alternativo' => 'nullable|string|max:255',
            'status'            => ['required', Rule::in(['rascunho', 'publicado', 'arquivado'])],
            'destacar'          => 'boolean',
            'data_publicacao'   => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required'                => 'O título da notícia é obrigatório.',
            'conteudo.required'              => 'O conteúdo da notícia é obrigatório.',
            'conteudo.min'                   => 'O conteúdo deve ter no mínimo 50 caracteres.',
            'imagem.required'                => 'A imagem da notícia é obrigatória.',
            'imagem.max'                     => 'A imagem não pode ultrapassar 2MB.',
            'data_publicacao.after_or_equal' => 'A data de publicação deve ser hoje ou futura.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'destacar' => $this->boolean('destacar'),
            'status'   => $this->status ?? 'rascunho',
        ]);
    }
}