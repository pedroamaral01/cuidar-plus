<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros_de_cuidados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            // Nulo quando o paciente registra um cuidado avulso, fora de lembrete.
            $table->foreignId('lembrete_id')->nullable()->constrained('lembretes')->nullOnDelete();
            $table->string('titulo');
            $table->string('tipo', 20)->default('outro');
            $table->text('observacao')->nullable();
            $table->timestamp('realizado_em');
            $table->timestamps();

            $table->index(['usuario_id', 'realizado_em']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros_de_cuidados');
    }
};
