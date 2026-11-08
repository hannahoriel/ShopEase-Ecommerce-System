<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('seller_vouchers', function (Blueprint $table): void {
            $table->unsignedBigInteger('seller_id')->nullable()->change();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('platform_code', 20)->nullable()->unique();
        });
    }

    public function down(): void
    {
        // Platform records must be retained; seller_id therefore stays nullable.
        Schema::table('seller_vouchers', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('created_by');
            $table->dropUnique(['platform_code']);
            $table->dropColumn('platform_code');
        });
    }
};
