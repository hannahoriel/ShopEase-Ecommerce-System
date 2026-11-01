<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_branch_routes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('origin_branch_id')->constrained('logistics_branches')->cascadeOnDelete();
            $table->foreignId('next_branch_id')->constrained('logistics_branches')->cascadeOnDelete();
            $table->string('status', 20)->default('active');
            $table->timestamps();
            $table->unique(['origin_branch_id', 'next_branch_id']);
            $table->index(['origin_branch_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_branch_routes');
    }
};
