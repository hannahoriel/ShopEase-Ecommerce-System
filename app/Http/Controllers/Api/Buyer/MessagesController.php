<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\SellerConversation;
use App\Models\Seller\SellerMessage;
use App\Models\User;
use App\Services\SellerMessageAutoReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MessagesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $conversations = SellerConversation::query()
            ->where('buyer_id', $request->user()->id)
            ->where('type', SellerConversation::TYPE_BUYER)
            ->with(['seller', 'order.items.product', 'product', 'messages.sender'])
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'data' => $conversations
                ->map(fn (SellerConversation $conversation): array => $this->serializeConversation($conversation))
                ->values(),
        ]);
    }

    public function storeConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
        ]);

        if (empty($validated['product_id']) === empty($validated['order_id'])) {
            throw ValidationException::withMessages([
                'product_id' => 'Choose one product or one of your orders to start a conversation.',
            ]);
        }

        if (! empty($validated['product_id'])) {
            $product = Product::query()
                ->whereKey($validated['product_id'])
                ->where('status', 'active')
                ->where('is_archived', false)
                ->whereHas('seller', fn ($seller) => $seller->where('registration_status', 'active'))
                ->firstOrFail();

            $conversation = SellerConversation::firstOrCreate([
                'seller_id' => $product->seller_id,
                'type' => SellerConversation::TYPE_BUYER,
                'buyer_id' => $request->user()->id,
                'order_id' => null,
                'product_id' => $product->id,
            ]);
        } else {
            $order = Order::query()
                ->whereKey($validated['order_id'])
                ->where('buyer_id', $request->user()->id)
                ->firstOrFail();
            $product = $order->items()->with('product')->get()->pluck('product')->filter()->first();

            $conversation = SellerConversation::firstOrCreate([
                'seller_id' => $order->seller_id,
                'type' => SellerConversation::TYPE_BUYER,
                'buyer_id' => $request->user()->id,
                'order_id' => $order->id,
                'product_id' => $product?->id,
            ]);
        }

        $conversation->load(['seller', 'order.items.product', 'product', 'messages.sender']);

        return response()->json([
            'data' => $this->serializeConversation($conversation, true),
        ], $conversation->wasRecentlyCreated ? 201 : 200);
    }

    public function show(Request $request, SellerConversation $conversation): JsonResponse
    {
        $conversation = $this->ownedConversation($request->user(), $conversation);
        SellerMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $conversation->load(['seller', 'order.items.product', 'product', 'messages.sender']);

        return response()->json([
            'data' => $this->serializeConversation($conversation, true),
        ]);
    }

    public function send(
        Request $request,
        SellerConversation $conversation,
        SellerMessageAutoReply $autoReply,
    ): JsonResponse {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);
        $conversation = $this->ownedConversation($request->user(), $conversation);
        if (trim($validated['body']) === '') {
            throw ValidationException::withMessages([
                'body' => 'Message cannot be empty.',
            ]);
        }
        $sellerUser = User::query()
            ->whereHas('sellerProfile', fn ($seller) => $seller->whereKey($conversation->seller_id))
            ->firstOrFail();

        [$buyerMessage, $sellerMessage] = DB::transaction(function () use ($conversation, $request, $validated, $autoReply, $sellerUser): array {
            $response = $autoReply->respond(
                $validated['body'],
                $conversation->loadMissing(['seller', 'order.shipment', 'order.items.product', 'product']),
            );
            $buyerMessage = $conversation->messages()->create([
                'sender_id' => $request->user()->id,
                'body' => trim($validated['body']),
                'read_at' => $response['needs_seller_follow_up'] ? null : now(),
            ]);
            $sellerMessage = $conversation->messages()->create([
                'sender_id' => $sellerUser->id,
                'body' => $response['text'],
            ]);
            $conversation->touch();

            return [$buyerMessage, $sellerMessage];
        });

        return response()->json([
            'data' => [
                'messages' => [
                    $this->serializeMessage($buyerMessage->load('sender')),
                    $this->serializeMessage($sellerMessage->load('sender')),
                ],
                'auto_reply' => true,
            ],
        ], 201);
    }

    private function serializeConversation(
        SellerConversation $conversation,
        bool $includeMessages = false,
    ): array {
        $latestMessage = $conversation->messages->last();
        $product = $conversation->product
            ?? $conversation->order?->items?->pluck('product')->filter()->first();

        $data = [
            'id' => $conversation->id,
            'seller_id' => $conversation->seller_id,
            'title' => $conversation->seller?->store_name ?? 'Seller',
            'product_id' => $product?->id,
            'product_name' => $product?->name,
            'preview' => $latestMessage?->body ?? 'Start a conversation with this seller.',
            'time' => $latestMessage?->created_at?->diffForHumans(short: true),
            'unread' => $conversation->messages
                ->where('sender_id', '!=', $conversation->buyer_id)
                ->whereNull('read_at')
                ->count(),
        ];

        if ($includeMessages) {
            $data['messages'] = $conversation->messages
                ->map(fn (SellerMessage $message): array => $this->serializeMessage($message))
                ->values();
        }

        return $data;
    }

    private function serializeMessage(SellerMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender' => $message->sender_id === $message->conversation->buyer_id ? 'buyer' : 'seller',
            'text' => $message->body,
            'time' => $message->created_at?->format('g:i A'),
        ];
    }

    private function ownedConversation(User $buyer, SellerConversation $conversation): SellerConversation
    {
        abort_unless(
            $conversation->buyer_id === $buyer->id
                && $conversation->type === SellerConversation::TYPE_BUYER,
            404,
        );

        return $conversation;
    }
}
