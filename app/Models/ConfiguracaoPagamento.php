<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @property int    $id
 * @property string $tipo_documento
 * @property int    $valor
 * @property string $banco
 * @property string $iban
 * @property string $beneficiario
 * @property string $nif
 * @property bool   $ativo
 */
class ConfiguracaoPagamento extends Model
{
    protected $table = 'configuracoes_pagamento';

    protected $fillable = [
        'tipo_documento',
        'valor',
        'banco',
        'iban',
        'beneficiario',
        'nif',
        'ativo',
    ];

    protected $casts = [
        'valor' => 'integer',
        'ativo' => 'boolean',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    /**
     * Apenas configurações activas.
     */
    public function scopeAtivo(Builder $query): Builder
    {
        return $query->where('ativo', true);
    }

    // ── Queries de negócio ────────────────────────────────────────────────────

    /**
     * Devolve a configuração activa para um tipo de documento.
     * Lança ModelNotFoundException se não existir — nunca deixa o sistema funcionar
     * com valores indefinidos.
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public static function obterParaTipo(string $tipoDocumento): self
    {
        return static::ativo()
            ->where('tipo_documento', $tipoDocumento)
            ->firstOrFail();
    }
}
