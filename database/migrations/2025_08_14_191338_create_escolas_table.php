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
    Schema::create('escolas', function (Blueprint $table) {
        $table->id(); // Equivalente a: id = db.Column(db.Integer, primary_key=True)
        $table->string('nome', 150)->unique();
        $table->string('cnpj', 18)->unique()->nullable();
        $table->string('status', 20)->default('ativo');
        $table->string('plano', 20)->default('essencial');
        $table->float('media_recuperacao')->default(6.0);
        $table->timestamps(); // Equivalente a: data_cadastro = db.Column(db.DateTime...) e adiciona o updated_at
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escolas');
    }
};
