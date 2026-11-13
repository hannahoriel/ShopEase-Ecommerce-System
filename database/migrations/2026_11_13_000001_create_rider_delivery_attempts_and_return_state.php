<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_delivery_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('attempt_number');
            $table->string('reason', 120);
            $table->text('notes')->nullable();
            $table->string('evidence_path');
            $table->timestamps();
            $table->unique(['order_id', 'attempt_number']);
        });

        Schema::table('shipments', function (Blueprint $table): void {
            $table->string('return_to_seller_status', 30)->nullable()->after('rider_id');
            $table->timestamp('returned_to_seller_at')->nullable()->after('return_to_seller_status');
            $table->string('return_to_seller_proof_path')->nullable()->after('returned_to_seller_at');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropColumn(['return_to_seller_status', 'returned_to_seller_at', 'return_to_seller_proof_path']);
        });
        Schema::dropIfExists('rider_delivery_attempts');
    }
};
