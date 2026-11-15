<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'privacy_acknowledged_at')) {
                $table->timestamp('privacy_acknowledged_at')->nullable();
            }
            if (! Schema::hasColumn('users', 'privacy_version')) {
                $table->string('privacy_version', 40)->nullable();
            }
            if (! Schema::hasColumn('users', 'privacy_acknowledged_role')) {
                $table->string('privacy_acknowledged_role', 30)->nullable();
            }
        });

        Schema::table('courier_registrations', function (Blueprint $table): void {
            if (! Schema::hasColumn('courier_registrations', 'privacy_acknowledged_at')) {
                $table->timestamp('privacy_acknowledged_at')->nullable();
            }
            if (! Schema::hasColumn('courier_registrations', 'privacy_version')) {
                $table->string('privacy_version', 40)->nullable();
            }
            if (! Schema::hasColumn('courier_registrations', 'privacy_acknowledged_role')) {
                $table->string('privacy_acknowledged_role', 30)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('courier_registrations', function (Blueprint $table): void {
            $columns = array_filter([
                Schema::hasColumn('courier_registrations', 'privacy_acknowledged_at') ? 'privacy_acknowledged_at' : null,
                Schema::hasColumn('courier_registrations', 'privacy_version') ? 'privacy_version' : null,
                Schema::hasColumn('courier_registrations', 'privacy_acknowledged_role') ? 'privacy_acknowledged_role' : null,
            ]);
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        Schema::table('users', function (Blueprint $table): void {
            $columns = array_filter([
                Schema::hasColumn('users', 'privacy_acknowledged_at') ? 'privacy_acknowledged_at' : null,
                Schema::hasColumn('users', 'privacy_version') ? 'privacy_version' : null,
                Schema::hasColumn('users', 'privacy_acknowledged_role') ? 'privacy_acknowledged_role' : null,
            ]);
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
