<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            // Morada pessoal
            if (!in_array('provincia_id', $existentes)) {
                $table->foreignId('provincia_id')->nullable()->after('bairro')
                      ->constrained('provincias')->nullOnDelete();
            }
            if (!in_array('municipio_id', $existentes)) {
                $table->foreignId('municipio_id')->nullable()->after('provincia_id')
                      ->constrained('municipios')->nullOnDelete();
            }

            // Instituição de trabalho
            if (!in_array('provincia_trabalho_id', $existentes)) {
                $table->foreignId('provincia_trabalho_id')->nullable()->after('sector')
                      ->constrained('provincias')->nullOnDelete();
            }
            if (!in_array('municipio_trabalho_id', $existentes)) {
                $table->foreignId('municipio_trabalho_id')->nullable()->after('provincia_trabalho_id')
                      ->constrained('municipios')->nullOnDelete();
            }

            // Curso e Função
            if (!in_array('curso_id', $existentes)) {
                $table->foreignId('curso_id')->nullable()->after('institution')
                      ->constrained('cursos')->nullOnDelete();
            }
            if (!in_array('funcao_id', $existentes)) {
                $table->foreignId('funcao_id')->nullable()->after('professional_category')
                      ->constrained('funcoes')->nullOnDelete();
            }
        });

        // Remover colunas string antigas substituídas por FK
        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            $remover = [
                'province', 'municipio', 'bairro',        // morada pessoal (bairro mantém-se em texto)
                'provincia_trabalho', 'municipio_trabalho', // trabalho
                'curso', 'professional_category',          // curso e função (string)
            ];

            // Remover apenas as que existem e NÃO são FK novas
            $fks = ['provincia_id','municipio_id','provincia_trabalho_id','municipio_trabalho_id','curso_id','funcao_id'];
            $confirmar = array_filter($remover, fn($col) => in_array($col, $existentes) && !in_array($col, $fks));

            if (!empty($confirmar)) {
                $table->dropColumn(array_values($confirmar));
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['provincia_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['provincia_trabalho_id']);
            $table->dropForeign(['municipio_trabalho_id']);
            $table->dropForeign(['curso_id']);
            $table->dropForeign(['funcao_id']);

            $table->dropColumn([
                'provincia_id', 'municipio_id',
                'provincia_trabalho_id', 'municipio_trabalho_id',
                'curso_id', 'funcao_id',
            ]);

            // Restaurar colunas string
            $table->string('province')->nullable();
            $table->string('municipio')->nullable();
            $table->string('provincia_trabalho')->nullable();
            $table->string('municipio_trabalho')->nullable();
            $table->string('curso')->nullable();
            $table->string('professional_category')->nullable();
        });
    }
};