<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('suspended_until')->nullable()->after('registration_status');
            $table->string('account_action_reason')->nullable()->after('suspended_until');
            $table->string('account_action_details', 300)->nullable()->after('account_action_reason');
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
