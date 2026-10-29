<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('logistics_account_settings')) {
            Schema::create('logistics_account_settings', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->json('privacy_settings')->nullable();
                $table->json('notification_preferences')->nullable();
                $table->string('cover_photo_path')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('logistics_account_change_requests')) {
            Schema::create('logistics_account_change_requests', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('section', 40)->default('profile');
                $table->json('requested_data');
                $table->string('status', 20)->default('pending');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('review_note')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status', 'created_at'], 'log_acc_change_user_status_created_idx');
            });
        } elseif (! Schema::hasIndex('logistics_account_change_requests', 'log_acc_change_user_status_created_idx')
            && ! Schema::hasIndex('logistics_account_change_requests', 'logistics_account_change_requests_user_id_status_created_at_index')) {
            // A previous failed attempt may have created the table but not its index.
            Schema::table('logistics_account_change_requests', function (Blueprint $table): void {
                $table->index(['user_id', 'status', 'created_at'], 'log_acc_change_user_status_created_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_account_change_requests');
        Schema::dropIfExists('logistics_account_settings');
    }
};
