<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

    /**
     * Evento de ciclo de vida do modelo.
     * Garante a integridade dos dados antes da persistência.
     */
    protected static function booted()
    {
        static::creating(function ($noticia) {
            // Gera UUID se não existir
            $noticia->uuid = $noticia->uuid ?? (string) Str::uuid();
            
            // Inicializa visualizações
            $noticia->visualizacoes = $noticia->visualizacoes ?? 0;

            // LÓGICA DE NEGÓCIO: Data de Publicação Automática
            if ($noticia->status === 'publicado' && $noticia->publicado_em === null) {
                $noticia->publicado_em = now();
            } elseif ($noticia->status === 'rascunho') {
                $noticia->publicado_em = null;
            }
        });

        static::updating(function ($noticia) {
            // Se status mudou para 'publicado' e não tem data, define agora
            if ($noticia->isDirty('status') && 
                $noticia->status === 'publicado' && 
                $noticia->publicado_em === null) {
                $noticia->publicado_em = now();
            }
            
            // Se status mudou para 'rascunho', limpa a data de publicação
            if ($noticia->isDirty('status') && $noticia->status === 'rascunho') {
                $noticia->publicado_em = null;
            }
        });
    }

    /**
     * Relacionamentos
     */
    public function criadoPor()
    {
        return $this->belongsTo(User::class, "criado_por");
    }

    public function atualizadoPor()
    {
        return $this->belongsTo(User::class, "atualizado_por");
    }

    /**
     * Scopes (Consultas reutilizáveis)
     */
    public function scopePublicados(Builder $query): Builder
    {
        return $query->where("status", "publicado")
                     ->whereNotNull("publicado_em")
                     ->where("publicado_em", "<=", now());
    }

    public function scopeDestacados(Builder $query): Builder
    {
        return $query->where("destacar", true)
                     ->where("status", "publicado")
                     ->whereNotNull("publicado_em");
    }

    public function scopeRascunhos(Builder $query): Builder
    {
        return $query->where("status", "rascunho");
    }

    public function scopeArquivados(Builder $query): Builder
    {
        return $query->where("status", "arquivado");
    }

    /**
     * ACCESSOR: Retorna a URL completa da imagem para a web.
     * Uso: $noticia->imagem_url
     */
    public function getImagemUrlAttribute(): string
    {
        if (empty($this->imagem_path)) {
            return asset('images/placeholder-news.jpg');
        }

        if (!Storage::disk('public')->exists($this->imagem_path)) {
            return asset('images/placeholder-news.jpg');
        }

        return asset('storage/' . $this->imagem_path);
    }

    /**
     * Verifica se a notícia tem imagem
     */
    public function hasImagem(): bool
    {
        return !empty($this->imagem_path) && 
               Storage::disk('public')->exists($this->imagem_path);
    }

    /**
     * Retorna o nome do arquivo da imagem
     */
    public function getImagemNomeAttribute(): string
    {
        if (empty($this->imagem_path)) {
            return '';
        }

        return basename($this->imagem_path);
    }

    /**
     * Verifica se a notícia está publicada
     */
    public function isPublicado(): bool
    {
        return $this->status === 'publicado' && 
               $this->publicado_em !== null && 
               $this->publicado_em <= now();
    }

    /**
     * Verifica se a notícia está em destaque
     */
    public function isDestaque(): bool
    {
        return (bool) $this->destacar;
    }

    /**
     * Formata a data de publicação
     */
    public function getDataPublicacaoFormatadaAttribute(): string
    {
        if (empty($this->publicado_em)) {
            return '—';
        }

        return $this->publicado_em->format('d/m/Y H:i');
    }

    /**
     * Formata a data de publicação para exibição amigável
     */
    public function getDataPublicacaoAmigavelAttribute(): string
    {
        if (empty($this->publicado_em)) {
            return '—';
        }

        return $this->publicado_em->diffForHumans();
    }

    /**
     * Retorna o título com limite de caracteres
     * Uso: $noticia->titulo_limitado (limite padrão 60)
     */
    public function getTituloLimitadoAttribute($limite = 60): string
    {
        return Str::limit($this->titulo, $limite);
    }

    /**
     * Retorna o conteúdo com limite de caracteres
     * Uso: $noticia->conteudo_limitado (limite padrão 150)
     */
    public function getConteudoLimitadoAttribute($limite = 150): string
    {
        return Str::limit(strip_tags($this->conteudo), $limite);
    }

    /**
     * Retorna o resumo da notícia
     * Uso: $noticia->resumo (limite padrão 120)
     * Uso: $noticia->resumo(80) (limite personalizado)
     */
    public function getResumoAttribute($limite = 120): string
    {
        return Str::limit(strip_tags($this->conteudo), $limite);
    }
}