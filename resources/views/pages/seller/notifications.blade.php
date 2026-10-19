
{{-- =========================================================
    SHOPEASE — SELLER ALL NOTIFICATIONS
    resources/views/pages/seller/notifications.blade.php

    Uses:
    - Existing seller sidebar and navbar
    - Laravel database notifications
    - Poppins font
    - Responsive seller dashboard layout
    - No hardcoded notification records
========================================================= --}}

@php
    $notificationItems = $notifications ?? collect();

    $activeFilter = request()->query('filter', 'all');

    if (!in_array($activeFilter, ['all', 'unread', 'read'], true)) {
        $activeFilter = 'all';
    }

    $searchQuery = trim((string) request()->query('search', ''));
    $selectedDate = request()->query('date', '');
    $selectedDate = is_string($selectedDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)
        && checkdate((int) substr($selectedDate, 5, 2), (int) substr($selectedDate, 8, 2), (int) substr($selectedDate, 0, 4))
        ? $selectedDate : '';

    $totalNotifications = $notificationCounts['all'] ?? 0;
    $unreadNotifications = $notificationCounts['unread'] ?? 0;
    $readNotifications = $notificationCounts['read'] ?? 0;

    $notificationsOnPage = collect($notificationItems->items());

    $formatNotificationTime = function ($notification) {
        if (!$notification->created_at) {
            return 'Date unavailable';
        }

        $created = $notification->created_at;

        return $created->diffForHumans() . ' · ' .
            $created->format('M d, Y \a\t h:i A');
    };
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>All Notifications - ShopEase</title>

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/css/seller/dashboard.css',
        'resources/css/seller/notifications.css',
        'resources/js/seller/notifications.js'
    ])
</head>

