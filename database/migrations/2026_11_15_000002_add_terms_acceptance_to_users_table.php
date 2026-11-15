<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable();
            }
            if (! Schema::hasColumn('users', 'terms_version')) {
                $table->string('terms_version', 40)->nullable();
            }
            if (! Schema::hasColumn('users', 'terms_accepted_role')) {
                $table->string('terms_accepted_role', 30)->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $columns = array_filter([
                Schema::hasColumn('users', 'terms_accepted_at') ? 'terms_accepted_at' : null,
                Schema::hasColumn('users', 'terms_version') ? 'terms_version' : null,
                Schema::hasColumn('users', 'terms_accepted_role') ? 'terms_accepted_role' : null,
            ]);
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
