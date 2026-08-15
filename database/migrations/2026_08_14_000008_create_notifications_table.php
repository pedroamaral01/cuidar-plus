<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabela nativa do trait Notifiable. O nome em inglês é mantido de propósito:
 * é o padrão reconhecido pela infraestrutura de Notifications do Laravel, e
 * renomeá-la exigiria reescrever o que o framework já entrega pronto
 * (ver docs/02-arquitetura-tecnica.md, seção 9).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'notifications_nao_lidas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
