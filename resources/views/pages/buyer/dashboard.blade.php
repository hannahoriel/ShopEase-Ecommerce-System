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

    <script id="buyerDashboardConfig" type="application/json">
        {!! json_encode([
            'productsUrl'    => '/api/v1/buyer/dashboard/products',
            'announcementsUrl' => '/api/v1/buyer/dashboard/announcements',
            'ordersUrl' => '/api/v1/buyer/orders',
            'notificationsUrl' => '/api/v1/buyer/notifications',
            'productBaseUrl' => '/buyer/product',
            'cartUrl'        => '/api/v1/buyer/cart',
            'apiToken'       => $apiToken ?? '',
        ]) !!}
    </script>
</head>

<body class="buyer-page">

    @include('components.buyer.navbar')

    @php
        $buyerIconPath = 'icons/buyer/';

        $categories = [
            ['name' => 'Pet Supplies',                  'slug' => 'pet-supplies',        'icon' => 'pets.png'],
            ['name' => 'Electronics & Gadgets',        'slug' => 'electronics',          'icon' => 'gadgets.png'],
            ['name' => "Women's Apparel",             'slug' => 'womens-apparel',       'icon' => 'women.png'],
            ['name' => "Men's Apparel",               'slug' => 'mens-apparel',         'icon' => 'men.png'],
            ['name' => 'Kids & Baby',                 'slug' => 'kids-and-baby',        'icon' => 'kids.png'],
            ['name' => 'Home & Garden',               'slug' => 'home-and-garden',      'icon' => 'garden.png'],
            ['name' => 'Sports & Outdoors',           'slug' => 'sports-and-outdoors',  'icon' => 'sports.png'],
            ['name' => 'Health & Beauty',             'slug' => 'health-and-beauty',    'icon' => 'health.png'],
            ['name' => 'Books & Media',               'slug' => 'books-and-media',      'icon' => 'books.png'],
            ['name' => 'Food & Gourmet',              'slug' => 'food-and-gourmet',     'icon' => 'gourmet.png'],
            ['name' => 'Automotive & Motorcycle',     'slug' => 'automotive',           'icon' => 'automotive.png'],
            ['name' => 'Furniture & Office Equipment','slug' => 'furniture',            'icon' => 'furniture.png'],
            ['name' => 'Jewelry & Watches',           'slug' => 'jewelry-and-watches',  'icon' => 'jewelry.png'],
            ['name' => 'Office & School Supplies',    'slug' => 'office-supplies',      'icon' => 'school.png'],
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
                            class="buyer-announcement-slide is-active hero-fallback"
                            data-announcement-slide="0"
                            aria-hidden="false"
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
                            <strong data-purchase-count="processing">0</strong>
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
                            <strong data-purchase-count="to-ship">0</strong>
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
                            <strong data-purchase-count="in-transit">0</strong>
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
                            <strong data-purchase-count="out-for-delivery">0</strong>
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
                            <strong data-purchase-count="delivered">0</strong>
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
                                data-category="{{ $category['slug'] }}"
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

                    <div class="notifications-list" id="buyerDashboardNotifications" aria-live="polite">
                        <p class="buyer-dashboard-notifications-state">Loading notifications…</p>
                    </div>
                    <p id="buyerDashboardNotificationsError" role="alert" hidden></p>
                </section>
            </section>

            {{-- RECOMMENDED --}}
            <section class="recommended-section" aria-labelledby="recommended-title">
                <div class="panel-heading recommended-heading">
                    <h2 id="recommended-title">Recommended for you</h2>
                    <button type="button" class="view-all-button">View All →</button>
                </div>

                <div id="recommendedProducts" class="product-grid">
                    <div id="recommendedProductsLoading" class="products-loading-state">
                        Loading products…
                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('components.buyer.footer')


    {{-- Shared Buyer Floating Chat --}}
    @include('components.buyer.floating-chat')

</body>
</html>
