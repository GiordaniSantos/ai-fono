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
        Schema::dropIfExists('execucoes_prescricoes');

        Schema::create('execucoes_exercicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('exercicio_id')->constrained('exercicios')->cascadeOnDelete();
            
            $table->date('data_execucao');
            $table->text('comentario_fono')->nullable();
            $table->unsignedTinyInteger('nivel_satisfacao')->nullable();
            $table->timestamps();

            $table->unique(['paciente_id', 'exercicio_id', 'data_execucao'], 'paciente_exercicio_data_unique');
        });

        Schema::table('prescricoes', function (Blueprint $table) {
            $table->dropColumn('realizada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('execucoes_exercicios');

        Schema::table('prescricoes', function (Blueprint $table) {
            $table->boolean('realizada')->default(false);
        });

        Schema::create('execucoes_prescricoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('prescricao_id')->constrained('prescricoes')->cascadeOnDelete();
            $table->date('data_execucao');
            $table->text('comentario_fono')->nullable();
            $table->unsignedTinyInteger('nivel_satisfacao')->nullable();
            $table->timestamps();

            $table->unique(['prescricao_id', 'data_execucao'], 'prescricao_data_unique');
        });
    }
};
