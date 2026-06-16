<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            // Adicionar todas as colunas faltantes
            $table->string('document_uuid')->unique()->after('id');
            $table->string('type')->after('document_uuid');
            $table->string('original_name')->after('type');
            $table->string('stored_path')->after('original_name');
            $table->string('hash_sha256')->unique()->after('stored_path');
            $table->string('mime_type')->after('hash_sha256');
            $table->integer('file_size')->after('mime_type');
            
            // Adicionar índices para performance
            $table->index('type');
            $table->index('document_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn([
                'document_uuid',
                'type',
                'original_name',
                'stored_path',
                'hash_sha256',
                'mime_type',
                'file_size'
            ]);
        });
    }
};