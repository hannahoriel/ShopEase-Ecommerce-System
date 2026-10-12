<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminNotificationController extends Controller
{
    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user() && strtolower((string) $request->user()->role) === 'admin', 403);
    }

    public function index(Request $request): View
    {
        $this->authorizeAdmin($request);

        $user = $request->user();
        $filter = $request->query('filter', 'all');
        if (!in_array($filter, ['all', 'unread', 'read'], true)) {
            $filter = 'all';
        }
        $search = trim((string) $request->query('search', ''));
        $date = (string) $request->query('date', '');
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = '';
        } else {
            [$year, $month, $day] = array_map('intval', explode('-', $date));
            if (!checkdate($month, $day, $year)) $date = '';
        }

        $base = $user->notifications();
        $counts = [
            'all' => (clone $base)->count(),
            'unread' => (clone $base)->whereNull('read_at')->count(),
            'read' => (clone $base)->whereNotNull('read_at')->count(),
        ];

        $query = $user->notifications()->latest();
        if ($filter === 'unread') $query->whereNull('read_at');
        if ($filter === 'read') $query->whereNotNull('read_at');
        if ($date !== '') $query->whereDate('created_at', $date);
        if ($search !== '') {
            $escaped = addcslashes($search, '%_\\');
            $query->where('data', 'like', '%' . $escaped . '%');
        }

        return view('pages.admin.notifications', [
            'notifications' => $query->paginate(10)->withQueryString(),
            'notificationCounts' => $counts,
        ]);
    }

    public function markRead(Request $request, string $notification): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $item = $request->user()->notifications()->whereKey($notification)->firstOrFail();
        if ($item->read_at === null) $item->markAsRead();
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);
        $request->user()->unreadNotifications()->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
