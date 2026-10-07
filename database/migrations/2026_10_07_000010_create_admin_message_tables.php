<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_conversations', function (Blueprint $table): void {
            $table->id();
            $table->string('category', 20);
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('complaint_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('complaint_party', 10)->nullable();
            $table->timestamps();

            $table->unique(['category', 'user_id', 'complaint_id', 'complaint_party'], 'admin_conversations_identity_unique');
            $table->index(['category', 'updated_at']);
        });

        Schema::create('admin_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->constrained('admin_conversations')->cascadeOnDelete();
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
        Schema::dropIfExists('admin_messages');
        Schema::dropIfExists('admin_conversations');
    }
};
