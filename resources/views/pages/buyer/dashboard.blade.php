{{-- =========================================================
     ShopEase Buyer Dashboard
     Figma-matched desktop layout
     Updated:
     - Slightly narrower desktop content width
     - Category PNGs enlarged and background removed at runtime
     - Purchase icons enlarged with no circular background
     - Chat icons/avatar backgrounds removed
     - Six custom inline product visuals
     - Uses available PNGs from public/icons/admin/buyer/
========================================================= --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Buyer Dashboard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite([
        'resources/css/app.css',
        'resources/css/buyer/pages/dashboard.css',
        'resources/js/buyer/pages/dashboard.js',
    ])
</head>

<body class="buyer-page">

    @include('components.buyer.navbar')

    @php
        $buyerIconPath = 'icons/buyer/';

        $categories = [
            ['name' => 'Pet Supplies',                  'icon' => 'pets.png'],
            ['name' => 'Electronics & Gadgets',        'icon' => 'gadgets.png'],
            ['name' => "Women's Apparel",             'icon' => 'women.png'],
            ['name' => "Men's Apparel",               'icon' => 'men.png'],
            ['name' => 'Kids & Baby',                 'icon' => 'kids.png'],
            ['name' => 'Home & Garden',               'icon' => 'garden.png'],
            ['name' => 'Sports & Outdoors',           'icon' => 'sports.png'],
            ['name' => 'Health & Beauty',             'icon' => 'health.png'],
            ['name' => 'Books & Media',               'icon' => 'books.png'],
            ['name' => 'Food & Gourmet',              'icon' => 'gourmet.png'],
            ['name' => 'Automotive & Motorcycle',     'icon' => 'automotive.png'],
            ['name' => 'Furniture & Office Equipment','icon' => 'furniture.png'],
            ['name' => 'Jewelry & Watches',           'icon' => 'jewelry.png'],
            ['name' => 'Office & School Supplies',    'icon' => 'school.png'],
        ];

        /*
         | Six temporary product visuals are drawn as inline SVGs below.
         | This keeps the dashboard self-contained while actual product
         | photography is not yet available in the supplied buyer folder.
         */
        $recommendedProducts = [
            ['name' => 'Wireless Earbuds Pro',           'price' => '₱1,499.00', 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'earbuds'],
            ['name' => 'Smart Watch Fitness Tracker',    'price' => '₱2,199.00', 'rating' => '4.7', 'sold' => '856 sold',  'art' => 'watch'],
            ['name' => 'Canvas Shoulder Bag',            'price' => '₱899.00',   'rating' => '4.6', 'sold' => '642 sold',  'art' => 'bag'],
            ['name' => 'Running Shoes for Men',          'price' => '₱1,899.00', 'rating' => '4.7', 'sold' => '980 sold',  'art' => 'shoes'],
            ['name' => 'Skincare Set',                   'price' => '₱1,299.00', 'rating' => '4.9', 'sold' => '1.4k sold', 'art' => 'skincare'],
            ['name' => 'Non-Stick Cookware Set',         'price' => '₱1,299.00', 'rating' => '4.7', 'sold' => '730 sold',  'art' => 'cookware'],

            ['name' => 'Wireless Earbuds Air',           'price' => '₱1,199.00', 'rating' => '4.8', 'sold' => '2.3k sold', 'art' => 'earbuds'],
            ['name' => 'Smart Watch Series 8',           'price' => '₱2,799.00', 'rating' => '4.8', 'sold' => '1.1k sold', 'art' => 'watch'],
            ['name' => 'Classic Mini Shoulder Bag',      'price' => '₱749.00',   'rating' => '4.7', 'sold' => '923 sold',  'art' => 'bag'],
            ['name' => 'Casual Sneakers',                'price' => '₱1,599.00', 'rating' => '4.8', 'sold' => '1.7k sold', 'art' => 'shoes'],
            ['name' => 'Daily Glow Skincare Kit',        'price' => '₱1,099.00', 'rating' => '4.8', 'sold' => '2.0k sold', 'art' => 'skincare'],
            ['name' => 'Granite Frying Pan Set',         'price' => '₱999.00',   'rating' => '4.6', 'sold' => '614 sold',  'art' => 'cookware'],

            ['name' => 'Noise Canceling Earbuds',        'price' => '₱1,899.00', 'rating' => '4.9', 'sold' => '1.8k sold', 'art' => 'earbuds'],
            ['name' => 'Fitness Smart Watch',            'price' => '₱1,899.00', 'rating' => '4.7', 'sold' => '1.3k sold', 'art' => 'watch'],
            ['name' => 'Soft Canvas Tote Bag',           'price' => '₱699.00',   'rating' => '4.6', 'sold' => '782 sold',  'art' => 'bag'],
            ['name' => 'Lightweight Running Shoes',      'price' => '₱1,699.00', 'rating' => '4.8', 'sold' => '1.1k sold', 'art' => 'shoes'],
            ['name' => 'Gentle Skincare Starter Set',    'price' => '₱899.00',   'rating' => '4.7', 'sold' => '956 sold',  'art' => 'skincare'],
            ['name' => 'Non-Stick Kitchen Essentials',   'price' => '₱1,499.00', 'rating' => '4.8', 'sold' => '845 sold',  'art' => 'cookware'],

            ['name' => 'Bluetooth Earbuds Lite',        'price' => '₱899.00',   'rating' => '4.6', 'sold' => '3.1k sold', 'art' => 'earbuds'],
            ['name' => 'Classic Digital Smart Watch',    'price' => '₱1,599.00', 'rating' => '4.7', 'sold' => '732 sold',  'art' => 'watch'],
            ['name' => 'Everyday Crossbody Bag',        'price' => '₱799.00',   'rating' => '4.7', 'sold' => '1.0k sold', 'art' => 'bag'],
            ['name' => 'Everyday Trainers',              'price' => '₱1,399.00', 'rating' => '4.6', 'sold' => '568 sold',  'art' => 'shoes'],
            ['name' => 'Hydrating Care Set',             'price' => '₱1,199.00', 'rating' => '4.9', 'sold' => '1.2k sold', 'art' => 'skincare'],
            ['name' => 'Ceramic Cookware Bundle',        'price' => '₱1,799.00', 'rating' => '4.8', 'sold' => '491 sold',  'art' => 'cookware'],
        ];
    @endphp

    <main id="buyer-home" class="buyer-main">
        <div class="buyer-container">

            {{-- TOP ROW --}}
            <section class="buyer-top-grid">

                <div
                    id="buyerAnnouncementCarousel"
                    class="buyer-hero buyer-announcement-carousel"
                    aria-roledescription="carousel"
                    aria-label="ShopEase announcements"
                >
                    <div class="buyer-announcement-track">

                        <article
                            class="buyer-announcement-slide is-active"
                            data-announcement-slide="0"
                            aria-hidden="false"
                        >
                            <img
                                src="{{ asset('images/seasonal-sale.png') }}"
                                alt="ShopEase Seasonal Sale"
                                class="buyer-hero-image"
                                onerror="this.style.display='none'; this.closest('.buyer-announcement-slide').classList.add('hero-fallback');"
                            >

                            <div class="hero-fallback-content" aria-hidden="true">
                                <span class="hero-pill">ShopEase Special</span>
                                <h1>Seasonal Sale</h1>
                                <p>Better Finds. Happier Days.</p>
                                <span class="hero-fallback-button">Shop Now <span>→</span></span>
                            </div>
                        </article>

                        <article
                            class="buyer-announcement-slide"
                            data-announcement-slide="1"
                            aria-hidden="true"
                        >
                            <div class="announcement-slide-content announcement-slide-content--dark">
                                <div class="announcement-copy">
                                    <span class="hero-pill">Limited Time</span>
                                    <h1>Free Shipping Finds</h1>
                                    <p>Discover everyday picks with easier checkout and delivery.</p>
                                    <button
                                        type="button"
                                        class="announcement-cta"
                                        data-scroll-target="recommendedProducts"
                                    >
                                        Shop Deals
                                        <span>→</span>
                                    </button>
                                </div>

                                <div class="announcement-visual" aria-hidden="true">
                                    <span class="announcement-orb announcement-orb--one"></span>
                                    <span class="announcement-orb announcement-orb--two"></span>

                                    <div class="announcement-shopping-bag">
                                        <span class="announcement-bag-handle"></span>
                                        <span class="announcement-bag-body">
                                            <span class="announcement-bag-logo">S</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <article
                            class="buyer-announcement-slide"
                            data-announcement-slide="2"
                            aria-hidden="true"
                        >
                            <div class="announcement-slide-content announcement-slide-content--soft">
                                <div class="announcement-copy">
                                    <span class="hero-pill">Buyer Picks</span>
                                    <h1>Fresh Finds For You</h1>
                                    <p>Explore trending products chosen for your ShopEase feed.</p>
                                    <button
                                        type="button"
                                        class="announcement-cta announcement-cta--light"
                                        data-scroll-target="recommendedProducts"
                                    >
                                        Explore Now
                                        <span>→</span>
                                    </button>
                                </div>

                                <div class="announcement-product-stack" aria-hidden="true">
                                    <div class="announcement-stack-card stack-card-one"></div>
                                    <div class="announcement-stack-card stack-card-two"></div>
                                    <div class="announcement-stack-card stack-card-three"></div>
                                </div>
                            </div>
                        </article>
                    </div>

                    <button
                        type="button"
                        class="announcement-arrow announcement-arrow--prev"
                        id="announcementPrev"
                        aria-label="Previous announcement"
                    >
                        ‹
                    </button>

                    <button
                        type="button"
                        class="announcement-arrow announcement-arrow--next"
                        id="announcementNext"
                        aria-label="Next announcement"
                    >
                        ›
                    </button>

                    <div class="hero-dots" role="tablist" aria-label="Announcement slides">
                        <button
                            type="button"
                            class="dot active"
                            data-announcement-dot="0"
                            aria-label="Show announcement 1"
                            aria-selected="true"
                        ></button>

                        <button
                            type="button"
                            class="dot"
                            data-announcement-dot="1"
                            aria-label="Show announcement 2"
                            aria-selected="false"
                        ></button>

                        <button
                            type="button"
                            class="dot"
                            data-announcement-dot="2"
                            aria-label="Show announcement 3"
                            aria-selected="false"
                        ></button>
                    </div>
                </div>

                <section
                    id="buyerPurchasesCard"
                    class="buyer-purchases-card"
                    aria-labelledby="purchases-title"
                    role="link"
                    tabindex="0"
                    data-purchases-url="{{ route('buyer.my-purchases') }}"
                    aria-label="Go to My Purchases"
                >
                    <div class="panel-heading purchases-heading">
                        <h2 id="purchases-title">My Purchases</h2>
                    </div>

                    <div class="purchase-grid">
                        <button
                            type="button"
                            class="purchase-card"
                            data-purchase-status-url="{{ route('buyer.my-purchases', ['section' => 'purchases', 'tab' => 'processing']) }}"
                        >
                            <span class="purchase-icon">
                                <img src="{{ asset('icons/buyer/processing.png') }}" alt="" data-clean-bg="true">
                            </span>
                            <strong>2</strong>
                            <span>Processing</span>
                        </button>

                        <button
                            type="button"
                            class="purchase-card"
                            data-purchase-status-url="{{ route('buyer.my-purchases', ['section' => 'purchases', 'tab' => 'to-ship']) }}"
                        >
                            <span class="purchase-icon">
                                <img src="{{ asset('icons/buyer/to-ship.png') }}" alt="" data-clean-bg="true">
                            </span>
                            <strong>2</strong>
                            <span>To Ship</span>
                        </button>

                        <button
                            type="button"
                            class="purchase-card"
                            data-purchase-status-url="{{ route('buyer.my-purchases', ['section' => 'purchases', 'tab' => 'in-transit']) }}"
                        >
                            <span class="purchase-icon">
                                <img src="{{ asset('icons/buyer/in-transit.png') }}" alt="" data-clean-bg="true">
                            </span>
                            <strong>2</strong>
                            <span>In Transit</span>
                        </button>

                        <button
                            type="button"
                            class="purchase-card"
                            data-purchase-status-url="{{ route('buyer.my-purchases', ['section' => 'purchases', 'tab' => 'out-for-delivery']) }}"
                        >
                            <span class="purchase-icon">
                                <img src="{{ asset('icons/buyer/out-for-delivery.png') }}" alt="" data-clean-bg="true">
                            </span>
                            <strong>2</strong>
                            <span class="purchase-label-nowrap">Out for Delivery</span>
                        </button>

                        <button
                            type="button"
                            class="purchase-card"
                            data-purchase-status-url="{{ route('buyer.my-purchases', ['section' => 'purchases', 'tab' => 'delivered']) }}"
                        >
                            <span class="purchase-icon">
                                <img src="{{ asset('icons/buyer/delivered.png') }}" alt="" data-clean-bg="true">
                            </span>
                            <strong>2</strong>
                            <span>Delivered</span>
                        </button>
                    </div>
                </section>
            </section>

            {{-- SECOND ROW --}}
            <section class="buyer-middle-grid">

                <section class="categories-panel" aria-labelledby="categories-title">
                    <div class="panel-heading categories-heading">
                        <h2 id="categories-title">Browse Categories</h2>
                    </div>

                    <div id="buyerCategories" class="category-grid">
                        @foreach ($categories as $category)
                            <button
                                type="button"
                                class="category-card"
                                data-category="{{ $category['name'] }}"
                            >
                                <span class="category-icon-box">
                                    <img
                                        class="clean-icon"
                                        src="{{ asset($buyerIconPath . $category['icon']) }}"
                                        alt="{{ $category['name'] }}"
                                        data-clean-bg="true"
                                    >
                                </span>
                                <span class="category-label">{{ $category['name'] }}</span>
                            </button>
                        @endforeach
                    </div>
                </section>

                <section class="notifications-panel" aria-labelledby="notifications-title">
                    <div class="notifications-header">
                        <div class="notifications-title-wrap">
                            <span class="notifications-header-icon" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M10 21h4"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            <h2 id="notifications-title">Notifications</h2>
                        </div>

                        <a
                            href="{{ route('buyer.my-purchases', ['section' => 'notifications']) }}"
                            class="view-all-button"
                        >
                            View All →
                        </a>
                    </div>

                    <div class="notifications-list">

                        <button type="button" class="notification-row is-unread">
                            <span class="notification-icon notification-icon-order" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M4 7h16l-1 12H5L4 7Z"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M8 9V6a4 4 0 0 1 8 0v3"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linecap="round"
                                    />
                                </svg>
                            </span>

                            <span class="notification-copy">
                                <strong>Order is ready to ship</strong>
                                <span>Your order from The Shop PH has been packed and is ready for pickup.</span>
                            </span>

                            <span class="notification-meta">
                                <time>5m ago</time>
                                <span class="notification-dot" aria-label="Unread"></span>
                            </span>
                        </button>

                        <button type="button" class="notification-row is-unread">
                            <span class="notification-icon notification-icon-delivery" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M3 6h11v10H3V6Zm11 3h4l3 3v4h-7V9Z"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linejoin="round"
                                    />
                                    <circle cx="7" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/>
                                    <circle cx="18" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/>
                                </svg>
                            </span>

                            <span class="notification-copy">
                                <strong>Package is in transit</strong>
                                <span>Your parcel is on the way and will be delivered soon.</span>
                            </span>

                            <span class="notification-meta">
                                <time>32m ago</time>
                                <span class="notification-dot" aria-label="Unread"></span>
                            </span>
                        </button>

                        <button type="button" class="notification-row">
                            <span class="notification-icon notification-icon-promo" aria-hidden="true">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M4 12V5h7l9 9-7 7-9-9Z"
                                        stroke="currentColor"
                                        stroke-width="1.7"
                                        stroke-linejoin="round"
                                    />
                                    <circle cx="8" cy="9" r="1.3" fill="currentColor"/>
                                </svg>
                            </span>

                            <span class="notification-copy">
                                <strong>New voucher available</strong>
                                <span>You received a new ShopEase voucher for your next purchase.</span>
                            </span>

                            <span class="notification-meta">
                                <time>2h ago</time>
                            </span>
                        </button>


                    </div>
                </section>
            </section>

            {{-- RECOMMENDED --}}
            <section class="recommended-section" aria-labelledby="recommended-title">
                <div class="panel-heading recommended-heading">
                    <h2 id="recommended-title">Recommended for you</h2>
                    <button type="button" class="view-all-button">View All →</button>
                </div>

                <div id="recommendedProducts" class="product-grid">
                    @foreach ($recommendedProducts as $product)
                        <a
    href="{{ route('buyer.product') }}"
    class="product-card"
>
                            <div class="product-image-box">
                                <button type="button" class="favorite-button" aria-label="Add to favorites">♡</button>

                                @if ($product['art'] === 'earbuds')
                                    <svg class="product-art" viewBox="0 0 300 180" role="img" aria-label="Wireless earbuds product image">
                                        <defs>
                                            <linearGradient id="bg1" x1="0" x2="1" y1="0" y2="1">
                                                <stop offset="0%" stop-color="#edf3ee"/>
                                                <stop offset="100%" stop-color="#ded8d1"/>
                                            </linearGradient>
                                            <linearGradient id="case1" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#fdfdfd"/>
                                                <stop offset="100%" stop-color="#d8d7d2"/>
                                            </linearGradient>
                                        </defs>
                                        <rect width="300" height="180" rx="12" fill="url(#bg1)"/>
                                        <ellipse cx="228" cy="135" rx="60" ry="18" fill="#b3a99f" opacity=".25"/>
                                        <rect x="82" y="78" width="110" height="62" rx="26" fill="url(#case1)" stroke="#b4b0aa" stroke-width="3"/>
                                        <path d="M95 91 Q137 61 179 91" fill="none" stroke="#bbb8b2" stroke-width="3"/>
                                        <rect x="121" y="104" width="33" height="13" rx="6.5" fill="#f0f0ed" stroke="#c5c2bd"/>
                                        <rect x="114" y="54" width="19" height="52" rx="10" fill="#f8f8f5" stroke="#b9b7b1" stroke-width="3" transform="rotate(-11 114 54)"/>
                                        <circle cx="121" cy="91" r="5" fill="#96938d"/>
                                        <rect x="158" y="48" width="19" height="55" rx="10" fill="#f8f8f5" stroke="#b9b7b1" stroke-width="3" transform="rotate(15 158 48)"/>
                                        <circle cx="168" cy="90" r="5" fill="#96938d"/>
                                    </svg>

                                @elseif ($product['art'] === 'watch')
                                    <svg class="product-art" viewBox="0 0 300 180" role="img" aria-label="Smart watch product image">
                                        <defs>
                                            <linearGradient id="bg2" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#dce8ef"/>
                                                <stop offset="100%" stop-color="#ece5de"/>
                                            </linearGradient>
                                            <linearGradient id="strap2" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#20252a"/>
                                                <stop offset="100%" stop-color="#4d5660"/>
                                            </linearGradient>
                                        </defs>
                                        <rect width="300" height="180" rx="12" fill="url(#bg2)"/>
                                        <ellipse cx="155" cy="145" rx="80" ry="13" fill="#7f8992" opacity=".18"/>
                                        <rect x="135" y="26" width="30" height="128" rx="15" fill="url(#strap2)"/>
                                        <rect x="101" y="48" width="98" height="90" rx="22" fill="#151a20" stroke="#505861" stroke-width="4" transform="rotate(-8 150 93)"/>
                                        <rect x="112" y="59" width="77" height="68" rx="15" fill="#151f25"/>
                                        <circle cx="151" cy="93" r="23" fill="#102d3c"/>
                                        <path d="M151 73 L151 93 L164 102" fill="none" stroke="#6ad1db" stroke-width="4" stroke-linecap="round"/>
                                        <path d="M129 117 Q151 105 174 116" fill="none" stroke="#f5ad45" stroke-width="4"/>
                                        <circle cx="182" cy="81" r="4" fill="#93e0e4"/>
                                    </svg>

                                @elseif ($product['art'] === 'bag')
                                    <svg class="product-art" viewBox="0 0 300 180" role="img" aria-label="Canvas shoulder bag product image">
                                        <defs>
                                            <linearGradient id="bg3" x1="0" x2="1" y1="0" y2="1">
                                                <stop offset="0%" stop-color="#edf1e5"/>
                                                <stop offset="100%" stop-color="#e5d9cc"/>
                                            </linearGradient>
                                            <linearGradient id="bag3" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#c99d68"/>
                                                <stop offset="100%" stop-color="#9a7044"/>
                                            </linearGradient>
                                        </defs>
                                        <rect width="300" height="180" rx="12" fill="url(#bg3)"/>
                                        <circle cx="42" cy="36" r="22" fill="#99b58d" opacity=".34"/>
                                        <circle cx="67" cy="48" r="29" fill="#aec4a4" opacity=".3"/>
                                        <ellipse cx="162" cy="146" rx="83" ry="15" fill="#7d674e" opacity=".18"/>
                                        <path d="M102 76 Q150 18 201 77" fill="none" stroke="#855d35" stroke-width="10" stroke-linecap="round"/>
                                        <path d="M90 72 Q151 44 211 75 L198 140 Q153 157 103 141 Z" fill="url(#bag3)" stroke="#805b39" stroke-width="4"/>
                                        <path d="M110 77 Q152 95 192 77" fill="none" stroke="#d8b380" stroke-width="4" opacity=".75"/>
                                        <circle cx="151" cy="107" r="7" fill="#7b5737"/>
                                        <rect x="116" y="118" width="71" height="10" rx="5" fill="#b78652" opacity=".7"/>
                                    </svg>

                                @elseif ($product['art'] === 'shoes')
                                    <svg class="product-art" viewBox="0 0 300 180" role="img" aria-label="Running shoes product image">
                                        <defs>
                                            <linearGradient id="bg4" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#f3efe8"/>
                                                <stop offset="100%" stop-color="#dcd6cc"/>
                                            </linearGradient>
                                            <linearGradient id="shoe4" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#ffffff"/>
                                                <stop offset="100%" stop-color="#dad8d4"/>
                                            </linearGradient>
                                        </defs>
                                        <rect width="300" height="180" rx="12" fill="url(#bg4)"/>
                                        <ellipse cx="172" cy="140" rx="92" ry="17" fill="#776f65" opacity=".18"/>
                                        <path d="M72 101 L102 70 L139 85 L161 108 L218 118 Q234 123 235 139 L87 139 Q68 136 66 124 Z" fill="url(#shoe4)" stroke="#b0aaa2" stroke-width="4"/>
                                        <path d="M109 72 L144 90 L169 112 L106 106 Z" fill="#30373e"/>
                                        <path d="M86 119 Q151 127 226 127" fill="none" stroke="#6a7881" stroke-width="5"/>
                                        <path d="M117 91 L141 99 M124 87 L147 95 M133 84 L154 92" stroke="#ffffff" stroke-width="4" stroke-linecap="round"/>
                                        <path d="M87 101 L66 86 Q58 79 63 69 L73 60 L111 78 Z" fill="#222a2f" opacity=".9"/>
                                        <path d="M119 144 L225 144" stroke="#92908c" stroke-width="5" stroke-linecap="round"/>
                                    </svg>

                                @elseif ($product['art'] === 'skincare')
                                    <svg class="product-art" viewBox="0 0 300 180" role="img" aria-label="Skincare set product image">
                                        <defs>
                                            <linearGradient id="bg5" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#f3e5dc"/>
                                                <stop offset="100%" stop-color="#ebd6cf"/>
                                            </linearGradient>
                                            <linearGradient id="bottle5" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#f6b9a9"/>
                                                <stop offset="100%" stop-color="#e38e7d"/>
                                            </linearGradient>
                                        </defs>
                                        <rect width="300" height="180" rx="12" fill="url(#bg5)"/>
                                        <circle cx="48" cy="40" r="31" fill="#98ae86" opacity=".28"/>
                                        <ellipse cx="166" cy="145" rx="92" ry="14" fill="#8e6e66" opacity=".16"/>
                                        <rect x="88" y="63" width="39" height="72" rx="10" fill="#f5f0e7" stroke="#cab7ae" stroke-width="3"/>
                                        <rect x="95" y="49" width="25" height="18" rx="5" fill="#ddd4cb"/>
                                        <rect x="143" y="56" width="40" height="79" rx="11" fill="url(#bottle5)" stroke="#bf7869" stroke-width="3"/>
                                        <rect x="151" y="43" width="24" height="17" rx="5" fill="#e1b0a5"/>
                                        <rect x="197" y="73" width="48" height="54" rx="15" fill="#f4ded7" stroke="#cdaea6" stroke-width="3"/>
                                        <rect x="204" y="65" width="34" height="11" rx="5" fill="#c9b7af"/>
                                        <text x="108" y="105" text-anchor="middle" font-size="10" fill="#9c6e62" font-family="Poppins, sans-serif">CARE</text>
                                        <text x="163" y="101" text-anchor="middle" font-size="10" fill="#fff" font-family="Poppins, sans-serif">GLOW</text>
                                        <circle cx="221" cy="102" r="11" fill="#e9bdaf"/>
                                    </svg>

                                @else
                                    <svg class="product-art" viewBox="0 0 300 180" role="img" aria-label="Non-stick cookware set product image">
                                        <defs>
                                            <linearGradient id="bg6" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#e9e2d7"/>
                                                <stop offset="100%" stop-color="#f2ece4"/>
                                            </linearGradient>
                                            <linearGradient id="pot6" x1="0" x2="1">
                                                <stop offset="0%" stop-color="#7f6f5e"/>
                                                <stop offset="100%" stop-color="#4d4339"/>
                                            </linearGradient>
                                        </defs>
                                        <rect width="300" height="180" rx="12" fill="url(#bg6)"/>
                                        <ellipse cx="160" cy="145" rx="92" ry="14" fill="#62574d" opacity=".18"/>
                                        <ellipse cx="125" cy="113" rx="48" ry="31" fill="url(#pot6)" stroke="#40382f" stroke-width="4"/>
                                        <ellipse cx="125" cy="102" rx="43" ry="25" fill="#2f2924"/>
                                        <path d="M169 104 L220 89 Q233 86 237 94 L237 100 L173 119 Z" fill="#66574b" stroke="#453a31" stroke-width="4"/>
                                        <ellipse cx="201" cy="76" rx="31" ry="15" fill="#7b6b5e" stroke="#4d433a" stroke-width="4"/>
                                        <ellipse cx="201" cy="72" rx="25" ry="11" fill="#2e2925"/>
                                        <path d="M82 93 Q59 86 50 70" fill="none" stroke="#67584b" stroke-width="9" stroke-linecap="round"/>
                                        <rect x="87" y="133" width="87" height="8" rx="4" fill="#2d2823"/>
                                    </svg>
                                @endif
                            </div>

                            <h3>{{ $product['name'] }}</h3>

                            <p class="product-rating">
                                <span class="rating-star">★</span>
                                {{ $product['rating'] }}
                                <span class="sold-count">({{ $product['sold'] }})</span>
                            </p>

                            <div class="product-bottom-row">
                                <strong>{{ $product['price'] }}</strong>
                                <button type="button" class="product-cart-button" aria-label="Add {{ $product['name'] }} to cart">
                                    <img src="{{ asset($buyerIconPath . 'product-cart.png') }}" alt="" class="clean-icon" data-clean-bg="true">
                                </button>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        </div>
    </main>

    @include('components.buyer.footer')


    {{-- Shared Buyer Floating Chat --}}
    @include('components.buyer.floating-chat')

</body>
</html>