<body class="m-0 p-0 font-[Poppins,sans-serif] bg-[#FCF8F6]">

    {{-- ACTUAL SELLER COMPONENTS --}}

    @section('page-title', 'Notifications')

    @include('components.seller.sidebar')
    @include('components.seller.navbar')

    <main
        id="sellerNotificationsPage"
        class="seller-notifications-page"
    >
        <div class="seller-notifications-content">

            @if(session('success'))
                <div class="sn-flash" role="status">
                    <svg
                        width="16"
                        height="16"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path d="m5 12 4 4L19 6"/>
                    </svg>

                    {{ session('success') }}
                </div>
            @endif

            {{-- NOTIFICATIONS PANEL --}}

            <section class="sn-main-card">

                <div class="sn-card-toolbar">

                    {{-- SERVER-SIDE SEARCH --}}

                    <form
                        class="sn-search-form"
                        method="GET"
                        action="{{ route('seller.notifications.index') }}"
                    >
                        <input
                            type="hidden"
                            name="filter"
                            value="{{ $activeFilter }}"
                        >

                        <div class="sn-search-field">
                            <input
                                class="sn-search-input"
                                type="search"
                                name="search"
                                value="{{ $searchQuery }}"
                                placeholder="Search notifications..."
                                aria-label="Search notifications"
                            >
                            <button type="submit" class="sn-search-button" aria-label="Search">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="7"/>
                                    <path d="m16 16 5 5"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Native date picker: exactly one calendar icon --}}
                        <input
                            id="snDateFilter"
                            class="sn-date-input"
                            type="date"
                            name="date"
                            value="{{ $selectedDate }}"
                            aria-label="Filter notifications by date"
                            title="Filter notifications by date"
                            onchange="this.form.requestSubmit()"
                        >

                        @if($selectedDate !== '')
                            <a class="sn-clear-date" href="{{ route('seller.notifications.index', ['filter' => $activeFilter, 'search' => $searchQuery, 'date' => $selectedDate]) }}" aria-label="Clear date filter">Clear date</a>
                        @endif

                    </form>

                    <form method="POST" action="{{ route('seller.notifications.read-all') }}" class="sn-toolbar-mark-all">
                        @csrf
                        @method('PATCH')
                        <button class="sn-mark-all" type="submit" {{ $unreadNotifications === 0 ? 'disabled' : '' }}>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m4 12 5 5L20 6"/>
                                <path d="m3 6 3 3"/>
                            </svg>
                            Mark All as Read
                        </button>
                    </form>
                </div>

                {{-- FILTER TABS --}}

                <nav
                    class="sn-tabs"
                    aria-label="Notification filters"
                >

                    <a
                        href="{{ route('seller.notifications.index', ['filter' => 'all', 'search' => $searchQuery, 'date' => $selectedDate]) }}"
                        class="sn-tab {{ $activeFilter === 'all' ? 'active' : '' }}"
                        {{ $activeFilter === 'all' ? 'aria-current=page' : '' }}
                    >
                        All

                        <span class="sn-tab-count">
                            {{ $totalNotifications }}
                        </span>
                    </a>

                    <a
                        href="{{ route('seller.notifications.index', ['filter' => 'unread', 'search' => $searchQuery, 'date' => $selectedDate]) }}"
                        class="sn-tab {{ $activeFilter === 'unread' ? 'active' : '' }}"
                        {{ $activeFilter === 'unread' ? 'aria-current=page' : '' }}
                    >
                        Unread

                        <span class="sn-tab-count">
                            {{ $unreadNotifications }}
                        </span>
                    </a>

                    <a
                        href="{{ route('seller.notifications.index', ['filter' => 'read', 'search' => $searchQuery, 'date' => $selectedDate]) }}"
                        class="sn-tab {{ $activeFilter === 'read' ? 'active' : '' }}"
                        {{ $activeFilter === 'read' ? 'aria-current=page' : '' }}
                    >
                        Read

                        <span class="sn-tab-count">
                            {{ $readNotifications }}
                        </span>
                    </a>

                </nav>

                {{-- NOTIFICATION LIST --}}

                <div class="sn-list">

                    @forelse($notificationItems as $notification)

                        @php
                            $notificationData = is_array($notification->data)
                                ? $notification->data
                                : [];

                            $notificationTitle =
                                $notificationData['title']
                                ?? $notificationData['subject']
                                ?? 'ShopEase Notification';

                            $notificationDescription =
                                $notificationData['message']
                                ?? $notificationData['body']
                                ?? $notificationData['description']
                                ?? 'You have a new update regarding your ShopEase account.';

                            $notificationType = strtolower(
                                (string) (
                                    $notificationData['type']
                                    ?? $notificationData['category']
                                    ?? 'general'
                                )
                            );

                            $iconType = 'general';

                            if (
                                str_contains($notificationType, 'warning') ||
                                str_contains($notificationType, 'policy') ||
                                str_contains($notificationType, 'compliance') ||
                                str_contains($notificationType, 'removed')
                            ) {
                                $iconType = 'warning';
                            } elseif (
                                str_contains($notificationType, 'order') ||
                                str_contains($notificationType, 'shipping') ||
                                str_contains($notificationType, 'delivery')
                            ) {
                                $iconType = 'order';
                            } elseif (
                                str_contains($notificationType, 'success') ||
                                str_contains($notificationType, 'approved') ||
                                str_contains($notificationType, 'resolved')
                            ) {
                                $iconType = 'success';
                            } elseif (
                                str_contains($notificationType, 'message') ||
                                str_contains($notificationType, 'chat')
                            ) {
                                $iconType = 'message';
                            }

                            $isUnread = $notification->read_at === null;

                            /*
                             * Only allow relative internal paths.
                             * External redirects are intentionally disallowed.
                             */
                            $rawTarget = $notificationData['url']
                                ?? $notificationData['link']
                                ?? null;

                            $targetUrl = null;

                            if (
                                is_string($rawTarget) &&
                                str_starts_with($rawTarget, '/') &&
                                !str_starts_with($rawTarget, '//') &&
                                !str_contains($rawTarget, '\\') &&
                                !preg_match('/[\x00-\x1F\x7F]/', $rawTarget)
                            ) {
                                $targetUrl = $rawTarget;
                            }
                        @endphp

                        <article
                            class="sn-notification {{ $isUnread ? 'is-unread' : '' }}"
                        >

                            <div
                                class="sn-notification-icon {{ $iconType }}"
                                aria-hidden="true"
                            >

                                @if($iconType === 'warning')
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="m12 3 10 18H2L12 3Z"/>
                                        <path d="M12 9v5"/>
                                        <path d="M12 17h.01"/>
                                    </svg>

                                @elseif($iconType === 'order')
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="m3 7 9-4 9 4-9 4-9-4Z"/>
                                        <path d="M3 7v10l9 4 9-4V7"/>
                                        <path d="M12 11v10"/>
                                    </svg>

                                @elseif($iconType === 'success')
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <circle cx="12" cy="12" r="9"/>
                                        <path d="m8 12 3 3 5-6"/>
                                    </svg>

                                @elseif($iconType === 'message')
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M21 11.5a8.5 8.5 0 0 1-12 7.7L3 21l1.8-6A8.5 8.5 0 1 1 21 11.5Z"/>
                                    </svg>

                                @else
                                    <svg
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    >
                                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                        <path d="M10 21h4"/>
                                    </svg>
                                @endif

                            </div>

                            <div class="sn-notification-body">

                                <div class="sn-notification-top">

                                    <h3 class="sn-notification-title">
                                        @if($isUnread)
                                            <span
                                                class="sn-unread-dot"
                                                aria-label="Unread"
                                            ></span>
                                        @endif

                                        {{ $notificationTitle }}
                                    </h3>

                                </div>

                                <p class="sn-notification-description">
                                    {{ $notificationDescription }}
                                </p>

                                <div class="sn-notification-footer">

                                    <span class="sn-notification-time">
                                        {{ $formatNotificationTime($notification) }}
                                    </span>

                                    @if($isUnread)

                                        <form
                                            method="POST"
                                            action="{{ route('seller.notifications.read', $notification->id) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                class="sn-action"
                                                type="submit"
                                            >
                                                Mark as Read
                                            </button>
                                        </form>

                                    @else

                                        <span class="sn-read-label">
                                            Already read
                                        </span>

                                    @endif

                                    @if($targetUrl)
                                        <a
                                            class="sn-action muted"
                                            href="{{ $targetUrl }}"
                                        >
                                            View Details →
                                        </a>
                                    @endif

                                </div>

                            </div>

                            <div class="sn-notification-side">

                                <span class="sn-status-pill {{ $isUnread ? 'unread' : '' }}">
                                    {{ $isUnread ? 'Unread' : 'Read' }}
                                </span>

                            </div>

                        </article>

                    @empty

                        <div class="sn-empty-state">

                            <div class="sn-empty-icon">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.7"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"/>
                                    <path d="M10 21h4"/>
                                </svg>
                            </div>

                            <h3>
                                @if($searchQuery !== '')
                                    No matching notifications
                                @elseif($activeFilter === 'unread')
                                    You're all caught up!
                                @elseif($activeFilter === 'read')
                                    No read notifications yet
                                @else
                                    No notifications yet
                                @endif
                            </h3>

                            <p>
                                @if($searchQuery !== '')
                                    Try searching with a different keyword.
                                @elseif($activeFilter === 'unread')
                                    You don't have any unread notifications.
                                @elseif($activeFilter === 'read')
                                    Notifications you've marked as read will appear here.
                                @else
                                    Your shop notifications will appear here
                                    when there are new updates.
                                @endif
                            </p>

                        </div>

                    @endforelse

                </div>

                {{-- PAGINATION --}}

                @if($notificationItems->hasPages())

                    <div class="sn-pagination">

                        <span class="sn-pagination-info">
                            Showing
                            {{ $notificationItems->firstItem() }}
                            to
                            {{ $notificationItems->lastItem() }}
                            of
                            {{ $notificationItems->total() }}
                            notifications
                        </span>

                        <div class="sn-pagination-buttons">

                            <a
                                class="sn-page-button {{ $notificationItems->onFirstPage() ? 'disabled' : '' }}"
                                href="{{ $notificationItems->previousPageUrl() ?? '#' }}"
                                aria-label="Previous page"
                            >
                                ‹
                            </a>

                            @foreach(range(1, $notificationItems->lastPage()) as $page)
                                @if(
                                    $notificationItems->lastPage() <= 7 ||
                                    $page === 1 ||
                                    $page === $notificationItems->lastPage() ||
                                    abs($page - $notificationItems->currentPage()) <= 1
                                )
                                    <a
                                        class="sn-page-button {{ $page === $notificationItems->currentPage() ? 'active' : '' }}"
                                        href="{{ $notificationItems->url($page) }}"
                                        {{ $page === $notificationItems->currentPage() ? 'aria-current=page' : '' }}
                                    >
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            <a
                                class="sn-page-button {{ !$notificationItems->hasMorePages() ? 'disabled' : '' }}"
                                href="{{ $notificationItems->nextPageUrl() ?? '#' }}"
                                aria-label="Next page"
                            >
                                ›
                            </a>

                        </div>

                    </div>

                @endif

            </section>

        </div>
    </main>

    {{-- =========================================
         KEEP PAGE ALIGNED TO SELLER SIDEBAR
    ========================================= --}}

</body>
</html>
