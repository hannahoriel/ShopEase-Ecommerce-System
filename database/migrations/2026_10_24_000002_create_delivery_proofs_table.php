<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_proofs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->constrained('riders')->cascadeOnDelete();
            $table->string('status', 30);
            $table->string('scanned_code', 2048);
            $table->string('photo_path');
            $table->string('delivery_method', 30)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_proofs');
    }
};
