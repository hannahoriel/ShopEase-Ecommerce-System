{{-- =========================================================
     ShopEase Buyer Cart
     resources/views/pages/buyer/cart.blade.php

     Based on the existing buyer dashboard sizing/content coverage:
     - Plus Jakarta Sans
     - max-width: 1660px
     - fixed buyer navbar component
     - compact desktop spacing
========================================================= --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ShopEase - Shopping Cart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/css/buyer/pages/cart.css',
        'resources/js/buyer/pages/cart.js',
    ])
</head>

<body
    class="buyer-cart-page"
    data-checkout-url="{{ route('buyer.checkout') }}"
>

    {{-- Existing Buyer Navbar --}}
    @include('components.buyer.navbar', ['hideCategories' => true])

    @php
        $cartGroups = [
            [
                'shop' => 'Apple Services',
                'items' => [
                    [
                        'id' => 1,
                        'product_code' => 'SE-EB-001',
                        'name' => 'Wireless Earbuds Pro',
                        'color' => 'White',
                        'variant' => 'Standard',
                        'available_colors' => ['White', 'Black', 'Midnight Blue'],
                        'available_variants' => ['Standard', 'Pro', 'Pro Max'],
                        'price' => 1499,
                        'quantity' => 1,
                        'art' => 'earbuds',
                    ],
                ],
            ],
            [
                'shop' => 'Huawei',
                'items' => [
                    [
                        'id' => 2,
                        'product_code' => 'SE-WT-002',
                        'name' => 'Wireless Earbuds Pro',
                        'color' => 'White',
                        'variant' => 'Standard',
                        'available_colors' => ['White', 'Black', 'Silver'],
                        'available_variants' => ['40mm', '44mm', '46mm'],
                        'price' => 1499,
                        'quantity' => 1,
                        'art' => 'watch',
                    ],
                ],
            ],
            [
                'shop' => 'Bags MUMU',
                'items' => [
                    [
                        'id' => 3,
                        'product_code' => 'SE-BG-003',
                        'name' => 'Wireless Earbuds Pro',
                        'color' => 'White',
                        'variant' => 'Standard',
                        'available_colors' => ['White', 'Brown', 'Black'],
                        'available_variants' => ['Small', 'Medium', 'Large'],
                        'price' => 1499,
                        'quantity' => 1,
                        'art' => 'bag',
                    ],
                ],
            ],
        ];

        $recommendedProducts = [
            ['name' => 'Wireless Earbuds Pro', 'price' => 1499, 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'earbuds'],
            ['name' => 'Wireless Earbuds Pro', 'price' => 1499, 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'watch'],
            ['name' => 'Wireless Earbuds Pro', 'price' => 1499, 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'bag'],
            ['name' => 'Wireless Earbuds Pro', 'price' => 1499, 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'shoes'],
            ['name' => 'Wireless Earbuds Pro', 'price' => 1499, 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'skincare'],
            ['name' => 'Wireless Earbuds Pro', 'price' => 1499, 'rating' => '4.8', 'sold' => '1.2k sold', 'art' => 'cookware'],
        ];
    @endphp

    <main class="cart-main">
        <div class="cart-container">

            {{-- PAGE TITLE --}}
            <section class="cart-page-heading">
                <h1>Shopping Cart</h1>

                <nav class="cart-breadcrumbs" aria-label="Breadcrumb">
                    <a
                        href="{{ url('/buyer/dashboard') }}"
                        class="cart-breadcrumb-home"
                    >
                        Home
                    </a>

                    <span class="cart-breadcrumb-separator" aria-hidden="true">›</span>

                    <span class="cart-breadcrumb-current" aria-current="page">
                        Cart
                    </span>
                </nav>
            </section>

            {{-- MAIN TWO-COLUMN AREA --}}
            <section class="cart-layout">

                {{-- LEFT: CART --}}
                <div class="cart-left-column">

                    {{-- TABLE HEADER --}}
                    <div class="cart-table-header">
                        <div class="cart-col-product">
                            <label class="cart-check-wrap" aria-label="Select all items">
                                <input
                                    type="checkbox"
                                    id="selectAllCart"
                                    class="cart-checkbox"
                                    checked
                                >
                                <span class="cart-checkmark"></span>
                            </label>

                            <span>Product</span>
                        </div>

                        <div class="cart-col-quantity">Quantity</div>
                        <div class="cart-col-total">Total Price</div>
                        <div class="cart-col-action"></div>
                    </div>

                    {{-- SHOP GROUPS --}}
                    <div id="cartGroups" class="cart-groups">

                        @foreach ($cartGroups as $groupIndex => $group)
                            <section
                                class="cart-shop-card"
                                data-shop-index="{{ $groupIndex }}"
                            >
                                <div class="cart-shop-header">

                                    <label class="cart-check-wrap" aria-label="Select {{ $group['shop'] }}">
                                        <input
                                            type="checkbox"
                                            class="cart-checkbox shop-checkbox"
                                            checked
                                        >
                                        <span class="cart-checkmark"></span>
                                    </label>

                                    <span class="cart-shop-icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24" fill="none">
                                            <path
                                                d="M4 9h16l-1-5H5L4 9Z"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linejoin="round"
                                            />
                                            <path
                                                d="M5 9v10h14V9"
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

                                    <span class="cart-shop-name">
                                        {{ $group['shop'] }}
                                    </span>
                                </div>

                                @foreach ($group['items'] as $item)
                                    <article
                                        class="cart-item-row"
                                        data-cart-item
                                        data-item-id="{{ $item['id'] }}"
                                        data-product-code="{{ $item['product_code'] }}"
                                        data-product-url="{{ route('buyer.product', ['code' => $item['product_code']]) }}"
                                        data-price="{{ $item['price'] }}"
                                    >
                                        <div class="cart-item-product">

                                            <label class="cart-check-wrap" aria-label="Select {{ $item['name'] }}">
                                                <input
                                                    type="checkbox"
                                                    class="cart-checkbox item-checkbox"
                                                    checked
                                                >
                                                <span class="cart-checkmark"></span>
                                            </label>

                                            <a
                                                href="{{ route('buyer.product', ['code' => $item['product_code']]) }}"
                                                class="cart-product-image cart-product-link"
                                                aria-label="Open {{ $item['name'] }}"
                                            >

                                                @if ($item['art'] === 'earbuds')
                                                    <svg class="cart-product-art" viewBox="0 0 180 150" aria-hidden="true">
                                                        <rect width="180" height="150" rx="14" fill="#E9E8E2"/>
                                                        <ellipse cx="91" cy="121" rx="50" ry="10" fill="#8C8B85" opacity=".16"/>
                                                        <rect x="52" y="69" width="75" height="49" rx="20" fill="#2E3133"/>
                                                        <rect x="57" y="74" width="65" height="35" rx="16" fill="#494C4E"/>
                                                        <rect x="60" y="39" width="14" height="50" rx="7" fill="#222628" transform="rotate(-10 60 39)"/>
                                                        <circle cx="68" cy="77" r="4" fill="#828688"/>
                                                        <rect x="108" y="35" width="14" height="52" rx="7" fill="#222628" transform="rotate(12 108 35)"/>
                                                        <circle cx="116" cy="75" r="4" fill="#828688"/>
                                                    </svg>

                                                @elseif ($item['art'] === 'watch')
                                                    <svg class="cart-product-art" viewBox="0 0 180 150" aria-hidden="true">
                                                        <defs>
                                                            <linearGradient id="watchFaceCart" x1="0" x2="1">
                                                                <stop offset="0%" stop-color="#0D2634"/>
                                                                <stop offset="100%" stop-color="#27495C"/>
                                                            </linearGradient>
                                                        </defs>
                                                        <rect width="180" height="150" rx="14" fill="#E7ECEF"/>
                                                        <rect x="79" y="17" width="24" height="116" rx="12" fill="#23282D"/>
                                                        <rect x="52" y="39" width="78" height="76" rx="18" fill="#171C21" stroke="#56616A" stroke-width="3"/>
                                                        <rect x="59" y="46" width="64" height="62" rx="14" fill="url(#watchFaceCart)"/>
                                                        <circle cx="91" cy="77" r="21" fill="#163545"/>
                                                        <path d="M91 60v18l12 7" fill="none" stroke="#65D8DF" stroke-width="3" stroke-linecap="round"/>
                                                        <path d="M71 96Q91 84 112 95" fill="none" stroke="#FFB64C" stroke-width="4"/>
                                                    </svg>

                                                @else
                                                    <svg class="cart-product-art" viewBox="0 0 180 150" aria-hidden="true">
                                                        <rect width="180" height="150" rx="14" fill="#F1E9DE"/>
                                                        <ellipse cx="92" cy="125" rx="53" ry="10" fill="#7F644B" opacity=".16"/>
                                                        <path d="M58 65Q91 19 126 65" fill="none" stroke="#95653A" stroke-width="8" stroke-linecap="round"/>
                                                        <path d="M47 63Q90 42 132 65L124 120Q92 132 56 120Z" fill="#BF8E54" stroke="#8B623B" stroke-width="3"/>
                                                        <path d="M61 68Q91 81 119 68" fill="none" stroke="#E0BD8D" stroke-width="3"/>
                                                        <circle cx="91" cy="92" r="6" fill="#86603D"/>
                                                    </svg>
                                                @endif

                                            </a>

                                            <div class="cart-product-copy">
                                                <a
                                                    href="{{ route('buyer.product', ['code' => $item['product_code']]) }}"
                                                    class="cart-product-title-link"
                                                >
                                                    <h3>{{ $item['name'] }}</h3>
                                                </a>

                                                <div class="cart-selected-variation">
                                                    <p>
                                                        <span class="cart-selected-color">Color: {{ $item['color'] }}</span>
                                                        <span class="cart-selected-variant">Variant: {{ $item['variant'] }}</span>
                                                    </p>

                                                    <button
                                                        type="button"
                                                        class="edit-variation-button"
                                                        aria-expanded="false"
                                                    >
                                                        Edit
                                                    </button>
                                                </div>

                                                <div class="cart-variation-editor" hidden>
                                                    <label>
                                                        <span>Color</span>
                                                        <select class="cart-color-select">
                                                            @foreach ($item['available_colors'] as $colorOption)
                                                                <option
                                                                    value="{{ $colorOption }}"
                                                                    {{ $colorOption === $item['color'] ? 'selected' : '' }}
                                                                >
                                                                    {{ $colorOption }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </label>

                                                    <label>
                                                        <span>Variant</span>
                                                        <select class="cart-variant-select">
                                                            @foreach ($item['available_variants'] as $variantOption)
                                                                <option
                                                                    value="{{ $variantOption }}"
                                                                    {{ $variantOption === $item['variant'] ? 'selected' : '' }}
                                                                >
                                                                    {{ $variantOption }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </label>

                                                    <div class="cart-variation-editor-actions">
                                                        <button type="button" class="variation-cancel-button">
                                                            Cancel
                                                        </button>
                                                        <button type="button" class="variation-save-button">
                                                            Save
                                                        </button>
                                                    </div>
                                                </div>

                                                <strong>
                                                    ₱{{ number_format($item['price'], 2) }}
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="cart-item-quantity">
                                            <div class="quantity-control">
                                                <button
                                                    type="button"
                                                    class="quantity-button quantity-minus"
                                                    aria-label="Decrease quantity"
                                                >
                                                    −
                                                </button>

                                                <input
                                                    type="number"
                                                    class="quantity-input"
                                                    value="{{ $item['quantity'] }}"
                                                    min="1"
                                                    max="99"
                                                    readonly
                                                >

                                                <button
                                                    type="button"
                                                    class="quantity-button quantity-plus"
                                                    aria-label="Increase quantity"
                                                >
                                                    +
                                                </button>
                                            </div>
                                        </div>

                                        <div class="cart-item-total">
                                            ₱{{ number_format($item['price'] * $item['quantity'], 2) }}
                                        </div>

                                        <div class="cart-item-action">
                                            <button
                                                type="button"
                                                class="remove-cart-item"
                                                aria-label="Remove {{ $item['name'] }}"
                                            >
                                                <svg viewBox="0 0 24 24" fill="none">
                                                    <path
                                                        d="M4 7h16M9 7V4h6v3m-8 0 1 13h8l1-13M10 11v5m4-5v5"
                                                        stroke="currentColor"
                                                        stroke-width="1.7"
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                    />
                                                </svg>
                                            </button>
                                        </div>
                                    </article>
                                @endforeach
                            </section>
                        @endforeach

                    </div>
                </div>

                {{-- RIGHT: ORDER SUMMARY --}}
                <aside class="order-summary-card">
                    <div class="order-summary-header">
                        <h2>Order Summary</h2>
                    </div>

                    <div class="order-summary-body">

                        {{-- REAL-TIME SELECTED PRODUCTS --}}
                        <div
                            id="selectedProductsSummary"
                            class="selected-products-summary"
                            aria-live="polite"
                        ></div>

                        <div class="summary-divider"></div>

                        <div class="summary-row">
                            <span id="subtotalLabel">Subtotal (3 items)</span>
                            <strong id="subtotalValue">₱4,497.00</strong>
                        </div>

                        <div class="summary-row">
                            <span>Shipping Fee</span>
                            <strong id="shippingValue">₱80.00</strong>
                        </div>

                        <div class="summary-spacer"></div>

                        <div class="summary-total-box">
                            <span>Total</span>
                            <strong id="grandTotalValue">₱4,577.00</strong>
                        </div>

                        <div class="summary-payment-row">
                            <span>Payment Method</span>
                            <strong>Cash on Delivery</strong>
                        </div>

                        <button
                            type="button"
                            id="proceedCheckoutButton"
                            class="checkout-button"
                        >
                            Proceed to Checkout
                        </button>
                    </div>
                </aside>

            </section>

            {{-- YOU MAY ALSO LIKE --}}
            <section class="also-like-section">
                <h2>You May Also Like</h2>

                <div class="also-like-grid">
                    @foreach ($recommendedProducts as $product)
                        <a
    href="{{ route('buyer.product') }}"
    class="recommendation-card"
>
                            <div class="recommendation-image">

                                @if ($product['art'] === 'earbuds')
                                    <svg viewBox="0 0 240 180" class="recommendation-art" aria-hidden="true">
                                        <rect width="240" height="180" rx="16" fill="#EBECE8"/>
                                        <ellipse cx="122" cy="145" rx="67" ry="13" fill="#7C807C" opacity=".14"/>
                                        <rect x="70" y="91" width="100" height="55" rx="24" fill="#2B3032"/>
                                        <rect x="79" y="36" width="18" height="74" rx="9" fill="#1C2224" transform="rotate(-12 79 36)"/>
                                        <rect x="144" y="31" width="18" height="75" rx="9" fill="#1C2224" transform="rotate(12 144 31)"/>
                                        <circle cx="88" cy="90" r="6" fill="#7F8588"/>
                                        <circle cx="153" cy="87" r="6" fill="#7F8588"/>
                                    </svg>

                                @elseif ($product['art'] === 'watch')
                                    <svg viewBox="0 0 240 180" class="recommendation-art" aria-hidden="true">
                                        <rect width="240" height="180" rx="16" fill="#DDE7ED"/>
                                        <rect x="104" y="18" width="32" height="145" rx="16" fill="#252B31"/>
                                        <rect x="67" y="43" width="107" height="100" rx="24" fill="#111820" stroke="#56616B" stroke-width="4"/>
                                        <rect x="76" y="53" width="89" height="80" rx="17" fill="#123242"/>
                                        <circle cx="121" cy="94" r="30" fill="#163E52"/>
                                        <path d="M121 69v25l18 10" fill="none" stroke="#68D8DF" stroke-width="4" stroke-linecap="round"/>
                                        <path d="M92 120Q121 103 150 118" fill="none" stroke="#F6AD42" stroke-width="5"/>
                                    </svg>

                                @elseif ($product['art'] === 'bag')
                                    <svg viewBox="0 0 240 180" class="recommendation-art" aria-hidden="true">
                                        <rect width="240" height="180" rx="16" fill="#F1E8DA"/>
                                        <ellipse cx="124" cy="151" rx="72" ry="12" fill="#7A6047" opacity=".15"/>
                                        <path d="M77 79Q121 23 166 79" fill="none" stroke="#93643A" stroke-width="10" stroke-linecap="round"/>
                                        <path d="M63 75Q122 48 181 76L170 144Q123 157 75 143Z" fill="#C28F53" stroke="#8D643B" stroke-width="4"/>
                                        <path d="M83 83Q123 101 162 83" fill="none" stroke="#E2BE8A" stroke-width="4"/>
                                    </svg>

                                @elseif ($product['art'] === 'shoes')
                                    <svg viewBox="0 0 240 180" class="recommendation-art" aria-hidden="true">
                                        <rect width="240" height="180" rx="16" fill="#ECE8E1"/>
                                        <ellipse cx="132" cy="145" rx="83" ry="13" fill="#77716B" opacity=".16"/>
                                        <path d="M55 112 90 75l40 17 26 29 53 10q13 3 14 17H67q-15-2-17-15Z" fill="#F8F8F6" stroke="#AAA59F" stroke-width="4"/>
                                        <path d="M94 77l39 18 23 27-67-9Z" fill="#2C343A"/>
                                        <path d="M73 127q68 12 143 10" fill="none" stroke="#5B6972" stroke-width="5"/>
                                    </svg>

                                @elseif ($product['art'] === 'skincare')
                                    <svg viewBox="0 0 240 180" class="recommendation-art" aria-hidden="true">
                                        <rect width="240" height="180" rx="16" fill="#E8D4CA"/>
                                        <ellipse cx="123" cy="148" rx="79" ry="12" fill="#8D6B63" opacity=".16"/>
                                        <rect x="70" y="73" width="38" height="70" rx="10" fill="#F6F1E8" stroke="#C6B8AE" stroke-width="3"/>
                                        <rect x="76" y="57" width="26" height="19" rx="5" fill="#D6CCC4"/>
                                        <rect x="118" y="61" width="42" height="82" rx="11" fill="#E99C8B" stroke="#C77869" stroke-width="3"/>
                                        <rect x="127" y="47" width="24" height="18" rx="5" fill="#D8A99F"/>
                                        <rect x="170" y="82" width="46" height="58" rx="14" fill="#F0D7CE" stroke="#CBAAA1" stroke-width="3"/>
                                    </svg>

                                @else
                                    <svg viewBox="0 0 240 180" class="recommendation-art" aria-hidden="true">
                                        <rect width="240" height="180" rx="16" fill="#ECE6DD"/>
                                        <ellipse cx="125" cy="147" rx="80" ry="12" fill="#65584F" opacity=".16"/>
                                        <ellipse cx="96" cy="115" rx="45" ry="29" fill="#6F6256" stroke="#453C34" stroke-width="4"/>
                                        <ellipse cx="96" cy="104" rx="39" ry="23" fill="#302A25"/>
                                        <path d="M137 106 194 88q14-4 18 5v8l-69 20Z" fill="#67594D" stroke="#453A32" stroke-width="4"/>
                                        <ellipse cx="177" cy="77" rx="30" ry="14" fill="#76685C" stroke="#4C433A" stroke-width="4"/>
                                    </svg>
                                @endif

                            </div>

                            <div class="recommendation-copy">
                                <h3>{{ $product['name'] }}</h3>

                                <div class="recommendation-rating">
                                    <span class="recommendation-star">★</span>
                                    <span>{{ $product['rating'] }}</span>
                                    <span class="recommendation-sold">
                                        ({{ $product['sold'] }})
                                    </span>
                                </div>

                                <div class="recommendation-bottom">
                                    <strong>
                                        ₱{{ number_format($product['price'], 2) }}
                                    </strong>

                                    <button
                                        type="button"
                                        class="recommendation-cart-button"
                                        aria-label="Add {{ $product['name'] }} to cart"
                                    >
                                        <img
                                            src="{{ asset('icons/buyer/product-cart.png') }}"
                                            alt=""
                                        >
                                    </button>
                                </div>
                            </div>
                        </a>
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
