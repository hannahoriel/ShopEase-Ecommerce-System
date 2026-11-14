<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seller_pickup_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rider_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('attempt_number');
            $table->date('pickup_date');
            $table->time('pickup_time');
            $table->string('status', 20)->default('scheduled');
            $table->timestamp('missed_at')->nullable();
            $table->timestamp('picked_up_at')->nullable();
            $table->timestamps();
            $table->unique(['order_id', 'attempt_number']);
            $table->index(['status', 'pickup_date']);
        });

        DB::table('orders')
            ->join('shipments', 'shipments.order_id', '=', 'orders.id')
            ->where('orders.status', 'to_ship')
            ->whereNotNull('orders.pickup_date')
            ->whereNotNull('orders.pickup_time')
            ->select([
                'orders.id as order_id',
                'shipments.rider_id',
                'orders.pickup_date',
                'orders.pickup_time',
            ])
            ->orderBy('orders.id')
            ->get()
            ->each(function (object $order): void {
                DB::table('seller_pickup_attempts')->insert([
                    'order_id' => $order->order_id,
                    'rider_id' => $order->rider_id,
                    'attempt_number' => 1,
                    'pickup_date' => $order->pickup_date,
                    'pickup_time' => $order->pickup_time,
                    'status' => 'scheduled',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            });

        Schema::table('rider_earning_entries', function (Blueprint $table): void {
            $table->foreignId('posted_by')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('rider_earning_entries', function (Blueprint $table): void {
            $table->foreignId('posted_by')->nullable(false)->change();
        });

        Schema::dropIfExists('seller_pickup_attempts');
    }
};
