<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->orderBy('id')->chunkById(500, function ($orders): void {
            foreach ($orders as $order) {
                $commission = round((float) $order->total * 0.10, 2);

                DB::table('orders')
                    ->where('id', $order->id)
                    ->update(['commission_amount' => $commission]);

                DB::table('commission_transactions')
                    ->where('order_id', $order->id)
                    ->update([
                        'order_amount' => $order->total,
                        'commission_rate' => 10,
                        'commission_amount' => $commission,
                    ]);
            }
        });
    }

    public function down(): void
    {
        // Previous commission amounts cannot be reliably reconstructed.
    }
};
