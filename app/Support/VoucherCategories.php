<?php

namespace App\Support;

class VoucherCategories
{
    public static function matches(string $productCategory, string $category): bool
    {
        $aliases = ['electronics' => 'electronics-and-gadgets', 'automotive' => 'automotive-motorcycle',
            'furniture' => 'furniture-and-office-equipment', 'office-supplies' => 'office-and-school-supplies'];
        $key = function (string $value) use ($aliases): string {
            $value = $aliases[$value] ?? $value;
            $value = config('admin.seller_compliance.categories.'.$value, $value);
            $value = preg_replace('/\band\b/i', '', $value);
            return strtolower(preg_replace('/[^a-z0-9]/i', '', $value));
        };
        return $key($productCategory) === $key($category);
    }
}
