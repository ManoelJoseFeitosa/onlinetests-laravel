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
    Schema::create('matriculas', function (Blueprint $table) {
        $table->id();
        $table->foreignId('aluno_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('serie_id')->constrained()->onDelete('cascade');
        $table->foreignId('ano_letivo_id')->constrained('ano_letivos')->onDelete('cascade');
        $table->string('status', 30)->default('cursando');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
