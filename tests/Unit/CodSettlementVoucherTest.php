<?php

namespace Tests\Unit;

use App\Models\Admin\Order;
use App\Services\CodSettlementService;
use PHPUnit\Framework\TestCase;

class CodSettlementVoucherTest extends TestCase
{
    public function test_platform_merchandise_discount_is_reimbursed_to_seller(): void
    {
        $order = new Order([
            'voucher_type' => 'merchandise',
            'voucher_funding_source' => 'platform',
            'voucher_discount' => 20,
        ]);

        // Buyer paid ₱180 for ₱200 goods after a 10% ShopEase voucher.
        // The seller receives the full goods value less the regular ₱20 commission.
        $this->assertSame(180.0, CodSettlementService::sellerAmount($order, 180, 20, 0));
    }

    public function test_shipping_voucher_funder_covers_discount_and_logistics_rate_is_separate(): void
    {
        $platformOrder = new Order([
            'voucher_type' => 'shipping',
            'voucher_funding_source' => 'platform',
            'voucher_discount' => 25,
        ]);
        $sellerOrder = new Order([
            'voucher_type' => 'shipping',
            'voucher_funding_source' => 'seller',
            'voucher_discount' => 25,
        ]);

        // Shipping discounts do not change the seller's product payout when ShopEase funds them.
        $this->assertSame(180.0, CodSettlementService::sellerAmount($platformOrder, 200, 20, 25));
        // A seller-funded shipping discount is charged to seller proceeds.
        $this->assertSame(155.0, CodSettlementService::sellerAmount($sellerOrder, 200, 20, 25));
        // Settlement keeps the agreed logistics fee at ₱50 for both platform and seller promos.
        $this->assertSame(50.0, CodSettlementService::LOGISTICS_SHIPPING_FEE);
    }
}
