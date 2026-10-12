
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
        'resources/css/seller/dashboard.css'
    ])

    <style>
        /* =========================================
           SELLER NOTIFICATIONS — BASE
        ========================================= */

        .seller-notifications-page,
        .seller-notifications-page *,
        .seller-notifications-page *::before,
        .seller-notifications-page *::after {
            box-sizing: border-box;
        }

        .seller-notifications-page {
            min-height: 100vh;
            margin-left: 270px;
            padding-top: 120px;
            background: #FCF8F6;
            color: #17120F;
            font-family: 'Poppins', sans-serif;
            transition: margin-left 200ms ease;
        }

        .seller-notifications-page button,
        .seller-notifications-page input {
            font-family: 'Poppins', sans-serif;
        }

        .seller-notifications-content {
            width: 100%;
            padding: 24px 25px 48px 20px;
        }

        .sn-mark-all {
            height: 37px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 15px;
            border: 1px solid #9E241F;
            border-radius: 8px;
            background: #9E241F;
            color: #FFFFFF;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .sn-mark-all svg {
            width: 15px;
            height: 15px;
        }

        .sn-mark-all:hover:not(:disabled) {
            background: #841C19;
            transform: translateY(-1px);
        }

        .sn-mark-all:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        /* =========================================
           MAIN CARD
        ========================================= */

        .sn-main-card {
            overflow: hidden;
            background: #FFFFFF;
            border: 1px solid #F0E9E6;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(42, 20, 15, .04);
        }

        .sn-card-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
            padding: 20px 22px 0;
        }

        /* =========================================
           SEARCH
        ========================================= */

        .sn-toolbar-mark-all {
            flex: 0 0 auto;
        }

        .sn-search-form {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            flex: 1 1 400px;
            max-width: 100%;
        }

        .sn-search-field {
            position: relative;
            width: 285px;
            max-width: 100%;
        }

        .sn-date-input {
            width: 170px;
            height: 36px;
            padding: 0 11px;
            border: 1px solid #D9D6D4;
            border-radius: 8px;
            background: #FFFFFF;
            color: #76716E;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 400;
            outline: none;
            cursor: pointer;
            color-scheme: light;
        }

        .sn-date-input:focus {
            border-color: #ABA4A1;
            box-shadow: 0 0 0 3px rgba(120, 120, 120, .09);
        }

        .sn-date-input::-webkit-calendar-picker-indicator {
            cursor: pointer;
            opacity: .6;
        }

        .sn-clear-date {
            color: #817A76;
            font-size: 11px;
            font-weight: 500;
            text-decoration: none;
        }

        .sn-clear-date:hover { color: #9E241F; }

        .sn-search-input {
            width: 100%;
            height: 36px;
            padding: 0 41px 0 13px;
            border: 1px solid #D9D6D4;
            border-radius: 8px;
            outline: none;
            background: #FFFFFF;
            color: #48413F;
            font-size: 11px;
            font-weight: 400;
            transition: border-color .2s, box-shadow .2s;
        }

        .sn-search-input::placeholder {
            color: #A9A3A0;
        }

        .sn-search-input:focus {
            border-color: #ABA4A1;
            box-shadow: 0 0 0 3px rgba(120, 120, 120, .09);
        }

        .sn-search-button {
            position: absolute;
            right: 3px;
            top: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            padding: 0;
            border: 0;
            border-radius: 6px;
            background: transparent;
            color: #928B87;
            cursor: pointer;
        }

        .sn-search-button svg {
            width: 17px;
            height: 17px;
        }

        .sn-search-button:hover {
            background: #F6F3F2;
        }

        /* =========================================
           TABS
        ========================================= */

        .sn-tabs {
            display: flex;
            align-items: center;
            gap: 30px;
            margin-top: 13px;
            padding: 0 22px;
            border-bottom: 1px solid #EEE9E6;
            overflow-x: auto;
            scrollbar-width: none;
        }

        .sn-tabs::-webkit-scrollbar {
            display: none;
        }

        .sn-tab {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            min-height: 49px;
            color: #96908D;
            text-decoration: none;
            white-space: nowrap;
            font-size: 12px;
            font-weight: 400;
            transition: color .2s;
        }

        .sn-tab:hover {
            color: #9E241F;
        }

        .sn-tab.active {
            color: #9E241F;
            font-weight: 600;
        }

        .sn-tab.active::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -1px;
            height: 3px;
            border-radius: 4px 4px 0 0;
            background: #9E241F;
        }

        .sn-tab-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 20px;
            min-height: 20px;
            padding: 2px 6px;
            border-radius: 6px;
            background: #F5F2F0;
            color: #89817D;
            font-size: 10px;
            font-weight: 500;
        }

        .sn-tab.active .sn-tab-count {
            background: #FBE3DD;
            color: #9E241F;
        }

        /* =========================================
           NOTIFICATION ROW
        ========================================= */

        .sn-list {
            display: flex;
            flex-direction: column;
        }

        .sn-notification {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 19px 22px;
            border-bottom: 1px solid #F1ECEA;
            background: #FFFFFF;
            transition: background .18s;
        }

        .sn-notification:last-child {
            border-bottom: 0;
        }

        .sn-notification:hover {
            background: #FFFAF8;
        }

        .sn-notification.is-unread {
            background: #FFFAF7;
        }

        .sn-notification.is-unread:hover {
            background: #FFF4EF;
        }

        .sn-notification-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 43px;
            height: 43px;
            flex: 0 0 43px;
            border-radius: 11px;
            background: #FFF0EC;
            color: #9E241F;
        }

        .sn-notification-icon svg {
            width: 20px;
            height: 20px;
        }

        .sn-notification-icon.warning {
            background: #FFF1D9;
            color: #CA8123;
        }

        .sn-notification-icon.order {
            background: #EAF0FF;
            color: #547AC6;
        }

        .sn-notification-icon.success {
            background: #E5F2E4;
            color: #328C40;
        }

        .sn-notification-icon.message {
            background: #F2E8FF;
            color: #8654AD;
        }

        .sn-notification-body {
            flex: 1;
            min-width: 0;
        }

        .sn-notification-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 6px;
        }

        .sn-notification-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            color: #25201E;
            font-size: 12px;
            line-height: 1.65;
            font-weight: 500;
            overflow-wrap: anywhere;
        }

        .sn-notification.is-unread .sn-notification-title {
            color: #17120F;
            font-weight: 600;
        }

        .sn-unread-dot {
            width: 7px;
            height: 7px;
            flex: 0 0 7px;
            border-radius: 50%;
            background: #A52A2A;
        }

        .sn-notification-time {
            color: #AAA3A0;
            font-size: 10px;
            line-height: 1.6;
            font-weight: 400;
        }

        .sn-notification-description {
            margin: 0;
            max-width: 850px;
            color: #817A76;
            font-size: 11px;
            line-height: 1.85;
            font-weight: 400;
            overflow-wrap: anywhere;
        }

        .sn-notification-footer {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-top: 12px;
        }

        .sn-action {
            padding: 0;
            border: 0;
            background: transparent;
            color: #9E241F;
            font-size: 10px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: color .2s;
        }

        .sn-action:hover {
            color: #701713;
            text-decoration: underline;
        }

        .sn-action.muted {
            color: #8B8480;
        }

        .sn-action.muted:hover {
            color: #55504D;
        }

        .sn-read-label {
            color: #AAA39F;
            font-size: 10px;
            font-weight: 400;
        }

        .sn-notification-side {
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 0 0 auto;
        }

        .sn-status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 5px 10px;
            border-radius: 999px;
            background: #F2F0EF;
            color: #837D7A;
            font-size: 9px;
            font-weight: 500;
        }

        .sn-status-pill.unread {
            background: #FDE7E0;
            color: #9E241F;
        }

        /* =========================================
           EMPTY STATE
        ========================================= */

        .sn-empty-state {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 320px;
            padding: 42px 20px;
            text-align: center;
        }

        .sn-empty-icon {
            display: grid;
            place-items: center;
            width: 68px;
            height: 68px;
            margin-bottom: 18px;
            border-radius: 18px;
            background: #FFF0EC;
            color: #A52A2A;
        }

        .sn-empty-icon svg {
            width: 30px;
            height: 30px;
        }

        .sn-empty-state h3 {
            margin: 0 0 8px;
            color: #292320;
            font-size: 15px;
            font-weight: 600;
        }

        .sn-empty-state p {
            max-width: 365px;
            margin: 0;
            color: #99918E;
            font-size: 11px;
            line-height: 1.9;
        }

        /* =========================================
           PAGINATION
        ========================================= */

        .sn-pagination {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            min-height: 64px;
            padding: 14px 22px;
            border-top: 1px solid #F0EBE8;
        }

        .sn-pagination-info {
            color: #98908D;
            font-size: 11px;
        }

        .sn-pagination-buttons {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        .sn-page-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 29px;
            height: 29px;
            padding: 0 8px;
            border: 1px solid #EEE6E2;
            border-radius: 7px;
            background: #FFFFFF;
            color: #6E6561;
            font-size: 11px;
            font-weight: 500;
            text-decoration: none;
        }

        .sn-page-button:hover {
            background: #FFF2EE;
            border-color: #F1D4CD;
        }

        .sn-page-button.active {
            background: #9E241F;
            border-color: #9E241F;
            color: #FFFFFF;
        }

        .sn-page-button.disabled {
            opacity: .35;
            pointer-events: none;
        }

        /* =========================================
           FEEDBACK
        ========================================= */

        .sn-flash {
            display: flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 16px;
            padding: 12px 16px;
            border: 1px solid #D4E9D2;
            border-radius: 9px;
            background: #F0F9EE;
            color: #347239;
            font-size: 11px;
            font-weight: 500;
        }

        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 760px) {
            .seller-notifications-page {
                margin-left: 0 !important;
                padding-top: 104px;
            }

            .seller-notifications-content {
                padding: 20px 16px 36px;
            }

            .sn-card-toolbar {
                padding: 17px 16px 0;
            }

            .sn-tabs {
                padding: 0 16px;
                gap: 21px;
            }

            .sn-notification {
                padding: 17px 16px;
                gap: 11px;
            }

            .sn-notification-icon {
                width: 37px;
                height: 37px;
                flex-basis: 37px;
            }

            .sn-notification-icon svg {
                width: 18px;
                height: 18px;
            }

            .sn-pagination {
                padding: 15px 16px;
            }
        }

        @media (max-width: 520px) {
            .sn-search-form,
            .sn-search-field,
            .sn-date-input {
                width: 100%;
            }

            .sn-toolbar-mark-all,
            .sn-mark-all {
                width: 100%;
            }

            .sn-notification-side {
                display: none;
            }


        }
    </style>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const page = document.getElementById(
                'sellerNotificationsPage'
            );

            const sidebar = document.getElementById(
                'sellerSidebar'
            );

            if (!page) {
                return;
            }

            function updateSidebarOffset() {

                if (window.innerWidth <= 760) {
                    page.style.marginLeft = '0px';
                    return;
                }

                const collapsed = sidebar &&
                    sidebar.classList.contains(
                        'seller-sidebar-collapsed'
                    );

                page.style.marginLeft = collapsed
                    ? '100px'
                    : '270px';
            }

            updateSidebarOffset();

            window.addEventListener(
                'resize',
                updateSidebarOffset
            );

            if (sidebar && typeof MutationObserver !== 'undefined') {

                const observer = new MutationObserver(function () {
                    requestAnimationFrame(updateSidebarOffset);
                });

                observer.observe(sidebar, {
                    attributes: true,
                    attributeFilter: ['class']
                });
            }

            document.body.addEventListener(
                'toggle-seller-sidebar',
                function () {
                    requestAnimationFrame(updateSidebarOffset);
                }
            );

        });
    </script>

</body>
</html>
