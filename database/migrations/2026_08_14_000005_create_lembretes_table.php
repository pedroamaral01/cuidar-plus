<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembretes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('titulo');
            $table->string('tipo', 20)->default('outro');
            $table->time('horario');
            $table->boolean('ativo')->default(true);
            // Marca a última vez que o lembrete gerou notificação, para o
            // agendador não notificar o mesmo horário duas vezes.
            $table->timestamp('notificado_em')->nullable();
            $table->timestamps();

            $table->index(['usuario_id', 'ativo']);
            $table->index(['ativo', 'horario']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembretes');
    }
};
