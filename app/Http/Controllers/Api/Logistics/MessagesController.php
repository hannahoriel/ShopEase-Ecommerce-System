<?php

namespace App\Http\Controllers\Api\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminConversation;
use App\Models\Admin\AdminMessage;
use App\Models\Logistics\Logistics;
use App\Models\Logistics\LogisticsBranch;
use App\Models\Logistics\LogisticsConversation;
use App\Models\Logistics\LogisticsDeliveryHub;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
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
        $user = $request->user();

        if ($user->role === User::ROLE_LOGISTICS) {
            $logistics = Logistics::query()->where('user_id', $user->id)->firstOrFail();
            $contacts = collect();
            $admin = User::query()->where('role', User::ROLE_ADMIN)->orderBy('id')->first();
            if ($admin) {
                $adminConversation = AdminConversation::query()
                    ->where('category', AdminConversation::CATEGORY_LOGISTICS)
                    ->where('user_id', $user->id)
                    ->with(['messages.sender:id,name,role'])
                    ->first();
                $contacts->push($this->adminContact($admin, $adminConversation, $user));
            }

            $branches = LogisticsBranch::query()
                ->where('logistics_id', $logistics->id)
                ->whereNull('archived_at')
                ->where('status', 'active')
                ->with(['user:id,name,email,role', 'deliveryHubs' => fn ($query) => $query
                    ->whereNull('archived_at')->where('status', 'active')->with('user:id,name,email,role')])
                ->orderBy('name')
                ->get();

            foreach ($branches as $branch) {
                if ($branch->user) {
                    $conversation = LogisticsConversation::query()
                        ->where('logistics_id', $logistics->id)->where('contact_user_id', $branch->user_id)
                        ->with(['messages.sender:id,name,role'])->first();
                    $contacts->push($this->facilityContact(
                        'sorting', $branch->id, $branch->user, $branch->name,
                        $branch->province ?: 'Sorting Center', collect([$branch->province, $branch->municipality])->filter()->join(' · '),
                        $conversation, $user
                    ));
                }

                foreach ($branch->deliveryHubs as $hub) {
                    if (! $hub->user) {
                        continue;
                    }
                    $conversation = LogisticsConversation::query()
                        ->where('logistics_id', $logistics->id)->where('contact_user_id', $hub->user_id)
                        ->with(['messages.sender:id,name,role'])->first();
                    $contacts->push($this->facilityContact(
                        'delivery', $hub->id, $hub->user, $hub->hub_name, $branch->name,
                        collect([$hub->municipality, $hub->province])->filter()->join(' · '),
                        $conversation, $user, $branch->id
                    ));
                }
            }

            return response()->json(['data' => $contacts->values()]);
        }

        $conversationQuery = LogisticsConversation::query()
            ->where('contact_user_id', $user->id)
            ->with(['logistics.user:id,name,email', 'messages.sender:id,name,role'])
            ->withCount(['messages as unread_count' => fn (Builder $messages) => $messages
                ->whereNull('read_at')->where('sender_id', '!=', $user->id)]);
        $conversations = $conversationQuery->orderByDesc('updated_at')->get();
        $conversations->each(function (LogisticsConversation $conversation) use ($user): void {
            $conversation->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
            $conversation->unsetRelation('messages')->load('messages.sender:id,name,role');
            $conversation->unread_count = 0;
        });

        return response()->json(['data' => $conversations->map(fn (LogisticsConversation $conversation) => [
            'key' => $conversation->contact_type.':'.$conversation->id,
            'type' => $conversation->contact_type,
            'conversation_id' => $conversation->id,
            'name' => $conversation->logistics?->user?->name ?? 'Logistics',
            'sub' => ucfirst($conversation->contact_type === 'sorting' ? 'Sorting Center' : 'Delivery Hub'),
            'detail' => $conversation->logistics?->user?->email ?? '',
            'messages' => $conversation->messages->map(fn ($message) => $this->serializeMessage($message, $user))->all(),
            'unread' => $conversation->unread_count,
        ])->values()]);
    }

    public function thread(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['sorting', 'delivery', 'admin'])],
            'contact_id' => ['required', 'integer'],
        ]);
        $user = $request->user();

        if ($validated['type'] === 'admin') {
            abort_unless($user->role === User::ROLE_LOGISTICS, 403);
            $admin = User::query()->where('role', User::ROLE_ADMIN)->findOrFail($validated['contact_id']);
            $conversation = AdminConversation::query()->firstOrCreate([
                'category' => AdminConversation::CATEGORY_LOGISTICS,
                'user_id' => $user->id,
                'complaint_id' => null,
                'complaint_party' => null,
            ]);
            $conversation->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
            return response()->json(['data' => [
                'conversation_id' => $conversation->id,
                'messages' => $conversation->messages()->with('sender:id,name,role')->get()
                    ->map(fn ($message) => $this->serializeMessage($message, $user, true))->all(),
                'contact_id' => $admin->id,
            ]]);
        }

        abort_unless($user->role === User::ROLE_LOGISTICS, 403);
        $logistics = Logistics::query()->where('user_id', $user->id)->firstOrFail();
        $contact = $this->findOwnedFacility($validated['type'], $validated['contact_id'], $logistics->id);
        $conversation = LogisticsConversation::query()
            ->where('logistics_id', $logistics->id)->where('contact_user_id', $contact['user']->id)
            ->with(['messages.sender:id,name,role'])->first();

        if ($conversation) {
            $conversation->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
            $conversation->unsetRelation('messages')->load('messages.sender:id,name,role');
        }

        return response()->json(['data' => [
            'conversation_id' => $conversation?->id,
            'contact_id' => $validated['contact_id'],
            'messages' => $conversation?->messages->map(fn ($message) => $this->serializeMessage($message, $user))->all() ?? [],
        ]]);
    }

    public function send(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['sorting', 'delivery', 'admin'])],
            'contact_id' => ['required', 'integer'],
            'body' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:15360'],
        ]);
        $body = trim($validated['body'] ?? '');
        $file = $request->file('attachment');
        if ($body === '' && ! $file) {
            throw ValidationException::withMessages(['body' => 'Write a message or attach a file before sending.']);
        }
        $user = $request->user();

        if ($validated['type'] === 'admin') {
            abort_unless($user->role === User::ROLE_LOGISTICS, 403);
            User::query()->where('role', User::ROLE_ADMIN)->findOrFail($validated['contact_id']);
            $conversation = AdminConversation::query()->firstOrCreate([
                'category' => AdminConversation::CATEGORY_LOGISTICS,
                'user_id' => $user->id,
                'complaint_id' => null,
                'complaint_party' => null,
            ]);
            $conversation->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'body' => $body !== '' ? $body : null,
                ...$this->storeAttachment($file, "admin-messages/{$conversation->id}"),
            ]);
            $conversation->touch();
            return response()->json(['data' => [
                'conversation_id' => $conversation->id,
                'message' => $this->serializeMessage($message->load('sender:id,name,role'), $user, true),
            ]], 201);
        }

        if ($user->role === User::ROLE_LOGISTICS) {
            $logistics = Logistics::query()->where('user_id', $user->id)->firstOrFail();
            $contact = $this->findOwnedFacility($validated['type'], $validated['contact_id'], $logistics->id);
            $conversation = LogisticsConversation::query()->firstOrCreate([
                'logistics_id' => $logistics->id,
                'contact_user_id' => $contact['user']->id,
            ], [
                'contact_type' => $validated['type'],
                'logistics_branch_id' => $contact['branch_id'],
                'logistics_delivery_hub_id' => $contact['hub_id'],
            ]);
        } else {
            $conversation = LogisticsConversation::query()->findOrFail($validated['contact_id']);
            $expectedType = $user->role === User::ROLE_SORTING_CENTER ? 'sorting' : 'delivery';
            abort_unless((int) $conversation->contact_user_id === (int) $user->id
                && $conversation->contact_type === $expectedType, 403);
        }

        $message = DB::transaction(function () use ($conversation, $user, $body, $file) {
            $message = $conversation->messages()->create([
                'sender_id' => $user->id,
                'body' => $body !== '' ? $body : null,
                ...$this->storeAttachment($file, "logistics-messages/{$conversation->id}"),
            ]);
            $conversation->touch();
            return $message;
        });

        return response()->json(['data' => [
            'conversation_id' => $conversation->id,
            'message' => $this->serializeMessage($message->load('sender:id,name,role'), $user),
        ]], 201);
    }

    public function show(Request $request, LogisticsConversation $conversation): JsonResponse
    {
        $user = $request->user();
        if ($user->role === User::ROLE_LOGISTICS) {
            abort_unless((int) $conversation->logistics?->user_id === (int) $user->id, 403);
        } else {
            $expectedType = $user->role === User::ROLE_SORTING_CENTER ? 'sorting' : 'delivery';
            abort_unless((int) $conversation->contact_user_id === (int) $user->id
                && $conversation->contact_type === $expectedType, 403);
        }
        $conversation->messages()->where('sender_id', '!=', $user->id)->whereNull('read_at')->update(['read_at' => now()]);
        $conversation->load('messages.sender:id,name,role');

        return response()->json(['data' => [
            'conversation_id' => $conversation->id,
            'messages' => $conversation->messages->map(fn ($message) => $this->serializeMessage($message, $user))->all(),
        ]]);
    }

    public function attachment(Request $request, string $type, int $conversation, int $message): StreamedResponse
    {
        $user = $request->user();
        if ($type === 'admin') {
            abort_unless($user->role === User::ROLE_LOGISTICS, 403);
            $thread = AdminConversation::query()->where('category', AdminConversation::CATEGORY_LOGISTICS)
                ->where('user_id', $user->id)->findOrFail($conversation);
            $item = AdminMessage::query()->where('conversation_id', $thread->id)->findOrFail($message);
        } else {
            abort_unless($type === 'facility', 404);
            $thread = LogisticsConversation::query()->findOrFail($conversation);
            if ($user->role === User::ROLE_LOGISTICS) {
                abort_unless((int) $thread->logistics?->user_id === (int) $user->id, 403);
            } else {
                $expectedType = $user->role === User::ROLE_SORTING_CENTER ? 'sorting' : 'delivery';
                abort_unless((int) $thread->contact_user_id === (int) $user->id
                    && $thread->contact_type === $expectedType, 403);
            }
            $item = $thread->messages()->findOrFail($message);
        }

        abort_unless($item->attachment_path, 404);
        $filename = basename(str_replace('\\', '/', $item->attachment_name ?? 'attachment'));

        return Storage::disk('local')->download(
            $item->attachment_path,
            $filename,
            ['Content-Type' => $item->attachment_mime_type ?: 'application/octet-stream']
        );
    }

    private function findOwnedFacility(string $type, int $id, int $logisticsId): array
    {
        if ($type === 'sorting') {
            $branch = LogisticsBranch::query()->where('logistics_id', $logisticsId)->whereKey($id)
                ->whereNull('archived_at')->where('status', 'active')->with('user')->firstOrFail();
            abort_unless($branch->user && $branch->user->role === User::ROLE_SORTING_CENTER, 422, 'This sorting center has no login account.');
            return ['user' => $branch->user, 'branch_id' => $branch->id, 'hub_id' => null];
        }

        $hub = LogisticsDeliveryHub::query()->whereHas('logisticsBranch', fn (Builder $query) => $query
            ->where('logistics_id', $logisticsId)->whereNull('archived_at')->where('status', 'active'))
            ->whereKey($id)->whereNull('archived_at')->where('status', 'active')->with(['user', 'logisticsBranch'])->firstOrFail();
        abort_unless($hub->user && $hub->user->role === User::ROLE_DELIVERY_HUB, 422, 'This delivery hub has no login account.');
        return ['user' => $hub->user, 'branch_id' => $hub->logistics_branch_id, 'hub_id' => $hub->id];
    }

    private function adminContact(User $admin, ?AdminConversation $conversation, User $owner): array
    {
        $messages = $conversation?->messages ?? collect();
        $latest = $messages->last();
        return [
            'key' => 'admin:'.$admin->id, 'type' => 'admin', 'contact_id' => $admin->id,
            'conversation_id' => $conversation?->id, 'name' => 'ShopEase Admin',
            'sub' => 'Platform administration', 'detail' => $admin->email,
            'preview' => $latest?->body ?? $latest?->attachment_name ?? 'Platform administration',
            'last_message_at' => $latest?->created_at?->toISOString(),
            'unread' => $messages->whereNull('read_at')->where('sender_id', '!=', $owner->id)->count(),
            'messages' => $messages->map(fn ($message) => $this->serializeMessage($message, $owner, true))->all(),
        ];
    }

    private function facilityContact(string $type, int $id, User $contact, string $name, string $sub, string $detail, ?LogisticsConversation $conversation, User $owner, ?int $branchId = null): array
    {
        $messages = $conversation?->messages ?? collect();
        $latest = $messages->last();
        return [
            'key' => $type.':'.$id, 'type' => $type, 'contact_id' => $id,
            'conversation_id' => $conversation?->id, 'name' => $name, 'sub' => $sub,
            'detail' => $detail, 'preview' => $latest?->body ?? $latest?->attachment_name ?? $contact->email,
            'last_message_at' => $latest?->created_at?->toISOString(),
            'unread' => $messages->whereNull('read_at')->where('sender_id', '!=', $owner->id)->count(),
            'messages' => $messages->map(fn ($message) => $this->serializeMessage($message, $owner))->all(),
            'branch_id' => $branchId,
        ];
    }

    private function serializeMessage($message, User $viewer, bool $adminThread = false): array
    {
        $attachmentUrl = null;
        if ($message->attachment_path) {
            $prefix = match ($viewer->role) {
                User::ROLE_SORTING_CENTER => 'sorting_center',
                User::ROLE_DELIVERY_HUB => 'delivery_hub',
                default => 'logistics',
            };
            $type = $adminThread ? 'admin' : 'facility';
            $attachmentUrl = url("/api/v1/{$prefix}/messages/attachments/{$type}/{$message->conversation_id}/{$message->id}");
        }

        return [
            'id' => $message->id,
            'text' => $message->body,
            'at' => $message->created_at?->toISOString(),
            'mine' => (int) $message->sender_id === (int) $viewer->id,
            'sender' => $message->sender?->name,
            'read_at' => $message->read_at?->toISOString(),
            'attachment_name' => $message->attachment_name,
            'attachment_size' => $message->attachment_size,
            'attachment_mime_type' => $message->attachment_mime_type,
            'attachment_url' => $attachmentUrl,
        ];
    }

    private function storeAttachment(?\Illuminate\Http\UploadedFile $file, string $directory): array
    {
        if (! $file) {
            return [];
        }

        $path = $file->store($directory, 'local');
        if (! $path) {
            throw new RuntimeException('Unable to store the message attachment.');
        }

        return [
            'attachment_path' => $path,
            'attachment_name' => $file->getClientOriginalName(),
            'attachment_mime_type' => $file->getMimeType(),
            'attachment_size' => $file->getSize(),
        ];
    }
}
