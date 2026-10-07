<?php

namespace App\Services;

use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\SellerConversation;
use Illuminate\Support\Str;

class SellerMessageAutoReply
{
    private const FALLBACK = "Can't answer the question because it isn't related to the product or your order. Please ask about this product or your order.";

    public function reply(string $question, SellerConversation $conversation): string
    {
        $question = Str::lower(trim($question));
        $order = $conversation->order;
        $product = $conversation->product
            ?? $order?->items()->with('product')->get()->pluck('product')->filter()->first();

        if ($this->isOrderQuestion($question)) {
            return $order
                ? $this->orderReply($order)
                : 'Shipment timing and tracking become available in your order details after you place an order.';
        }

        if (! $product || ! $this->isProductQuestion($question)) {
            return self::FALLBACK;
        }

        return $this->productReply($question, $product);
    }

    private function isOrderQuestion(string $question): bool
    {
        return (bool) preg_match(
            '/\b(ship|shipment|shipping|deliver|delivery|tracking|track|courier|package|parcel|order|eta|kailan|nasaan|padala|dumating|maihatid)\b/u',
            $question,
        );
    }

    private function isProductQuestion(string $question): bool
    {
        return (bool) preg_match(
            '/\b(product|item|available|availability|stock|price|cost|magkano|presyo|kulay|colors?|sizes?|sukat|variants?|variations?|materials?|descriptions?|specifications?|what is this|tell me about|how much|come in|may stock|mayroon pa|meron pa|available pa)\b/u',
            $question,
        );
    }

    private function orderReply(Order $order): string
    {
        $status = Str::headline((string) $order->status);
        $shipment = $order->shipment;

        if (! $shipment) {
            return "Your order #{$order->order_number} is currently {$status}. Shipment tracking is not available yet.";
        }

        $details = [];
        if ($shipment->courier) {
            $details[] = "courier: {$shipment->courier}";
        }
        if ($shipment->tracking_number) {
            $details[] = "tracking number: {$shipment->tracking_number}";
        }
        if ($shipment->estimated_delivery) {
            $details[] = 'estimated delivery: '.$shipment->estimated_delivery->format('M j, Y');
        }
        if ($shipment->current_location) {
            $details[] = "latest location: {$shipment->current_location}";
        }
        $latestScan = $shipment->scans->first();
        if ($latestScan) {
            $details[] = 'latest shipping status: '.Str::headline($latestScan->status);
            if ($latestScan->location && $latestScan->location !== $shipment->current_location) {
                $details[] = "scan location: {$latestScan->location}";
            }
        }

        return "Your order #{$order->order_number} is currently {$status}."
            .($details ? ' Shipment details — '.implode('; ', $details).'.' : ' Shipment details are not available yet.');
    }

    private function productReply(string $question, Product $product): string
    {
        if (preg_match('/\b(available|availability|stock|may stock|mayroon pa|meron pa|available pa)\b/u', $question)) {
            $requestedOption = $product->options()
                ->get(['name', 'stock'])
                ->first(fn ($option) => preg_match(
                    '/\b'.preg_quote(Str::lower($option->name), '/').'\b/u',
                    $question,
                ) === 1);
            if ($requestedOption) {
                if ($product->status !== 'active' || $product->is_archived || $requestedOption->stock < 1) {
                    return "{$product->name} is currently unavailable in {$requestedOption->name}.";
                }

                return "Yes, {$product->name} is available in {$requestedOption->name}. There are {$requestedOption->stock} unit(s) of this option in stock.";
            }

            if ($product->status !== 'active' || $product->is_archived || $product->stock_quantity < 1) {
                return "{$product->name} is currently unavailable.";
            }

            return "Yes, {$product->name} is available. There are {$product->stock_quantity} unit(s) in stock.";
        }

        if (preg_match('/\b(price|cost|magkano|presyo|how much)\b/u', $question)) {
            return "{$product->name} is priced at ₱".number_format((float) $product->price, 2).'.';
        }

        if (preg_match('/\b(colors?|kulay|sizes?|sukat|variants?|variations?)\b/u', $question) || preg_match('/\bcome in\b/u', $question)) {
            $optionTypes = [];
            if (preg_match('/\b(colors?|kulay)\b/u', $question)) {
                $optionTypes[] = 'color';
            }
            if (preg_match('/\b(sizes?|sukat)\b/u', $question)) {
                $optionTypes[] = 'size';
            }
            if (preg_match('/\b(variants?|variations?)\b/u', $question) || preg_match('/\bcome in\b/u', $question)) {
                $optionTypes = ['color', 'size', 'variation'];
            }

            $options = $product->options()
                ->whereIn('type', $optionTypes)
                ->pluck('name')
                ->filter()
                ->unique()
                ->values();

            return $options->isNotEmpty()
                ? "{$product->name} options: ".$options->implode(', ').'.'
                : "No color, size, or variant options are listed for {$product->name}.";
        }

        if ($product->description) {
            return "{$product->name}: {$product->description}";
        }

        return "The listed product is {$product->name}"
            .($product->category ? " in the {$product->category} category." : '.');
    }
}
