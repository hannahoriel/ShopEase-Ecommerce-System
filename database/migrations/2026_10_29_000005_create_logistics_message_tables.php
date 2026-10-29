<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_conversations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_id')->constrained('logistics')->cascadeOnDelete();
            $table->string('contact_type', 30);
            $table->foreignId('contact_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('logistics_branch_id')->nullable()->constrained('logistics_branches')->nullOnDelete();
            $table->foreignId('logistics_delivery_hub_id')->nullable()->constrained('logistics_delivery_hubs')->nullOnDelete();
            $table->timestamps();

            $table->unique(['logistics_id', 'contact_user_id'], 'logistics_conversation_contact_unique');
            $table->index(['contact_user_id', 'updated_at']);
        });

        Schema::create('logistics_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('logistics_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('body')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime_type', 150)->nullable();
            $table->unsignedBigInteger('attachment_size')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
            $table->index(['conversation_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_messages');
        Schema::dropIfExists('logistics_conversations');
    }
};
