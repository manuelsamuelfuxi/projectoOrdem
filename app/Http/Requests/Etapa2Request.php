<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class Etapa2Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // ── Académicos (obrigatórios) ───────────────────────
            'instituicao_formacao'   => 'required|string|max:255',
            'curso_id'               => 'required|integer|exists:cursos,id',
            'nivel'                  => 'required|in:medio,superior,outro',
            'classe'                 => 'required|string|max:100',

            // ── Profissionais (opcionais) ───────────────────────
            'nome_instituicao'       => 'nullable|string|max:255',
            'funcao_id'              => 'nullable|integer|exists:funcoes,id',
            'sector'                 => 'nullable|in:publico,privado',
            'provincia_trabalho_id'  => 'nullable|integer|exists:provincias,id',
            'municipio_trabalho_id'  => 'nullable|integer|exists:municipios,id',
            'telefone_trabalho'      => 'nullable|string|max:20',
            'email_trabalho'         => 'nullable|email|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'instituicao_formacao.required'  => 'A escola / universidade é obrigatória.',
            'curso_id.required'              => 'O curso é obrigatório.',
            'curso_id.exists'                => 'Selecione um curso válido.',
            'nivel.required'                 => 'O nível académico é obrigatório.',
            'nivel.in'                       => 'O nível deve ser Médio, Superior ou Outro.',
            'classe.required'                => 'A classe é obrigatória.',
            'funcao_id.exists'               => 'Selecione uma função válida.',
            'sector.in'                      => 'O sector deve ser Público ou Privado.',
            'provincia_trabalho_id.exists'   => 'Selecione uma província válida.',
            'municipio_trabalho_id.exists'   => 'Selecione um município válido.',
            'email_trabalho.email'           => 'Insira um endereço de e-mail válido.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Verificar que o município de trabalho pertence à província de trabalho
            // (só se ambos tiverem sido preenchidos, já que agora são opcionais)
            $provinciaId = $this->input('provincia_trabalho_id');
            $municipioId = $this->input('municipio_trabalho_id');
            if ($provinciaId && $municipioId) {
                $pertence = \App\Models\Municipio::where('id', $municipioId)
                    ->where('provincia_id', $provinciaId)
                    ->exists();
                if (!$pertence) {
                    $validator->errors()->add(
                        'municipio_trabalho_id',
                        'O município não pertence à província seleccionada.'
                    );
                }
            }
        });
    }
}