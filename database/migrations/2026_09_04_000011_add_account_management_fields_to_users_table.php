<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'registration_status')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('registration_status', 20)->default('active');
            });
        }
        if (! Schema::hasColumn('users', 'approved_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('approved_at')->nullable();
            });
        }
        if (! Schema::hasColumn('users', 'rejected_at')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->timestamp('rejected_at')->nullable();
            });
        }
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'suspended_until')) {
                $table->timestamp('suspended_until')->nullable()->after('registration_status');
            }
            if (! Schema::hasColumn('users', 'account_action_reason')) {
                $table->string('account_action_reason')->nullable()->after('suspended_until');
            }
            if (! Schema::hasColumn('users', 'account_action_details')) {
                $table->string('account_action_details', 300)->nullable()->after('account_action_reason');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'suspended_until',
                'account_action_reason',
                'account_action_details',
            ]);
        });
    }
};
