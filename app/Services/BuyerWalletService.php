<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** All movements are server-controlled; never accept a top-up amount from the buyer. */
class BuyerWalletService
{
    private function cents($value): int
    {
        return (int) round(((float) $value) * 100);
    }

    private function money(int $cents): string
    {
        return number_format($cents / 100, 2, '.', '');
    }

    public function account(int $userId): object
    {
        DB::table('buyer_wallets')->insertOrIgnore([
            'user_id' => $userId,
            'balance' => '0.00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('buyer_wallets')->where('user_id', $userId)->first();
    }

    /** Must run inside the caller's DB transaction. Locks the balance row. */
    public function move(int $userId, string $type, $amount, string $refType, int $refId, ?string $description = null): void
    {
        if (! in_array($type, ['refund_credit', 'order_payment', 'payment_reversal'], true)) {
            throw new \InvalidArgumentException('Unsupported wallet transaction.');
        }

        $value = $this->cents($amount);
        if ($value <= 0) {
            throw new \InvalidArgumentException('Wallet movement must be positive.');
        }

        $signed = $type === 'order_payment' ? -$value : $value;
        $this->account($userId);
        $wallet = DB::table('buyer_wallets')->where('user_id', $userId)->lockForUpdate()->first();
        $existing = DB::table('buyer_wallet_transactions')
            ->where('type', $type)
            ->where('reference_type', $refType)
            ->where('reference_id', $refId)
            ->first();
        if ($existing) return;

        $next = $this->cents($wallet->balance) + $signed;
        if ($next < 0) {
            throw ValidationException::withMessages(['payment_method' => ['Insufficient ShopEase Wallet balance for the complete order.']]);
        }

        DB::table('buyer_wallets')->where('id', $wallet->id)->update([
            'balance' => $this->money($next),
            'updated_at' => now(),
        ]);
        DB::table('buyer_wallet_transactions')->insert([
            'wallet_id' => $wallet->id,
            'type' => $type,
            'amount' => $this->money($signed),
            'balance_after' => $this->money($next),
            'reference_type' => $refType,
            'reference_id' => $refId,
            'description' => $description,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
