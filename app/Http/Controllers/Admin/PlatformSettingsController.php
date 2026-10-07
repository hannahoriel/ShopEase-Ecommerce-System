<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Announcement;
use App\Models\Admin\PlatformPolicy;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PlatformSettingsController extends Controller
{
    private const AUDIENCES = ['All Users', 'Buyers', 'Sellers', 'Logistics'];

    private const POLICY_CATEGORIES = [
        'Prohibited & Restricted Items',
        'User Conduct',
        'Shipping & Delivery',
        'Privacy & Data',
        'Buyer Policy',
        'Seller Policy',
        'Logistics Policy',
    ];

    public function page(Request $request)
    {
        abort_unless($request->user()?->role === User::ROLE_ADMIN, 403);

        $this->publishDueRecords();

        return view('pages.admin.platform-settings', [
            'announcements' => Announcement::latest()->get()->map(fn (Announcement $item) => $this->announcementData($item))->values(),
            'policies' => PlatformPolicy::latest()->get()->map(fn (PlatformPolicy $item) => $this->policyData($item))->values(),
        ]);
    }

    public function index(): JsonResponse
    {
        $this->publishDueRecords();

        return response()->json([
            'announcements' => Announcement::latest()->get()->map(fn (Announcement $item) => $this->announcementData($item))->values(),
            'policies' => PlatformPolicy::latest()->get()->map(fn (PlatformPolicy $item) => $this->policyData($item))->values(),
        ]);
    }

    public function storeAnnouncement(Request $request): JsonResponse
    {
        $data = $this->validateAnnouncement($request);
        $publishAt = $this->resolvePublishTime($data);
        $this->ensureValidSchedule($data, $publishAt);
        $isScheduled = $publishAt->isFuture();

        $announcement = Announcement::create([
            'title' => $data['title'],
            'type' => $data['type'],
            'body' => $data['description'],
            'badge_label' => $data['type'],
            'audience' => $data['audience'],
            'status' => $isScheduled ? 'Scheduled' : 'Published',
            'published_at' => $publishAt,
            'created_by' => $request->user()->id,
            'is_active' => ! $isScheduled,
            'banner_path' => $request->hasFile('banner')
                ? $request->file('banner')->store('platform-announcements', 'public')
                : null,
        ]);

        return response()->json(['announcement' => $this->announcementData($announcement)], 201);
    }

    public function updateAnnouncement(Request $request, Announcement $announcement): JsonResponse
    {
        $data = $this->validateAnnouncement($request);
        $publishAt = $this->resolvePublishTime($data);
        $this->ensureValidSchedule($data, $publishAt);
        $isScheduled = $publishAt->isFuture();

        if ($request->boolean('remove_banner') && $announcement->banner_path) {
            Storage::disk('public')->delete($announcement->banner_path);
            $announcement->banner_path = null;
        }

        if ($request->hasFile('banner')) {
            if ($announcement->banner_path) {
                Storage::disk('public')->delete($announcement->banner_path);
            }

            $announcement->banner_path = $request->file('banner')->store('platform-announcements', 'public');
        }

        $announcement->fill([
            'title' => $data['title'],
            'type' => $data['type'],
            'body' => $data['description'],
            'badge_label' => $data['type'],
            'audience' => $data['audience'],
            'status' => $isScheduled ? 'Scheduled' : 'Published',
            'published_at' => $publishAt,
            'is_active' => ! $isScheduled,
        ])->save();

        return response()->json(['announcement' => $this->announcementData($announcement)]);
    }

    public function destroyAnnouncement(Announcement $announcement): JsonResponse
    {
        if ($announcement->banner_path) {
            Storage::disk('public')->delete($announcement->banner_path);
        }

        $announcement->delete();

        return response()->json(['message' => 'Announcement deleted.']);
    }

    public function storePolicy(Request $request): JsonResponse
    {
        $data = $this->validatePolicy($request);
        $publishAt = $data['status'] === 'Scheduled'
            ? Carbon::parse($data['publish_at'])
            : now();
        $this->ensureValidPolicySchedule($data, $publishAt);
        $isScheduled = $data['status'] === 'Scheduled';

        $policy = PlatformPolicy::create([
            'title' => $data['title'],
            'category' => $data['category'],
            'description' => $data['description'],
            'content' => $data['content'],
            'status' => $isScheduled ? 'Scheduled' : 'Published',
            'published_at' => $publishAt,
            'created_by' => $request->user()->id,
        ]);

        return response()->json(['policy' => $this->policyData($policy)], 201);
    }

    public function updatePolicy(Request $request, PlatformPolicy $policy): JsonResponse
    {
        $data = $this->validatePolicy($request);
        $publishAt = $data['status'] === 'Scheduled'
            ? Carbon::parse($data['publish_at'])
            : now();
        $this->ensureValidPolicySchedule($data, $publishAt);

        $policy->fill([
            'title' => $data['title'],
            'category' => $data['category'],
            'description' => $data['description'],
            'content' => $data['content'],
            'status' => $data['status'],
            'published_at' => $publishAt,
        ])->save();

        return response()->json(['policy' => $this->policyData($policy)]);
    }

    public function destroyPolicy(PlatformPolicy $policy): JsonResponse
    {
        $policy->delete();

        return response()->json(['message' => 'Policy deleted.']);
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'description' => ['required', 'string', 'max:500'],
            'type' => ['required', Rule::in(['Announcement', 'Policy Update'])],
            'audience' => ['required', Rule::in(self::AUDIENCES)],
            'status' => ['required', Rule::in(['Published', 'Scheduled'])],
            'publish_date' => ['required_if:status,Scheduled', 'nullable', 'date'],
            'publish_time' => ['required_if:status,Scheduled', 'nullable', 'date_format:H:i'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
            'remove_banner' => ['sometimes', 'boolean'],
        ]);
    }

    private function validatePolicy(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'category' => ['required', Rule::in(self::POLICY_CATEGORIES)],
            'description' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string', 'max:500'],
            'status' => ['required', Rule::in(['Published', 'Scheduled'])],
            'publish_at' => ['required_if:status,Scheduled', 'nullable', 'date'],
        ]);
    }

    private function resolvePublishTime(array $data): Carbon
    {
        if ($data['status'] === 'Scheduled') {
            return Carbon::parse($data['publish_date'].' '.$data['publish_time']);
        }

        return now();
    }

    private function ensureValidSchedule(array $data, Carbon $publishAt): void
    {
        if ($data['status'] === 'Scheduled' && ! $publishAt->isFuture()) {
            throw ValidationException::withMessages([
                'publish_date' => 'Choose a future date and time for a scheduled announcement.',
            ]);
        }
    }

    private function ensureValidPolicySchedule(array $data, Carbon $publishAt): void
    {
        if ($data['status'] === 'Scheduled' && ! $publishAt->isFuture()) {
            throw ValidationException::withMessages([
                'publish_at' => 'Choose a future date and time to schedule this policy.',
            ]);
        }
    }

    private function publishDueRecords(): void
    {
        Announcement::where('status', 'Scheduled')
            ->where('published_at', '<=', now())
            ->update(['status' => 'Published', 'is_active' => true]);

        PlatformPolicy::where('status', 'Scheduled')
            ->where('published_at', '<=', now())
            ->update(['status' => 'Published']);
    }

    private function announcementData(Announcement $announcement): array
    {
        return [
            'id' => (string) $announcement->id,
            'type' => $announcement->type ?? 'Announcement',
            'title' => $announcement->title,
            'description' => $announcement->body,
            'audience' => $announcement->audience ?? 'All Users',
            'status' => $announcement->status ?? ($announcement->is_active ? 'Published' : 'Scheduled'),
            'date' => $announcement->published_at?->format('M d, Y') ?? $announcement->created_at?->format('M d, Y'),
            'time' => $announcement->published_at?->format('h:i A') ?? '',
            'rawDate' => $announcement->status === 'Scheduled' ? $announcement->published_at?->format('Y-m-d') : '',
            'rawTime' => $announcement->status === 'Scheduled' ? $announcement->published_at?->format('H:i') : '',
            'bannerUrl' => $announcement->banner_path ? Storage::disk('public')->url($announcement->banner_path) : '',
            'bannerName' => $announcement->banner_path ? basename($announcement->banner_path) : '',
            'theme' => $announcement->type === 'Policy Update' ? 'purple' : 'red',
            'icon' => $announcement->type === 'Policy Update' ? 'document' : 'megaphone',
        ];
    }

    private function policyData(PlatformPolicy $policy): array
    {
        return [
            'id' => (string) $policy->id,
            'title' => $policy->title,
            'category' => $policy->category,
            'description' => $policy->description,
            'content' => $policy->content,
            'status' => $policy->status,
            'audience' => 'All Users',
            'date' => $policy->published_at?->format('M d, Y') ?? $policy->created_at?->format('M d, Y'),
            'time' => $policy->published_at?->format('h:i A') ?? '',
            'rawDate' => $policy->status === 'Scheduled' ? $policy->published_at?->format('Y-m-d') : '',
            'rawTime' => $policy->status === 'Scheduled' ? $policy->published_at?->format('H:i') : '',
            'theme' => 'blue',
            'icon' => 'shield',
        ];
    }
}
