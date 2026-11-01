<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\Order;
use App\Models\Seller\Product;
use App\Models\Seller\SellerConversation;
use App\Models\Seller\SellerMessage;
use App\Models\Seller\Seller;
use App\Models\User;
use App\Services\SellerMessageAutoReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MessagesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $conversations = SellerConversation::query()
            ->where('buyer_id', $request->user()->id)
            ->where('type', SellerConversation::TYPE_BUYER)
            ->with(['seller.user:id,name,profile_photo_path', 'order.items.product', 'product', 'messages.sender'])
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();
        $conversations = $conversations->groupBy('seller_id')->map(function ($threads): array {
            $latest = $threads->first();
            $data = $this->serializeConversation($latest);
            $data['unread'] = (int) $threads->sum(fn (SellerConversation $thread) => $thread->messages
                ->where('sender_id', '!=', $thread->buyer_id)->whereNull('read_at')->count());
            return $data;
        })->values();

        return response()->json([
            'data' => $conversations,
        ]);
    }

    public function storeConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => ['nullable', 'integer', 'exists:products,id'],
            'order_id' => ['nullable', 'integer', 'exists:orders,id'],
            'seller_id' => ['nullable', 'integer', 'exists:sellers,id'],
        ]);

        $contexts = collect(['product_id', 'order_id', 'seller_id'])->filter(fn ($key) => ! empty($validated[$key]))->count();
        if ($contexts !== 1) {
            throw ValidationException::withMessages([
                'product_id' => 'Choose one shop, product, or one of your orders to start a conversation.',
            ]);
        }

        if (! empty($validated['seller_id'])) {
            $seller = Seller::query()
                ->whereKey($validated['seller_id'])
                ->where('registration_status', 'active')
                ->firstOrFail();
            $conversation = SellerConversation::query()
                ->where('seller_id', $seller->id)
                ->where('buyer_id', $request->user()->id)
                ->where('type', SellerConversation::TYPE_BUYER)
                ->orderByDesc('updated_at')->orderByDesc('id')->first();
            if (! $conversation) {
                $conversation = SellerConversation::firstOrCreate(
                    ['seed_key' => "direct-account-buyer-{$request->user()->id}-seller-{$seller->id}"],
                    ['seller_id' => $seller->id, 'type' => SellerConversation::TYPE_BUYER, 'buyer_id' => $request->user()->id],
                );
            }
        } elseif (! empty($validated['product_id'])) {
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

        $conversation->load(['seller.user:id,name,profile_photo_path', 'order.items.product', 'product', 'messages.sender']);

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

        $conversation->load(['seller.user:id,name,profile_photo_path', 'order.items.product', 'product', 'messages.sender']);

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
            'title' => $conversation->seller?->store_name ?: ($conversation->seller?->business_name ?: ($conversation->seller?->user?->name ?? 'Seller')),
            'seller_avatar_url' => $conversation->seller?->user?->profile_photo_path
                ? Storage::disk('public')->url($conversation->seller->user->profile_photo_path)
                : null,
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
