<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Validation\Validator;

class SubmeterPedidoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Dados pessoais
            "nome_completo" => "required|string|max:255|regex:/^[a-zA-ZÀ-ÿ\s]+$/",
            "nome_para_certidao" => "nullable|string|max:255",
            "data_nascimento" => "required|date|before:today|after:1900-01-01",
            "local_nascimento" => "required|string|max:255",
            "genero" => ["required", Rule::in(["masculino", "feminino", "outro"])],
            "nacionalidade" => "required|string|max:100|default:Angola",
            "numero_bi" => "required|string|max:20|unique:applications,bi_number",
            "data_emissao_bi" => "required|date|before:today",
            "entidade_emissora_bi" => "required|string|max:255",
            "nif" => "nullable|string|max:14",
            "email" => "required|email|max:255",
            "telefone" => "required|string|max:20|regex:/^[0-9]{9,12}$/",
            "telefone_alternativo" => "nullable|string|max:20|regex:/^[0-9]{9,12}$/",
            "endereco" => "required|string|max:500",
            "codigo_postal" => "nullable|string|max:20",
            "cidade" => "required|string|max:100",
            "provincia" => "required|string|max:100",
            "tipo_documento" => ["required", Rule::in(["carteira", "licenca"])],
            "categoria_profissional" => "required|string|max:255",
            "especializacao" => "nullable|string|max:255",
            "instituicao_formacao" => "required|string|max:255",
            "numero_licenca_profissional" => "nullable|string|max:50",
            "validade_licenca" => "nullable|date|after:today",
            "documentos" => "required|array|min:3",
            "documentos.fotografia" => [
                "required",
                File::image()
                    ->max(5 * 1024)
                    ->dimensions(Rule::dimensions()->maxWidth(3000)->maxHeight(3000)->minWidth(300)->minHeight(300))
            ],
            "documentos.bi_frente" => "required|file|mimes:jpg,jpeg,png,pdf|max:" . (5 * 1024),
            "documentos.bi_verso" => "required|file|mimes:jpg,jpeg,png,pdf|max:" . (5 * 1024),
            "documentos.certificado_habilitacoes" => "required|file|mimes:pdf|max:" . (10 * 1024),
            "documentos.certificado_curso" => "required|file|mimes:pdf|max:" . (10 * 1024),
            "documentos.comprovativo_residencia" => "required|file|mimes:jpg,jpeg,png,pdf|max:" . (5 * 1024),
            "documentos.declaracao_compromisso" => "required|file|mimes:pdf|max:" . (5 * 1024),
            "documentos.curriculum" => "required|file|mimes:pdf|max:" . (5 * 1024),
            "documentos.outro" => "nullable|file|mimes:jpg,jpeg,png,pdf|max:" . (10 * 1024),
        ];
    }

    public function messages(): array
    {
        return [
            "nome_completo.required" => "O nome completo é obrigatório.",
            "nome_completo.regex" => "O nome completo deve conter apenas letras e espaços.",
            "numero_bi.required" => "O número do Bilhete de Identidade é obrigatório.",
            "numero_bi.unique" => "Este número de BI já foi utilizado num pedido anterior.",
            "email.required" => "O email é obrigatório.",
            "email.email" => "Digite um email válido.",
            "telefone.required" => "O telefone é obrigatório.",
            "telefone.regex" => "Digite um número de telefone válido (apenas dígitos).",
            "documentos.fotografia.required" => "A fotografia é obrigatória.",
            "documentos.fotografia.dimensions" => "A fotografia deve ter no mínimo 300x300 pixels.",
            "documentos.bi_frente.required" => "A frente do BI é obrigatória.",
            "documentos.certificado_habilitacoes.required" => "O certificado de habilitações é obrigatório.",
            "documentos.certificado_curso.required" => "O certificado do curso é obrigatório.",
            "validade_licenca.after" => "A validade da licença deve ser uma data futura.",
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            "nacionalidade" => $this->input("nacionalidade", "Angola"),
            "numero_bi" => strtoupper(preg_replace("/[^A-Z0-9]/", "", $this->numero_bi ?? "")),
            "telefone" => preg_replace("/[^0-9]/", "", $this->telefone ?? ""),
        ]);
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            // Validação: idade mínima 18 anos
            if ($this->data_nascimento) {
                $idade = now()->diffInYears($this->data_nascimento);
                if ($idade < 18) {
                    $validator->errors()->add("data_nascimento", "O candidato deve ter no mínimo 18 anos.");
                }
            }

            // Validação: BI emitido há menos de 10 anos
            if ($this->data_emissao_bi && $this->data_emissao_bi->diffInYears(now()) > 10) {
                $validator->errors()->add("data_emissao_bi", "O BI foi emitido há mais de 10 anos. É necessário renovar.");
            }
        });
    }
}