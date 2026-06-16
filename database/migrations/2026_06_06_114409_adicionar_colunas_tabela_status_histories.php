<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('status_histories', function (Blueprint $table) {
            $table->string('from_status')->after('id');
            $table->string('to_status')->after('from_status');
            $table->foreignId('pedido_id')->constrained('applications')->after('to_status');
            $table->foreignId('changed_by')->nullable()->constrained('users')->after('pedido_id');
            $table->json('metadata')->nullable()->after('changed_by');
            $table->string('ip_address')->nullable()->after('metadata');
            $table->text('user_agent')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('status_histories', function (Blueprint $table) {
            $table->dropForeign(['pedido_id']);
            $table->dropForeign(['changed_by']);
            $table->dropColumn(['from_status', 'to_status', 'pedido_id', 'changed_by', 'metadata', 'ip_address', 'user_agent']);
        });
    }
};