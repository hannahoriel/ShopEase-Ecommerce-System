<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('buyer_wallets', function(Blueprint $table){
   $table->id();$table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
   $table->decimal('balance',14,2)->default(0);$table->timestamps();
  });
  Schema::create('buyer_wallet_transactions',function(Blueprint $table){
   $table->id();$table->foreignId('wallet_id')->constrained('buyer_wallets')->cascadeOnDelete();
   $table->string('type',30); // refund_credit, order_payment, payment_reversal
   $table->decimal('amount',14,2); // positive for credit, negative for payment
   $table->decimal('balance_after',14,2);
   $table->string('reference_type',40);$table->unsignedBigInteger('reference_id');
   $table->string('description',255)->nullable();$table->timestamps();
   $table->unique(['type','reference_type','reference_id'],'wallet_event_unique');
   $table->index(['wallet_id','created_at']);
  });
 }
 public function down():void {Schema::dropIfExists('buyer_wallet_transactions');Schema::dropIfExists('buyer_wallets');}
};
