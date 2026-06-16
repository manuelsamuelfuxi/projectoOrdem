<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remover deleted_at da tabela documents
        if (Schema::hasTable('documents') && Schema::hasColumn('documents', 'deleted_at')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
        
        // Remover deleted_at da tabela payments
        if (Schema::hasTable('payments') && Schema::hasColumn('payments', 'deleted_at')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropColumn('deleted_at');
            });
        }
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('payments', function (Blueprint $table) {
            $table->softDeletes();
        });
    }
};