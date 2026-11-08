<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_message_reads', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_message_id')->constrained('logistics_messages')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->useCurrent();
            $table->unique(['logistics_message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_message_reads');
    }
};
