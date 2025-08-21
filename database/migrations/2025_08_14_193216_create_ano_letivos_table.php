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
    Schema::create('ano_letivos', function (Blueprint $table) {
        $table->id();
        $table->integer('ano')->unsigned();
        $table->string('status', 20)->default('ativo');
        $table->foreignId('escola_id')->constrained('escolas')->onDelete('cascade');
        $table->timestamps();

        // Garante que não haja um ano letivo duplicado para a mesma escola
        $table->unique(['ano', 'escola_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ano_letivos');
    }
};
