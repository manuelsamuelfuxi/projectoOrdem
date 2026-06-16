<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Remover a constraint unique
            $table->dropUnique('documents_hash_sha256_unique');
            
            // Manter apenas um índice normal para consultas rápidas
            $table->index('hash_sha256');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['hash_sha256']);
            $table->unique('hash_sha256');
        });
    }
};