<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SEGURANÇA: Só roda se a tabela 'applications' já existir.
        // Se a tabela não existe (ex: nova BD), não faz nada.
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            $remover = [
                'reference_uuid',
                'birth_place',
                'nationality',
                'bi_issue_date',
                'bi_issuing_entity',
                'nif',
                'address',
                'postal_code',
                'city',
            ];

            $confirmar = array_filter($remover, fn($col) => in_array($col, $existentes));

            if (!empty($confirmar)) {
                $table->dropColumn(array_values($confirmar));
            }
        });

        // Adicionar municipio e bairro caso ainda não existam
        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            if (!in_array('municipio', $existentes)) {
                $table->string('municipio')->nullable()->after('province');
            }

            if (!in_array('bairro', $existentes)) {
                $table->string('bairro')->nullable()->after('municipio');
            }
        });
    }

    public function down(): void
    {
        // O mesmo 'down' original, que é inteligente o suficiente,
        // mas vamos adicionar a verificação extra aqui também para ser consistente.
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            if (!in_array('reference_uuid', $existentes)) {
                $table->uuid('reference_uuid')->unique()->nullable()->after('id');
            }
            if (!in_array('birth_place', $existentes)) {
                $table->string('birth_place')->nullable()->after('birth_date');
            }
            if (!in_array('nationality', $existentes)) {
                $table->string('nationality')->nullable()->after('gender');
            }
            if (!in_array('bi_issue_date', $existentes)) {
                $table->date('bi_issue_date')->nullable()->after('bi_number');
            }
            if (!in_array('bi_issuing_entity', $existentes)) {
                $table->string('bi_issuing_entity')->nullable()->after('bi_issue_date');
            }
            if (!in_array('nif', $existentes)) {
                $table->string('nif')->nullable()->after('bi_issuing_entity');
            }
            if (!in_array('address', $existentes)) {
                $table->text('address')->nullable()->after('alternative_phone');
            }
            if (!in_array('postal_code', $existentes)) {
                $table->string('postal_code')->nullable()->after('address');
            }
            if (!in_array('city', $existentes)) {
                $table->string('city')->nullable()->after('postal_code');
            }
        });

        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            if (in_array('municipio', $existentes)) {
                $table->dropColumn('municipio');
            }
            if (in_array('bairro', $existentes)) {
                $table->dropColumn('bairro');
            }
        });
    }
};