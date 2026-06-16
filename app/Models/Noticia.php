<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class Noticia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "noticias";

    protected $fillable = [
        "uuid",
        "titulo",
        "conteudo",
        "imagem_path",
        "legenda_imagem",
        "texto_alternativo",
        "status",
        "destacar",
        "visualizacoes",
        "publicado_em",
        "criado_por",
        "atualizado_por",
    ];

    protected $casts = [
        "publicado_em" => "datetime",
        "destacar" => "boolean",
        "visualizacoes" => "integer",
    ];

    protected static function booted()
    {
        static::creating(function ($noticia) {
            $noticia->uuid = $noticia->uuid ?? (string) str()->uuid();
            $noticia->visualizacoes = $noticia->visualizacoes ?? 0;
        });
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, "criado_por");
    }

    public function atualizadoPor()
    {
        return $this->belongsTo(User::class, "atualizado_por");
    }

    public function scopePublicados(Builder $query): Builder
    {
        return $query->where("status", "publicado")
                     ->where("publicado_em", "<=", now());
    }

    public function scopeDestacados(Builder $query): Builder
    {
        return $query->where("destacar", true)
                     ->where("status", "publicado");
    }
}