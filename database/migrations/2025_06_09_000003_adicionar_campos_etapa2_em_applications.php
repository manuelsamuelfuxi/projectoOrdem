<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────
        //  SEGURANÇA: Só executa se a tabela já existir.
        //  Isso evita crash em bases de dados novas/limpas.
        // ─────────────────────────────────────────────────────────────
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            // ── Morada pessoal ────────────────────────────────────────────
            if (!in_array('provincia_id', $existentes)) {
                if (in_array('bairro', $existentes)) {
                    $table->foreignId('provincia_id')->nullable()->after('bairro')
                          ->constrained('provincias')->nullOnDelete();
                } else {
                    $table->foreignId('provincia_id')->nullable()
                          ->constrained('provincias')->nullOnDelete();
                }
            }

            if (!in_array('municipio_id', $existentes)) {
                $table->foreignId('municipio_id')->nullable()->after('provincia_id')
                      ->constrained('municipios')->nullOnDelete();
            }

            // ── Instituição de trabalho ──────────────────────────────────
            if (!in_array('provincia_trabalho_id', $existentes)) {
                if (in_array('sector', $existentes)) {
                    $table->foreignId('provincia_trabalho_id')->nullable()->after('sector')
                          ->constrained('provincias')->nullOnDelete();
                } else {
                    $table->foreignId('provincia_trabalho_id')->nullable()
                          ->constrained('provincias')->nullOnDelete();
                }
            }

            if (!in_array('municipio_trabalho_id', $existentes)) {
                $table->foreignId('municipio_trabalho_id')->nullable()->after('provincia_trabalho_id')
                      ->constrained('municipios')->nullOnDelete();
            }

            // ── Curso ─────────────────────────────────────────────────────
            if (!in_array('curso_id', $existentes)) {
                if (in_array('institution', $existentes)) {
                    $table->foreignId('curso_id')->nullable()->after('institution')
                          ->constrained('cursos')->nullOnDelete();
                } else {
                    $table->foreignId('curso_id')->nullable()
                          ->constrained('cursos')->nullOnDelete();
                }
            }

            // ── Função ────────────────────────────────────────────────────
            if (!in_array('funcao_id', $existentes)) {
                if (in_array('professional_category', $existentes)) {
                    $table->foreignId('funcao_id')->nullable()->after('professional_category')
                          ->constrained('funcoes')->nullOnDelete();
                } else {
                    $table->foreignId('funcao_id')->nullable()
                          ->constrained('funcoes')->nullOnDelete();
                }
            }
        });

        // Remover colunas string antigas substituídas por FK (se existirem)
        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            $fks = ['provincia_id','municipio_id','provincia_trabalho_id','municipio_trabalho_id','curso_id','funcao_id'];

            $remover = [
                'province', 'municipio', 'provincia_trabalho', 'municipio_trabalho',
                'curso', 'professional_category',
            ];

            $confirmar = array_filter($remover, fn($col) => in_array($col, $existentes) && !in_array($col, $fks));

            if (!empty($confirmar)) {
                $table->dropColumn(array_values($confirmar));
            }
        });
    }

    public function down(): void
    {
        // Proteção extra para o down também
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            foreach (['provincia_id','municipio_id','provincia_trabalho_id','municipio_trabalho_id','curso_id','funcao_id'] as $fk) {
                if (in_array($fk, $existentes)) {
                    $table->dropForeign([$fk]);
                }
            }

            $remover = array_filter(
                ['provincia_id','municipio_id','provincia_trabalho_id','municipio_trabalho_id','curso_id','funcao_id'],
                fn($col) => in_array($col, $existentes)
            );

            if (!empty($remover)) {
                $table->dropColumn(array_values($remover));
            }

            $table->string('province')->nullable();
            $table->string('municipio')->nullable();
            $table->string('provincia_trabalho')->nullable();
            $table->string('municipio_trabalho')->nullable();
            $table->string('curso')->nullable();
            $table->string('professional_category')->nullable();
        });
    }
};