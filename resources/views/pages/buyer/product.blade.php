{{-- =========================================================
     ShopEase Buyer Product Details
     resources/views/pages/buyer/product.blade.php

     Design references:
     - Buyer dashboard typography: Plus Jakarta Sans
     - Buyer dashboard desktop content width: 1450px
     - Same reusable buyer navbar
     - Navbar categories hidden on this page
========================================================= --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ShopEase - Product Details</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/css/buyer/pages/product.css',
        'resources/js/buyer/pages/product.js',
    ])
</head>

<body class="buyer-product-page">

    {{-- SAME BUYER NAVBAR AS DASHBOARD/CART, BUT WITHOUT CATEGORY STRIP --}}
    @include('components.buyer.navbar', ['hideCategories' => true])

    @php
        $product = [
            'name' => '300000W Solar Light BUY1TAKE1 Outdoor LED Solar Floodlights Waterproof Street Lamp With solar panel',
            'price' => 219,
            'old_price' => 1699,
            'discount' => '-87%',
            'rating' => '4.5',
            'rating_count' => '10K+ Ratings',
            'sold' => '10K+ Sold',
            'shop' => 'DR Shop',
            'shop_status' => 'Active 4 Minutes Ago',
            'stock' => 11,
        ];

        $variations = [
            '5000W - Bright MAX',
            '8000W - Bright MAX',
            '10000W - Bright MAX',
            '20000W - Bright MAX',
            '30000W - Bright MAX',
            '50000W - Bright MAX',
            '80000W - Bright MAX',
            '100000W - Bright MAX',
        ];

        $recommendedProducts = [
            ['name' => 'Wireless Earbuds Pro',        'price' => '₱1,499.00', 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'earbuds'],
            ['name' => 'Smart Watch Series 8',        'price' => '₱1,499.00', 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'watch'],
            ['name' => 'Stylish Shoulder Bag',        'price' => '₱1,499.00', 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'bag'],
            ['name' => 'Running Shoes',               'price' => '₱1,499.00', 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'shoes'],
            ['name' => 'Skincare Set',                'price' => '₱1,499.00', 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'skincare'],
            ['name' => 'Kitchen Utensils Set',        'price' => '₱1,499.00', 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'cookware'],
        ];
    @endphp

    <main class="product-main">
        <div class="product-container">

            {{-- =====================================================
                 BREADCRUMB
            ====================================================== --}}
            @php
                /*
                 * Dynamic breadcrumb context.
                 *
                 * Default:
                 * Home > Product
                 *
                 * Optional URL parameters can add category levels:
                 * ?category=Electronics%20%26%20Gadgets
                 * &subcategory=Lighting
                 * &child=Outdoor%20Lights
                 *
                 * This keeps the breadcrumb from showing category levels
                 * when the buyer simply opened the product from Home,
                 * Cart, or a recommendation card.
                 */
                $breadcrumbItems = [];

                if (request()->filled('category')) {
                    $breadcrumbItems[] = request('category');
                }

                if (request()->filled('subcategory')) {
                    $breadcrumbItems[] = request('subcategory');
                }

                if (request()->filled('child')) {
                    $breadcrumbItems[] = request('child');
                }

                $breadcrumbProductName =
                    \Illuminate\Support\Str::limit(
                        $product['name'] ?? 'Product',
                        58
                    );
            @endphp

            <nav class="product-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/buyer/dashboard') }}" class="breadcrumb-home">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path
                            d="M3 10.5 12 3l9 7.5V21h-6v-6H9v6H3Z"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linejoin="round"
                        />
                    </svg>

                    Home
                </a>

                @foreach ($breadcrumbItems as $breadcrumbItem)
                    <span class="breadcrumb-separator" aria-hidden="true">›</span>

                    <span class="breadcrumb-category">
                        {{ $breadcrumbItem }}
                    </span>
                @endforeach

                <span class="breadcrumb-separator" aria-hidden="true">›</span>

                <span class="breadcrumb-current" aria-current="page">
                    {{ $breadcrumbProductName }}
                </span>
            </nav>

            {{-- =====================================================
                 MAIN PRODUCT CARD
            ====================================================== --}}
            <section class="product-hero-card">

                <div class="product-gallery">

                    <div class="gallery-thumbnails">

                        @for ($i = 0; $i < 5; $i++)
                            <button
                                type="button"
                                class="gallery-thumb {{ $i === 0 ? 'active' : '' }}"
                                data-gallery-index="{{ $i }}"
                                aria-label="View product image {{ $i + 1 }}"
                            >
                                <svg viewBox="0 0 160 120" class="solar-thumb-art" aria-hidden="true">
                                    <defs>
                                        <linearGradient id="thumbBg{{ $i }}" x1="0" x2="1">
                                            <stop offset="0%" stop-color="{{ $i % 2 === 0 ? '#D5D8D7' : '#1D252A' }}"/>
                                            <stop offset="100%" stop-color="{{ $i % 2 === 0 ? '#EEF0EF' : '#5A6469' }}"/>
                                        </linearGradient>
                                    </defs>

                                    <rect width="160" height="120" rx="8" fill="url(#thumbBg{{ $i }})"/>
                                    <ellipse cx="82" cy="97" rx="52" ry="9" fill="#101719" opacity=".25"/>
                                    <rect x="38" y="39" width="84" height="49" rx="6" fill="#1A1E20" stroke="#434B4F" stroke-width="3"/>
                                    <rect x="46" y="47" width="68" height="32" rx="3" fill="#E8E65F"/>
                                    <path d="M51 52h58M51 58h58M51 64h58M51 70h58"
                                          stroke="#F8F49A"
                                          stroke-width="2"
                                          opacity=".9"/>
                                    <path d="M54 47v32M65 47v32M76 47v32M87 47v32M98 47v32"
                                          stroke="#C5C353"
                                          stroke-width="1.4"
                                          opacity=".8"/>
                                    <path d="M45 87 30 106M115 87l15 19"
                                          stroke="#202427"
                                          stroke-width="6"
                                          stroke-linecap="round"/>
                                </svg>

                                @if ($i === 4)
                                    <span class="thumb-overlay">+3</span>
                                @endif
                            </button>
                        @endfor

                    </div>

                    <div class="gallery-main">

                        <button
                            type="button"
                            class="gallery-arrow gallery-arrow-left"
                            id="galleryPrev"
                            aria-label="Previous image"
                        >
                            ‹
                        </button>

                        <div class="main-product-visual" id="mainProductVisual">
                            <svg viewBox="0 0 760 620" class="main-solar-art" aria-hidden="true">
                                <defs>
                                    <linearGradient id="mainBackdrop" x1="0" x2="1" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#EEF0EF"/>
                                        <stop offset="100%" stop-color="#C7CBCB"/>
                                    </linearGradient>

                                    <linearGradient id="mainFrame" x1="0" x2="1">
                                        <stop offset="0%" stop-color="#171B1D"/>
                                        <stop offset="100%" stop-color="#303638"/>
                                    </linearGradient>

                                    <linearGradient id="ledGlow" x1="0" x2="0" y1="0" y2="1">
                                        <stop offset="0%" stop-color="#FFF9AA"/>
                                        <stop offset="100%" stop-color="#D7D34D"/>
                                    </linearGradient>
                                </defs>

                                <rect width="760" height="620" rx="12" fill="url(#mainBackdrop)"/>

                                <ellipse cx="392" cy="495" rx="250" ry="58" fill="#777B7A" opacity=".22"/>
                                <ellipse cx="390" cy="476" rx="236" ry="47" fill="#1D2020"/>

                                <g transform="translate(168 120)">
                                    <rect x="0" y="0" width="425" height="278" rx="18"
                                          fill="url(#mainFrame)"
                                          stroke="#0D0F10"
                                          stroke-width="11"/>

                                    <rect x="34" y="31" width="357" height="195" rx="8"
                                          fill="url(#ledGlow)"
                                          stroke="#EAE36C"
                                          stroke-width="5"/>

                                    @for ($r = 0; $r < 9; $r++)
                                        <line
                                            x1="45"
                                            y1="{{ 42 + ($r * 20) }}"
                                            x2="380"
                                            y2="{{ 42 + ($r * 20) }}"
                                            stroke="#FEFBB4"
                                            stroke-width="4"
                                            opacity=".9"
                                        />
                                    @endfor

                                    @for ($c = 0; $c < 14; $c++)
                                        <line
                                            x1="{{ 52 + ($c * 24) }}"
                                            y1="38"
                                            x2="{{ 52 + ($c * 24) }}"
                                            y2="220"
                                            stroke="#C2BE4C"
                                            stroke-width="2"
                                            opacity=".72"
                                        />
                                    @endfor

                                    <text x="206" y="257"
                                          text-anchor="middle"
                                          font-size="29"
                                          fill="#FFFFFF"
                                          font-family="Plus Jakarta Sans, sans-serif"
                                          font-style="italic">
                                        Solar Light
                                    </text>

                                    <path d="M19 278 -20 347"
                                          stroke="#252A2C"
                                          stroke-width="18"
                                          stroke-linecap="round"/>

                                    <path d="M406 278 447 347"
                                          stroke="#252A2C"
                                          stroke-width="18"
                                          stroke-linecap="round"/>
                                </g>
                            </svg>
                        </div>

                        <button
                            type="button"
                            class="gallery-arrow gallery-arrow-right"
                            id="galleryNext"
                            aria-label="Next image"
                        >
                            ›
                        </button>
                    </div>
                </div>

                <div class="product-info">

                    <span class="preferred-badge">Preferred</span>

                    <h1>{{ $product['name'] }}</h1>

                    <div class="product-meta-row">
                        <div class="product-rating-value">
                            <strong>{{ $product['rating'] }}</strong>
                            <span class="stars">★★★★★</span>
                        </div>

                        <span class="meta-divider"></span>

                        <span>{{ $product['rating_count'] }}</span>

                        <span class="meta-divider"></span>

                        <span>{{ $product['sold'] }}</span>

                        <span class="meta-divider"></span>

                        <button type="button" class="report-button">Report</button>
                    </div>

                    <div class="price-panel">
                        <strong>₱{{ number_format($product['price'], 0) }}</strong>
                        <del>₱{{ number_format($product['old_price'], 0) }}</del>
                        <span>{{ $product['discount'] }}</span>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Shipping</div>

                        <div class="shipping-copy">
                            <div class="shipping-main">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M3 6h11v10H3zM14 9h4l3 3v4h-7z"
                                          stroke="currentColor"
                                          stroke-width="1.7"
                                          stroke-linejoin="round"/>
                                    <circle cx="7" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/>
                                    <circle cx="18" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/>
                                </svg>

                                <span>5 - 7 Oct</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Shipping Guarantee</div>

                        <div class="guarantee-copy">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 3 5 6v5c0 4.5 2.8 8 7 10 4.2-2 7-5.5 7-10V6Z"
                                      stroke="currentColor"
                                      stroke-width="1.8"
                                      stroke-linejoin="round"/>
                                <path d="m8.5 11.5 2.2 2.2 4.8-5"
                                      stroke="currentColor"
                                      stroke-width="1.8"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>

                            <span>Fulfilled by ShopEase · Merchandise Protection ›</span>
                        </div>
                    </div>

                    <div class="info-row variations-row">
                        <div class="info-label">Variations</div>

                        <div class="variations-wrap">
                            @foreach ($variations as $index => $variation)
                                <button
                                    type="button"
                                    class="variation-button {{ $index === 4 ? 'selected' : '' }}"
                                >
                                    {{ $variation }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="info-row quantity-row">
                        <div class="info-label">Quantity</div>

                        <div class="quantity-wrap">
                            <div class="quantity-control">
                                <button type="button" id="quantityMinus">−</button>

                                <input
                                    type="number"
                                    id="productQuantity"
                                    value="1"
                                    min="1"
                                    max="{{ $product['stock'] }}"
                                    readonly
                                >

                                <button type="button" id="quantityPlus">+</button>
                            </div>

                            <span>{{ $product['stock'] }} In Stock</span>
                        </div>
                    </div>

                    <div class="product-action-row">
                        <button type="button" class="add-cart-button" id="addToCartButton">
                            <img src="{{ asset('icons/buyer/product-cart.png') }}" alt="">
                            Add to Cart
                        </button>

                        <button type="button" class="buy-now-button" id="buyNowButton">
                            Buy Now
                        </button>
                    </div>
                </div>

                <div class="product-trust-row">
                    <div>
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M12 3 5 6v5c0 4.5 2.8 8 7 10 4.2-2 7-5.5 7-10V6Z"
                                  stroke="currentColor"
                                  stroke-width="1.7"/>
                            <path d="m8.5 11.5 2.2 2.2 4.8-5"
                                  stroke="currentColor"
                                  stroke-width="1.7"/>
                        </svg>
                        <span>100% Authentic</span>
                    </div>

                    <div>
                        <svg viewBox="0 0 24 24" fill="none">
                            <rect x="4" y="7" width="16" height="13" rx="2"
                                  stroke="currentColor"
                                  stroke-width="1.7"/>
                            <path d="M8 7V5h8v2M8 12h8"
                                  stroke="currentColor"
                                  stroke-width="1.7"/>
                        </svg>
                        <span>Secure Payments</span>
                    </div>


                    <div>
                        <svg viewBox="0 0 24 24" fill="none">
                            <path d="M5 16v-5a7 7 0 0 1 14 0v5"
                                  stroke="currentColor"
                                  stroke-width="1.7"/>
                            <path d="M5 15H3v4h4v-4ZM19 15h2v4h-4v-4ZM9 21h6"
                                  stroke="currentColor"
                                  stroke-width="1.7"/>
                        </svg>
                        <span>Customer Support</span>
                    </div>
                </div>
            </section>

            {{-- =====================================================
                 SELLER CARD
            ====================================================== --}}
            <section class="seller-card">
                <div class="seller-left">
                    <div class="seller-logo">
                        DR
                        <small>OFFICIAL</small>
                    </div>

                    <div class="seller-main-copy">
                        <h2>{{ $product['shop'] }}</h2>
                        <p>{{ $product['shop_status'] }}</p>

                        <div class="seller-buttons">
                            <button type="button" class="chat-now-button">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M5 5h14v10H9l-4 4Z"
                                          stroke="currentColor"
                                          stroke-width="1.7"/>
                                </svg>
                                Chat Now
                            </button>

                            <button type="button" class="view-shop-button">
                                <svg viewBox="0 0 24 24" fill="none">
                                    <path d="M4 9h16l-1-5H5L4 9ZM5 9v10h14V9"
                                          stroke="currentColor"
                                          stroke-width="1.7"/>
                                </svg>
                                View Shop
                            </button>
                        </div>
                    </div>
                </div>

                <div class="seller-stats">
                    <div><span>Ratings</span><strong>49.2K</strong></div>
                    <div><span>Response Rate</span><strong>100%</strong></div>
                    <div><span>Joined</span><strong>4 years ago</strong></div>
                    <div><span>Products</span><strong>21</strong></div>
                    <div><span>Response Time</span><strong>within minutes</strong></div>
                    <div><span>Followers</span><strong>16.5K</strong></div>
                </div>
            </section>

            {{-- =====================================================
                 PRODUCT SPECIFICATIONS
            ====================================================== --}}
            <section class="product-section-card">
                <div class="product-section-heading">
                    <h2>Product Specifications</h2>
                </div>

                <div class="spec-grid">
                    <div class="spec-column">
                        <div><span>Category</span><strong>Electronics &amp; Gadgets › Lighting › Outdoor Lights</strong></div>
                        <div><span>Brand</span><strong>DR Shop</strong></div>
                        <div><span>Power</span><strong>300000W</strong></div>
                        <div><span>Number of LEDs</span><strong>114 LEDs</strong></div>
                        <div><span>Lamp Size</span><strong>145*118mm</strong></div>
                        <div><span>Panel Size</span><strong>220*140mm</strong></div>
                        <div><span>Battery</span><strong>6v6w 3000mAH</strong></div>
                        <div><span>Lighting Feature</span><strong>Heavy Duty</strong></div>
                    </div>

                    <div class="spec-column">
                        <div><span>Lighting Type</span><strong>Solar Lights</strong></div>
                        <div><span>Light Bulb Type</span><strong>LED</strong></div>
                        <div><span>Warranty Type</span><strong>Manufacturer Warranty</strong></div>
                        <div><span>Waterproof</span><strong>IP67</strong></div>
                        <div><span>Cable Length</span><strong>5M</strong></div>
                        <div><span>Smart Equipment</span><strong>Yes</strong></div>
                        <div><span>Country of Origin</span><strong>Malaysia</strong></div>
                        <div><span>Ships From</span><strong>Silang, Cavite</strong></div>
                    </div>
                </div>
            </section>

            {{-- =====================================================
                 DESCRIPTION
            ====================================================== --}}
            <section class="product-section-card">
                <div class="product-section-heading">
                    <h2>Product Description</h2>
                </div>

                <div class="product-description">
                    <p><strong>★★Welcome to DR OFFICIAL LED Store★★</strong></p>
                    <p>💞 Follow the store to get more real-time data of store products</p>
                    <p>🎁 The store is near Manila, and we will ship within 24 hours. You will receive the express package within 3-4 days.</p>
                    <p>💰 Payment method supports cash on delivery and online payment</p>
                    <p>👇 Attention 👇</p>
                    <p>👉 As long as you buy one get one free, you will get 2 solar lights.</p>
                    <p>👉 Other options only have 1 solar light.</p>
                    <p>==========================================</p>
                    <p>💖🔥🔥 About the 10-year warranty coverage:</p>
                    <p>❤️ Battery aging, solar panel aging, lamp bead damage, product appearance damage</p>
                    <p>💥🔥 About the problems when receiving the goods:</p>
                    <p>🧑 (After receiving the product, please install it and place it in the sun to charge before testing)</p>
                    <p>👉 If the product you received is cracked or damaged</p>
                    <p class="description-indent">Please take a picture and send it to the customer service lady, she will replace the product for you.</p>
                    <p>👉 If the product you received is missing a certain part</p>
                    <p class="description-indent">Please put all the items you received together, take photos and send them to customer service.</p>
                    <p>👉 If the solar light cannot be charged, please contact customer service immediately.</p>
                </div>
            </section>

            {{-- =====================================================
                 RATINGS / REVIEWS
            ====================================================== --}}
            <section class="product-section-card ratings-card">

                <div class="product-section-heading">
                    <h2>Product Ratings</h2>
                </div>

                <div class="ratings-summary">
                    <div class="rating-score-box">
                        <strong>4.5 <small>out of 5</small></strong>
                        <span>★★★★☆</span>
                    </div>

                    <div class="rating-filter-list">
                        <button type="button" class="rating-filter active" data-rating-filter="all">All</button>
                        <button type="button" class="rating-filter" data-rating-filter="5">5 Star (10K+)</button>
                        <button type="button" class="rating-filter" data-rating-filter="4">4 Star (1.6K)</button>
                        <button type="button" class="rating-filter" data-rating-filter="3">3 Star (1.5K)</button>
                        <button type="button" class="rating-filter" data-rating-filter="2">2 Star (369)</button>
                        <button type="button" class="rating-filter" data-rating-filter="1">1 Star (1.5K)</button>
                        <button type="button" class="rating-filter" data-rating-filter="comments">With Comments (3.4K)</button>
                        <button type="button" class="rating-filter" data-rating-filter="media">With Media (2.2K)</button>
                    </div>
                </div>

                <div class="review-list">

                    <div id="ratingsFilterEmpty" class="ratings-filter-empty" hidden>
                        No reviews match this filter yet.
                    </div>

                    <article class="review-item" data-rating="5" data-has-comments="true" data-has-media="true">
                        <div class="review-avatar">
                            <svg viewBox="0 0 48 48">
                                <circle cx="24" cy="24" r="24" fill="#DDE7F0"/>
                                <circle cx="24" cy="18" r="8" fill="#C78B69"/>
                                <path d="M13 39Q15 28 24 28T35 39" fill="#435661"/>
                                <path d="M16 15Q19 8 28 10Q34 11 34 18Q29 14 24 15Q20 15 16 15Z" fill="#2B2524"/>
                            </svg>
                        </div>

                        <div class="review-content">
                            <strong>a3lmnesSss</strong>
                            <div class="review-stars">★★★★★</div>
                            <small>2025-07-16 17:56 | Variation: 5000W-Bright MAX</small>

                            <p>
                                Suitability: easy to use<br>
                                Quality: tight bubble column wrapped items<br>
                                Appearance: 10 Star!
                            </p>

                            <p>
                                The logistics is fast, the packaging is tight, there is no damage, the quality is still very good,
                                and the customer service attitude is also very good. Rapid response, legal 16 hour endurance,
                                I'm very satisfied with the brightness, and it's worth appreciating.
                            </p>

                            <div class="review-media">
                                @for ($i = 0; $i < 6; $i++)
                                    <div class="review-media-box">
                                        <svg viewBox="0 0 120 80">
                                            <rect width="120" height="80" rx="6" fill="{{ $i % 2 === 0 ? '#D5D8D7' : '#293135' }}"/>
                                            <rect x="26" y="23" width="68" height="38" rx="4" fill="#1C2022"/>
                                            <rect x="33" y="29" width="54" height="23" rx="2" fill="#DED959"/>
                                        </svg>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </article>

                    <article class="review-item" data-rating="5" data-has-comments="true" data-has-media="false">
                        <div class="review-avatar">
                            <svg viewBox="0 0 48 48">
                                <circle cx="24" cy="24" r="24" fill="#F0E0D6"/>
                                <circle cx="24" cy="18" r="8" fill="#E0A27E"/>
                                <path d="M13 39Q15 28 24 28T35 39" fill="#1F2428"/>
                                <path d="M15 17Q15 8 24 8Q33 8 34 17Q29 12 24 13Q19 12 15 17Z" fill="#2C1718"/>
                            </svg>
                        </div>

                        <div class="review-content">
                            <strong>6dy4drexcel</strong>
                            <div class="review-stars">★★★★★</div>
                            <small>2025-07-16 17:50 | Variation: 5000W-Bright MAX</small>

                            <p>
                                Appearance: maganda... 100%<br>
                                Suitability: indoor n outdoor perfect use<br>
                                Quality: good quality and condition
                            </p>
                        </div>
                    </article>

                </div>
            </section>

            {{-- =====================================================
                 RECOMMENDED PRODUCTS
            ====================================================== --}}
            <section class="recommended-section">
                <div class="recommended-heading">
                    <h2>You May Also Like</h2>
                </div>

                <div class="product-grid">

                    @foreach ($recommendedProducts as $productCard)
                        <article class="product-card">
                            <div class="product-image-box">

                                @if ($productCard['art'] === 'earbuds')
                                    <svg class="product-art" viewBox="0 0 300 180" aria-hidden="true">
                                        <rect width="300" height="180" rx="12" fill="#E6EBE7"/>
                                        <ellipse cx="160" cy="145" rx="75" ry="13" fill="#7E8581" opacity=".16"/>
                                        <rect x="90" y="87" width="110" height="61" rx="25" fill="#2D3335"/>
                                        <rect x="111" y="39" width="19" height="72" rx="10" fill="#202426" transform="rotate(-10 111 39)"/>
                                        <rect x="166" y="35" width="19" height="74" rx="10" fill="#202426" transform="rotate(11 166 35)"/>
                                    </svg>

                                @elseif ($productCard['art'] === 'watch')
                                    <svg class="product-art" viewBox="0 0 300 180" aria-hidden="true">
                                        <rect width="300" height="180" rx="12" fill="#DCE7EE"/>
                                        <rect x="136" y="20" width="29" height="140" rx="14" fill="#252A2F"/>
                                        <rect x="100" y="43" width="102" height="97" rx="23" fill="#151B20" stroke="#58636A" stroke-width="4"/>
                                        <rect x="111" y="54" width="80" height="76" rx="16" fill="#10384B"/>
                                        <circle cx="151" cy="91" r="28" fill="#17475E"/>
                                    </svg>

                                @elseif ($productCard['art'] === 'bag')
                                    <svg class="product-art" viewBox="0 0 300 180" aria-hidden="true">
                                        <rect width="300" height="180" rx="12" fill="#EFE6D9"/>
                                        <path d="M103 79Q151 19 202 78" fill="none" stroke="#93633A" stroke-width="10" stroke-linecap="round"/>
                                        <path d="M89 75Q151 45 213 77L201 143Q151 158 102 143Z" fill="#C28E51" stroke="#855D39" stroke-width="4"/>
                                    </svg>

                                @elseif ($productCard['art'] === 'shoes')
                                    <svg class="product-art" viewBox="0 0 300 180" aria-hidden="true">
                                        <rect width="300" height="180" rx="12" fill="#EFEAE2"/>
                                        <path d="M70 106 105 70l38 18 25 30 62 12q14 3 16 16H82q-15-2-18-14Z" fill="#FAFAF8" stroke="#ABA59F" stroke-width="4"/>
                                        <path d="M108 73l39 18 25 28-67-8Z" fill="#30383E"/>
                                    </svg>

                                @elseif ($productCard['art'] === 'skincare')
                                    <svg class="product-art" viewBox="0 0 300 180" aria-hidden="true">
                                        <rect width="300" height="180" rx="12" fill="#F1DCD5"/>
                                        <rect x="82" y="66" width="40" height="78" rx="10" fill="#F6F2EA"/>
                                        <rect x="139" y="54" width="44" height="90" rx="11" fill="#E89C8B"/>
                                        <rect x="198" y="76" width="51" height="65" rx="16" fill="#F0D7CF"/>
                                    </svg>

                                @else
                                    <svg class="product-art" viewBox="0 0 300 180" aria-hidden="true">
                                        <rect width="300" height="180" rx="12" fill="#EDE7DE"/>
                                        <ellipse cx="121" cy="116" rx="50" ry="32" fill="#716256"/>
                                        <ellipse cx="121" cy="104" rx="44" ry="25" fill="#302B26"/>
                                        <path d="M168 105 229 88q15-4 20 5v8l-75 21Z" fill="#66594E"/>
                                    </svg>
                                @endif

                            </div>

                            <h3>{{ $productCard['name'] }}</h3>

                            <p class="product-rating">
                                <span class="rating-star">★</span>
                                {{ $productCard['rating'] }}
                                <span class="sold-count">({{ $productCard['sold'] }})</span>
                            </p>

                            <div class="product-bottom-row">
                                <strong>{{ $productCard['price'] }}</strong>

                                <button
                                    type="button"
                                    class="product-cart-button"
                                    aria-label="Add {{ $productCard['name'] }} to cart"
                                >
                                    <img
                                        src="{{ asset('icons/buyer/product-cart.png') }}"
                                        alt=""
                                    >
                                </button>
                            </div>
                        </article>
                    @endforeach

                </div>
            </section>

        </div>
    </main>


    {{-- Shared Buyer Footer --}}
    @include('components.buyer.footer')


    {{-- Shared Buyer Floating Chat --}}
    @include('components.buyer.floating-chat')

</body>
</html>
