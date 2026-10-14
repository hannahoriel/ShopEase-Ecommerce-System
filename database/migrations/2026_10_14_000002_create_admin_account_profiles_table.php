<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('admin_account_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('first_name',100)->nullable();
            $table->string('last_name',100)->nullable();
            $table->date('birthday')->nullable();
            $table->string('sex',32)->nullable();
            $table->string('contact_number',25)->nullable();
            $table->string('profile_photo_path')->nullable();
            $table->string('cover_photo_path')->nullable();
            $table->json('preferences')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('admin_account_profiles'); }
};
