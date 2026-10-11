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
        Schema::create('exercicio_prescricao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescricao_id')->constrained('prescricoes')->cascadeOnDelete();
            $table->foreignId('exercicio_id')->constrained('exercicios')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table('prescricoes', function (Blueprint $table) {
            $table->dropForeign(['exercicio_id']);
            $table->dropColumn('exercicio_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescricoes', function (Blueprint $table) {
            $table->foreignId('exercicio_id')->nullable()->constrained('exercicios')->restrictOnDelete();
        });

        Schema::dropIfExists('exercicio_prescricao');
    }
};
