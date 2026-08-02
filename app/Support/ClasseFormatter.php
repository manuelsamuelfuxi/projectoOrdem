<?php

namespace App\Support;

class ClasseFormatter
{
    /**
     * Única responsabilidade: converter nivel + classe (valores brutos da BD)
     * no rótulo apresentável (ex: "2ª Classe", "3º Ano").
     */
    public static function formatar(string $nivel, string $classe): string
    {
        return match ($nivel) {
            'medio'    => $classe . 'ª Classe',
            'superior' => str_replace('ano', 'º Ano', $classe),
            default    => $classe,
        };
    }
}