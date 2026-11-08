<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_returns', function (Blueprint $table): void {
            $table->foreignId('complaint_id')->nullable()->unique()->constrained('complaints')->cascadeOnDelete();
            $table->string('problem_type', 80)->nullable();
            $table->string('buyer_solution', 30)->nullable();
            $table->date('pickup_date')->nullable();
            $table->string('pickup_time', 5)->nullable();
            $table->text('pickup_address')->nullable();
        });
    }
    public function down(): void
    {
        Schema::table('order_returns', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('complaint_id');
            $table->dropColumn(['problem_type','buyer_solution','pickup_date','pickup_time','pickup_address']);
        });
    }
};
