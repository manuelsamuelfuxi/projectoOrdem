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

            if (!in_array('nationality', $existentes)) {
                $table->string('nationality')->nullable()->after('gender');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $existentes = array_column(DB::select('DESCRIBE applications'), 'Field');

            if (in_array('nationality', $existentes)) {
                $table->dropColumn('nationality');
            }
        });
    }
};