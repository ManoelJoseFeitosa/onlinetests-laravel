<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('audit_logs', function (Blueprint $table) {
        $table->id();
        $table->timestamp('timestamp')->useCurrent();
        $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
        $table->string('user_email', 150);
        $table->string('action', 100)->index();
        $table->string('target_type', 50)->nullable()->index();
        $table->integer('target_id')->unsigned()->nullable();
        $table->json('details')->nullable();
        $table->string('ip_address', 45)->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
