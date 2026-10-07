<?php

namespace App\Http\Controllers\Api\Seller;

use App\Http\Controllers\Controller;
use App\Models\Admin\Complaint;
use App\Models\Admin\Order;
use App\Models\Seller\Seller;
use App\Models\Seller\SellerConversation;
use App\Models\Seller\SellerMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MessagesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in([SellerConversation::TYPE_BUYER, SellerConversation::TYPE_COMPLAINT])],
            'search' => ['nullable', 'string', 'max:100'],
        ]);
        $seller = $this->sellerFor($request->user());

        $this->syncComplaintConversations($seller);

        $query = SellerConversation::query()
            ->where('seller_id', $seller->id)
            ->where('type', $validated['type'])
            ->with(['buyer:id,name', 'order.items:id,order_id,product_name', 'complaint.order', 'messages.sender:id,name,role'])
            ->withCount([
                'messages as unread_count' => fn ($messages) => $messages
                    ->whereNull('read_at')
                    ->where('sender_id', '!=', $request->user()->id),
            ]);

        if (! empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function ($conversations) use ($search): void {
                $conversations
                    ->whereHas('buyer', fn ($buyer) => $buyer->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('order', fn ($order) => $order->where('order_number', 'like', "%{$search}%"))
                    ->orWhereHas('complaint', fn ($complaint) => $complaint
                        ->where('subject', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%"))
                    ->orWhereHas('messages', fn ($messages) => $messages->where('body', 'like', "%{$search}%"));
            });
        }

        $conversations = $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn (SellerConversation $conversation): array => $this->serializeConversation(
                $conversation,
                $request->user(),
            ))
            ->values();

        $typeCounts = SellerConversation::query()
            ->where('seller_id', $seller->id)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type')
            ->map(fn ($total): int => (int) $total)
            ->all();

        $unreadCounts = SellerMessage::query()
            ->join('seller_conversations', 'seller_conversations.id', '=', 'seller_messages.conversation_id')
            ->where('seller_conversations.seller_id', $seller->id)
            ->where('seller_messages.sender_id', '!=', $request->user()->id)
            ->whereNull('seller_messages.read_at')
            ->selectRaw('seller_conversations.type, COUNT(*) as total')
            ->groupBy('seller_conversations.type')
            ->pluck('total', 'type')
            ->map(fn ($total): int => (int) $total)
            ->all();

        return response()->json([
            'data' => $conversations,
            'counts' => [
                SellerConversation::TYPE_BUYER => $typeCounts[SellerConversation::TYPE_BUYER] ?? 0,
                SellerConversation::TYPE_COMPLAINT => $typeCounts[SellerConversation::TYPE_COMPLAINT] ?? 0,
            ],
            'unread' => [
                SellerConversation::TYPE_BUYER => $unreadCounts[SellerConversation::TYPE_BUYER] ?? 0,
                SellerConversation::TYPE_COMPLAINT => $unreadCounts[SellerConversation::TYPE_COMPLAINT] ?? 0,
            ],
        ]);
    }

    public function contacts(Request $request): JsonResponse
    {
        $seller = $this->sellerFor($request->user());
        $orders = Order::query()
            ->where('seller_id', $seller->id)
            ->whereNotNull('buyer_id')
            ->with(['buyer:id,name', 'items:id,order_id,product_name'])
            ->latest('created_at')
            ->get()
            ->unique('buyer_id')
            ->map(fn (Order $order): array => [
                'order_id' => $order->id,
                'buyer_id' => $order->buyer_id,
                'buyer_name' => $order->buyer?->name ?? 'Buyer',
                'order_number' => $order->order_number ?: 'ORD-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
                'products' => $order->items->pluck('product_name')->filter()->implode(', '),
            ])
            ->values();

        return response()->json(['data' => $orders]);
    }

    public function storeConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'integer', 'exists:orders,id'],
        ]);
        $seller = $this->sellerFor($request->user());
        $order = Order::query()
            ->where('seller_id', $seller->id)
            ->whereKey($validated['order_id'])
            ->whereHas('buyer', fn ($buyer) => $buyer->where('role', User::ROLE_BUYER))
            ->firstOrFail();

        $conversation = SellerConversation::firstOrCreate(
            [
                'seller_id' => $seller->id,
                'type' => SellerConversation::TYPE_BUYER,
                'buyer_id' => $order->buyer_id,
                'order_id' => $order->id,
            ],
        );

        return response()->json([
            'data' => $this->serializeConversation(
                $conversation->load(['buyer:id,name', 'order.items:id,order_id,product_name', 'messages.sender:id,name,role']),
                $request->user(),
            ),
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

        $conversation->load([
            'buyer:id,name',
            'order.items:id,order_id,product_name',
            'complaint.order',
            'messages.sender:id,name,role',
        ]);

        return response()->json([
            'data' => $this->serializeConversation($conversation, $request->user(), true),
        ]);
    }

    public function send(Request $request, SellerConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:1000'],
            'attachment' => ['nullable', 'file', 'max:15360'],
        ], [
            'attachment.max' => 'You can only attach files up to 15 MB.',
        ]);
        $conversation = $this->ownedConversation($request->user(), $conversation);
        $body = trim($validated['body'] ?? '');
        $attachment = $request->file('attachment');
        if ($body === '' && ! $attachment) {
            throw ValidationException::withMessages([
                'body' => 'Write a message or attach a file before sending.',
            ]);
        }

        $attachmentPath = $attachment
            ? $attachment->store("seller-messages/{$conversation->id}", 'local')
            : null;
        if ($attachment && ! $attachmentPath) {
            throw new RuntimeException('Unable to store the seller message attachment.');
        }

        $message = DB::transaction(function () use ($conversation, $request, $body, $attachment, $attachmentPath): SellerMessage {
            $message = $conversation->messages()->create([
                'sender_id' => $request->user()->id,
                'body' => $body !== '' ? $body : null,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $attachment?->getClientOriginalName(),
                'attachment_mime_type' => $attachment?->getMimeType(),
                'attachment_size' => $attachment?->getSize(),
            ]);
            $conversation->touch();

            return $message->load('sender:id,name,role');
        });

        return response()->json([
            'data' => $this->serializeMessage($message, $request->user()),
        ], 201);
    }

    public function attachment(
        Request $request,
        SellerConversation $conversation,
        SellerMessage $message,
    ): StreamedResponse {
        $conversation = $this->ownedConversation($request->user(), $conversation);
        abort_unless(
            $message->conversation_id === $conversation->id && $message->attachment_path,
            404,
        );

        $inlineTypes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif',
            'video/mp4', 'video/webm', 'video/ogg',
        ];
        $disposition = ! $request->boolean('download')
            && in_array($message->attachment_mime_type, $inlineTypes, true)
            ? 'inline'
            : 'attachment';
        $attachmentName = basename(str_replace('\\', '/', $message->attachment_name ?? 'attachment'));

        return Storage::disk('local')->response(
            $message->attachment_path,
            $attachmentName,
            [
                'Content-Type' => $message->attachment_mime_type ?: 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
            ],
            $disposition,
        );
    }

    private function syncComplaintConversations(Seller $seller): void
    {
        Complaint::query()
            ->whereHas('order', fn ($orders) => $orders->where('seller_id', $seller->id))
            ->with('order:id,seller_id')
            ->get()
            ->each(function (Complaint $complaint) use ($seller): void {
                SellerConversation::firstOrCreate(
                    [
                        'seller_id' => $seller->id,
                        'type' => SellerConversation::TYPE_COMPLAINT,
                        'complaint_id' => $complaint->id,
                    ],
                    [
                        'buyer_id' => $complaint->user_id,
                        'order_id' => $complaint->order_id,
                    ],
                );
            });
    }

    private function serializeConversation(
        SellerConversation $conversation,
        User $sellerUser,
        bool $includeMessages = false,
    ): array {
        $complaint = $conversation->complaint;
        $order = $conversation->order ?? $complaint?->order;
        $buyer = $conversation->buyer;
        $messages = $conversation->messages;
        $latestMessage = $messages->last();
        $isComplaint = $conversation->type === SellerConversation::TYPE_COMPLAINT;
        $orderNumber = $order?->order_number ?: ($order ? 'ORD-'.str_pad((string) $order->id, 6, '0', STR_PAD_LEFT) : null);
        $complaintNumber = $complaint ? 'CMP-'.str_pad((string) $complaint->id, 4, '0', STR_PAD_LEFT) : null;
        $productNames = $order?->items?->pluck('product_name')->filter()->implode(', ');

        $payload = [
            'id' => $conversation->id,
            'type' => $conversation->type,
            'title' => $isComplaint
                ? "Complaint #{$complaintNumber}"
                : ($buyer?->name ?? 'Buyer'),
            'initials' => $this->initials($isComplaint ? 'ShopEase Admin' : ($buyer?->name ?? 'Buyer')),
            'badge' => $isComplaint ? 'Admin' : 'Buyer',
            'subtitle' => $isComplaint
                ? 'Admin Support · '.str_replace('_', ' ', ucfirst($complaint?->status ?? 'open'))
                : ($orderNumber ? "Order #{$orderNumber} · Buyer" : 'Buyer Account'),
            'context_label' => $isComplaint ? 'Complaint Reference' : 'Related Order',
            'context_value' => $isComplaint
                ? trim("#{$complaintNumber}".($orderNumber ? " · Order #{$orderNumber}" : ''))
                : trim(($orderNumber ? "#{$orderNumber}" : 'Order').($productNames ? " · {$productNames}" : '')),
            'context_button' => $isComplaint ? 'View Complaint' : 'View Order',
            'preview' => $latestMessage?->body ?? $latestMessage?->attachment_name ?? ($isComplaint ? 'Complaint conversation opened.' : 'Conversation started.'),
            'time' => $latestMessage?->created_at?->diffForHumans(short: true) ?? $conversation->updated_at?->diffForHumans(short: true),
            'unread' => (int) ($conversation->unread_count ?? $messages
                ->whereNull('read_at')
                ->where('sender_id', '!=', $sellerUser->id)
                ->count()),
            'updated_at' => $latestMessage?->created_at?->toISOString() ?? $conversation->updated_at?->toISOString(),
        ];

        if ($includeMessages) {
            $payload['messages'] = $messages->map(fn (SellerMessage $message): array => $this->serializeMessage(
                $message,
                $sellerUser,
            ))->values();
        }

        return $payload;
    }

    private function serializeMessage(SellerMessage $message, User $sellerUser): array
    {
        return [
            'id' => $message->id,
            'sender' => $message->sender_id === $sellerUser->id
                ? 'seller'
                : ($message->sender?->role === User::ROLE_ADMIN ? 'admin' : 'buyer'),
            'sender_name' => $message->sender?->name ?? 'User',
            'text' => $message->body,
            'attachment' => $message->attachment_path ? [
                'name' => $message->attachment_name,
                'mime_type' => $message->attachment_mime_type,
                'size' => $message->attachment_size,
                'url' => route('seller.api.messages.attachment', [
                    'conversation' => $message->conversation_id,
                    'message' => $message->id,
                ]),
            ] : null,
            'time' => $message->created_at?->format('g:i A'),
            'date' => $message->created_at?->toDateString(),
            'seen' => $message->sender_id === $sellerUser->id && $message->read_at !== null,
        ];
    }

    private function ownedConversation(User $user, SellerConversation $conversation): SellerConversation
    {
        abort_unless($conversation->seller_id === $this->sellerFor($user)->id, 404);

        return $conversation;
    }

    private function sellerFor(User $user): Seller
    {
        return Seller::query()->where('user_id', $user->id)->firstOrFail();
    }

    private function initials(string $name): string
    {
        return collect(explode(' ', trim($name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }
}
