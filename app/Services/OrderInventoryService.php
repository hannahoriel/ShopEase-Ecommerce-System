<?php

namespace App\Services;

use App\Models\Admin\Order;
use App\Models\Admin\OrderItem;
use App\Models\Seller\Product;
use App\Models\Seller\ProductOption;
use App\Models\Seller\ProductVariantCombination;
use Illuminate\Validation\ValidationException;

class OrderInventoryService
{
    private const OPTION_GROUPS = [
        'variation' => ['type' => 'variation', 'plural' => 'variations'],
        'color' => ['type' => 'color', 'plural' => 'colors'],
        'size' => ['type' => 'size', 'plural' => 'sizes'],
    ];

    public function reserve(OrderItem $item): void
    {
        $this->adjustItemStock($item, -1);
    }

    public function restoreOrder(Order $order): void
    {
        foreach ($order->items()->get() as $item) {
            $this->adjustItemStock($item, 1);
        }
    }

    private function adjustItemStock(OrderItem $item, int $direction): void
    {
        if (! $item->product_id) {
            return;
        }

        $product = Product::query()
            ->lockForUpdate()
            ->find($item->product_id);

        if (! $product) {
            return;
        }

        $quantity = (int) $item->quantity;

        $selectedOptions = [];
        foreach (self::OPTION_GROUPS as $field => $group) {
            $value = trim((string) ($item->{$field} ?? ''));
            if ($value === '') {
                continue;
            }

            $option = ProductOption::query()
                ->where('product_id', $product->id)
                ->where('type', $group['type'])
                ->where('name', $value)
                ->lockForUpdate()
                ->first();

            if (! $option) {
                if ($direction > 0) {
                    continue;
                }

                throw ValidationException::withMessages([
                    'cart' => ["The selected {$field} for {$product->name} is no longer available."],
                ]);
            }

            $selectedOptions[$group['plural']] = $option;
        }

        $combinations = ProductVariantCombination::query()
            ->where('product_id', $product->id)
            ->lockForUpdate()
            ->get();
        $matchedCombination = null;

        if ($combinations->isNotEmpty()) {
            $matchedCombination = $combinations->first(
                fn (ProductVariantCombination $combination) => $this->combinationMatches(
                    $combination,
                    $item
                )
            );

            if (! $matchedCombination && $direction < 0) {
                throw ValidationException::withMessages([
                    'cart' => ["The selected combination for {$product->name} is no longer available."],
                ]);
            }

            if ($matchedCombination && $direction < 0 && (! $matchedCombination->available || $matchedCombination->stock < $quantity)) {
                throw ValidationException::withMessages([
                    'cart' => ["There is not enough stock for the selected combination of {$product->name}."],
                ]);
            }
        }

        if ($combinations->isEmpty()) {
            if ($direction < 0 && $product->stock_quantity < $quantity) {
                throw ValidationException::withMessages([
                    'cart' => ["There is not enough stock for {$product->name}. Refresh your cart and try again."],
                ]);
            }

            foreach ($selectedOptions as $option) {
                if ($direction < 0 && $option->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => ["There is not enough stock for {$product->name} ({$option->name})."],
                    ]);
                }
            }
        }

        if ($matchedCombination) {
            $matchedCombination->stock += $direction * $quantity;
            $matchedCombination->save();

            $product->stock_quantity = $combinations->sum('stock');

            foreach ($selectedOptions as $plural => $option) {
                $option->stock = $combinations
                    ->filter(fn (ProductVariantCombination $combination): bool => $this->choiceMatchesOption(
                        $combination,
                        $plural,
                        $option->name
                    ))
                    ->sum('stock');
                $option->save();
            }
        } else {
            $product->stock_quantity += $direction * $quantity;
        }

        $product->save();

        if ($combinations->isEmpty()) {
            foreach ($selectedOptions as $option) {
                $option->stock += $direction * $quantity;
                $option->save();
            }
        }
    }

    private function choiceMatchesOption(
        ProductVariantCombination $combination,
        string $plural,
        string $optionName
    ): bool {
        $singular = match ($plural) {
            'variations' => 'variation',
            'colors' => 'color',
            'sizes' => 'size',
            default => $plural,
        };

        $choice = $combination->choices[$plural] ?? $combination->choices[$singular] ?? null;

        return (string) $choice === $optionName;
    }

    private function combinationMatches(ProductVariantCombination $combination, OrderItem $item): bool
    {
        $choices = $combination->choices ?? [];
        if ($choices === []) {
            return false;
        }

        foreach ($choices as $group => $choice) {
            $field = match ($group) {
                'variations', 'variation' => 'variation',
                'colors', 'color' => 'color',
                'sizes', 'size' => 'size',
                default => null,
            };

            if (! $field || trim((string) ($item->{$field} ?? '')) !== (string) $choice) {
                return false;
            }
        }

        foreach (self::OPTION_GROUPS as $field => $group) {
            $submitted = trim((string) ($item->{$field} ?? ''));
            $choice = $choices[$group['plural']] ?? $choices[$field] ?? null;
            if ($submitted !== (string) ($choice ?? '')) {
                return false;
            }
        }

        return true;
    }
}
