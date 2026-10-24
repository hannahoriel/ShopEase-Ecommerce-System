<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_attendance_records', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->date('attendance_date');
            $table->timestamp('clock_in_at')->nullable();
            $table->string('clock_in_photo')->nullable();
            $table->timestamp('clock_out_at')->nullable();
            $table->string('clock_out_photo')->nullable();
            $table->string('status', 20)->default('present');
            $table->timestamps();
            $table->unique(['rider_id', 'attendance_date']);
        });

        Schema::create('rider_recipients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone', 30)->nullable();
            $table->string('address');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('rider_cod_collections', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->timestamp('collected_at');
            $table->timestamps();
        });

        Schema::create('rider_remittances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->string('method', 30);
            $table->string('reference_number', 120)->nullable();
            $table->string('receipt_path')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->index(['rider_id', 'status', 'created_at']);
        });

        Schema::create('rider_earning_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('posted_by')->constrained('users')->restrictOnDelete();
            $table->string('type', 30);
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->string('status', 20)->default('posted');
            $table->timestamp('posted_at');
            $table->timestamps();
            $table->index(['rider_id', 'posted_at']);
        });

        Schema::create('rider_support_tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->string('priority', 20)->default('medium');
            $table->string('status', 20)->default('open');
            $table->text('resolution')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
            $table->index(['rider_id', 'status', 'created_at']);
        });

        Schema::create('rider_delivery_actions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('rider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('action', 20);
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->date('reschedule_date')->nullable();
            $table->string('reschedule_slot')->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_delivery_actions');
        Schema::dropIfExists('rider_support_tickets');
        Schema::dropIfExists('rider_earning_entries');
        Schema::dropIfExists('rider_remittances');
        Schema::dropIfExists('rider_cod_collections');
        Schema::dropIfExists('rider_recipients');
        Schema::dropIfExists('rider_attendance_records');
    }
};
