<?php

namespace App\Models\Seller;

use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SellerConversation extends Model
{
    public const TYPE_BUYER = 'buyers';

    public const TYPE_COMPLAINT = 'complaints';

    protected $fillable = [
        'seller_id',
        'type',
        'buyer_id',
        'order_id',
        'product_id',
        'complaint_id',
        'seed_key',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SellerMessage::class, 'conversation_id')->orderBy('created_at')->orderBy('id');
    }
}
