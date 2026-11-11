<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sorting_center_parcel_issues')) {
            return;
        }

        Schema::create('sorting_center_parcel_issues', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sorting_center_id')->constrained('logistics_branches')->cascadeOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('tracking_number', 90)->index();
            $table->string('issue_type', 80);
            $table->text('description');
            $table->string('evidence_name')->nullable();
            $table->string('evidence_path')->nullable();
            $table->string('status', 40)->default('Open');
            $table->json('accountability')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sorting_center_parcel_issues');
    }
};
