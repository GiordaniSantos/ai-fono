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
        Schema::create('prescricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fonoaudiologo_id')->constrained('fonoaudiologos')->cascadeOnDelete();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('exercicio_id')->constrained('exercicios')->restrictOnDelete();

            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->unsignedSmallInteger('frequencia_diaria')->default(1)->comment('Vezes ao dia');
            $table->boolean('realizada')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescricoes');
    }
};
