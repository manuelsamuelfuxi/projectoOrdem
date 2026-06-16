<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;
use App\Models\Pedido;

class EnviarComprovativoRequest extends FormRequest
{
    public function authorize(): bool
{
    return true;
}

    public function rules(): array
    {
        return [
            'comprovativo' => [
                'required',
                File::types(['jpg', 'jpeg', 'png', 'pdf'])
                    ->max(5 * 1024)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'comprovativo.required' => 'O comprovativo de pagamento é obrigatório.',
            'comprovativo.max' => 'O comprovativo deve ter no máximo 5MB.',
        ];
    }
}