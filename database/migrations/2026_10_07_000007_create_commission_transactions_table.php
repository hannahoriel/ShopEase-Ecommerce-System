<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('commission_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->decimal('order_amount', 12, 2);
            $table->decimal('commission_rate', 5, 2)->default(10);
            $table->decimal('commission_amount', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->timestamp('earned_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'earned_at']);
            $table->index(['seller_id', 'status']);
        });

        $now = now();
        $rows = DB::table('orders')->get()->map(function (object $order) use ($now): array {
            $amount = (float) $order->total;
            $commission = (float) $order->commission_amount;
            $status = in_array($order->status, ['cancelled', 'refunded'], true)
                ? 'reversed'
                : ($order->status === 'completed' ? 'earned' : 'pending');

            return [
                'order_id' => $order->id,
                'seller_id' => $order->seller_id,
                'order_amount' => $order->total,
                'commission_rate' => $amount > 0 ? round(($commission / $amount) * 100, 2) : 0,
                'commission_amount' => $order->commission_amount,
                'status' => $status,
                'earned_at' => $status === 'earned' ? ($order->updated_at ?? $order->created_at) : null,
                'created_at' => $order->created_at ?? $now,
                'updated_at' => $order->updated_at ?? $now,
            ];
        });

        foreach ($rows->chunk(500) as $chunk) {
            DB::table('commission_transactions')->insert($chunk->all());
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('commission_transactions');
    }
};
