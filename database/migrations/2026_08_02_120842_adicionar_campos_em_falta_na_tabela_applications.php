<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Adiciona os campos que o formulário de submissão (Etapa 1 e Etapa 2)
     * já envia para o PedidoService::criarPedido(), mas que ainda não
     * existiam fisicamente na tabela applications.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('bairro')->nullable()->after('municipio_id');
            $table->string('nivel')->nullable()->after('curso_id');
            $table->string('classe')->nullable()->after('nivel');
            $table->string('nome_instituicao_trabalho')->nullable()->after('funcao_id');
            $table->string('sector')->nullable()->after('nome_instituicao_trabalho');
            $table->string('telefone_trabalho')->nullable()->after('municipio_trabalho_id');
            $table->string('email_trabalho')->nullable()->after('telefone_trabalho');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'bairro',
                'nivel',
                'classe',
                'nome_instituicao_trabalho',
                'sector',
                'telefone_trabalho',
                'email_trabalho',
            ]);
        });
    }
};