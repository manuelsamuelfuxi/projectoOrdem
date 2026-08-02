<?php

namespace App\Http\Requests;

use App\Enums\TipoDocumento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Etapa3Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_documento_upload' => ['required', 'string', Rule::enum(TipoDocumento::class)],
            'arquivo'               => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:10240'],
        ];
    }

    /**
     * Já convertido para o tipo forte — o resto da aplicação nunca mais
     * lida com a string crua deste campo.
     */
    public function tipoDocumento(): TipoDocumento
    {
        return TipoDocumento::from($this->validated('tipo_documento_upload'));
    }
}