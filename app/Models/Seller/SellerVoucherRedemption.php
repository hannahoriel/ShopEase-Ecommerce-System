<?php

namespace App\Models\Seller;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerVoucherRedemption extends Model
{
    protected $fillable = ['seller_voucher_id', 'buyer_id', 'order_id', 'discount_amount'];
    protected $casts = ['discount_amount' => 'decimal:2'];

    public function voucher(): BelongsTo { return $this->belongsTo(SellerVoucher::class, 'seller_voucher_id'); }
    public function buyer(): BelongsTo { return $this->belongsTo(User::class, 'buyer_id'); }
}
