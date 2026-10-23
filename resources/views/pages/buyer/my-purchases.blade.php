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
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite([
        'resources/css/app.css',
        'resources/css/buyer/pages/my-purchases.css',
        'resources/js/buyer/pages/my-purchases.js',
    ])
    @vite('resources/js/buyer/components/buyer-likes.js')
    <meta name="buyer-likes-user-id" content="{{ auth()->id() ?? 'guest' }}">
    <script id="buyerPurchasesConfig" type="application/json">
        {!! json_encode([
            'ordersUrl' => '/api/v1/buyer/orders',
            'complaintsUrl' => '/api/v1/buyer/orders',
            'notificationsUrl' => '/api/v1/buyer/notifications',
            'adminMessagesUrl' => \Illuminate\Support\Facades\Route::has('buyer.messages') ? route('buyer.messages') : url('/buyer/messages'),
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
            ['key' => 'completed',          'label' => 'Completed'],
            ['key' => 'return-refund',      'label' => 'Return/Refund'],
            ['key' => 'cancelled',          'label' => 'Cancelled'],
        ];

        // UI demo only. These are not real transactions or database records.
        $orders = [
            ['order_id' => 'SH-2026-1001', 'shop' => 'Tech Haven', 'product' => 'Wireless Bluetooth Earbuds Pro', 'variant' => 'Black · Bluetooth 5.3', 'price' => 1299, 'quantity' => 1, 'total' => 1299, 'status' => 'processing', 'status_label' => 'Processing', 'art' => 'earbuds', 'estimated_delivery' => 'Oct 28–30, 2026', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1002', 'shop' => 'Mia Boutique', 'product' => 'Everyday Canvas Tote Bag', 'variant' => 'Beige · Medium', 'price' => 459, 'quantity' => 2, 'total' => 918, 'status' => 'to-ship', 'status_label' => 'To Ship', 'art' => 'bag', 'estimated_delivery' => 'Oct 27–29, 2026', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1003', 'shop' => 'Glow Essentials', 'product' => 'Hydrating Skincare Essentials Set', 'variant' => 'Sensitive Skin', 'price' => 899, 'quantity' => 1, 'total' => 899, 'status' => 'in-transit', 'status_label' => 'In Transit', 'art' => 'skincare', 'estimated_delivery' => 'Oct 25–27, 2026', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1004', 'shop' => 'Stride Studio', 'product' => 'Everyday Running Sneakers', 'variant' => 'White · Size 39', 'price' => 1799, 'quantity' => 1, 'total' => 1799, 'status' => 'out-for-delivery', 'status_label' => 'Out for Delivery', 'art' => 'shoes', 'estimated_delivery' => 'Oct 23, 2026', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1005', 'shop' => 'Watch Avenue', 'product' => 'Minimalist Smart Watch', 'variant' => 'Midnight Black', 'price' => 2199, 'quantity' => 1, 'total' => 2199, 'status' => 'delivered', 'status_label' => 'Delivered', 'art' => 'watch', 'estimated_delivery' => 'Delivered Oct 20, 2026', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1006', 'shop' => 'Home & Cozy', 'product' => 'Ceramic Coffee Mug Set', 'variant' => 'Cream · Set of 2', 'price' => 349, 'quantity' => 2, 'total' => 698, 'status' => 'completed', 'status_label' => 'Completed', 'art' => 'other', 'estimated_delivery' => 'Delivered Oct 17, 2026', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1007', 'shop' => 'Beauty Bloom', 'product' => 'Vitamin C Glow Serum', 'variant' => '30 ml', 'price' => 599, 'quantity' => 1, 'total' => 599, 'status' => 'completed', 'status_label' => 'Completed', 'art' => 'skincare', 'estimated_delivery' => 'Delivered Oct 15, 2026', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1008', 'shop' => 'Urban Finds', 'product' => 'Classic Crossbody Bag', 'variant' => 'Brown · Small', 'price' => 799, 'quantity' => 1, 'total' => 799, 'status' => 'return-refund', 'status_label' => 'Return Requested', 'art' => 'bag', 'estimated_delivery' => 'Return under review', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1009', 'shop' => 'Tech Haven', 'product' => 'Portable Wireless Speaker', 'variant' => 'Navy Blue', 'price' => 999, 'quantity' => 1, 'total' => 999, 'status' => 'return-refund', 'status_label' => 'Refund Requested', 'art' => 'other', 'estimated_delivery' => 'Refund under review', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1010', 'shop' => 'Stride Studio', 'product' => 'Classic Canvas Sneakers', 'variant' => 'Black · Size 40', 'price' => 1299, 'quantity' => 1, 'total' => 1299, 'status' => 'cancelled', 'status_label' => 'Cancelled', 'art' => 'shoes', 'estimated_delivery' => '—', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1011', 'shop' => 'Sound Source', 'product' => 'Noise-Cancelling Headphones', 'variant' => 'Silver', 'price' => 2499, 'quantity' => 1, 'total' => 2499, 'status' => 'processing', 'status_label' => 'Processing', 'art' => 'earbuds', 'estimated_delivery' => 'Oct 29–31, 2026', 'additional_products' => 0],
            ['order_id' => 'SH-2026-1012', 'shop' => 'Green Living', 'product' => 'Indoor Plant Pot Set', 'variant' => 'White · Set of 3', 'price' => 549, 'quantity' => 1, 'total' => 549, 'status' => 'delivered', 'status_label' => 'Delivered', 'art' => 'other', 'estimated_delivery' => 'Delivered Oct 19, 2026', 'additional_products' => 0],
        ];
    @endphp

    <main class="purchases-main">
        <div class="purchases-container">
            <div class="purchases-layout">

                @php
                    $requestedSidePanel = request('section');

                    $activeSidePanel = in_array(
                        $requestedSidePanel,
                        ['purchases', 'likes', 'notifications', 'account', 'complaints', 'vouchers', 'privacy-security', 'notification-settings', 'platform-policies'],
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
                        <x-account-avatar
                            :name="$buyerDisplayName ?? auth()->user()->name"
                            :image="$buyerAvatarUrl ?? null"
                            size="56px"
                            class="buyer-profile-avatar"
                            id="buyerProfilePhoto"
                        />

                        <div class="buyer-profile-copy">
                            <strong>{{ $buyerDisplayName ?? auth()->user()->name }}</strong>
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

                        <button type="button" class="buyer-account-nav-link buyer-side-tab {{ $activeSidePanel === 'likes' ? 'is-active' : '' }}" data-side-panel="likes">
                            <span class="buyer-account-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.8 4.8a5.3 5.3 0 0 0-7.5 0L12 6.1l-1.3-1.3a5.3 5.3 0 0 0-7.5 7.5L12 21l8.8-8.7a5.3 5.3 0 0 0 0-7.5Z"/></svg></span>
                            <span>My Likes</span><span id="buyerLikesSidebarCount" class="buyer-likes-sidebar-count" aria-label="Liked products count" hidden></span>
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

                        <button type="button" class="buyer-account-nav-link buyer-side-tab {{ $activeSidePanel === 'complaints' ? 'is-active' : '' }}" data-side-panel="complaints">
                            <span class="buyer-account-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16v12H9l-5 3V5Z"/><path d="M8 9h8M8 13h5"/></svg></span>
                            <span>Complaints</span>
                        </button>
                        <button type="button" class="buyer-account-nav-link buyer-side-tab {{ $activeSidePanel === 'vouchers' ? 'is-active' : '' }}" data-side-panel="vouchers">
                            <span class="buyer-account-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"><path d="M4 6h16v4a2 2 0 0 0 0 4v4H4v-4a2 2 0 0 0 0-4V6Z"/><path d="M12 6v12" stroke-dasharray="2 3"/></svg></span>
                            <span>Vouchers &amp; Promotions</span>
                        </button>
                        <button type="button" id="buyerSettingsToggle" class="buyer-account-nav-link buyer-settings-toggle {{ in_array($activeSidePanel, ['privacy-security', 'notification-settings', 'platform-policies'], true) ? 'is-active' : '' }}" aria-expanded="{{ in_array($activeSidePanel, ['privacy-security', 'notification-settings', 'platform-policies'], true) ? 'true' : 'false' }}" aria-controls="buyerSettingsSubmenu">
                            <span class="buyer-account-nav-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg></span>
                            <span>Settings</span><svg class="buyer-settings-chevron" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m7 10 5 5 5-5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <div id="buyerSettingsSubmenu" class="buyer-settings-submenu" {{ in_array($activeSidePanel, ['privacy-security', 'notification-settings', 'platform-policies'], true) ? '' : 'hidden' }}>
                            <button type="button" class="buyer-account-nav-link buyer-side-tab buyer-settings-item {{ $activeSidePanel === 'privacy-security' ? 'is-active' : '' }}" data-side-panel="privacy-security">Privacy and Security</button>
                            <button type="button" class="buyer-account-nav-link buyer-side-tab buyer-settings-item {{ $activeSidePanel === 'notification-settings' ? 'is-active' : '' }}" data-side-panel="notification-settings">Notifications Settings</button>
                            <button type="button" class="buyer-account-nav-link buyer-side-tab buyer-settings-item {{ $activeSidePanel === 'platform-policies' ? 'is-active' : '' }}" data-side-panel="platform-policies">Platform Policies</button>
                        </div>
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
                                data-purchase-row data-demo-order
                                data-view-order-id="{{ $order['order_id'] }}"
                                role="link" tabindex="0" aria-label="View order {{ $order['order_id'] }}"
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
                                            Sample order · Oct 2026
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

                    {{-- Buyer My Likes: browser-only preview; unified with dashboard hearts. --}}
                    <div class="buyer-side-panel {{ $activeSidePanel === 'likes' ? 'is-active' : '' }}" data-panel-content="likes" {{ $activeSidePanel !== 'likes' ? 'hidden' : '' }}>
                        <section class="purchases-page-heading">
                            <h1>My Likes</h1>
                            <nav class="purchases-breadcrumbs" aria-label="Breadcrumb"><a href="{{ url('/buyer/dashboard') }}">Home</a><span aria-hidden="true">›</span><span aria-current="page">My Likes</span></nav>
                        </section>
                        <section class="buyer-likes-shell" aria-labelledby="buyerLikesHeading">
                            <div class="buyer-likes-heading"><div><h2 id="buyerLikesHeading">Products you love</h2><p>Save products with the heart icon and find them here anytime.</p></div><span id="buyerLikesCount" class="buyer-likes-count">0 products</span></div>
                            <div id="buyerLikesGrid" class="buyer-likes-grid" aria-live="polite"></div>
                            <div id="buyerLikesEmpty" class="buyer-likes-empty" hidden>
                                <span class="buyer-likes-empty-icon" aria-hidden="true">♡</span>
                                <h3>No liked products yet</h3><p>Tap the heart on products you like to save them here.</p>
                                <a href="{{ url('/buyer/dashboard') }}">Explore products →</a>
                            </div>
                            <p class="buyer-likes-footnote">Preview: liked products are saved only in this browser. Connect a buyer favorites API to sync across devices.</p>
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

                            <p id="buyerNotificationsError" role="alert" hidden></p>
                            <div class="buyer-notifications-list" id="buyerNotificationsList" aria-live="polite">

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

                        @php
                            $buyerProfile = $buyerProfile ?? auth()->user()->buyerProfile;
                            $buyerBirthday = old('birthday', data_get($buyerProfile, 'birthday', data_get($buyerProfile, 'birthdate', '')));
                            try { $buyerBirthday = $buyerBirthday ? \Carbon\Carbon::parse($buyerBirthday)->format('Y-m-d') : ''; } catch (\Throwable $e) { $buyerBirthday = ''; }
                            $buyerDisplayName = $buyerDisplayName ?? trim(data_get($buyerProfile, 'first_name', '') . ' ' . data_get($buyerProfile, 'last_name', '')) ?: auth()->user()->name;
                            // Optional URLs from controller; no fake images or records.
                            $buyerCoverUrl = $buyerCoverUrl ?? null;
                            $buyerAvatarUrl = $buyerAvatarUrl ?? null;
                            $buyerValidIdUrl = $buyerValidIdUrl ?? null;
                        @endphp
                        <section class="bp-workspace" aria-label="Buyer profile details">
                            <h2 class="bp-title">Account Details</h2>
                            <p class="bp-intro">Update your personal information, contact details, address, and account photos.</p>
                            <form id="buyerProfileForm" data-update-url="{{ url('/api/v1/buyer/account') }}" novalidate>
                                <div class="bp-cover" id="bpCover">
                                    <img id="bpCoverPreview" src="{{ $buyerCoverUrl ?? '' }}" alt="Buyer cover photo" @if(!$buyerCoverUrl) hidden @endif>
                                    <label class="bp-photo-button bp-cover-action" for="bpCoverInput"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 7h4l2-3h4l2 3h4v13H4z"/><circle cx="12" cy="13" r="4"/></svg> Change Cover</label>
                                    <input id="bpCoverInput" name="cover_photo" type="file" accept="image/png,image/jpeg,image/webp" hidden>
                                </div>
                                <div class="bp-identity">
                                    <div class="bp-avatar" id="bpAvatar">
                                        <img id="bpAvatarPreview" src="{{ $buyerAvatarUrl ?? '' }}" alt="Buyer profile photo" @if(!$buyerAvatarUrl) hidden @endif>
                                        <span id="bpAvatarFallback" @if($buyerAvatarUrl) hidden @endif>{{ strtoupper(collect(preg_split('/\s+/u', trim($buyerDisplayName), -1, PREG_SPLIT_NO_EMPTY) ?: [])->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->take(2)->implode('') ?: 'B') }}</span>
                                        <label class="bp-avatar-action" for="bpAvatarInput" title="Change profile photo" aria-label="Change profile photo"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h4l2-3h4l2 3h4v13H4z"/><circle cx="12" cy="13" r="4"/></svg></label>
                                    </div>
                                    <div class="bp-identity-copy"><h3>{{ $buyerDisplayName }}</h3><p>Buyer Profile</p><label class="bp-photo-button" for="bpAvatarInput">Change Profile Photo</label><input id="bpAvatarInput" name="profile_photo" type="file" accept="image/png,image/jpeg,image/webp" hidden></div>
                                </div>
                                <section class="bp-section"><div class="bp-section-heading"><span class="bp-section-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 21c.5-5 3.4-7 8-7s7.5 2 8 7"/></svg></span><div><h3>Personal Information</h3><p>Your registered personal and contact details</p></div></div>
                                    <div class="bp-fields"><div class="bp-field"><label for="bp-last-name">Last Name <span class="bp-required">*</span></label><input class="bp-control" id="bp-last-name" name="last_name" type="text" value="{{ old('last_name', data_get($buyerProfile, 'last_name', '')) }}" placeholder="Enter your last name" required autocomplete="family-name"></div><div class="bp-field"><label for="bp-first-name">First Name <span class="bp-required">*</span></label><input class="bp-control" id="bp-first-name" name="first_name" type="text" value="{{ old('first_name', data_get($buyerProfile, 'first_name', '')) }}" placeholder="Enter your first name" required autocomplete="given-name"></div><div class="bp-field"><label for="bp-middle-name">Middle Name</label><input class="bp-control" id="bp-middle-name" name="middle_name" type="text" value="{{ old('middle_name', data_get($buyerProfile, 'middle_name', data_get($buyerProfile, 'middle_initial', ''))) }}" placeholder="Enter your middle name" autocomplete="off"></div><div class="bp-field"><label for="bp-sex">Sex <span class="bp-required">*</span></label><select class="bp-control" id="bp-sex" name="sex" required><option value="">Select sex</option><option value="Male" @selected(old('sex', data_get($buyerProfile, 'sex', '')) === 'Male')>Male</option><option value="Female" @selected(old('sex', data_get($buyerProfile, 'sex', '')) === 'Female')>Female</option><option value="Prefer not to say" @selected(old('sex', data_get($buyerProfile, 'sex', '')) === 'Prefer not to say')>Prefer not to say</option></select></div><div class="bp-field"><label for="bp-email">Email <span class="bp-required">*</span></label><input class="bp-control" id="bp-email" name="email" type="email" value="{{ old('email', $buyerEmail ?? auth()->user()->email) }}" placeholder="Enter your email" required autocomplete="email"></div><div class="bp-field"><label for="bp-contact-no">Contact No. <span class="bp-required">*</span></label><input class="bp-control" id="bp-contact-no" name="contact_no" type="tel" value="{{ old('contact_no', data_get($buyerProfile, 'contact_no', data_get($buyerProfile, 'phone', ''))) }}" placeholder="09XXXXXXXXX or +639XXXXXXXXX" required autocomplete="tel"></div><div class="bp-field"><label for="bp-birthday">Birthday <span class="bp-required">*</span></label><input class="bp-control" id="bp-birthday" name="birthday" type="date" value="{{ old('birthday', $buyerBirthday) }}" placeholder="" required max="{{ now()->toDateString() }}" autocomplete="off"></div><div class="bp-field"><label for="bp-age">Age</label><input class="bp-control" id="bp-age" type="text" value="{{ data_get($buyerProfile, 'age', '') }}" placeholder="Auto generated" readonly aria-readonly="true"></div></div>
                                </section>
                                <section class="bp-section"><div class="bp-section-heading"><span class="bp-section-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 10 12 3l8 7v11H4Z"/><path d="M9 21v-8h6v8"/></svg></span><div><h3>Address Information</h3><p>Your registered address details</p></div></div>
                                    <div class="bp-fields bp-fields-address"><div class="bp-field"><label for="bp-province">Province <span class="bp-required">*</span></label><div class="bp-location-picker" data-bp-location="province">
    <input class="bp-control bp-location-search" id="bp-province" type="text" value="{{ old('province', data_get($buyerProfile, 'province', '')) }}" placeholder="Type to search province" required autocomplete="off" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-label="Search province" >
    <input type="hidden" id="bp-province-selected" name="province" value="{{ old('province', data_get($buyerProfile, 'province', '')) }}">
    <span class="bp-location-chevron" aria-hidden="true"></span>
    <div class="bp-location-options" role="listbox"></div>
</div></div>
<div class="bp-field"><label for="bp-municipality">Municipality <span class="bp-required">*</span></label><div class="bp-location-picker" data-bp-location="municipality">
    <input class="bp-control bp-location-search" id="bp-municipality" type="text" value="{{ old('municipality', data_get($buyerProfile, 'municipality', '')) }}" placeholder="Select province first" required autocomplete="off" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-label="Search municipality" disabled>
    <input type="hidden" id="bp-municipality-selected" name="municipality" value="{{ old('municipality', data_get($buyerProfile, 'municipality', '')) }}">
    <span class="bp-location-chevron" aria-hidden="true"></span>
    <div class="bp-location-options" role="listbox"></div>
</div></div>
<div class="bp-field"><label for="bp-barangay">Barangay <span class="bp-required">*</span></label><div class="bp-location-picker" data-bp-location="barangay">
    <input class="bp-control bp-location-search" id="bp-barangay" type="text" value="{{ old('barangay', data_get($buyerProfile, 'barangay', '')) }}" placeholder="Select municipality first" required autocomplete="off" role="combobox" aria-expanded="false" aria-autocomplete="list" aria-label="Search barangay" disabled>
    <input type="hidden" id="bp-barangay-selected" name="barangay" value="{{ old('barangay', data_get($buyerProfile, 'barangay', '')) }}">
    <span class="bp-location-chevron" aria-hidden="true"></span>
    <div class="bp-location-options" role="listbox"></div>
</div></div>
<div class="bp-field"><label for="bp-zip-code">Zip Code <span class="bp-required">*</span></label><input class="bp-control" id="bp-zip-code" name="zip_code" type="text" value="{{ old('zip_code', data_get($buyerProfile, 'zip_code', '')) }}" placeholder="Enter zip code" required autocomplete="off"></div>
<div class="bp-field"><label for="bp-street">Street</label><input class="bp-control" id="bp-street" name="street" type="text" value="{{ old('street', data_get($buyerProfile, 'street', '')) }}" placeholder="Enter street" autocomplete="off"></div>
<div class="bp-field"><label for="bp-house-number">House No.</label><input class="bp-control" id="bp-house-number" name="house_number" type="text" value="{{ old('house_number', data_get($buyerProfile, 'house_number', '')) }}" placeholder="Enter house no." autocomplete="off"></div>
<div class="bp-field"><label for="bp-building">Building</label><input class="bp-control" id="bp-building" name="building" type="text" value="{{ old('building', data_get($buyerProfile, 'building', '')) }}" placeholder="Enter building" autocomplete="off"></div>
<div class="bp-field"><label for="bp-subdivision">Subdivision</label><input class="bp-control" id="bp-subdivision" name="subdivision" type="text" value="{{ old('subdivision', data_get($buyerProfile, 'subdivision', '')) }}" placeholder="Enter subdivision" autocomplete="off"></div></div>
                                </section>
                                <section class="bp-section"><div class="bp-section-heading"><span class="bp-section-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M7 10h5M7 14h10"/></svg></span><div><h3>Identity Verification</h3><p>Approved Valid ID is view-only and cannot be edited here.</p></div></div>
                                    <div class="bp-id-grid"><div class="bp-id-tile"><span class="bp-id-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="4" width="18" height="16" rx="2"/><circle cx="9" cy="10" r="2"/><path d="m5 18 5-5 3 3 3-4 3 6"/></svg></span><strong>Valid ID</strong><small>Approved document · Read only</small>@if($buyerValidIdUrl)<a href="{{ $buyerValidIdUrl }}" target="_blank" rel="noopener noreferrer" class="bp-id-view">View approved ID</a>@else<span class="bp-id-no-document">No approved ID available</span>@endif</div><p class="bp-id-note">For security, you cannot replace or modify your Valid ID from this form. Contact support for identity document concerns.</p></div>
                                </section>
                                <div class="bp-actions"><button type="button" class="bp-reset" id="bpDiscard" disabled>Discard Changes</button><button type="submit" class="bp-save" id="bpRequestChanges" disabled>Save Changes</button></div>
                            </form>
                        </section>
                    </div>

                    <div class="buyer-side-panel {{ $activeSidePanel === 'complaints' ? 'is-active' : '' }}" data-panel-content="complaints" {{ $activeSidePanel !== 'complaints' ? 'hidden' : '' }}">
                        <section class="purchases-page-heading"><h1>Complaints</h1><nav class="purchases-breadcrumbs" aria-label="Breadcrumb"><a href="{{ url('/buyer/dashboard') }}">Home</a><span aria-hidden="true">›</span><span aria-current="page">Complaints</span></nav></section>
                        
                        <div class="seller-performance-page buyer-complaints-clone">
            <section class="sp-complaints" aria-label="Complaints and disputes">
              <div class="sp-section-heading sp-complaint-heading"><div><h2>Complaints &amp; Disputes</h2><p>Track complaints submitted about your orders</p></div></div>
              <div class="complaints-filter-card"><div class="complaints-filter-row">
                <div class="complaints-search-wrap"><input id="complaints-search" class="complaints-search" type="search" placeholder="Search ID, name" aria-label="Search complaints"><svg class="complaints-search-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5"/></svg></div>
                <div class="complaints-date-wrap"><input id="complaints-date-filter" class="complaints-date-input" type="date" aria-label="Complaint date"><button id="complaints-date-button" type="button" class="complaints-date-button" aria-label="Open calendar"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Z"/></svg></button></div>
                <button id="complaints-reload" type="button" class="complaints-reload" title="Reset filters" aria-label="Reset filters"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"/></svg></button>
              </div></div>
              <div id="complaints-workspace" class="complaints-workspace">
                <section class="complaints-table-card"><div class="complaints-tabs" role="tablist" aria-label="Complaint status">
                    <button class="complaints-tab active" type="button" data-tab="all" role="tab" aria-selected="true">All Complaints</button>
                    <button class="complaints-tab" type="button" data-tab="open" role="tab" aria-selected="false">Open</button>
                    <button class="complaints-tab" type="button" data-tab="in-progress" role="tab" aria-selected="false">In progress</button>
                    <button class="complaints-tab" type="button" data-tab="resolved" role="tab" aria-selected="false">Resolved</button>
                </div><div class="complaints-table-scroll"><table class="complaints-table"><thead><tr><th>Complaint ID</th><th>Parties</th><th>Type</th><th>Status</th><th>Date Filed</th></tr></thead><tbody id="complaints-table-body"></tbody></table><div id="complaints-empty" class="complaints-empty">No complaints match the selected filters.</div></div>
                <div class="complaints-table-footer"><p id="complaints-count" class="complaints-count">Showing 0 out of 0 entries</p><div id="complaints-pagination" class="complaints-pagination"><button type="button" id="complaints-prev" class="complaints-page-button" aria-label="Previous page">‹</button><div id="complaints-page-numbers" class="complaints-page-numbers"></div><button type="button" id="complaints-next" class="complaints-page-button" aria-label="Next page">›</button><select id="complaints-items" class="complaints-items" aria-label="Items per page"><option value="7">Items per page: 7</option><option value="10">Items per page: 10</option><option value="20">Items per page: 20</option></select></div></div></section>
                <aside id="complaints-detail-card" class="complaints-detail-card" aria-hidden="true">
                    <div class="complaints-detail-header"><div><div class="complaints-detail-eyebrow">Complaint ID</div><div id="detail-id" class="complaints-detail-id">—</div></div><div class="complaints-detail-header-right"><span id="detail-status" class="complaint-status open">Open</span><button id="complaints-detail-close" class="complaints-detail-close" aria-label="Close preview" type="button">×</button></div></div>
                    <div class="complaints-detail-body"><div class="complaints-detail-section"><h4 class="complaints-detail-label">Date Filed</h4><p id="detail-date" class="complaints-detail-text">—</p></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Complaint Type</h4><p id="detail-type" class="complaints-detail-text">—</p></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Description</h4><p id="detail-description" class="complaints-detail-text">—</p></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Parties Involved</h4><div id="detail-parties" class="complaints-parties-list"></div></div><div class="complaints-detail-section"><h4 class="complaints-detail-label">Supporting Evidence</h4><div id="detail-evidence" class="complaints-evidence"></div></div></div>
                    <div class="complaints-detail-actions"><button id="complaints-view-details" type="button" class="complaints-view-details">View Details</button></div>
                </aside>
              </div>
            </section>
</div>
                    </div>

                    <div class="buyer-side-panel {{ $activeSidePanel === 'vouchers' ? 'is-active' : '' }}" data-panel-content="vouchers" {{ $activeSidePanel !== 'vouchers' ? 'hidden' : '' }}">
                        <section class="purchases-page-heading"><h1>Vouchers & Promotions</h1><nav class="purchases-breadcrumbs" aria-label="Breadcrumb"><a href="{{ url('/buyer/dashboard') }}">Home</a><span aria-hidden="true">›</span><span aria-current="page">Vouchers & Promotions</span></nav></section>
                        <section class="bv-vouchers" aria-labelledby="bv-title">
                            <div class="bv-section-head"><div><h2 id="bv-title">Available vouchers</h2><p>Explore discounts for your next order. Tap a voucher to review all terms before checkout.</p></div><span class="bv-count" id="bvVoucherCount">0 vouchers</span></div>
                            <div class="bv-voucher-grid" id="bvVoucherGrid" aria-live="polite"></div>
                            <div class="bv-voucher-note"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.7"/><path d="M12 11v5m0-8h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg><p><strong>Preview vouchers:</strong> Sample offers for UI testing only. Voucher availability, discounts, redemption and COD eligibility are not connected to checkout yet.</p></div>
                        </section>
                    </div>

                    <div class="buyer-side-panel {{ $activeSidePanel === 'privacy-security' ? 'is-active' : '' }}" data-panel-content="privacy-security" {{ $activeSidePanel !== 'privacy-security' ? 'hidden' : '' }}">
                        <section class="purchases-page-heading"><h1>Privacy and Security</h1><nav class="purchases-breadcrumbs" aria-label="Breadcrumb"><a href="{{ url('/buyer/dashboard') }}">Home</a><span aria-hidden="true">›</span><span aria-current="page">Privacy and Security</span></nav></section>
                        <section class="buyer-security-settings" id="buyerSecuritySettings">
<div class="sa-settings-card"><div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/password.png') }}" alt=""></span><div><h2>Change Password</h2><p>Use your current password and verify your registered email before saving.</p></div></div>
<form id="buyerChangePasswordForm" autocomplete="off"><div class="sa-settings-form"><div class="sa-full"><label for="buyerCurrentPassword">Current Password <span class="sa-required">*</span></label><input class="sa-control" type="password" id="buyerCurrentPassword" autocomplete="current-password" required></div><div><label for="buyerNewPassword">New Password <span class="sa-required">*</span></label><input class="sa-control" type="password" id="buyerNewPassword" autocomplete="new-password" minlength="8" required><small>At least 8 characters. Use a strong and unique password.</small></div><div><label for="buyerConfirmPassword">Confirm New Password <span class="sa-required">*</span></label><input class="sa-control" type="password" id="buyerConfirmPassword" autocomplete="new-password" minlength="8" required></div></div><div class="sa-settings-actions"><button id="buyerChangePasswordButton" class="sa-settings-primary" type="submit" disabled>Continue to Email Verification</button></div></form></div>
<section class="sa-settings-card sa-account-recovery"><div><h2>Forgot your password?</h2><p>Verify the email associated with your buyer account, then create a new password. Your current password will not be required.</p></div><button id="buyerForgotPasswordButton" type="button" class="sa-settings-outline">Reset Password</button></section>
</section>
                    </div>

                    <div class="buyer-side-panel {{ $activeSidePanel === 'notification-settings' ? 'is-active' : '' }}" data-panel-content="notification-settings" {{ $activeSidePanel !== 'notification-settings' ? 'hidden' : '' }}">
                        <section class="purchases-page-heading"><h1>Notifications Settings</h1><nav class="purchases-breadcrumbs" aria-label="Breadcrumb"><a href="{{ url('/buyer/dashboard') }}">Home</a><span aria-hidden="true">›</span><span aria-current="page">Notifications Settings</span></nav></section>
                        <form id="buyerNotificationSettingsForm" class="buyer-settings-form"><section class="sa-settings-card"><div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/notification.png') }}" alt=""></span><div><h2>Orders & Shopping Activity</h2><p>Control notifications for your purchases and account.</p></div></div><label class="sa-toggle-row"><span><strong>Order updates</strong><small>Processing, shipping and delivery updates</small></span><span class="sa-switch"><input type="checkbox" name="order_updates" checked><span class="sa-switch-track"></span></span></label><label class="sa-toggle-row"><span><strong>Delivery reminders</strong><small>Out-for-delivery and delivery attempt notices</small></span><span class="sa-switch"><input type="checkbox" name="delivery_alerts" checked><span class="sa-switch-track"></span></span></label><label class="sa-toggle-row"><span><strong>Returns & refunds</strong><small>Updates to returns, refund requests and resolutions</small></span><span class="sa-switch"><input type="checkbox" name="returns_refunds" checked><span class="sa-switch-track"></span></span></label><label class="sa-toggle-row"><span><strong>Complaints & disputes</strong><small>Case status and admin support updates</small></span><span class="sa-switch"><input type="checkbox" name="complaints" checked><span class="sa-switch-track"></span></span></label><label class="sa-toggle-row"><span><strong>Messages</strong><small>New seller and ShopEase admin messages</small></span><span class="sa-switch"><input type="checkbox" name="messages" checked><span class="sa-switch-track"></span></span></label></section><section class="sa-settings-card"><div class="sa-settings-top"><span class="sa-settings-emblem"><img src="{{ asset('icons/seller/account/notification.png') }}" alt=""></span><div><h2>Communication Preferences</h2><p>Choose which optional updates you receive.</p></div></div><label class="sa-toggle-row"><span><strong>Email notifications</strong><small>Get important account alerts by email</small></span><span class="sa-switch"><input type="checkbox" name="email_alerts" checked><span class="sa-switch-track"></span></span></label><label class="sa-toggle-row"><span><strong>Vouchers & promotions</strong><small>New available COD discounts and offers</small></span><span class="sa-switch"><input type="checkbox" name="vouchers" checked><span class="sa-switch-track"></span></span></label><label class="sa-toggle-row"><span><strong>Platform announcements</strong><small>System maintenance and buyer feature updates</small></span><span class="sa-switch"><input type="checkbox" name="announcements" checked><span class="sa-switch-track"></span></span></label><label class="sa-toggle-row"><span><strong>Product recommendations</strong><small>Personalized shopping suggestions</small></span><span class="sa-switch"><input type="checkbox" name="recommendations" ><span class="sa-switch-track"></span></span></label></section><div class="sa-settings-actions"><button type="submit" id="buyerSaveNotificationSettings" class="sa-settings-primary" disabled>Save Preferences</button></div></form>
                    </div>

                    <div class="buyer-side-panel {{ $activeSidePanel === 'platform-policies' ? 'is-active' : '' }}" data-panel-content="platform-policies" {{ $activeSidePanel !== 'platform-policies' ? 'hidden' : '' }}">
                        <section class="purchases-page-heading"><h1>Platform Policies</h1><nav class="purchases-breadcrumbs" aria-label="Breadcrumb"><a href="{{ url('/buyer/dashboard') }}">Home</a><span aria-hidden="true">›</span><span aria-current="page">Platform Policies</span></nav></section>
                        <article class="sa-policy-document"><header class="sa-policy-header"><div class="sa-policy-stamp">ShopEase · BUYER DOCUMENT</div><h2>Buyer Platform Policies</h2><p>Reference guide for buyer account holders</p><div class="sa-policy-rule"></div><p class="sa-policy-note"><strong>Draft / demonstration:</strong> This text is placeholder guidance for UI review, not the official ShopEase terms. Replace with reviewed platform policies before publishing.</p></header><nav class="sa-policy-index" aria-label="Policy contents"><strong>Contents</strong><a href="#buyerPolicy1">01. Buyer responsibilities</a><a href="#buyerPolicy2">02. Ordering & Cash on Delivery</a><a href="#buyerPolicy3">03. Order tracking & delivery</a><a href="#buyerPolicy4">04. Cancellations, returns & refunds</a><a href="#buyerPolicy5">05. Vouchers & promotions</a><a href="#buyerPolicy6">06. Complaints & customer support</a><a href="#buyerPolicy7">07. Privacy & security</a></nav><section id="buyerPolicy1"><h3>01. Buyer responsibilities</h3><p>Use accurate personal and delivery information. Keep your account credentials secure and follow applicable platform rules.</p></section><section id="buyerPolicy2"><h3>02. Ordering & Cash on Delivery</h3><p>ShopEase currently supports Cash on Delivery (COD) only. Review total costs, voucher eligibility, order details and shipping information before placing an order.</p></section><section id="buyerPolicy3"><h3>03. Order tracking & delivery</h3><p>Review your order status and make yourself available to receive parcels. Follow courier and seller instructions when delivery attempts occur.</p></section><section id="buyerPolicy4"><h3>04. Cancellations, returns & refunds</h3><p>Submit requests through the available order channels and provide truthful reasons and supporting evidence when required. Outcomes depend on applicable platform rules.</p></section><section id="buyerPolicy5"><h3>05. Vouchers & promotions</h3><p>Discounts may have expiration dates, minimum spends, category restrictions, redemption limits and COD eligibility requirements. Terms shown on a voucher apply.</p></section><section id="buyerPolicy6"><h3>06. Complaints & customer support</h3><p>Use complaint and ShopEase Admin messaging tools responsibly. Keep supporting evidence accurate and communicate respectfully.</p></section><section id="buyerPolicy7"><h3>07. Privacy & security</h3><p>Protect account details, avoid sharing verification codes and use authorized ShopEase channels for account recovery and support.</p></section><footer class="sa-policy-footer">ShopEase Buyer Guidelines <span>End of draft document</span></footer></article>
                    </div>
                </section>
            </div>
        </div>
    </main>

    {{-- Profile change approval notice — UI-only; no request is sent to admin. --}}
    <div id="bpProfileToast" class="bp-toast" role="status" aria-live="polite" hidden></div>

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

    <div id="buyerOrderCancellationModal" class="fixed inset-0 z-[220] hidden items-center justify-center bg-black/40 px-4 py-6" aria-hidden="true">
        <section class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl" role="dialog" aria-modal="true" aria-labelledby="buyerOrderCancellationTitle">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 id="buyerOrderCancellationTitle" class="text-xl font-bold text-gray-900">Cancel order</h2>
                    <p id="buyerOrderCancellationLabel" class="mt-1 text-sm text-gray-500"></p>
                </div>
                <button type="button" id="buyerOrderCancellationClose" class="rounded-full p-2 text-gray-500 hover:bg-gray-100" aria-label="Close cancellation form">×</button>
            </div>
            <div class="mt-5 space-y-4">
                <label class="block text-sm font-medium text-gray-700" for="buyerOrderCancellationReason">
                    Why are you cancelling?
                    <select id="buyerOrderCancellationReason" required class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">Select a reason</option>
                        <option value="changed_mind">I changed my mind</option>
                        <option value="ordered_by_mistake">I ordered by mistake</option>
                        <option value="wrong_address">The delivery address is incorrect</option>
                        <option value="delivery_too_slow">Delivery is taking too long</option>
                        <option value="payment_issue">I have a payment issue</option>
                        <option value="other">Other</option>
                    </select>
                </label>
                <label id="buyerOrderCancellationOtherWrap" class="block text-sm font-medium text-gray-700" for="buyerOrderCancellationOther" hidden>
                    Please specify
                    <textarea id="buyerOrderCancellationOther" rows="3" maxlength="500" class="mt-1 block w-full resize-y rounded-lg border border-gray-300 px-3 py-2" placeholder="Enter your reason"></textarea>
                </label>
                <p id="buyerOrderCancellationError" class="text-sm text-red-600" role="alert" hidden></p>
                <div class="flex justify-end gap-3">
                    <button type="button" id="buyerOrderCancellationKeep" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">Keep order</button>
                    <button type="button" id="buyerOrderCancellationSubmit" class="rounded-lg bg-[#7B1B1B] px-4 py-2 text-sm font-semibold text-white disabled:opacity-60">Submit cancellation</button>
                </div>
            </div>
        </section>
    </div>

    {{-- Shared Buyer Footer --}}
    @include('components.buyer.footer')

    {{-- Shared Buyer Floating Chat --}}
    @include('components.buyer.floating-chat')


    {{-- Buyer Complaints demo: seller performance matched modal + evidence viewer. --}}
    <div id="complaints-details-modal" class="sp-admin-modal-overlay" aria-hidden="true" hidden>
      <div class="complaints-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="complaints-modal-title" tabindex="-1">
        <div class="sp-modal-title-row"><h3 id="complaints-modal-title" class="complaints-modal-title">Complaint Details</h3><button id="complaints-modal-close" type="button" aria-label="Close complaint details" class="sp-modal-x">×</button></div>
        <div class="complaints-modal-grid complaints-modal-top-grid"><section class="complaints-modal-card complaints-modal-summary"><div class="complaints-modal-summary-main"><div class="complaints-modal-id-block"><span class="complaints-modal-label">Complaint ID</span><strong id="modal-complaint-id" class="complaints-modal-id">—</strong></div><span id="modal-complaint-status" class="complaints-modal-status open">—</span></div><div class="complaints-modal-meta"><div class="complaints-modal-meta-item"><span>Date Filed</span><strong id="modal-date-filed">—</strong></div><div class="complaints-modal-meta-item"><span>Last Updated</span><strong id="modal-last-updated">—</strong></div></div></section><section class="complaints-modal-card complaints-modal-type-order"><div><span class="complaints-modal-label">Complaint Type</span><div class="complaints-modal-type-value"><span id="modal-complaint-type">—</span></div></div><div><span class="complaints-modal-label">Order ID</span><strong id="modal-order-id-top" class="complaints-modal-order-id">—</strong></div></section></div>
        <div class="complaints-modal-grid"><section class="complaints-modal-card complaints-modal-description-card"><h4 class="complaints-modal-card-title">Description</h4><p id="modal-description-copy" class="complaints-modal-description">—</p></section><section class="complaints-modal-card complaints-modal-parties-card"><h4 class="complaints-modal-card-title">Parties Involved</h4><div id="modal-parties-list" class="complaints-modal-party-list"></div></section></div>
        <div class="complaints-modal-grid complaints-modal-middle-grid"><section class="complaints-modal-card complaints-modal-order-card"><h4 class="complaints-modal-card-title">Order Information</h4><div id="modal-order-information" class="complaints-modal-order-list"></div></section><section class="complaints-modal-card complaints-modal-item-card"><h4 class="complaints-modal-card-title">Ordered Item</h4><div id="modal-product-info"></div></section></div>
        <div class="complaints-modal-grid complaints-modal-bottom-grid"><section class="complaints-modal-card complaints-modal-evidence-card"><h4 class="complaints-modal-card-title">Supporting Evidence</h4><div id="modal-evidence-list" class="complaints-modal-evidence-list"></div></section><section class="complaints-modal-card sp-contact-admin-card" aria-labelledby="sp-contact-admin-heading">
                    <div class="sp-contact-admin-copy">
                        <h4 class="complaints-modal-card-title" id="sp-contact-admin-heading">Need help with this complaint?</h4>
                        <p>Contact ShopEase support regarding this complaint.</p>
                    </div>
                    <button id="sp-contact-admin" class="sp-contact-admin-button" type="button">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 8.7 3.9 8.4 8.4 0 0 1 12.5 3h.5a8.5 8.5 0 0 1 8 8v.5Z"/></svg>
                        Contact Support
                    </button>
                </section></div>
        <div class="complaints-modal-footer-grid"><section class="complaints-modal-card complaints-modal-notes-card"><h4 class="complaints-modal-card-title">Status Updates</h4><div id="complaints-modal-notes-list" class="complaints-modal-notes-list"></div></section><div class="sp-modal-bottom-actions"><p>Status updates are managed by ShopEase administration.</p><button id="complaints-modal-done" type="button" class="sp-modal-done">Close</button></div></div>
      </div>
    </div>


    <div id="sp-evidence-viewer" class="sp-evidence-viewer" aria-hidden="true" hidden>
        <div class="sp-evidence-viewer-dialog" role="dialog" aria-modal="true" aria-label="Supporting evidence preview">
            <button id="sp-evidence-close" class="sp-evidence-viewer-close" type="button" aria-label="Close evidence preview">&times;</button>
            <img id="sp-evidence-image" alt="Supporting evidence">
            <p id="sp-evidence-caption">Supporting evidence</p>
        </div>
    </div>


    {{-- Buyer Voucher details: UI-only sample offers; no backend redemption. --}}
    <div id="bvVoucherOverlay" class="bv-overlay" hidden aria-hidden="true">
        <section class="bv-dialog" role="dialog" aria-modal="true" aria-labelledby="bvModalTitle" aria-describedby="bvModalSubtitle" tabindex="-1">
            <div class="bv-dialog-head"><div class="bv-modal-kicker">VOUCHER DETAILS</div><button type="button" id="bvModalClose" class="bv-modal-close" aria-label="Close voucher details"><svg viewBox="0 0 24 24" fill="none"><path d="M6 6 18 18M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg></button></div>
            <div class="bv-modal-hero"><span class="bv-modal-symbol" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M4 6h16v4a2 2 0 0 0 0 4v4H4v-4a2 2 0 0 0 0-4V6Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M12 6v12" stroke="currentColor" stroke-width="1.7" stroke-dasharray="2 3"/></svg></span><div><strong id="bvModalValue">—</strong><p id="bvModalSubtitle">—</p></div></div>
            <div class="bv-modal-body">
                <h2 id="bvModalTitle">Voucher details</h2>
                <p id="bvModalDescription" class="bv-modal-description"></p>
                <div class="bv-modal-details" id="bvModalDetails"></div>
                <div class="bv-modal-terms"><h3>Terms &amp; conditions</h3><ul id="bvModalTerms"></ul></div>
                <div class="bv-cod-note"><svg viewBox="0 0 24 24" fill="none"><path d="M3 7h11v10H3V7Zm11 3h4l3 3v4h-7v-7Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="7" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/><circle cx="18" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/></svg><div><strong>Cash on Delivery (COD) only</strong><span>ShopEase currently accepts COD only. No card, e-wallet or bank payment is available.</span></div></div>
            </div>
            <div class="bv-modal-footer"><button type="button" id="bvModalDone">Okay, Got it</button></div>
        </section>
    </div>

    {{-- Password email verification --}}
    <div class="buyer-security-modal-overlay" id="buyerSecurityOtpModal" hidden>
      <section class="email-verification-card" role="dialog" aria-modal="true" aria-labelledby="buyerSecurityOtpTitle" style="position:relative">
        <button type="button" class="sa-modal-close" id="buyerCloseOtp" aria-label="Close">×</button>
        <div class="email-verification-icon-wrapper"><img src="{{ asset('icons/login/email-verify.png') }}" alt="Email verification" class="email-verification-icon"></div>
        <h2 class="email-verification-title" id="buyerSecurityOtpTitle">Verify Your Email</h2>
        <p class="email-verification-subtitle">Email verification for<br><strong>{{ auth()->user()->email }}</strong></p>
        <div class="email-code-container" id="buyerOtpDigits" aria-label="Six digit email verification code">
           @for($i=1;$i<=6;$i++)
             <input type="text" class="email-code-input buyer-otp-digit" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" aria-label="Digit {{ $i }}">
           @endfor
        </div>
        <p class="buyer-security-demo-disclaimer" id="buyerOtpDeliveryMessage" role="status" hidden></p><div class="email-spam-message">
          <svg class="email-spam-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><circle cx="12" cy="16.5" r=".5" fill="currentColor"/></svg>
          <span>Didn’t receive the code? Check your spam folder or resend it</span>
        </div>
        <div class="email-divider"><div class="email-divider-line"></div><span class="email-divider-text">or</span><div class="email-divider-line"></div></div>
        <button type="button" class="email-resend-button" id="buyerResendOtp">
          <svg class="email-resend-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 11a8 8 0 1 0 2 5"/><path d="M20 5v6h-6"/></svg><span>Resend Code</span>
        </button>
        <p class="sa-security-message" id="buyerOtpError" role="alert" hidden></p>
        <button type="button" class="email-verify-button" id="buyerVerifyOtp">Verify Email</button>
      </section>
    </div>
    <div class="buyer-security-modal-overlay" id="buyerForgotPasswordModal" hidden>
      <section class="sa-modal-dialog sa-security-dialog" role="dialog" aria-modal="true" aria-labelledby="buyerForgotTitle">
        <button type="button" class="sa-modal-close" id="buyerCloseForgot" aria-label="Close">×</button>
        <h2 id="buyerForgotTitle">Create a New Password</h2>
        <p class="sa-modal-description">Enter a new password after verifying your registered email.</p>
        <form id="buyerForgotFinalForm"><div class="sa-settings-form single"><div><label for="buyerResetNew">New Password</label><input id="buyerResetNew" type="password" autocomplete="new-password" class="sa-control" minlength="8" required></div><div><label for="buyerResetConfirm">Confirm New Password</label><input id="buyerResetConfirm" type="password" autocomplete="new-password" class="sa-control" minlength="8" required></div></div>
          <p class="sa-security-message" id="buyerResetError" role="alert" hidden></p><button class="sa-settings-primary sa-otp-verify" type="submit">Reset Password</button></form>
      </section>
    </div>

<div id="buyerSettingsToast" class="buyer-settings-toast" role="status" aria-live="polite" hidden></div>
</body>
</html>
