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
    Schema::table('resultados', function (Blueprint $table) {
        $table->decimal('nota_original', 5, 2)->nullable()->after('nota');
        $table->foreignId('nota_ajustada_por')->nullable()->constrained('users')->onDelete('set null')->after('nota_original');
        $table->text('justificativa_ajuste')->nullable()->after('nota_ajustada_por');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('resultados', function (Blueprint $table) {
            //
        });
    }
};
