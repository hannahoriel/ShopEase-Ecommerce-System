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

    @vite(['resources/css/app.css'])
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

                <div class="buyer-hero">
                    <img
                        src="{{ asset('images/seasonal-sale.png') }}"
                        alt="ShopEase Seasonal Sale"
                        class="buyer-hero-image"
                        onerror="this.style.display='none'; this.parentElement.classList.add('hero-fallback');"
                    >

                    <div class="hero-fallback-content" aria-hidden="true">
                        <span class="hero-pill">ShopEase Special</span>
                        <h1>Seasonal Sale</h1>
                        <p>Better Finds. Happier Days.</p>
                        <span class="hero-fallback-button">Shop Now <span>→</span></span>
                    </div>

                    <button
                        type="button"
                        id="shopNowButton"
                        class="hero-shop-button"
                        aria-label="Shop now"
                    ></button>

                    <div class="hero-dots" aria-hidden="true">
                        <span class="dot active"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>
                </div>

                <section class="buyer-purchases-card" aria-labelledby="purchases-title">
                    <div class="panel-heading purchases-heading">
                        <h2 id="purchases-title">My Purchases</h2>
                        <button type="button" class="view-all-button">View All →</button>
                    </div>

                    <div class="purchase-grid">
                        <button type="button" class="purchase-card">
                            <span class="purchase-icon">
                                <img src="{{ asset('icons/buyer/to-ship.png') }}" alt="" data-clean-bg="true">
                            </span>
                            <strong>2</strong>
                            <span>To Ship</span>
                        </button>

                        <button type="button" class="purchase-card">
                            <span class="purchase-icon">
                                <img src="{{ asset('icons/buyer/in-transit.png') }}" alt="" data-clean-bg="true">
                            </span>
                            <strong>2</strong>
                            <span>In Transit</span>
                        </button>

                        <button type="button" class="purchase-card">
                            <span class="purchase-icon">
                                <img src="{{ asset('icons/buyer/out-for-delivery.png') }}" alt="" data-clean-bg="true">
                            </span>
                            <strong>2</strong>
                            <span class="purchase-label-nowrap">Out for Delivery</span>
                        </button>

                        <button type="button" class="purchase-card">
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
                        <button type="button" id="viewAllCategories" class="view-all-button">View All →</button>
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

                <section class="chats-panel" aria-labelledby="chats-title">
                    <div class="chat-header">
                        <div class="chat-title-wrap">
                            <span class="chat-header-icon">
                                <img
                                    class="clean-icon"
                                    src="{{ asset('icons/buyer/chats-maroon.png') }}"
                                    alt=""
                                    
                                >
                            </span>
                            <h2 id="chats-title">Chats</h2>
                        </div>
                        <button type="button" class="view-all-button">View All →</button>
                    </div>

                    <div class="chat-list">
                        <button type="button" class="chat-row">
                            <span class="chat-avatar">
                                <svg class="invented-profile" viewBox="0 0 48 48" aria-hidden="true">
                                    <circle cx="24" cy="24" r="24" fill="#dfead8"/>
                                    <circle cx="24" cy="18" r="8" fill="#d59e7a"/>
                                    <path d="M13 38Q15 28 24 28T35 38" fill="#5f7462"/>
                                    <path d="M16 15Q20 7 28 11Q33 12 34 18Q29 14 24 15Q20 16 16 15Z" fill="#3a2d2a"/>
                                    <circle cx="21" cy="18" r="1.3" fill="#302a28"/>
                                    <circle cx="27" cy="18" r="1.3" fill="#302a28"/>
                                </svg>
                            </span>
                            <span class="chat-copy">
                                <strong>The Shop PH</strong>
                                <span>Hi your order has been prepared and ready to ship. You...</span>
                            </span>
                            <time>2h ago</time>
                        </button>

                        <button type="button" class="chat-row">
                            <span class="chat-avatar">
                                <svg class="invented-profile" viewBox="0 0 48 48" aria-hidden="true">
                                    <circle cx="24" cy="24" r="24" fill="#dce7f1"/>
                                    <circle cx="24" cy="18" r="8" fill="#f0bf96"/>
                                    <path d="M12 39Q15 28 24 28T36 39" fill="#374956"/>
                                    <path d="M16 14Q21 5 30 10Q34 12 35 18Q30 14 25 14Q21 14 16 14Z" fill="#1e2630"/>
                                    <circle cx="21" cy="18" r="1.3" fill="#302a28"/>
                                    <circle cx="27" cy="18" r="1.3" fill="#302a28"/>
                                    <path d="M19 23Q24 26 29 23" fill="none" stroke="#b26f5d" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span class="chat-copy">
                                <strong>Tech Haven</strong>
                                <span>Hi your order has been prepared and ready to ship. You...</span>
                            </span>
                            <time>2h ago</time>
                        </button>

                        <button type="button" class="chat-row">
                            <span class="chat-avatar">
                                <svg class="invented-profile" viewBox="0 0 48 48" aria-hidden="true">
                                    <circle cx="24" cy="24" r="24" fill="#f0dfd1"/>
                                    <circle cx="24" cy="18" r="8" fill="#bd825d"/>
                                    <path d="M12 39Q15 28 24 28T36 39" fill="#7b5e4f"/>
                                    <path d="M15 17Q15 8 24 8Q33 8 34 17Q29 12 24 13Q19 12 15 17Z" fill="#4d3030"/>
                                    <circle cx="21" cy="18" r="1.3" fill="#2d2525"/>
                                    <circle cx="27" cy="18" r="1.3" fill="#2d2525"/>
                                    <path d="M19 23Q24 26 29 23" fill="none" stroke="#9e5f55" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span class="chat-copy">
                                <strong>Mia Boutique</strong>
                                <span>Hi your order has been prepared and ready to ship. You...</span>
                            </span>
                            <time>2h ago</time>
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
                        <article class="product-card">
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
                        </article>
                    @endforeach
                </div>
            </section>
        </div>
    </main>

    <!-- =========================================================
         REALISTIC FOOTER
    ========================================================== -->

    <footer class="landing-footer">

        <div class="footer-inner">

            <div class="footer-main">

                <div class="footer-brand">

                    <img
                        src="{{ asset('icons/logos/ShopEase.png') }}"
                        alt="ShopEase"
                    >

                    <p class="footer-brand-tagline">
                        Shop smarter, discover products you love,
                        and enjoy a smoother experience from checkout
                        to delivery.
                    </p>

                    <div class="footer-trust-row">

                        <span class="footer-trust-chip">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="10" width="16" height="10" rx="2" />
                                <path d="M8 10V7a4 4 0 0 1 8 0v3" />
                            </svg>
                            Secure Checkout
                        </span>

                        <span class="footer-trust-chip">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 7h11v10H3z" />
                                <path d="M14 10h4l3 3v4h-7z" />
                                <circle cx="7" cy="19" r="1.5" />
                                <circle cx="18" cy="19" r="1.5" />
                            </svg>
                            Order Tracking
                        </span>

                    </div>

                    <div class="footer-socials">

                        <a href="#" class="footer-social" aria-label="Facebook">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M14 8h3V5h-3c-2.8 0-4 1.8-4 4v2H7v3h3v5h3v-5h3l.5-3H13V9c0-.7.3-1 1-1z" />
                            </svg>
                        </a>

                        <a href="#" class="footer-social" aria-label="Instagram">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
                                <rect x="4" y="4" width="16" height="16" rx="4" />
                                <circle cx="12" cy="12" r="3.5" />
                                <circle cx="17.2" cy="6.8" r="1" fill="currentColor" stroke="none" />
                            </svg>
                        </a>

                        <a href="#" class="footer-social" aria-label="TikTok">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M15 4v9.2a4.8 4.8 0 1 1-4-4.7v2.8a2 2 0 1 0 1 1.9V4h3z" />
                            </svg>
                        </a>

                        <a href="#" class="footer-social" aria-label="YouTube">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 8.2a2.6 2.6 0 0 0-1.8-1.8C17.6 6 12 6 12 6s-5.6 0-7.2.4A2.6 2.6 0 0 0 3 8.2 27 27 0 0 0 2.6 12 27 27 0 0 0 3 15.8a2.6 2.6 0 0 0 1.8 1.8C6.4 18 12 18 12 18s5.6 0 7.2-.4a2.6 2.6 0 0 0 1.8-1.8 27 27 0 0 0 .4-3.8 27 27 0 0 0-.4-3.8z" />
                                <path d="M10 9.5l5 2.5-5 2.5z" fill="#3D080C" />
                            </svg>
                        </a>

                    </div>

                </div>


                <div class="footer-column">

                    <h3 class="footer-column-title">
                        Shop
                    </h3>

                    <div class="footer-links">
                        <a href="#guestShop" class="footer-link">All Products</a>
                        <a href="#guestShop" class="footer-link">Electronics &amp; Gadgets</a>
                        <a href="#guestShop" class="footer-link">Women's Apparel</a>
                        <a href="#guestShop" class="footer-link">Home &amp; Garden</a>
                        <a href="#guestShop" class="footer-link">Health &amp; Beauty</a>
                        <a href="#guestShop" class="footer-link">Great Deals</a>
                    </div>

                </div>


                <div class="footer-column">

                    <h3 class="footer-column-title">
                        Help &amp; Support
                    </h3>

                    <div class="footer-links">
                        <a href="#" class="footer-link">Order Tracking</a>
                        <a href="#" class="footer-link">Shipping Information</a>
                        <a href="#" class="footer-link">Returns &amp; Refunds</a>
                        <a href="#" class="footer-link">Contact Support</a>
                        <a href="#" class="footer-link">Frequently Asked Questions</a>
                    </div>

                </div>


                <div class="footer-column">

                    <h3 class="footer-column-title">
                        Sell on ShopEase
                    </h3>

                    <div class="footer-links">
                        <a href="#" class="footer-link">Become a Seller</a>
                        <a href="#" class="footer-link">Seller Guidelines</a>
                        <a href="#" class="footer-link">Seller Compliance</a>
                        <a href="#" class="footer-link">Seller Support</a>
                    </div>

                    <div style="margin-top:18px;">

                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M4 5h16v14H4z" />
                                <path d="m4 7 8 6 8-6" />
                            </svg>
                            <span class="footer-contact-text">
                                support@shopease.com
                            </span>
                        </div>

                        <div class="footer-contact-item">
                            <svg class="footer-contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6.5 3.5h3l1.5 4-2 1.6a14 14 0 0 0 5.9 5.9l1.6-2 4 1.5v3c0 .8-.7 1.5-1.5 1.5C11.1 19 5 12.9 5 5c0-.8.7-1.5 1.5-1.5z" />
                            </svg>
                            <span class="footer-contact-text">
                                Customer Care · Mon–Sat
                            </span>
                        </div>

                    </div>

                </div>

            </div>


            <div class="footer-bottom">

                <p class="footer-text">
                    Shop Easier, Live Better. © {{ date('Y') }} ShopEase.
                </p>

                <div class="footer-legal">
                    <a href="#">Privacy Policy</a>
                    <a href="#">Terms of Service</a>
                    <a href="#">Cookie Policy</a>
                </div>

            </div>

        </div>

    </footer>

    <style>
        :root {
            --buyer-bg: #FCF6F4;
            --buyer-panel: #fff8f5;
            --buyer-white: #ffffff;
            --buyer-maroon: #6d1b25;
            --buyer-dark-maroon: #3b171c;
            --buyer-text: #211918;
            --buyer-muted: #77716e;
            --buyer-line: #eadedb;
            --buyer-soft-line: #f0ddd9;
            --maroon-dark: #52070B;
            --peach: #FF876E;
        }

        /* =========================================================
           REALISTIC MAROON FOOTER
           No whitespace gap above or below.
        ========================================================== */

        .landing-footer {
            width: 100%;
            margin: 0;
            padding: 30px 5.5% 14px;

            display: block;

            background:
                linear-gradient(145deg, #3D080C 0%, #52070B 52%, #650E14 100%);

            color: rgba(255,255,255,0.70);
            border-top: 1px solid rgba(255,255,255,0.07);
        }


        .footer-inner {
            width: min(1360px, 100%);
            margin: 0 auto;
        }


        .footer-main {
            display: grid;
            grid-template-columns: 1.45fr repeat(3, 1fr);
            gap: 32px;
            padding-bottom: 26px;
            align-items: start;
        }


        .footer-brand {
            display: block;
            min-width: 0;
        }


        .footer-brand img {
            width: 505px;
            height: auto;
            display: block;
            object-fit: contain;
            margin-top: -160px;
            margin-bottom: -142px;
            margin-left: -88px;
            transform: translateY(-0px);
        }


        .footer-brand-tagline {
            max-width: 290px;
            margin: 0;
            font-size: 12px;
            line-height: 1.75;
            color: rgba(255,255,255,0.62);
        }


        .footer-trust-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }


        .footer-trust-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 10px;
            border: 1px solid rgba(255,255,255,0.13);
            border-radius: 999px;
            background: rgba(255,255,255,0.045);
            color: rgba(255,255,255,0.68);
            font-size: 9px;
            font-weight: 600;
        }


        .footer-trust-chip svg {
            width: 12px;
            height: 12px;
            color: var(--peach);
            flex-shrink: 0;
        }


        .footer-column {
            min-width: 0;
        }


        .footer-column > div[style*="margin-top:18px"] {
            margin-top: 12px !important;
        }


        .footer-column-title {
            margin: 0 0 12px;
            font-size: 12px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: 0.15px;
        }


        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }


        .footer-link {
            width: fit-content;
            font-size: 11px;
            color: rgba(255,255,255,0.58);
            transition: color 0.2s ease, transform 0.2s ease;
        }


        .footer-link:hover {
            color: var(--peach);
            transform: translateX(2px);
        }


        .footer-contact-item {
            display: flex;
            gap: 9px;
            align-items: flex-start;
            margin-bottom: 8px;
        }


        .footer-contact-icon {
            width: 14px;
            height: 14px;
            flex: 0 0 14px;
            color: var(--peach);
            margin-top: 2px;
        }


        .footer-contact-text {
            font-size: 10.5px;
            line-height: 1.55;
            color: rgba(255,255,255,0.58);
        }


        .footer-bottom {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-top: 12px;
            border-top: 1px solid rgba(255,255,255,0.10);
        }


        .footer-text {
            margin: 0;
            font-size: 9.5px;
            line-height: 1.6;
            color: rgba(255,255,255,0.48);
        }


        .footer-legal {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }


        .footer-legal a {
            font-size: 9px;
            color: rgba(255,255,255,0.45);
            transition: color 0.2s ease;
        }


        .footer-legal a:hover {
            color: #FFFFFF;
        }


        .footer-socials {
            display: flex;
            align-items: center;
            gap: 7px;
            margin-top: 12px;
        }


        .footer-social {
            width: 31px;
            height: 31px;
            border: 1px solid rgba(255,255,255,0.22);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #FFFFFF;
            background: rgba(255,255,255,0.025);
            transition: all 0.25s ease;
        }


        .footer-social:hover {
            background: var(--peach);
            border-color: var(--peach);
            color: var(--maroon-dark);
            transform: translateY(-2px);
        }


        .footer-social svg {
            width: 13px;
            height: 13px;
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body.buyer-page {
            margin: 0;
            padding: 0;
            min-width: 320px;
            background: var(--buyer-bg);
            color: var(--buyer-text);
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        button,
        input,
        textarea,
        select {
            font: inherit;
        }

        button {
            border: 0;
        }

        .buyer-main {
            width: 100%;
            padding: 150px 0 20px;
            background: var(--buyer-bg);
        }

        /* Slightly narrower than the previous 1760px version */
        .buyer-container {
            width: 100%;
            max-width: 1660px;
            margin: 0 auto;
            padding: 0 18px;
        }

        .buyer-top-grid,
        .buyer-middle-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) minmax(300px, 1fr);
            gap: 10px;
        }

        .buyer-hero {
            position: relative;
            min-height: 178px;
            overflow: hidden;
            border: 1px solid #f0d9d3;
            border-radius: 13px;
            background: #f8dcd5;
            isolation: isolate;
        }

        .buyer-hero-image {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
            user-select: none;
            -webkit-user-drag: none;
        }

        .hero-shop-button {
            position: absolute;
            left: 6.5%;
            bottom: 12%;
            width: 155px;
            height: 38px;
            border-radius: 999px;
            background: transparent;
            cursor: pointer;
            z-index: 3;
        }

        .hero-dots {
            position: absolute;
            right: 13px;
            bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 2;
        }

        .hero-dots .dot {
            width: 6px;
            height: 6px;
            border-radius: 999px;
            background: rgba(107, 30, 39, .28);
        }

        .hero-dots .dot.active {
            background: #7c2932;
        }

        .hero-fallback-content {
            display: none;
            position: absolute;
            inset: 0 auto 0 0;
            width: 55%;
            padding: 28px 25px;
            z-index: 1;
        }

        .hero-fallback .hero-fallback-content {
            display: block;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            height: 20px;
            padding: 0 13px;
            border-radius: 999px;
            background: #f7bcc1;
            color: #63202a;
            font-size: 9px;
            font-weight: 700;
        }

        .hero-fallback-content h1 {
            margin: 8px 0 0;
            color: #5e0718;
            font-size: clamp(32px, 3vw, 41px);
            line-height: .98;
            font-weight: 800;
        }

        .hero-fallback-content p {
            margin: 3px 0 10px;
            color: #7f2835;
            font-size: 13px;
        }

        .hero-fallback-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 11px;
            min-width: 102px;
            height: 29px;
            padding: 0 12px;
            border-radius: 999px;
            background: #6d1b25;
            color: #fff;
            font-size: 8px;
            font-weight: 600;
        }

        .buyer-purchases-card {
            padding: 14px;
            border: 1px solid var(--buyer-soft-line);
            border-radius: 13px;
            background: #FAECE8;
            box-shadow: 0 4px 13px rgba(75, 33, 27, .035);
        }

        .panel-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .panel-heading h2,
        .chat-header h2 {
            margin: 0;
            color: var(--buyer-dark-maroon);
            font-size: 20px;
            line-height: 1.2;
            font-weight: 700;
        }

        .purchases-heading {
            margin: 0 2px 10px;
        }

        .purchases-heading h2 {
            font-size: 18px;
        }

        .view-all-button {
            padding: 0;
            background: transparent;
            color: #7c2932;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.1;
            cursor: pointer;
            white-space: nowrap;
            transition: color .2s ease;
        }

        .view-all-button:hover {
            color: #5f1720;
        }

        .purchase-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 7px;
        }

        .purchase-card {
            min-width: 0;
            min-height: 118px;
            padding: 10px 5px 8px;
            border: 1px solid #f0e3e0;
            border-radius: 9px;
            background: #fff;
            color: var(--buyer-text);
            text-align: center;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
        }

        .purchase-card:hover,
        .purchase-card.is-selected {
            transform: translateY(-2px);
            border-color: #ead2ce;
            box-shadow: 0 6px 13px rgba(80, 35, 30, .07);
        }

        /* No circle/background behind purchase PNGs */
        .purchase-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent !important;
            border-radius: 0 !important;
        }

        .purchase-icon img {
            width: 46px;
            height: 46px;
            object-fit: contain;
        }

        .purchase-card strong {
            display: block;
            margin-top: 6px;
            color: #241918;
            font-size: 16px;
            line-height: 1;
            font-weight: 700;
        }

        .purchase-card > span:last-child {
            display: block;
            margin-top: 4px;
            color: #4f4745;
            font-size: 12px;
            line-height: 1.35;
        }

        .buyer-middle-grid {
            margin-top: 20px;
            align-items: start;
        }

        .categories-panel {
            min-width: 0;
        }

        .categories-heading {
            margin-bottom: 5px;
        }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(0, 1fr));
            gap: 5px 7px;
        }

        .category-card {
            min-width: 0;
            height: 101px;
            padding: 0 2px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            background: transparent;
            border: 1px solid transparent;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: transform .2s ease, background .2s ease, border-color .2s ease, box-shadow .2s ease;
        }

        .category-card:hover {
            transform: translateY(-2px);
        }

        .category-card.selected {
            border-color: #c99a9d;
            background: #fff2ee;
            box-shadow: 0 5px 13px rgba(95, 28, 36, .07);
        }

        /* Transparent icon holder + enlarged PNG */
        .category-icon-box {
            width: 80px;
            height: 80px;
            flex: 0 0 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            background: transparent !important;
        }

        .category-icon-box img {
            width: 68px;
            height: 68px;
            object-fit: contain;
            background: transparent !important;
        }

        .category-label {
            max-width: 125px;
            margin-top: 1px;
            color: #382d2b;
            font-size: 12px;
            line-height: 1.2;
            font-weight: 600;
        }

        .chats-panel {
            min-width: 0;
            min-height: 250px;
            overflow: hidden;
            border: 1px solid #f0ddd9;
            border-radius: 12px;
            background: #FAECE8;
        }

        .chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 40px;
            padding: 7px 12px 8px;
            border-bottom: 1px solid #f0ddd9;
        }

        .chat-title-wrap {
            display: flex;
            align-items: center;
            gap: 7px;
        }

        /* No maroon circle behind chat-header icon */
        .chat-header-icon {
            width: 18px;
            height: auto;
            display: flex;
            margin-left: 10px;
            align-items: center;
            justify-content: center;
            border-radius: 0;
            background: transparent !important;
        }

        .chat-header-icon img {
            width: 38px;
            height: 38px;
            object-fit: contain;
            background: transparent !important;
        }

        .chat-list {
            display: flex;
            flex-direction: column;
        }

        .chat-row {
            width: 100%;
            min-width: 0;
            padding: 11px 10px;
            display: flex;
            align-items: center;
            text-align: left;
            background: transparent;
            border-bottom: 1px solid #eadedb;
            cursor: pointer;
            transition: background .2s ease;
        }

        .chat-row:last-child {
            border-bottom: 0;
        }

        .chat-row:hover {
            background: #fff0eb;
        }

        /* No colored circle behind profile icon */
        .chat-avatar {
            width: 43px;
            height: 43px;
            flex: 0 0 43px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border-radius: 0;
            background: transparent !important;
        }

        .chat-avatar img {
            width: 40px;
            height: 40px;
            object-fit: contain;
            padding: 0;
            background: transparent !important;
        }

        .chat-avatar .invented-profile {
            width: 40px;
            height: 40px;
            display: block;
        }

        .chat-copy {
            min-width: 0;
            flex: 1;
            margin-left: 8px;
        }

        .chat-copy strong,
        .chat-copy span {
            display: block;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chat-copy strong {
            color: #291f1f;
            font-size: 13px;
            line-height: 1.25;
            font-weight: 700;
        }

        .chat-copy span {
            margin-top: 3px;
            color: #77716e;
            font-size: 11px;
            line-height: 1.3;
        }

        .chat-row time {
            flex: 0 0 auto;
            margin-left: 7px;
            color: #8e8784;
            font-size: 10px;
            line-height: 1.1;
        }

        .recommended-section {
            margin-top: 20px;
        }

        .recommended-heading {
            margin-bottom: 8px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 9px;
        }

        .product-card {
            min-width: 0;
            min-height: 270px;
            padding: 8px;
            border: 1px solid #e9dfdc;
            border-radius: 10px;
            background: #fff;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 17px rgba(70, 35, 28, .07);
        }

        .product-image-box {
            position: relative;
            height: 132px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 7px;
            background: #f7f4f1;
        }

        .product-art {
            width: 100%;
            height: 100%;
            display: block;
        }

        .favorite-button {
            position: absolute;
            top: 5px;
            right: 5px;
            z-index: 2;
            width: 23px;
            height: 23px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, .95);
            color: #8d2734;
            font-size: 15px;
            line-height: 1;
            cursor: pointer;
            box-shadow: 0 2px 7px rgba(0, 0, 0, .08);
            transition: color .2s ease, transform .2s ease, background .2s ease;
        }

        .favorite-button:hover,
        .favorite-button.is-favorite {
            color: #b1283d;
            background: #fff;
            transform: scale(1.07);
        }

        .product-card h3 {
            min-height: 23px;
            margin: 8px 0 0;
            overflow: hidden;
            color: #221d1b;
            font-size: 14px;
            line-height: 1.3;
            font-weight: 600;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-rating {
            margin: 3px 0 0;
            color: #3a3a38;
            font-size: 12px;
            line-height: 1.3;
        }

        .rating-star {
            color: #f28a20;
        }

        .sold-count {
            margin-left: 10px;
        }

        .product-bottom-row {
            margin-top: 45px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 5px;
        }

        .product-bottom-row strong {
            color: #17120f;
            font-size: 15px;
            line-height: 1.15;
            font-weight: 700;
        }

        .product-cart-button {
            width: 30px;
            height: auto;
            flex: 0 0 23px;
            margin-right: 4px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: transparent;
            cursor: pointer;
            transition: background .2s ease, transform .2s ease;
        }

        .product-cart-button img {
            width: 18px;
            height: 18px;
            object-fit: contain;
            background: transparent !important;
        }

        .product-cart-button:hover {
            background: #fbe2de;
            transform: scale(1.05);
        }

        .product-cart-button.added {
            background: #edf5eb;
        }


        .purchase-label-nowrap {
            white-space: nowrap;
            font-size: 11px;
        }

        .purchase-card:nth-child(4) .purchase-icon img {
            transform: translateY(0);
        }

        /* Keep the real chats-maroon artwork visible; no wrapper/background tint. */
        .chat-header-icon,
        .chat-header-icon img {
            background: transparent !important;
        }

        .chat-header-icon img {
            display: block;
        }

        @media (min-width: 1281px) {
            .buyer-container {
                max-width: 1550px;
            }
        }

        @media (max-width: 1280px) {
            .buyer-top-grid,
            .buyer-middle-grid {
                grid-template-columns: minmax(0, 1.32fr) minmax(280px, .95fr);
            }

            .buyer-container {
                padding-left: 20px;
                padding-right: 20px;
            }
        }

        @media (max-width: 900px) {
            .buyer-main {
                padding-top: 108px;
            }

            .buyer-container {
                padding-left: 18px;
                padding-right: 18px;
            }

            .buyer-top-grid,
            .buyer-middle-grid {
                grid-template-columns: 1fr;
            }

            .buyer-hero {
                min-height: 200px;
            }

            .buyer-purchases-card {
                min-height: 170px;
            }

            .chats-panel {
                min-height: 150px;
            }

            .category-grid {
                grid-template-columns: repeat(7, minmax(0, 1fr));
            }

            .product-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 650px) {
            .buyer-main {
                padding-top: 102px;
            }

            .buyer-container {
                padding-left: 12px;
                padding-right: 12px;
            }

            .buyer-hero {
                min-height: 185px;
            }

            .category-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 6px;
            }

            .category-card {
                height: 90px;
            }

            .category-icon-box {
                width: 66px;
                height: 66px;
                flex-basis: 66px;
            }

            .category-icon-box img {
                width: 54px;
                height: 54px;
            }

            .product-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 430px) {
            .buyer-main {
                padding-top: 96px;
            }

            .buyer-hero {
                min-height: 175px;
            }

            .buyer-purchases-card {
                padding: 10px;
            }

            .purchase-icon {
                width: 42px;
                height: 42px;
            }

            .purchase-icon img {
                width: 27px;
                height: 27px;
            }

            .category-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }


        /* =========================================================
           FOOTER RESPONSIVE
        ========================================================== */
        @media (max-width: 900px) {
            .landing-footer {
                padding: 36px 18px 14px;
            }

            .footer-main {
                grid-template-columns: 1fr 1fr;
                gap: 24px 18px;
                padding-bottom: 22px;
            }

            .footer-brand {
                grid-column: 1 / -1;
            }

            .footer-brand img {
                width: 115px;
                margin-top: 0;
                margin-bottom: 15px;
                transform: none;
            }

            .footer-brand-tagline {
                max-width: 430px;
                font-size: 11px;
            }

            .footer-column-title {
                margin-bottom: 13px;
                font-size: 11px;
            }

            .footer-link {
                font-size: 10px;
            }

            .footer-contact-text {
                font-size: 9.5px;
            }

            .footer-bottom {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition: none !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const chatIcon = document.querySelector('.chat-header-icon img');
            if (chatIcon) {
                chatIcon.addEventListener('error', () => {
                    chatIcon.src = "{{ asset('icons/admin/buyer/chats-maroon.png') }}";
                }, { once: true });
            }


            const shopNowButton = document.getElementById('shopNowButton');
            const recommendedProducts = document.getElementById('recommendedProducts');

            shopNowButton?.addEventListener('click', () => {
                recommendedProducts?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });

            /* Category selection */
            const categoryCards = document.querySelectorAll('.category-card');

            categoryCards.forEach((card) => {
                card.addEventListener('click', () => {
                    categoryCards.forEach((item) => item.classList.remove('selected'));
                    card.classList.add('selected');
                    console.log('Selected category:', card.dataset.category || '');
                });
            });

            /* View all categories */
            document.getElementById('viewAllCategories')?.addEventListener('click', () => {
                document.getElementById('buyerCategories')?.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            });

            /* Favorites */
            document.querySelectorAll('.favorite-button').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.stopPropagation();
                    const active = button.classList.toggle('is-favorite');
                    button.textContent = active ? '♥' : '♡';
                    button.setAttribute('aria-pressed', active ? 'true' : 'false');
                });
            });

            /* Add to cart */
            document.querySelectorAll('.product-cart-button').forEach((button) => {
                button.addEventListener('click', (event) => {
                    event.stopPropagation();

                    const image = button.querySelector('img');
                    const originalSrc = image?.getAttribute('src');

                    button.classList.add('added');
                    button.innerHTML = '<span style="font-size:12px;font-weight:700;color:#477B4E;">✓</span>';

                    window.setTimeout(() => {
                        button.classList.remove('added');
                        button.innerHTML = '';

                        if (originalSrc) {
                            const newImage = document.createElement('img');
                            newImage.src = originalSrc;
                            newImage.alt = '';
                            newImage.width = 18;
                            newImage.height = 18;
                            newImage.className = 'clean-icon';
                            newImage.dataset.cleanBg = 'true';
                            button.appendChild(newImage);
                            removePngBackground(newImage);
                        }
                    }, 900);
                });
            });

            /* Purchase selection */
            document.querySelectorAll('.purchase-card').forEach((card) => {
                card.addEventListener('click', () => {
                    document.querySelectorAll('.purchase-card').forEach((item) => {
                        item.classList.remove('is-selected');
                    });
                    card.classList.add('is-selected');
                });
            });

            /*
             * Remove a baked square/pastel background from supplied PNG icons.
             * It samples the edge/background color and flood-fills only connected
             * pixels close to that color, preserving the central icon artwork.
             */
            function removePngBackground(img) {
                if (!img || img.dataset.cleaned === 'true') return;

                const clean = () => {
                    if (!img.naturalWidth || !img.naturalHeight) return;

                    const canvas = document.createElement('canvas');
                    canvas.width = img.naturalWidth;
                    canvas.height = img.naturalHeight;

                    const ctx = canvas.getContext('2d', { willReadFrequently: true });
                    if (!ctx) return;

                    ctx.drawImage(img, 0, 0);

                    let imageData;
                    try {
                        imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    } catch (error) {
                        return;
                    }

                    const { data, width, height } = imageData;

                    const sample = (x, y) => {
                        const i = (y * width + x) * 4;
                        return [data[i], data[i + 1], data[i + 2], data[i + 3]];
                    };

                    const edgeSamples = [
                        sample(0, 0),
                        sample(width - 1, 0),
                        sample(0, height - 1),
                        sample(width - 1, height - 1),
                        sample(Math.floor(width / 2), 0),
                        sample(Math.floor(width / 2), height - 1)
                    ].filter((p) => p[3] > 0);

                    if (!edgeSamples.length) return;

                    const background = edgeSamples.reduce(
                        (acc, p) => [acc[0] + p[0], acc[1] + p[1], acc[2] + p[2]],
                        [0, 0, 0]
                    ).map((v) => v / edgeSamples.length);

                    const visited = new Uint8Array(width * height);
                    const queue = [];
                    const tolerance = 48;

                    const colorDistance = (r, g, b) => {
                        return Math.sqrt(
                            Math.pow(r - background[0], 2) +
                            Math.pow(g - background[1], 2) +
                            Math.pow(b - background[2], 2)
                        );
                    };

                    const enqueue = (x, y) => {
                        if (x < 0 || y < 0 || x >= width || y >= height) return;

                        const idx = y * width + x;
                        if (visited[idx]) return;

                        const i = idx * 4;
                        if (data[i + 3] === 0) {
                            visited[idx] = 1;
                            return;
                        }

                        if (colorDistance(data[i], data[i + 1], data[i + 2]) <= tolerance) {
                            visited[idx] = 1;
                            queue.push(idx);
                        }
                    };

                    for (let x = 0; x < width; x++) {
                        enqueue(x, 0);
                        enqueue(x, height - 1);
                    }

                    for (let y = 0; y < height; y++) {
                        enqueue(0, y);
                        enqueue(width - 1, y);
                    }

                    while (queue.length) {
                        const idx = queue.shift();
                        const x = idx % width;
                        const y = Math.floor(idx / width);
                        const i = idx * 4;

                        data[i + 3] = 0;

                        enqueue(x + 1, y);
                        enqueue(x - 1, y);
                        enqueue(x, y + 1);
                        enqueue(x, y - 1);
                    }

                    ctx.putImageData(imageData, 0, 0);

                    img.src = canvas.toDataURL('image/png');
                    img.dataset.cleaned = 'true';
                };

                if (img.complete && img.naturalWidth) {
                    clean();
                } else {
                    img.addEventListener('load', clean, { once: true });
                }
            }

            document.querySelectorAll('.clean-icon[data-clean-bg="true"]').forEach(removePngBackground);
        });
    </script>

</body>
</html>
