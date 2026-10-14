<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('admin_account_profiles', function (Blueprint $table) {
            $table->string('admin_id_photo_path')->nullable();
        });
    }
    public function down(): void {
        Schema::table('admin_account_profiles', function (Blueprint $table) {
            $table->dropColumn('admin_id_photo_path');
        });
    }
};
