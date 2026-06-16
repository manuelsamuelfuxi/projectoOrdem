<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->uuid('public_id')->unique(); // Campo do Model
        $table->string('name');
        $table->string('email')->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password');
        $table->string('role')->default('admin'); // Campo do Enum
        $table->boolean('is_active')->default(true); // Campo que estava faltando
        $table->rememberToken();
        $table->softDeletes(); // Do Trait SoftDeletes no Model
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};