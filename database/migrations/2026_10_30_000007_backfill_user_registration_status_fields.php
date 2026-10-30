<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'registration_status')) {
            Schema::table('users', fn (Blueprint $table) => $table->string('registration_status', 20)->default('active'));
        }
        if (! Schema::hasColumn('users', 'approved_at')) {
            Schema::table('users', fn (Blueprint $table) => $table->timestamp('approved_at')->nullable());
        }
        if (! Schema::hasColumn('users', 'rejected_at')) {
            Schema::table('users', fn (Blueprint $table) => $table->timestamp('rejected_at')->nullable());
        }

        if (Schema::hasTable('registrations')) {
            DB::table('registrations')
                ->whereNotNull('user_id')
                ->orderBy('id')
                ->get(['user_id', 'status', 'reviewed_at'])
                ->each(function (object $registration): void {
                    $status = match ($registration->status) {
                        'pending' => 'pending',
                        'rejected' => 'rejected',
                        default => 'active',
                    };
                    DB::table('users')->where('id', $registration->user_id)->update([
                        'registration_status' => $status,
                        'approved_at' => $status === 'active' ? $registration->reviewed_at : null,
                        'rejected_at' => $status === 'rejected' ? $registration->reviewed_at : null,
                    ]);
                });
        }
    }

    public function down(): void
    {
        $columns = array_values(array_filter(
            ['registration_status', 'approved_at', 'rejected_at'],
            fn (string $column): bool => Schema::hasColumn('users', $column),
        ));
        if ($columns !== []) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};
