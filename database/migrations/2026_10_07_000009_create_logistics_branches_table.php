<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_branches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('logistics_id')->constrained('logistics')->cascadeOnDelete();
            $table->string('name', 150);
            $table->string('address', 500);
            $table->string('contact_person')->nullable();
            $table->string('phone', 30)->nullable();
            $table->timestamps();

            $table->index(['logistics_id', 'name']);
        });

        Schema::table('riders', function (Blueprint $table): void {
            $table->foreignId('logistics_branch_id')
                ->nullable()
                ->after('user_id')
                ->constrained('logistics_branches')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('riders', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('logistics_branch_id');
        });

        Schema::dropIfExists('logistics_branches');
    }
};
