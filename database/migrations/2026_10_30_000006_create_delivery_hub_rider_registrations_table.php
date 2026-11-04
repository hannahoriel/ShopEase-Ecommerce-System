<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = 'delivery_hub_rider_registrations';

        // Keep the base table creation separate from constraints so a failed
        // MySQL constraint addition can be safely retried against a partial table.
        if (! Schema::hasTable($tableName)) {
            Schema::create($tableName, function (Blueprint $table): void {
                $table->id();
                $table->unsignedBigInteger('logistics_delivery_hub_id');
                $table->unsignedBigInteger('user_id');
                $table->string('status', 20)->default('pending');
                $table->string('rejection_reason', 500)->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable();
                $table->timestamp('reviewed_at')->nullable();
                $table->timestamps();
            });
        }

        $indexes = Schema::getIndexes($tableName);
        $hasIndex = static fn (array $columns, bool $unique = false): bool => collect($indexes)->contains(
            static fn (array $index): bool => $index['columns'] === $columns && (! $unique || $index['unique'])
        );

        Schema::table($tableName, function (Blueprint $table) use ($hasIndex): void {
            if (! $hasIndex(['user_id'], true)) {
                $table->unique('user_id', 'dh_rider_reg_user_unique');
            }
            if (! $hasIndex(['logistics_delivery_hub_id', 'status', 'created_at'])) {
                $table->index(['logistics_delivery_hub_id', 'status', 'created_at'], 'dh_rider_reg_hub_status_created_idx');
            }
        });

        $foreignKeys = Schema::getForeignKeys($tableName);
        $hasForeignKey = static fn (string $column): bool => collect($foreignKeys)->contains(
            static fn (array $foreignKey): bool => in_array($column, $foreignKey['columns'], true)
        );

        Schema::table($tableName, function (Blueprint $table) use ($hasForeignKey): void {
            if (! $hasForeignKey('logistics_delivery_hub_id')) {
                $table->foreign('logistics_delivery_hub_id', 'dh_rider_reg_hub_fk')
                    ->references('id')->on('logistics_delivery_hubs')->cascadeOnDelete();
            }
            if (! $hasForeignKey('user_id')) {
                $table->foreign('user_id', 'dh_rider_reg_user_fk')
                    ->references('id')->on('users')->cascadeOnDelete();
            }
            if (! $hasForeignKey('reviewed_by')) {
                $table->foreign('reviewed_by', 'dh_rider_reg_reviewer_fk')
                    ->references('id')->on('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_hub_rider_registrations');
    }
};
