<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Adicionar a coluna application_id
            $table->unsignedBigInteger('application_id')->after('id');
            
            // Criar a chave estrangeira
            $table->foreign('application_id')
                  ->references('id')
                  ->on('applications')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Remover a chave estrangeira primeiro
            $table->dropForeign(['application_id']);
            
            // Depois remover a coluna
            $table->dropColumn('application_id');
        });
    }
};