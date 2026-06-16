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
            $colunas = DB::select('DESCRIBE applications');
            $existentes = array_column($colunas, 'Field');

            $novas = [
                'reference_uuid'              => fn() => $table->uuid('reference_uuid')->unique()->after('id'),
                'process_number'              => fn() => $table->string('process_number')->unique()->nullable()->after('reference_uuid'),
                'full_name'                   => fn() => $table->string('full_name')->after('process_number'),
                'birth_name'                  => fn() => $table->string('birth_name')->nullable()->after('full_name'),
                'birth_date'                  => fn() => $table->date('birth_date')->nullable()->after('birth_name'),
                'birth_place'                 => fn() => $table->string('birth_place')->nullable()->after('birth_date'),
                'gender'                      => fn() => $table->string('gender')->nullable()->after('birth_place'),
                'nationality'                 => fn() => $table->string('nationality')->nullable()->after('gender'),
                'bi_number'                   => fn() => $table->string('bi_number')->nullable()->after('nationality'),
                'bi_issue_date'               => fn() => $table->date('bi_issue_date')->nullable()->after('bi_number'),
                'bi_issuing_entity'           => fn() => $table->string('bi_issuing_entity')->nullable()->after('bi_issue_date'),
                'nif'                         => fn() => $table->string('nif')->nullable()->after('bi_issuing_entity'),
                'email'                       => fn() => $table->string('email')->after('nif'),
                'phone'                       => fn() => $table->string('phone')->nullable()->after('email'),
                'alternative_phone'           => fn() => $table->string('alternative_phone')->nullable()->after('phone'),
                'address'                     => fn() => $table->text('address')->nullable()->after('alternative_phone'),
                'postal_code'                 => fn() => $table->string('postal_code')->nullable()->after('address'),
                'city'                        => fn() => $table->string('city')->nullable()->after('postal_code'),
                'province'                    => fn() => $table->string('province')->nullable()->after('city'),
                'document_type'               => fn() => $table->string('document_type')->nullable()->after('province'),
                'professional_category'       => fn() => $table->string('professional_category')->nullable()->after('document_type'),
                'specialization'              => fn() => $table->string('specialization')->nullable()->after('professional_category'),
                'institution'                 => fn() => $table->string('institution')->nullable()->after('specialization'),
                'professional_license_number' => fn() => $table->string('professional_license_number')->nullable()->after('institution'),
                'professional_license_expiry' => fn() => $table->date('professional_license_expiry')->nullable()->after('professional_license_number'),
                'status'                      => fn() => $table->string('status')->default('submetido')->after('professional_license_expiry'),
                'admin_notes'                 => fn() => $table->text('admin_notes')->nullable()->after('status'),
                'correction_feedback'         => fn() => $table->text('correction_feedback')->nullable()->after('admin_notes'),
                'submitted_at'                => fn() => $table->timestamp('submitted_at')->nullable()->after('correction_feedback'),
                'payment_confirmed_at'        => fn() => $table->timestamp('payment_confirmed_at')->nullable()->after('submitted_at'),
                'approved_at'                 => fn() => $table->timestamp('approved_at')->nullable()->after('payment_confirmed_at'),
                'document_issued_at'          => fn() => $table->timestamp('document_issued_at')->nullable()->after('approved_at'),
                'ip_address'                  => fn() => $table->string('ip_address')->nullable()->after('document_issued_at'),
                'user_agent'                  => fn() => $table->string('user_agent')->nullable()->after('ip_address'),
            ];

            foreach ($novas as $coluna => $definicao) {
                if (!in_array($coluna, $existentes)) {
                    $definicao();
                }
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            $colunas = DB::select('DESCRIBE payments');
            $existentes = array_column($colunas, 'Field');

            $novas = [
                'application_id'     => fn() => $table->unsignedBigInteger('application_id')->after('id'),
                'payment_uuid'       => fn() => $table->uuid('payment_uuid')->unique()->after('application_id'),
                'payment_reference'  => fn() => $table->string('payment_reference')->nullable()->after('payment_uuid'),
                'amount'             => fn() => $table->decimal('amount', 15, 2)->after('payment_reference'),
                'currency'           => fn() => $table->string('currency')->default('AOA')->after('amount'),
                'status'             => fn() => $table->string('status')->default('pendente')->after('currency'),
                'proof_path'         => fn() => $table->string('proof_path')->nullable()->after('status'),
                'proof_hash'         => fn() => $table->string('proof_hash')->nullable()->after('proof_path'),
                'proof_submitted_at' => fn() => $table->timestamp('proof_submitted_at')->nullable()->after('proof_hash'),
                'confirmed_at'       => fn() => $table->timestamp('confirmed_at')->nullable()->after('proof_submitted_at'),
                'confirmed_by'       => fn() => $table->unsignedBigInteger('confirmed_by')->nullable()->after('confirmed_at'),
                'rejection_reason'   => fn() => $table->text('rejection_reason')->nullable()->after('confirmed_by'),
                'bank_details'       => fn() => $table->json('bank_details')->nullable()->after('rejection_reason'),
                'deleted_at'         => fn() => $table->softDeletes()->after('bank_details'),
            ];

            foreach ($novas as $coluna => $definicao) {
                if (!in_array($coluna, $existentes)) {
                    $definicao();
                }
            }

            $colunasPagamento = DB::select('DESCRIBE payments');
            $existentesPagamento = array_column($colunasPagamento, 'Field');

            if (!in_array('application_id', $existentesPagamento)) {
                $table->foreign('application_id')
                      ->references('id')
                      ->on('applications')
                      ->onDelete('cascade');
            }

            if (!in_array('confirmed_by', $existentesPagamento)) {
                $table->foreign('confirmed_by')
                      ->references('id')
                      ->on('users')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropForeign(['confirmed_by']);
            $table->dropColumn([
                'application_id', 'payment_uuid', 'payment_reference', 'amount',
                'currency', 'status', 'proof_path', 'proof_hash', 'proof_submitted_at',
                'confirmed_at', 'confirmed_by', 'rejection_reason', 'bank_details', 'deleted_at',
            ]);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'reference_uuid', 'process_number', 'full_name', 'birth_name', 'birth_date',
                'birth_place', 'gender', 'nationality', 'bi_number', 'bi_issue_date',
                'bi_issuing_entity', 'nif', 'email', 'phone', 'alternative_phone', 'address',
                'postal_code', 'city', 'province', 'document_type', 'professional_category',
                'specialization', 'institution', 'professional_license_number',
                'professional_license_expiry', 'status', 'admin_notes', 'correction_feedback',
                'submitted_at', 'payment_confirmed_at', 'approved_at', 'document_issued_at',
                'ip_address', 'user_agent',
            ]);
        });
    }
};