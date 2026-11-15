<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['sorting_center_parcel_issues', 'delivery_hub_parcel_issues'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            $columns = Schema::getColumnListing($tableName);
            Schema::table($tableName, function (Blueprint $table) use ($columns): void {
                if (! in_array('admin_decision', $columns, true)) {
                    $table->string('admin_decision', 30)->nullable();
                }
                if (! in_array('decision_notes', $columns, true)) {
                    $table->text('decision_notes')->nullable();
                }
                if (! in_array('decided_by', $columns, true)) {
                    $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
                }
                if (! in_array('decided_at', $columns, true)) {
                    $table->timestamp('decided_at')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['sorting_center_parcel_issues', 'delivery_hub_parcel_issues'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                $columns = Schema::getColumnListing($tableName);
                Schema::table($tableName, function (Blueprint $table) use ($columns): void {
                    if (in_array('decided_by', $columns, true)) {
                        $table->dropConstrainedForeignId('decided_by');
                    }
                    $drop = array_values(array_intersect(['admin_decision', 'decision_notes', 'decided_at'], $columns));
                    if ($drop) {
                        $table->dropColumn($drop);
                    }
                });
            }
        }
    }
};
