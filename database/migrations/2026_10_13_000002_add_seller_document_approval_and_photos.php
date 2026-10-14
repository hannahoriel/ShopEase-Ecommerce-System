<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('sellers', function(Blueprint $table) {
            if (!Schema::hasColumn('sellers','cover_photo_path')) $table->string('cover_photo_path')->nullable();
            if (!Schema::hasColumn('sellers','profile_photo_path')) $table->string('profile_photo_path')->nullable();
        });
        Schema::create('seller_business_permits',function(Blueprint $table){
            $table->id();$table->unsignedBigInteger('seller_id')->index();$table->string('path');$table->string('original_name')->nullable();$table->unsignedBigInteger('approved_by')->nullable();$table->timestamps();
        });
        Schema::create('seller_document_change_requests',function(Blueprint $table){
            $table->id();$table->unsignedBigInteger('seller_id')->index();$table->unsignedBigInteger('user_id')->index();$table->string('request_type',40)->index();$table->string('document_path');$table->string('original_name')->nullable();$table->text('reason');$table->string('status',20)->default('pending')->index();$table->unsignedBigInteger('reviewed_by')->nullable();$table->timestamp('reviewed_at')->nullable();$table->text('admin_notes')->nullable();$table->timestamps();
        });
    }
    public function down():void {
        Schema::dropIfExists('seller_document_change_requests');Schema::dropIfExists('seller_business_permits');
        Schema::table('sellers',function(Blueprint $table){
            if(Schema::hasColumn('sellers','cover_photo_path')) $table->dropColumn('cover_photo_path');
            if(Schema::hasColumn('sellers','profile_photo_path')) $table->dropColumn('profile_photo_path');
        });
    }
};
