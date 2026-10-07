{{-- =========================================================
     ShopEase Buyer - My Purchases
     Suggested path:
     resources/views/pages/buyer/my-purchases.blade.php

     Shared components:
     - Navbar without categories
     - Buyer footer
     - Buyer floating chat
========================================================= --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ShopEase - My Purchases</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/css/buyer/pages/my-purchases.css',
        'resources/js/buyer/pages/my-purchases.js',
    ])
    <script id="buyerPurchasesConfig" type="application/json">
        {!! json_encode([
            'ordersUrl' => '/api/v1/buyer/orders',
            'complaintsUrl' => '/api/v1/buyer/orders',
            'apiToken' => $apiToken ?? '',
        ]) !!}
    </script>
</head>

<body class="buyer-purchases-page" data-active-purchase-tab="{{ $activePurchaseTab ?? request('tab', 'all') }}">

    {{-- Shared buyer navbar, categories hidden --}}
    @include('components.buyer.navbar', ['hideCategories' => true])

    @php
        $purchaseTabs = [
            ['key' => 'all',                'label' => 'All'],
            ['key' => 'processing',         'label' => 'Processing'],
            ['key' => 'to-ship',            'label' => 'To Ship'],
            ['key' => 'in-transit',         'label' => 'In Transit'],
            ['key' => 'out-for-delivery',   'label' => 'Out for Delivery'],
            ['key' => 'delivered',          'label' => 'Delivered'],
            ['key' => 'cancelled',          'label' => 'Cancelled'],
        ];

        $orders = [];
    @endphp

    <main class="purchases-main">
        <div class="purchases-container">
            <div class="purchases-layout">

                @php
                    $requestedSidePanel = request('section');

                    $activeSidePanel = in_array(
                        $requestedSidePanel,
                        ['purchases', 'notifications', 'account'],
                        true
                    )
                        ? $requestedSidePanel
                        : 'purchases';

                    $requestedPurchaseTab = request('tab', 'all');
                    $validPurchaseTabs = collect($purchaseTabs)->pluck('key')->all();

                    $activePurchaseTab = in_array(
                        $requestedPurchaseTab,
                        $validPurchaseTabs,
                        true
                    )
                        ? $requestedPurchaseTab
                        : 'all';
                @endphp

                {{-- LEFT PROFILE / ACCOUNT NAV --}}
                <aside class="purchases-sidebar" aria-label="Buyer account navigation">
                    <div class="buyer-profile-card">
                        <div
                            class="buyer-profile-avatar"
                            id="buyerProfilePhoto"
                            aria-label="Buyer profile photo"
                        >
                            {{-- Backend profile photo will be rendered here --}}
                        </div>

                        <div class="buyer-profile-copy">
                            <strong>{{ auth()->user()->name }}</strong>
                            <button type="button" class="buyer-edit-profile">
                                <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path
                                        d="M4 20h4l11-11-4-4L4 16v4Z"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="m13.5 6.5 4 4"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                    />
                                </svg>
                                Edit Profile
                            </button>
                        </div>
                    </div>

                    <div class="buyer-sidebar-divider"></div>

                    <nav class="buyer-account-nav">
                        <button type="button" class="buyer-account-nav-link buyer-side-tab {{ $activeSidePanel === 'account' ? 'is-active' : '' }}" data-side-panel="account">
                            <span class="buyer-account-nav-icon">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <circle cx="12" cy="8" r="4" stroke="currentColor" stroke-width="1.7"/>
                                    <path d="M5 20c.8-4 3.1-6 7-6s6.2 2 7 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span>My Account</span>
                        </button>

                        <button type="button" class="buyer-account-nav-link buyer-side-tab {{ $activeSidePanel === 'purchases' ? 'is-active' : '' }}" data-side-panel="purchases">
                            <span class="buyer-account-nav-icon">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M6 4h12v16H6V4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                    <path d="M9 8h6M9 12h6M9 16h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span>My Purchases</span>
                        </button>

                        <button type="button" class="buyer-account-nav-link buyer-side-tab {{ $activeSidePanel === 'notifications' ? 'is-active' : '' }}" data-side-panel="notifications">
                            <span class="buyer-account-nav-icon">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M10 21h4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span>Notifications</span>
                        </button>
                    </nav>
                </aside>

                {{-- RIGHT CONTENT --}}
                <section class="purchases-content">

                    <div class="buyer-side-panel {{ $activeSidePanel === 'purchases' ? 'is-active' : '' }}" data-panel-content="purchases" {{ $activeSidePanel !== 'purchases' ? 'hidden' : '' }}>
                    {{-- PAGE HEADING --}}
                    <section class="purchases-page-heading">
                        <h1>My Purchases</h1>

                        <nav class="purchases-breadcrumbs" aria-label="Breadcrumb">
                            <a href="{{ url('/buyer/dashboard') }}">Home</a>
                            <span aria-hidden="true">›</span>
                            <span aria-current="page">My Purchases</span>
                        </nav>
                    </section>

                    {{-- MAIN PURCHASE PANEL --}}
                    <section class="purchase-panel">

                {{-- STATUS TABS --}}
                <div class="purchase-tabs" role="tablist" aria-label="Purchase status">
                    @foreach ($purchaseTabs as $index => $tab)
                        <button
                            type="button"
                            class="purchase-tab {{ $activePurchaseTab === $tab['key'] ? 'is-active' : '' }}"
                            data-purchase-tab="{{ $tab['key'] }}"
                            role="tab"
                            aria-selected="{{ $activePurchaseTab === $tab['key'] ? 'true' : 'false' }}"
                        >
                            {{ $tab['label'] }}
                        </button>
                    @endforeach
                </div>

                {{-- SEARCH / FILTER BAR --}}
                <div class="purchase-toolbar">
                    <label class="purchase-search">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle
                                cx="11"
                                cy="11"
                                r="6.5"
                                stroke="currentColor"
                                stroke-width="1.8"
                            />
                            <path
                                d="m16 16 4 4"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                            />
                        </svg>

                        <input
                            type="search"
                            id="purchaseSearch"
                            placeholder="Search by Seller Name, Order ID or Product name"
                            autocomplete="off"
                        >
                    </label>
                </div>

                {{-- COLUMN GUIDE --}}
                <div class="purchase-column-guide" aria-hidden="true">
                    <div>Product</div>
                    <div>Quantity</div>
                    <div>Total</div>
                    <div>Estimated Delivery</div>
                    <div>Status</div>
                </div>

                {{-- SHOP-GROUPED PURCHASE LIST --}}
                <div class="purchase-table-wrap">
                    <div id="purchaseRows" class="purchase-shop-list">
                        @foreach ($orders as $order)
                            <article
                                class="purchase-shop-card"
                                data-purchase-row
                                data-status="{{ $order['status'] }}"
                                data-search="{{ strtolower($order['shop'] . ' ' . $order['order_id'] . ' ' . $order['product']) }}"
                                {{ $activePurchaseTab !== 'all' && $order['status'] !== $activePurchaseTab ? 'hidden' : '' }}
                            >
                                {{-- SHOP HEADER --}}
                                <div class="purchase-shop-header">
                                    <div class="purchase-shop-heading">
                                        <span class="purchase-shop-icon" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none">
                                                <path
                                                    d="M4 9h16l-1.2-5H5.2L4 9Z"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                    stroke-linejoin="round"
                                                />
                                                <path
                                                    d="M6 9v10h12V9"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                    stroke-linejoin="round"
                                                />
                                                <path
                                                    d="M9 19v-5h6v5"
                                                    stroke="currentColor"
                                                    stroke-width="1.7"
                                                    stroke-linejoin="round"
                                                />
                                            </svg>
                                        </span>

                                        <strong>{{ $order['shop'] }}</strong>
                                    </div>

                                    <div class="purchase-shop-order-meta">
                                        <strong class="purchase-shop-order-id">
                                            Order ID: {{ $order['order_id'] }}
                                        </strong>
                                        <span class="purchase-shop-placed-at">
                                            Placed Oct 4, 2026 · 2:14 PM
                                        </span>
                                    </div>
                                </div>

                                {{-- ONE PRODUCT PREVIEW --}}
                                <div class="purchase-row">
                                    <div class="purchase-product-cell">
                                        <div class="purchase-product-image" aria-hidden="true">

                                            @if ($order['art'] === 'earbuds')
                                                <svg viewBox="0 0 120 100">
                                                    <rect width="120" height="100" rx="10" fill="#ECEDEA"/>
                                                    <ellipse cx="61" cy="80" rx="34" ry="7" fill="#838681" opacity=".15"/>
                                                    <rect x="37" y="48" width="50" height="35" rx="15" fill="#292E30"/>
                                                    <rect x="41" y="20" width="11" height="43" rx="6" fill="#202426" transform="rotate(-10 41 20)"/>
                                                    <rect x="74" y="18" width="11" height="44" rx="6" fill="#202426" transform="rotate(12 74 18)"/>
                                                </svg>

                                            @elseif ($order['art'] === 'watch')
                                                <svg viewBox="0 0 120 100">
                                                    <rect width="120" height="100" rx="10" fill="#E4EBEF"/>
                                                    <rect x="52" y="10" width="17" height="80" rx="9" fill="#262B2F"/>
                                                    <rect x="34" y="25" width="54" height="51" rx="13" fill="#151B20"/>
                                                    <rect x="39" y="30" width="44" height="41" rx="10" fill="#14384A"/>
                                                    <circle cx="61" cy="51" r="13" fill="#1B526A"/>
                                                </svg>

                                            @elseif ($order['art'] === 'bag')
                                                <svg viewBox="0 0 120 100">
                                                    <rect width="120" height="100" rx="10" fill="#F1E9DD"/>
                                                    <path d="M38 47Q61 12 84 47" fill="none" stroke="#93643A" stroke-width="7" stroke-linecap="round"/>
                                                    <path d="M30 44Q60 30 91 45L84 82Q61 91 36 82Z" fill="#BF8E54" stroke="#8B623B" stroke-width="3"/>
                                                </svg>

                                            @elseif ($order['art'] === 'shoes')
                                                <svg viewBox="0 0 120 100">
                                                    <rect width="120" height="100" rx="10" fill="#ECE8E2"/>
                                                    <path d="M23 61 42 42l21 9 14 17 29 6q8 2 8 10H30q-9-1-10-9Z" fill="#FAFAF8" stroke="#AAA59F" stroke-width="3"/>
                                                    <path d="M45 43l21 10 13 16-36-6Z" fill="#30383E"/>
                                                </svg>

                                            @elseif ($order['art'] === 'skincare')
                                                <svg viewBox="0 0 120 100">
                                                    <rect width="120" height="100" rx="10" fill="#F1DDD6"/>
                                                    <rect x="27" y="43" width="20" height="42" rx="6" fill="#F7F2EA"/>
                                                    <rect x="53" y="34" width="23" height="51" rx="7" fill="#E99C8B"/>
                                                    <rect x="82" y="47" width="25" height="36" rx="8" fill="#F0D7CF"/>
                                                </svg>

                                            @else
                                                <svg viewBox="0 0 120 100">
                                                    <rect width="120" height="100" rx="10" fill="#ECE7DF"/>
                                                    <ellipse cx="50" cy="65" rx="28" ry="19" fill="#706257"/>
                                                    <ellipse cx="50" cy="58" rx="24" ry="14" fill="#302A25"/>
                                                    <path d="M76 61 108 51q8-2 10 4v5L80 71Z" fill="#67594D"/>
                                                </svg>
                                            @endif
                                        </div>

                                        <div class="purchase-product-copy">
                                            <strong>{{ $order['product'] }}</strong>
                                            <small>{{ $order['variant'] }}</small>

                                            <div class="purchase-product-price">
                                                <span>₱{{ number_format($order['price'], 2) }}</span>
                                            </div>

                                            @if (($order['additional_products'] ?? 0) > 0)
                                                <div class="purchase-more-products">
                                                    +{{ $order['additional_products'] }}
                                                    more
                                                    {{ $order['additional_products'] === 1 ? 'product' : 'products' }}
                                                    from this shop
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="purchase-quantity">
                                        {{ $order['quantity'] }}
                                    </div>

                                    <div class="purchase-total">
                                        ₱{{ number_format($order['total'], 2) }}
                                    </div>

                                    <div class="purchase-estimated-delivery">
                                        {{ $order['estimated_delivery'] ?? '—' }}
                                    </div>

                                    <div class="purchase-status-cell">
                                        <span class="purchase-status purchase-status--{{ $order['status'] }}">
                                            {{ $order['status_label'] }}
                                        </span>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <div id="purchaseEmptyState" class="purchase-empty-state" hidden>
                        <div class="purchase-empty-icon" aria-hidden="true">
                            <svg viewBox="0 0 100 90" fill="none">
                                <rect x="29" y="20" width="43" height="54" rx="6" fill="#FFF2EE" stroke="#D7AAA3" stroke-width="3"/>
                                <path d="M38 33h25M38 43h25M38 53h17" stroke="#A76963" stroke-width="3" stroke-linecap="round"/>
                                <circle cx="73" cy="64" r="15" fill="#F8D9D2"/>
                                <path d="m67 64 4 4 8-9" stroke="#7A2630" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>

                        <h3>No orders found</h3>
                        <p>Try changing the selected tab or search keyword.</p>
                    </div>
                </div>

                    </section>
                    </div>

                    <div class="buyer-side-panel {{ $activeSidePanel === 'notifications' ? 'is-active' : '' }}" data-panel-content="notifications" {{ $activeSidePanel !== 'notifications' ? 'hidden' : '' }}>
                        <section class="purchases-page-heading">
                            <h1>Notifications</h1>

                            <nav class="purchases-breadcrumbs" aria-label="Breadcrumb">
                                <a href="{{ url('/buyer/dashboard') }}">Home</a>
                                <span aria-hidden="true">›</span>
                                <span aria-current="page">Notifications</span>
                            </nav>
                        </section>

                        <section class="buyer-notifications-panel">
                            <div class="buyer-notifications-header">
                                <div>
                                    <h2>Notifications</h2>
                                    <p>Updates about your orders and ShopEase activity will appear here.</p>
                                </div>

                                <button type="button" class="buyer-mark-read-button" id="buyerMarkAllRead">
                                    Mark all as read
                                </button>
                            </div>

                            <div class="buyer-notifications-list">

                                <button type="button" class="buyer-notification-item is-unread">
                                    <span class="buyer-notification-icon buyer-notification-icon--order" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M5.5 8.5h13l-1 10h-11l-1-10Z"
                                                stroke="currentColor"
                                                stroke-width="1.65"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M9 9V7a3 3 0 0 1 6 0v2"
                                                stroke="currentColor"
                                                stroke-width="1.65"
                                                stroke-linecap="round"
                                            />
                                        </svg>
                                    </span>

                                    <span class="buyer-notification-copy">
                                        <strong>Order is being processed</strong>
                                        <span>The Shop PH is preparing your Wireless Earbuds Pro order.</span>
                                    </span>

                                    <span class="buyer-notification-meta">
                                        <time>5m ago</time>
                                        <span class="buyer-notification-unread-dot" aria-label="Unread"></span>
                                    </span>
                                </button>

                                <button type="button" class="buyer-notification-item is-unread">
                                    <span class="buyer-notification-icon buyer-notification-icon--shipping" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M3.5 7h10v8.5h-10V7Z"
                                                stroke="currentColor"
                                                stroke-width="1.65"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M13.5 10h3.7l3.3 3.3v2.2h-7V10Z"
                                                stroke="currentColor"
                                                stroke-width="1.65"
                                                stroke-linejoin="round"
                                            />
                                            <circle cx="7" cy="17.5" r="1.6" stroke="currentColor" stroke-width="1.65"/>
                                            <circle cx="17.5" cy="17.5" r="1.6" stroke="currentColor" stroke-width="1.65"/>
                                        </svg>
                                    </span>

                                    <span class="buyer-notification-copy">
                                        <strong>Your package is in transit</strong>
                                        <span>Your order from Tech Haven is on the way.</span>
                                    </span>

                                    <span class="buyer-notification-meta">
                                        <time>32m ago</time>
                                        <span class="buyer-notification-unread-dot" aria-label="Unread"></span>
                                    </span>
                                </button>

                                <button type="button" class="buyer-notification-item">
                                    <span class="buyer-notification-icon buyer-notification-icon--delivered" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path d="M4 12.5 9 17l11-11" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>

                                    <span class="buyer-notification-copy">
                                        <strong>Order delivered successfully</strong>
                                        <span>Your order from Mia Boutique has been delivered.</span>
                                    </span>

                                    <span class="buyer-notification-meta">
                                        <time>2h ago</time>
                                    </span>
                                </button>

                                <button type="button" class="buyer-notification-item">
                                    <span class="buyer-notification-icon buyer-notification-icon--promo" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M4.5 11.5V5.5h6.2L19.5 14l-6 6-9-8.5Z"
                                                stroke="currentColor"
                                                stroke-width="1.65"
                                                stroke-linejoin="round"
                                            />
                                            <circle cx="8.3" cy="8.6" r="1.25" fill="currentColor"/>
                                        </svg>
                                    </span>

                                    <span class="buyer-notification-copy">
                                        <strong>New ShopEase voucher available</strong>
                                        <span>You received a voucher you can use on your next purchase.</span>
                                    </span>

                                    <span class="buyer-notification-meta">
                                        <time>Yesterday</time>
                                    </span>
                                </button>

                            </div>
                        </section>
                    </div>

                    <div class="buyer-side-panel {{ $activeSidePanel === 'account' ? 'is-active' : '' }}" data-panel-content="account" {{ $activeSidePanel !== 'account' ? 'hidden' : '' }}>
                        <section class="purchases-page-heading">
                            <h1>My Account</h1>

                            <nav class="purchases-breadcrumbs" aria-label="Breadcrumb">
                                <a href="{{ url('/buyer/dashboard') }}">Home</a>
                                <span aria-hidden="true">›</span>
                                <span aria-current="page">My Account</span>
                            </nav>
                        </section>

                        <section class="buyer-account-empty-panel">
                            <div class="buyer-account-empty-copy">
                                <h2>Account Details</h2>
                                <p>Your buyer profile details can be connected here once the backend data is ready.</p>
                            </div>
                        </section>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <div id="buyerComplaintModal" class="fixed inset-0 z-[220] hidden items-center justify-center bg-black/40 px-4 py-6" aria-hidden="true">
        <section class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" role="dialog" aria-modal="true" aria-labelledby="buyerComplaintTitle">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="buyerComplaintTitle" class="text-xl font-bold text-gray-900">Report an order problem</h2>
                    <p id="buyerComplaintOrderLabel" class="mt-1 text-sm text-gray-500"></p>
                </div>
                <button type="button" id="buyerComplaintClose" class="rounded-full p-2 text-gray-500 hover:bg-gray-100" aria-label="Close complaint form">×</button>
            </div>
            <form id="buyerComplaintForm" class="mt-5 space-y-4">
                <input id="buyerComplaintOrderId" type="hidden">
                <label class="block text-sm font-medium text-gray-700" for="buyerComplaintType">
                    Complaint type
                    <select id="buyerComplaintType" name="type" required class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">Select a reason</option>
                        <option>Wrong Item</option>
                        <option>Missing Item</option>
                        <option>Damaged Item</option>
                        <option>Late Delivery</option>
                        <option>Order Not Received</option>
                        <option>Seller Conduct</option>
                        <option>Other</option>
                    </select>
                </label>
                <label class="block text-sm font-medium text-gray-700" for="buyerComplaintDescription">
                    Tell us what happened
                    <textarea id="buyerComplaintDescription" name="description" rows="5" minlength="10" maxlength="2000" required class="mt-1 block w-full resize-y rounded-lg border border-gray-300 px-3 py-2" placeholder="Describe the issue with your order (10-2000 characters)."></textarea>
                </label>
                <p id="buyerComplaintError" class="text-sm text-red-600" role="alert" hidden></p>
                <div class="flex justify-end gap-3">
                    <button type="button" id="buyerComplaintCancel" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Cancel</button>
                    <button id="buyerComplaintSubmit" type="submit" class="rounded-lg bg-[#7B1B1B] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">Submit complaint</button>
                </div>
            </form>
        </section>
    </div>

    {{-- Shared Buyer Footer --}}
    @include('components.buyer.footer')

    {{-- Shared Buyer Floating Chat --}}
    @include('components.buyer.floating-chat')

</body>
</html>
