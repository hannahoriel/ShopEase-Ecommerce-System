<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('delivery_hub_scanner_states')) {
            Schema::create('delivery_hub_scanner_states', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('logistics_delivery_hub_id')->unique('dh_scanner_hub_unique')
                    ->constrained('logistics_delivery_hubs')->cascadeOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->json('state');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_hub_scanner_states');
    }
};
