<?php

namespace App\Http\Controllers\Api\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Admin\AdminConversation;
use App\Models\Admin\AdminMessage;
use App\Models\Admin\Complaint;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintMessagesController extends Controller
{
    public function index(Request $request, Complaint $complaint): JsonResponse
    {
        $this->authorizeBuyerComplaint($request, $complaint);
        $conversation = $this->conversation($request, $complaint);
        $conversation->messages()
            ->where('sender_id', '!=', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json([
            'data' => $conversation->messages()->with('sender:id,name')->get()
                ->map(fn (AdminMessage $message): array => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'sender_name' => $message->sender?->name ?? 'ShopEase Support',
                    'is_me' => $message->sender_id === $request->user()->id,
                    'created_at' => $message->created_at?->toISOString(),
                ]),
        ]);
    }

    public function store(Request $request, Complaint $complaint): JsonResponse
    {
        $this->authorizeBuyerComplaint($request, $complaint);
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);
        $conversation = $this->conversation($request, $complaint);
        $message = $conversation->messages()->create([
            'sender_id' => $request->user()->id,
            'body' => trim($data['body']),
        ]);

        return response()->json([
            'message' => 'Message sent to ShopEase Support.',
            'data' => [
                'id' => $message->id,
                'body' => $message->body,
                'sender_name' => $request->user()->name,
                'is_me' => true,
                'created_at' => $message->created_at?->toISOString(),
            ],
        ], 201);
    }

    private function authorizeBuyerComplaint(Request $request, Complaint $complaint): void
    {
        abort_unless(
            $request->user()->role === 'buyer' && $complaint->user_id === $request->user()->id,
            404,
        );
    }

    private function conversation(Request $request, Complaint $complaint): AdminConversation
    {
        return AdminConversation::query()->firstOrCreate(
            [
                'category' => AdminConversation::CATEGORY_COMPLAINTS,
                'complaint_id' => $complaint->id,
                'complaint_party' => 'buyer',
            ],
            ['user_id' => $request->user()->id],
        );
    }
}
