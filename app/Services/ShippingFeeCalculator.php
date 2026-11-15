<?php

namespace App\Services;

class ShippingFeeCalculator
{
    public static function forItems(iterable $items): float
    {
        $billableWeight = 0.0;

        foreach ($items as $item) {
            $product = $item->product ?? null;
            $quantity = max(1, (int) ($item->quantity ?? 1));
            $actualWeight = max(0.001, (float) ($product?->weight_kg ?? 1));
            $dimensions = [
                (float) ($product?->length_cm ?? 0),
                (float) ($product?->width_cm ?? 0),
                (float) ($product?->height_cm ?? 0),
            ];
            $volumetricWeight = min($dimensions) > 0
                ? array_product($dimensions) / (float) config('shipping.volumetric_divisor', 5000)
                : 0;

            $billableWeight += max($actualWeight, $volumetricWeight) * $quantity;
        }

        $brackets = config('shipping.weight_brackets', []);
        foreach ($brackets as $bracket) {
            if ($billableWeight <= (float) $bracket['max_weight_kg']) {
                return (float) $bracket['fee'];
            }
        }

        $lastBracket = end($brackets);
        $lastWeight = (float) ($lastBracket['max_weight_kg'] ?? 20);
        $lastFee = (float) ($lastBracket['fee'] ?? 90);
        $additionalBracketWeight = max(0.001, (float) config('shipping.additional_bracket_weight_kg', 5));
        $additionalBracketFee = (float) config('shipping.additional_bracket_fee', 10);
        $additionalBrackets = (int) ceil(($billableWeight - $lastWeight) / $additionalBracketWeight);

        return round($lastFee + $additionalBrackets * $additionalBracketFee, 2);
    }
}
