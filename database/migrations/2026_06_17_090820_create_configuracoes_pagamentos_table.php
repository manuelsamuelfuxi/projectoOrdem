<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela de configuração de pagamento por tipo de documento.
 *
 * Nunca definir valores monetários ou dados bancários no código-fonte.
 * Toda a lógica de preço e destino bancário é gerida aqui.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracoes_pagamento', function (Blueprint $table) {
            $table->id();

            // Chave de negócio — um registo por tipo de documento
            $table->string('tipo_documento', 20)->unique()->comment('carteira | licenca');

            // Valor em Kwanzas (inteiro — sem decimais para evitar erros de ponto flutuante)
            $table->unsignedBigInteger('valor')->comment('Valor em AOA (ex: 50000 = 50 000 Kz)');

            // Dados bancários — nunca hardcoded na view ou no service
            $table->string('banco',        150)->comment('Nome completo do banco');
            $table->string('iban',          34)->comment('IBAN no formato internacional');
            $table->string('beneficiario', 255)->comment('Nome legal do beneficiário');
            $table->string('nif',           20)->comment('NIF do beneficiário');

            // Controlo operacional
            $table->boolean('ativo')->default(true)->comment('Permite desativar sem apagar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracoes_pagamento');
    }
};