<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->string('order_number')->nullable()->unique();
            $table->string('delivery_name')->nullable();
            $table->string('delivery_phone', 30)->nullable();
            $table->text('delivery_address')->nullable();
            $table->string('payment_method')->default('Cash on Delivery');
        });

        DB::table('orders')->whereNull('order_number')->orderBy('id')->chunkById(200, function ($orders): void {
            foreach ($orders as $order) {
                $date = substr(preg_replace('/\D/', '', (string) $order->created_at), 0, 8) ?: now()->format('Ymd');
                DB::table('orders')->where('id', $order->id)->update([
                    'order_number' => 'SE-' . $date . '-' . str_pad((string) $order->id, 8, '0', STR_PAD_LEFT),
                ]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique(['order_number']);
            $table->dropColumn([
                'order_number',
                'delivery_name',
                'delivery_phone',
                'delivery_address',
                'payment_method',
            ]);
        });
    }
};
