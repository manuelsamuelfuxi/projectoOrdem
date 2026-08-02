<?php

namespace App\Models\Concerns;

trait GeraDadosSubmissao
{
    protected static function bootGeraDadosSubmissao(): void
    {
        static::creating(function ($model) {
            $model->process_number = $model->process_number ?? static::gerarNumeroProcesso();
            $model->submitted_at   = $model->submitted_at   ?? now();
        });
    }

    protected static function gerarNumeroProcesso(): string
    {
        $ano    = now()->year;
        $ultimo = static::whereYear('created_at', $ano)->max('process_number');
        $numero = $ultimo ? (int) explode('/', $ultimo)[1] + 1 : 1;

        return sprintf('%d/%05d', $ano, $numero);
    }
}