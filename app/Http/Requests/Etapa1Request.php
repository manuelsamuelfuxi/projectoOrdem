<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class Etapa1Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_documento'       => 'required|in:carteira,licenca',
            'nome_completo'        => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'numero_bi'            => 'required|string|max:20',
            'data_nascimento'      => 'required|date|before:today|after:1900-01-01',
            'genero'               => 'required|in:masculino,feminino',
            'nacionalidade'        => ['required', 'string', 'in:' . implode(',', $this->nacionalidadesValidas())],
            'provincia_id'         => 'required|integer|exists:provincias,id',
            'municipio_id'         => 'required|integer|exists:municipios,id',
            'bairro'               => 'required|string|max:255',
            'email'                => 'required|email|max:255',
            'telefone'             => 'required|string|max:20',
            'telefone_alternativo' => 'nullable|string|max:20',
            'foto'                 => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'tipo_documento.required'   => 'O tipo de documento é obrigatório.',
            'tipo_documento.in'         => 'Tipo de documento inválido.',
            'nome_completo.required'    => 'O nome completo é obrigatório.',
            'nome_completo.regex'       => 'O nome completo deve conter apenas letras e espaços.',
            'numero_bi.required'        => 'O número do B.I. é obrigatório.',
            'data_nascimento.required'  => 'A data de nascimento é obrigatória.',
            'data_nascimento.before'    => 'A data de nascimento deve ser anterior a hoje.',
            'data_nascimento.after'     => 'A data de nascimento deve ser após 01/01/1900.',
            'genero.required'           => 'O género é obrigatório.',
            'genero.in'                 => 'O género deve ser Masculino ou Feminino.',
            'nacionalidade.required'    => 'A nacionalidade é obrigatória.',
            'nacionalidade.in'          => 'Selecione uma nacionalidade africana válida.',
            'provincia_id.required'     => 'A província é obrigatória.',
            'provincia_id.exists'       => 'Selecione uma província válida.',
            'municipio_id.required'     => 'O município é obrigatório.',
            'municipio_id.exists'       => 'Selecione um município válido.',
            'bairro.required'           => 'O bairro é obrigatório.',
            'email.required'            => 'O email é obrigatório.',
            'email.email'               => 'Insira um endereço de email válido.',
            'telefone.required'         => 'O telefone principal é obrigatório.',
            'foto.image'                => 'O ficheiro deve ser uma imagem.',
            'foto.mimes'                => 'A foto deve estar nos formatos: JPG, JPEG ou PNG.',
            'foto.max'                  => 'A foto deve ter no máximo 2MB.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $dataNascimento = $this->input('data_nascimento');
            if ($dataNascimento && now()->diffInYears($dataNascimento) < 18) {
                $validator->errors()->add('data_nascimento', 'O candidato deve ter no mínimo 18 anos.');
            }

            $provinciaId = $this->input('provincia_id');
            $municipioId = $this->input('municipio_id');
            if ($provinciaId && $municipioId) {
                $pertence = \App\Models\Municipio::where('id', $municipioId)
                    ->where('provincia_id', $provinciaId)
                    ->exists();
                if (!$pertence) {
                    $validator->errors()->add('municipio_id', 'O município não pertence à província seleccionada.');
                }
            }
        });
    }

    private function nacionalidadesValidas(): array
    {
        return [
            'Angolana','Argelina','Beninense','Botsuanesa','Burquinabê','Burundiana',
            'Cabo-verdiana','Camaronesa','Comorense','Congolesa','Congolesa (RDC)',
            'Costa-marfinense','Djiboutiana','Egípcia','Eritreia','Essuatinesa','Etíope',
            'Gabonesa','Gambiana','Ganesa','Guineense','Guinéu-equatoriana',
            'Guineense-bissauense','Keniana','Lesotiana','Liberiana','Líbia','Malgaxe',
            'Malauiana','Maliana','Marroquina','Mauritana','Mauriciana','Moçambicana',
            'Namibiana','Nigerina','Nigeriana','Ruandesa','São-tomense','Senegalesa',
            'Serra-leonesa','Somali','Sul-africana','Sul-sudanesa','Sudanesa',
            'Tanzaniana','Togolesa','Tunisina','Ugandesa','Zambiana','Zimbabueana',
        ];
    }
}