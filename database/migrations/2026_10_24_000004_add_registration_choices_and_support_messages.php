<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riders', function (Blueprint $table): void {
            $table->string('courier_company', 100)->nullable();
            $table->string('logistics_operation', 100)->nullable();
            $table->string('municipal_hub', 150)->nullable();
        });

        Schema::create('rider_support_ticket_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained('rider_support_tickets')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->timestamps();
            $table->index(['support_ticket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_support_ticket_messages');
        Schema::table('riders', function (Blueprint $table): void {
            $table->dropColumn(['courier_company', 'logistics_operation', 'municipal_hub']);
        });
    }
};
