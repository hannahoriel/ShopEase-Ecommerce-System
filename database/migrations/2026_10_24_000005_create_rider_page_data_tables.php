<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_calculations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->string('expression', 255);
            $table->string('result', 32);
            $table->string('category', 30);
            $table->timestamps();
            $table->index(['rider_id', 'created_at']);
        });

        Schema::create('rider_order_archives', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('dismissed_at')->nullable();
            $table->timestamps();
            $table->unique(['rider_id', 'order_id']);
            $table->index(['rider_id', 'archived_at', 'dismissed_at']);
        });

        Schema::create('rider_order_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_order_archives');
        Schema::dropIfExists('rider_order_messages');
        Schema::dropIfExists('rider_calculations');
    }
};
