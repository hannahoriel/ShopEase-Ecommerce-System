<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('seller_vouchers', fn (Blueprint $table) => $table->string('eligible_category')->nullable());
    }
    public function down(): void
    {
        Schema::table('seller_vouchers', fn (Blueprint $table) => $table->dropColumn('eligible_category'));
    }
};
