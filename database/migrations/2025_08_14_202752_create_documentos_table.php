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
    Schema::create('documentos', function (Blueprint $table) {
        $table->id();
        $table->string('titulo', 200);
        $table->text('descricao')->nullable();
        $table->string('caminho_arquivo', 300);
        $table->boolean('ativo')->default(true);
        $table->timestamps(); // renomeie o data_upload para os padrões do laravel
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};
