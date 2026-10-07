<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminConversation;
use App\Models\Admin\AdminMessage;
use App\Models\Admin\Complaint;
use App\Models\Logistics\Logistics;
use App\Models\Seller\Seller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'type' => ['nullable', Rule::in(AdminConversation::CATEGORIES)],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = AdminConversation::query()
            ->with(['user:id,name,email,role', 'complaint.order', 'messages.sender:id,name,role'])
            ->withCount([
                'messages as unread_count' => fn (Builder $messages) => $messages
                    ->whereNull('read_at')
                    ->where('sender_id', '!=', $request->user()->id),
            ]);

        if (! empty($validated['type'])) {
            $query->where('category', $validated['type']);
        }

        if (! empty($validated['search'])) {
            $search = trim($validated['search']);
            $query->where(function (Builder $conversations) use ($search): void {
                $conversations
                    ->whereHas('user', fn (Builder $user) => $user
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%"))
                    ->orWhereHas('complaint', fn (Builder $complaint) => $complaint
                        ->where('subject', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%"))
                    ->orWhereHas('messages', fn (Builder $messages) => $messages
                        ->where('body', 'like', "%{$search}%"));
            });
        }

        $conversations = $query
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'data' => $conversations
                ->where('category', '!=', AdminConversation::CATEGORY_COMPLAINTS)
                ->map(fn (AdminConversation $conversation): array => $this->serializeConversation($conversation))
                ->values()
                ->concat($this->serializeComplaintThreads(
                    $conversations->where('category', AdminConversation::CATEGORY_COMPLAINTS)
                ))
                ->values(),
            'counts' => collect(AdminConversation::CATEGORIES)
                ->mapWithKeys(fn (string $category): array => [
                    $category => $conversations->where('category', $category)->sum('unread_count'),
                ]),
        ]);
    }

    public function contacts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(AdminConversation::CATEGORIES)],
        ]);

        if ($validated['type'] === AdminConversation::CATEGORY_COMPLAINTS) {
            $contacts = Complaint::query()
                ->with(['user:id,name', 'order.seller.user:id,name'])
                ->latest()
                ->get()
                ->flatMap(function (Complaint $complaint): array {
                    $parties = [[
                        'user' => $complaint->user,
                        'party' => 'buyer',
                    ]];
                    if ($complaint->order?->seller?->user) {
                        $parties[] = [
                            'user' => $complaint->order->seller->user,
                            'party' => 'seller',
                        ];
                    }

                    return collect($parties)
                        ->filter(fn (array $entry): bool => $entry['user'] !== null)
                        ->map(fn (array $entry): array => [
                            'user_id' => $entry['user']->id,
                            'complaint_id' => $complaint->id,
                            'complaint_party' => $entry['party'],
                            'label' => "Complaint #{$complaint->id} · {$entry['party']} · {$entry['user']->name}",
                        ])
                        ->all();
                })
                ->values();

            return response()->json(['data' => $contacts]);
        }

        $role = match ($validated['type']) {
            AdminConversation::CATEGORY_LOGISTICS => User::ROLE_LOGISTICS,
            AdminConversation::CATEGORY_BUYERS => User::ROLE_BUYER,
            AdminConversation::CATEGORY_SELLERS => User::ROLE_SELLER,
        };

        $profiles = match ($validated['type']) {
            AdminConversation::CATEGORY_LOGISTICS => Logistics::query()->whereIn('user_id', User::query()->select('id')->where('role', $role)),
            AdminConversation::CATEGORY_SELLERS => Seller::query()->whereIn('user_id', User::query()->select('id')->where('role', $role)),
            default => null,
        };

        $users = $profiles
            ? User::query()->whereIn('id', $profiles->select('user_id'))
            : User::query()->where('role', $role);

        return response()->json([
            'data' => $users->orderBy('name')->get(['id', 'name', 'email', 'role'])
                ->map(fn (User $user): array => [
                    'user_id' => $user->id,
                    'label' => "{$user->name} · {$user->email}",
                ]),
        ]);
    }

    public function storeConversation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(AdminConversation::CATEGORIES)],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'complaint_id' => ['required_if:type,complaints', 'nullable', 'integer', 'exists:complaints,id'],
            'complaint_party' => ['required_if:type,complaints', 'nullable', Rule::in(['buyer', 'seller'])],
        ]);

        $category = $validated['type'];
        $user = User::query()->findOrFail($validated['user_id']);
        $expectedRole = match ($category) {
            AdminConversation::CATEGORY_LOGISTICS => User::ROLE_LOGISTICS,
            AdminConversation::CATEGORY_BUYERS => User::ROLE_BUYER,
            AdminConversation::CATEGORY_SELLERS => User::ROLE_SELLER,
            AdminConversation::CATEGORY_COMPLAINTS => null,
        };

        $complaint = null;
        if ($category !== AdminConversation::CATEGORY_COMPLAINTS) {
            abort_unless($user->role === $expectedRole, 422, 'The selected contact does not match this message category.');
        } else {
            $complaint = Complaint::query()->with('order.seller')->findOrFail($validated['complaint_id']);
            $allowedUserId = $validated['complaint_party'] === 'buyer'
                ? $complaint->user_id
                : $complaint->order?->seller?->user_id;
            abort_unless((int) $allowedUserId === (int) $user->id, 422, 'The selected user is not a party to this complaint.');
        }

        $conversation = AdminConversation::firstOrCreate([
            'category' => $category,
            'user_id' => $user->id,
            'complaint_id' => $category === AdminConversation::CATEGORY_COMPLAINTS ? $validated['complaint_id'] : null,
            'complaint_party' => $category === AdminConversation::CATEGORY_COMPLAINTS ? $validated['complaint_party'] : null,
        ]);

        if ($complaint) {
            foreach ([
                'buyer' => $complaint->user_id,
                'seller' => $complaint->order?->seller?->user_id,
            ] as $party => $partyUserId) {
                if ($partyUserId) {
                    AdminConversation::firstOrCreate([
                        'category' => AdminConversation::CATEGORY_COMPLAINTS,
                        'user_id' => $partyUserId,
                        'complaint_id' => $complaint->id,
                        'complaint_party' => $party,
                    ]);
                }
            }
        }

        return response()->json([
            'data' => $this->serializeConversation(
                $conversation->load(['user:id,name,email,role', 'complaint.order', 'messages.sender:id,name,role'])
            ),
        ], $conversation->wasRecentlyCreated ? 201 : 200);
    }

    public function show(Request $request, AdminConversation $conversation): JsonResponse
    {
        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'data' => $this->serializeConversation(
                $conversation->load(['user:id,name,email,role', 'complaint.order', 'messages.sender:id,name,role']),
                true
            ),
        ]);
    }

    public function send(Request $request, AdminConversation $conversation): JsonResponse
    {
        $validated = $request->validate([
            'body' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:15360'],
        ], [
            'attachment.max' => 'You can only attach files up to 15 MB.',
        ]);

        $body = trim($validated['body'] ?? '');
        $attachment = $request->file('attachment');
        if ($body === '' && ! $attachment) {
            throw ValidationException::withMessages([
                'body' => 'Write a message or attach a file before sending.',
            ]);
        }

        $attachmentPath = $attachment
            ? $attachment->store("admin-messages/{$conversation->id}", 'local')
            : null;
        if ($attachment && ! $attachmentPath) {
            throw new RuntimeException('Unable to store the admin message attachment.');
        }

        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => $body !== '' ? $body : null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $attachment?->getClientOriginalName(),
            'attachment_mime_type' => $attachment?->getMimeType(),
            'attachment_size' => $attachment?->getSize(),
        ]);
        $conversation->touch();

        return response()->json([
            'data' => $this->serializeMessage($message->load('sender:id,name,role'), $request->user()),
        ], 201);
    }

    public function attachment(Request $request, AdminConversation $conversation, AdminMessage $message): StreamedResponse
    {
        abort_unless(
            $message->conversation_id === $conversation->id && $message->attachment_path,
            404
        );
        $filename = basename(str_replace('\\', '/', $message->attachment_name ?? 'attachment'));

        return Storage::disk('local')->download(
            $message->attachment_path,
            $filename,
            ['Content-Type' => $message->attachment_mime_type ?: 'application/octet-stream']
        );
    }

    private function serializeComplaintThreads($conversations): array
    {
        return $conversations
            ->groupBy('complaint_id')
            ->map(function ($partyConversations): array {
                /** @var AdminConversation $first */
                $first = $partyConversations->first();
                $complaint = $first->complaint;
                $parties = [];

                foreach (['buyer', 'seller'] as $party) {
                    $conversation = $partyConversations->firstWhere('complaint_party', $party);
                    if (! $conversation) {
                        continue;
                    }
                    $parties[$party] = [
                        'conversationId' => $conversation->id,
                        'name' => $conversation->user?->name ?? ucfirst($party),
                        'initials' => $this->initials($conversation->user?->name),
                        'subtitle' => ucfirst($party).' · Complaint Party',
                        'unread' => $conversation->unread_count,
                        'messages' => $conversation->messages
                            ->map(fn (AdminMessage $message): array => $this->serializeMessage($message))
                            ->all(),
                    ];
                }

                $latest = $partyConversations
                    ->flatMap(fn (AdminConversation $conversation) => $conversation->messages)
                    ->sortByDesc('created_at')
                    ->first();
                $orderNumber = $complaint?->order?->order_number ?: 'ORD-'.$complaint?->order_id;

                return [
                    'id' => 'complaint-'.$first->complaint_id,
                    'type' => AdminConversation::CATEGORY_COMPLAINTS,
                    'complaintId' => 'Complaint #CMP-'.now()->format('Y').'-'.str_pad((string) $first->complaint_id, 4, '0', STR_PAD_LEFT),
                    'initials' => 'C'.$first->complaint_id,
                    'role' => 'Complaint',
                    'subtitle' => $complaint?->status ?? 'Open',
                    'status' => $complaint?->status ?? 'Open',
                    'contextLabel' => 'Complaint Reference',
                    'contextValue' => 'Complaint #'.$first->complaint_id.' · Order #'.$orderNumber,
                    'contextButton' => 'View Complaint',
                    'contextUrl' => route('admin.complaints.disputes', ['complaint' => $first->complaint_id]),
                    'preview' => $latest?->body ?? $latest?->attachment_name ?? 'Complaint conversation opened.',
                    'time' => $latest?->created_at?->diffForHumans(short: true) ?? $complaint?->created_at?->diffForHumans(short: true) ?? '',
                    'unread' => $partyConversations->sum('unread_count'),
                    'parties' => $parties,
                ];
            })
            ->values()
            ->all();
    }

    private function serializeConversation(AdminConversation $conversation, bool $includeMessages = true): array
    {
        $user = $conversation->user;
        $latest = $conversation->messages->last();
        $role = match ($conversation->category) {
            AdminConversation::CATEGORY_LOGISTICS => 'Logistics',
            AdminConversation::CATEGORY_BUYERS => 'Buyer',
            AdminConversation::CATEGORY_SELLERS => 'Seller',
            default => 'Complaint',
        };
        $profile = $conversation->category === AdminConversation::CATEGORY_LOGISTICS
            ? $user?->logisticsProfile
            : ($conversation->category === AdminConversation::CATEGORY_SELLERS ? $user?->sellerProfile : null);
        $title = $conversation->category === AdminConversation::CATEGORY_SELLERS
            ? ($profile?->store_name ?: $user?->name)
            : ($profile?->business_name ?: $user?->name);
        $contextValue = match ($conversation->category) {
            AdminConversation::CATEGORY_LOGISTICS => $profile?->business_name ?: ($user?->email ?? ''),
            AdminConversation::CATEGORY_SELLERS => 'Seller · '.($user?->email ?? ''),
            default => 'Buyer · '.($user?->email ?? ''),
        };

        return [
            'id' => $conversation->id,
            'type' => $conversation->category,
            'title' => $title ?: $user?->name,
            'initials' => $this->initials($title ?: $user?->name),
            'role' => $role,
            'subtitle' => $role.' Account',
            'contextLabel' => 'Contact Reference',
            'contextValue' => $contextValue,
            'contextButton' => 'View '.($role === 'Logistics' ? 'Logistics' : $role),
            'contextUrl' => $conversation->category === AdminConversation::CATEGORY_LOGISTICS
                ? route('admin.logistics.management')
                : route('admin.user.management.show', ['user' => $user?->id]),
            'preview' => $latest?->body ?? $latest?->attachment_name ?? 'Conversation started.',
            'time' => $latest?->created_at?->diffForHumans(short: true) ?? $conversation->created_at?->diffForHumans(short: true) ?? '',
            'unread' => $conversation->unread_count ?? 0,
            'messages' => $includeMessages
                ? $conversation->messages
                    ->map(fn (AdminMessage $message): array => $this->serializeMessage($message))
                    ->all()
                : [],
        ];
    }

    private function serializeMessage(AdminMessage $message, ?User $admin = null): array
    {
        return [
            'sender' => $message->sender?->role === User::ROLE_ADMIN ? 'admin' : 'party',
            'text' => $message->body ?? '',
            'time' => $message->created_at?->format('g:i A') ?? '',
            'seen' => $admin ? $message->sender_id === $admin->id && $message->read_at !== null : $message->read_at !== null,
            'attachment' => $message->attachment_path ? [
                'name' => $message->attachment_name,
                'mime_type' => $message->attachment_mime_type,
                'size' => $message->attachment_size,
                'url' => route('admin.messages.api.attachment', [
                    'conversation' => $message->conversation_id,
                    'message' => $message->id,
                ]),
            ] : null,
        ];
    }

    private function initials(?string $name): string
    {
        return collect(explode(' ', trim((string) $name)))
            ->filter()
            ->take(2)
            ->map(fn (string $part): string => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('') ?: 'U';
    }
}
