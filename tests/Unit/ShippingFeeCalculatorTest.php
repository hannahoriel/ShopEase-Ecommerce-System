<?php

namespace Tests\Unit;

use App\Services\ShippingFeeCalculator;
use Tests\TestCase;

class ShippingFeeCalculatorTest extends TestCase
{
    public function test_shipping_fee_uses_total_actual_weight_per_shop(): void
    {
        config([
            'shipping.weight_brackets' => [
                ['max_weight_kg' => 3, 'fee' => 50],
                ['max_weight_kg' => 5, 'fee' => 60],
                ['max_weight_kg' => 10, 'fee' => 70],
                ['max_weight_kg' => 15, 'fee' => 80],
                ['max_weight_kg' => 20, 'fee' => 90],
            ],
            'shipping.additional_bracket_weight_kg' => 5,
            'shipping.additional_bracket_fee' => 10,
            'shipping.volumetric_divisor' => 5000,
        ]);
        $product = (object) ['weight_kg' => 1.2];
        $item = (object) ['product' => $product, 'quantity' => 2];

        $this->assertSame(50.0, ShippingFeeCalculator::forItems([$item]));
    }

    public function test_shipping_fee_uses_volumetric_weight_when_it_is_higher(): void
    {
        config([
            'shipping.weight_brackets' => [
                ['max_weight_kg' => 3, 'fee' => 50],
                ['max_weight_kg' => 5, 'fee' => 60],
                ['max_weight_kg' => 10, 'fee' => 70],
                ['max_weight_kg' => 15, 'fee' => 80],
                ['max_weight_kg' => 20, 'fee' => 90],
            ],
            'shipping.additional_bracket_weight_kg' => 5,
            'shipping.additional_bracket_fee' => 10,
            'shipping.volumetric_divisor' => 5000,
        ]);
        $product = (object) ['weight_kg' => 0.5, 'length_cm' => 40, 'width_cm' => 30, 'height_cm' => 25];
        $item = (object) ['product' => $product, 'quantity' => 1];

        $this->assertSame(70.0, ShippingFeeCalculator::forItems([$item]));
    }

    public function test_shipping_fee_uses_configured_weight_brackets(): void
    {
        config([
            'shipping.weight_brackets' => [
                ['max_weight_kg' => 3, 'fee' => 50],
                ['max_weight_kg' => 5, 'fee' => 60],
                ['max_weight_kg' => 10, 'fee' => 70],
                ['max_weight_kg' => 15, 'fee' => 80],
                ['max_weight_kg' => 20, 'fee' => 90],
            ],
            'shipping.additional_bracket_weight_kg' => 5,
            'shipping.additional_bracket_fee' => 10,
            'shipping.volumetric_divisor' => 5000,
        ]);

        $this->assertSame(50.0, ShippingFeeCalculator::forItems([(object) ['product' => (object) ['weight_kg' => 3], 'quantity' => 1]]));
        $this->assertSame(60.0, ShippingFeeCalculator::forItems([(object) ['product' => (object) ['weight_kg' => 3.01], 'quantity' => 1]]));
        $this->assertSame(60.0, ShippingFeeCalculator::forItems([(object) ['product' => (object) ['weight_kg' => 5], 'quantity' => 1]]));
        $this->assertSame(70.0, ShippingFeeCalculator::forItems([(object) ['product' => (object) ['weight_kg' => 5.01], 'quantity' => 1]]));
        $this->assertSame(70.0, ShippingFeeCalculator::forItems([(object) ['product' => (object) ['weight_kg' => 10], 'quantity' => 1]]));
        $this->assertSame(80.0, ShippingFeeCalculator::forItems([(object) ['product' => (object) ['weight_kg' => 10.01], 'quantity' => 1]]));
        $this->assertSame(100.0, ShippingFeeCalculator::forItems([(object) ['product' => (object) ['weight_kg' => 25], 'quantity' => 1]]));
        $this->assertSame(250.0, ShippingFeeCalculator::forItems([(object) ['product' => (object) ['weight_kg' => 100], 'quantity' => 1]]));
    }
}
