<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("noticias", function (Blueprint $table) {
            $table->id();
            $table->uuid("uuid")->unique();
            $table->string("titulo");
            $table->longText("conteudo");
            $table->string("imagem_path")->nullable();
            $table->string("legenda_imagem")->nullable();
            $table->string("texto_alternativo")->nullable();
            $table->enum("status", ["rascunho", "publicado", "arquivado"])->default("rascunho");
            $table->boolean("destacar")->default(false);
            $table->integer("visualizacoes")->default(0);
            $table->timestamp("publicado_em")->nullable();
            $table->foreignId("criado_por")->constrained("users");
            $table->foreignId("atualizado_por")->nullable()->constrained("users");
            $table->softDeletes();
            $table->timestamps();

            // Índices para melhorar a busca
            $table->index("status");
            $table->index("publicado_em");
            $table->index("destacar");
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("noticias");
    }
};