<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('announcements', function (Blueprint $table): void {
            $table->string('type', 30)->default('Announcement')->after('title');
            $table->string('audience', 30)->default('All Users')->after('badge_label');
            $table->string('status', 20)->default('Published')->after('audience');
            $table->timestamp('published_at')->nullable()->after('status');
            $table->string('banner_path')->nullable()->after('published_at');
            $table->foreignId('created_by')->nullable()->after('banner_path')->constrained('users')->nullOnDelete();
        });

        Schema::create('platform_policies', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 80);
            $table->string('category', 80);
            $table->string('description', 500);
            $table->text('content');
            $table->string('status', 20)->default('Published');
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('platform_policies');

        Schema::table('announcements', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn([
                'type',
                'audience',
                'status',
                'published_at',
                'banner_path',
            ]);
        });
    }
};
