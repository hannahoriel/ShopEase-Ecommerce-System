{{-- =========================================================
     ShopEase Buyer Checkout
     Suggested path:
     resources/views/pages/buyer/checkout.blade.php

     Notes:
     - Custom checkout header (does NOT include buyer navbar)
     - Header copies the buyer navbar shape, gradient, logo sizing,
       and logo hover behavior
     - Uses buyer dashboard container sizing / Plus Jakarta Sans
     - Includes buyer footer + floating chat
     - First-entry address flow uses localStorage for UI demo only
========================================================= --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ShopEase - Checkout</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    @vite([
        'resources/css/app.css',
        'resources/css/buyer/pages/checkout.css',
        'resources/js/buyer/pages/checkout.js',
    ])
</head>

    @php
        /*
         * Temporary UI data.
         * Replace these arrays with your authenticated buyer/address/order
         * records once the checkout backend is connected.
         */
        $registrationAddress = [
            'name' => 'Buyer',
            'phone' => '+63 912 345 6789',
            'address' => 'House No., Street, Barangay, City, Province 0000',
        ];

        $checkoutGroups = [
            [
                'shop' => 'The Shop PH',
                'shipping' => 'Standard Local',
                'delivery' => 'Oct 7–8, 2026',
                'shipping_fee' => 58,
                'items' => [
                    [
                        'name' => 'Wireless Earbuds Pro',
                        'variant' => 'White · Standard',
                        'price' => 1499,
                        'quantity' => 1,
                        'art' => 'earbuds',
                    ],
                    [
                        'name' => 'Portable Charging Case',
                        'variant' => 'White',
                        'price' => 399,
                        'quantity' => 1,
                        'art' => 'case',
                    ],
                ],
            ],
            [
                'shop' => 'Tech Haven',
                'shipping' => 'Standard Local',
                'delivery' => 'Oct 8–9, 2026',
                'shipping_fee' => 65,
                'items' => [
                    [
                        'name' => 'Smart Watch Series 8',
                        'variant' => 'Black · 44mm',
                        'price' => 2199,
                        'quantity' => 1,
                        'art' => 'watch',
                    ],
                ],
            ],
        ];

        $merchandiseSubtotal = collect($checkoutGroups)
            ->flatMap(fn ($group) => $group['items'])
            ->sum(fn ($item) => $item['price'] * $item['quantity']);

        $shippingSubtotal = collect($checkoutGroups)
            ->sum('shipping_fee');

        $totalPayment = $merchandiseSubtotal + $shippingSubtotal;
    @endphp

<body
    class="checkout-page"
    data-my-purchases-url="{{ route('buyer.my-purchases') }}"
    data-registration-address-name="{{ $registrationAddress['name'] }}"
    data-registration-address-phone="{{ $registrationAddress['phone'] }}"
    data-registration-address-text="{{ $registrationAddress['address'] }}"
>



    {{-- =========================================================
         CUSTOM CHECKOUT HEADER
         Intentionally NOT fetching components.buyer.navbar
    ========================================================== --}}
    <header class="checkout-header">
        <div class="checkout-header-inner">
            <a
                href="{{ url('/buyer/dashboard') }}"
                class="checkout-logo-link"
                aria-label="ShopEase Home"
            >
                <img
                    src="{{ asset('icons/admin/dashboard/sidebar&navbar/shopease.png') }}"
                    alt="ShopEase"
                    class="checkout-logo-image"
                >
            </a>

            <span class="checkout-header-divider" aria-hidden="true"></span>

            <span class="checkout-header-label">Checkout</span>
        </div>
    </header>

    {{-- =========================================================
         CHECKOUT CONTENT
    ========================================================== --}}
    <div id="checkoutPageContent" class="checkout-page-content">

        <main class="checkout-main">
            <div class="checkout-container">

                {{-- DELIVERY ADDRESS --}}
                <section class="checkout-card address-card">
                    <div class="address-accent" aria-hidden="true"></div>

                    <div class="address-heading">
                        <span class="address-heading-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linejoin="round"
                                />
                                <circle
                                    cx="12"
                                    cy="10"
                                    r="2.2"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                />
                            </svg>
                        </span>

                        <h1>Delivery Address</h1>
                    </div>

                    <div class="address-content">
                        <div class="address-person">
                            <strong id="checkoutAddressName">
                                {{ $registrationAddress['name'] }}
                            </strong>

                            <span id="checkoutAddressPhone">
                                {{ $registrationAddress['phone'] }}
                            </span>
                        </div>

                        <p id="checkoutAddressText">
                            {{ $registrationAddress['address'] }}
                        </p>

                        <span class="default-address-badge">Default</span>

                        <button
                            type="button"
                            class="text-action-button"
                            id="changeAddressButton"
                        >
                            Change
                        </button>
                    </div>
                </section>

                {{-- PRODUCTS ORDERED --}}
                <section class="checkout-card products-card">

                    <div class="products-table-heading">
                        <div>Products Ordered</div>
                        <div>Unit Price</div>
                        <div>Quantity</div>
                        <div>Item Subtotal</div>
                    </div>

                    @foreach ($checkoutGroups as $groupIndex => $group)
                        <section
                            class="checkout-shop-group"
                            data-checkout-shop="{{ $groupIndex }}"
                        >
                            <div class="checkout-shop-header">
                                <div class="checkout-shop-name-wrap">
                                    <span class="checkout-shop-icon" aria-hidden="true">
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

                                    <strong>{{ $group['shop'] }}</strong>
                                </div>

                                <button type="button" class="shop-chat-button">
                                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path
                                            d="M5 5h14v10H9l-4 4V5Z"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                    Chat Now
                                </button>
                            </div>

                            @foreach ($group['items'] as $item)
                                <article class="checkout-product-row">

                                    <div class="checkout-product-cell">
                                        <div class="checkout-product-image" aria-hidden="true">

                                            @if ($item['art'] === 'earbuds')
                                                <svg viewBox="0 0 130 104">
                                                    <rect width="130" height="104" rx="10" fill="#ECEDEA"/>
                                                    <ellipse cx="65" cy="83" rx="35" ry="7" fill="#838681" opacity=".15"/>
                                                    <rect x="40" y="50" width="52" height="36" rx="15" fill="#292E30"/>
                                                    <rect x="44" y="21" width="11" height="44" rx="6" fill="#202426" transform="rotate(-10 44 21)"/>
                                                    <rect x="78" y="19" width="11" height="45" rx="6" fill="#202426" transform="rotate(12 78 19)"/>
                                                </svg>

                                            @elseif ($item['art'] === 'watch')
                                                <svg viewBox="0 0 130 104">
                                                    <rect width="130" height="104" rx="10" fill="#E4EBEF"/>
                                                    <rect x="57" y="9" width="18" height="86" rx="9" fill="#262B2F"/>
                                                    <rect x="37" y="25" width="58" height="54" rx="13" fill="#151B20"/>
                                                    <rect x="42" y="30" width="48" height="44" rx="10" fill="#14384A"/>
                                                    <circle cx="66" cy="52" r="14" fill="#1B526A"/>
                                                </svg>

                                            @else
                                                <svg viewBox="0 0 130 104">
                                                    <rect width="130" height="104" rx="10" fill="#F2EEE9"/>
                                                    <rect x="34" y="38" width="62" height="42" rx="12" fill="#E8E3DE" stroke="#B8AFA9" stroke-width="2"/>
                                                    <rect x="42" y="47" width="46" height="23" rx="7" fill="#F8F5F1"/>
                                                    <circle cx="65" cy="59" r="4" fill="#8B827E"/>
                                                </svg>
                                            @endif

                                        </div>

                                        <div class="checkout-product-copy">
                                            <strong>{{ $item['name'] }}</strong>
                                            <span>Variation: {{ $item['variant'] }}</span>
                                        </div>
                                    </div>

                                    <div class="checkout-unit-price">
                                        ₱{{ number_format($item['price'], 2) }}
                                    </div>

                                    <div class="checkout-quantity">
                                        {{ $item['quantity'] }}
                                    </div>

                                    <div class="checkout-item-subtotal">
                                        ₱{{ number_format(
                                            $item['price'] * $item['quantity'],
                                            2
                                        ) }}
                                    </div>
                                </article>
                            @endforeach

                            <div class="shop-checkout-options">

                                <label class="seller-message-field">
                                    <span>Message for Seller:</span>

                                    <input
                                        type="text"
                                        placeholder="Please leave a message..."
                                        maxlength="160"
                                    >
                                </label>

                                <div class="shipping-option">
                                    <div>
                                        <strong>Shipping Option</strong>
                                        <span>{{ $group['delivery'] }}</span>
                                        <small>{{ $group['shipping'] }}</small>
                                    </div>

                                    <button type="button" class="text-action-button">
                                        Change
                                    </button>

                                    <strong class="shipping-fee">
                                        ₱{{ number_format($group['shipping_fee'], 2) }}
                                    </strong>
                                </div>
                            </div>

                            <div class="shop-total-row">
                                <span>
                                    Order Total
                                    ({{ collect($group['items'])->sum('quantity') }}
                                    {{ collect($group['items'])->sum('quantity') === 1 ? 'item' : 'items' }}):
                                </span>

                                <strong>
                                    ₱{{ number_format(
                                        collect($group['items'])->sum(
                                            fn ($item) =>
                                                $item['price'] * $item['quantity']
                                        ) + $group['shipping_fee'],
                                        2
                                    ) }}
                                </strong>
                            </div>
                        </section>
                    @endforeach
                </section>

                {{-- PAYMENT --}}
                <section class="checkout-card payment-card">

                    <div class="payment-heading">
                        <h2>Payment Method</h2>

                        <div
                            class="payment-options"
                            aria-label="Payment Method"
                        >
                            <button
                                type="button"
                                class="payment-option is-selected"
                                data-payment-method="Cash on Delivery"
                                aria-pressed="true"
                            >
                                Cash on Delivery
                            </button>
                        </div>
                    </div>

                    <div class="selected-payment-method">
                        <span id="paymentMethodLabel">Cash on Delivery</span>

                        <small>
                            Pay when your order arrives.
                        </small>
                    </div>

                    <div class="checkout-summary">
                        <div class="checkout-summary-row">
                            <span>Merchandise Subtotal</span>
                            <strong>
                                ₱{{ number_format($merchandiseSubtotal, 2) }}
                            </strong>
                        </div>

                        <div class="checkout-summary-row">
                            <span>Shipping Subtotal</span>
                            <strong>
                                ₱{{ number_format($shippingSubtotal, 2) }}
                            </strong>
                        </div>

                        <div class="checkout-summary-row checkout-summary-total">
                            <span>Total Payment</span>
                            <strong>
                                ₱{{ number_format($totalPayment, 2) }}
                            </strong>
                        </div>
                    </div>

                    <div class="place-order-row">
                        <p>
                            By placing your order, you agree to ShopEase's
                            checkout and delivery terms.
                        </p>

                        <button
                            type="button"
                            id="placeOrderButton"
                            class="place-order-button"
                        >
                            Place Order
                        </button>
                    </div>
                </section>

            </div>
        </main>

        {{-- Shared Buyer Footer --}}
        @include('components.buyer.footer')
    </div>

    {{-- =========================================================
         FIRST-ENTRY ADDRESS MODAL
    ========================================================== --}}
    <div
        id="registrationAddressModal"
        class="checkout-modal-backdrop"
        hidden
    >
        <section
            class="checkout-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="registrationAddressModalTitle"
        >
            <div class="modal-icon modal-icon-address" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <path
                        d="M12 21s6-5.1 6-11a6 6 0 1 0-12 0c0 5.9 6 11 6 11Z"
                        stroke="currentColor"
                        stroke-width="1.8"
                        stroke-linejoin="round"
                    />
                    <circle
                        cx="12"
                        cy="10"
                        r="2.2"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                </svg>
            </div>

            <h2 id="registrationAddressModalTitle">
                Use your registration address?
            </h2>

            <p class="modal-description">
                We found the address saved when your ShopEase account was created.
                Would you like to use it for this order?
            </p>

            <div class="modal-address-preview">
                <strong>{{ $registrationAddress['name'] }}</strong>
                <span>{{ $registrationAddress['phone'] }}</span>
                <p>{{ $registrationAddress['address'] }}</p>
            </div>

            <div class="modal-actions">
                <button
                    type="button"
                    id="useRegistrationAddressNo"
                    class="modal-button modal-button-secondary"
                >
                    No, use another
                </button>

                <button
                    type="button"
                    id="useRegistrationAddressYes"
                    class="modal-button modal-button-primary"
                >
                    Yes, use this address
                </button>
            </div>
        </section>
    </div>

    {{-- =========================================================
         CREATE NEW ADDRESS MODAL
    ========================================================== --}}
    <div
        id="newAddressModal"
        class="checkout-modal-backdrop"
        hidden
    >
        <section
            class="checkout-modal checkout-modal-address-form"
            role="dialog"
            aria-modal="true"
            aria-labelledby="newAddressModalTitle"
        >
            <div class="modal-heading-row">
                <div>
                    <h2 id="newAddressModalTitle">Create New Address</h2>
                    <p>
                        Enter the delivery details you want to use for this order.
                    </p>
                </div>
            </div>

            <form id="newAddressForm">

                <div class="checkout-new-address-identity">
                    <div class="checkout-address-field-group">
                        <label
                            for="newAddressName"
                            class="checkout-address-field-label"
                        >
                            Full Name
                            <span class="required-star">*</span>
                        </label>

                        <input
                            type="text"
                            id="newAddressName"
                            class="checkout-address-form-input"
                            placeholder="Enter full name"
                            required
                        >
                    </div>

                    <div class="checkout-address-field-group">
                        <label
                            for="newAddressPhone"
                            class="checkout-address-field-label"
                        >
                            Phone Number
                            <span class="required-star">*</span>
                        </label>

                        <input
                            type="tel"
                            id="newAddressPhone"
                            class="checkout-address-form-input"
                            placeholder="+63 9XX XXX XXXX"
                            required
                        >
                    </div>
                </div>

                <div class="checkout-address-section-heading">
                    <h3>Address</h3>
                    <p>Type to search and select your address</p>
                </div>

                <div class="checkout-address-grid">

                    {{-- PROVINCE --}}
                    <div class="checkout-address-field-group">
                        <label
                            for="checkoutProvinceSearch"
                            class="checkout-address-field-label"
                        >
                            Province
                            <span class="required-star">*</span>
                        </label>

                        <div
                            class="checkout-search-select"
                            id="checkoutProvinceWrapper"
                        >
                            <input
                                type="text"
                                id="checkoutProvinceSearch"
                                class="checkout-search-select-input"
                                placeholder="Type to search province"
                                autocomplete="off"
                                role="combobox"
                                aria-expanded="false"
                                aria-controls="checkoutProvinceOptions"
                            >

                            <span class="checkout-search-select-arrow"></span>

                            <div
                                class="checkout-search-select-options"
                                id="checkoutProvinceOptions"
                                role="listbox"
                            ></div>

                            <input
                                type="hidden"
                                id="checkoutProvince"
                            >
                        </div>
                    </div>

                    {{-- MUNICIPALITY --}}
                    <div class="checkout-address-field-group">
                        <label
                            for="checkoutMunicipalitySearch"
                            class="checkout-address-field-label"
                        >
                            Municipality
                            <span class="required-star">*</span>
                        </label>

                        <div
                            class="checkout-search-select disabled"
                            id="checkoutMunicipalityWrapper"
                        >
                            <input
                                type="text"
                                id="checkoutMunicipalitySearch"
                                class="checkout-search-select-input"
                                placeholder="Select province first"
                                autocomplete="off"
                                role="combobox"
                                aria-expanded="false"
                                aria-controls="checkoutMunicipalityOptions"
                                disabled
                            >

                            <span class="checkout-search-select-arrow"></span>

                            <div
                                class="checkout-search-select-options"
                                id="checkoutMunicipalityOptions"
                                role="listbox"
                            ></div>

                            <input
                                type="hidden"
                                id="checkoutMunicipality"
                            >
                        </div>
                    </div>

                    {{-- BARANGAY --}}
                    <div class="checkout-address-field-group">
                        <label
                            for="checkoutBarangaySearch"
                            class="checkout-address-field-label"
                        >
                            Barangay
                            <span class="required-star">*</span>
                        </label>

                        <div
                            class="checkout-search-select disabled"
                            id="checkoutBarangayWrapper"
                        >
                            <input
                                type="text"
                                id="checkoutBarangaySearch"
                                class="checkout-search-select-input"
                                placeholder="Select municipality first"
                                autocomplete="off"
                                role="combobox"
                                aria-expanded="false"
                                aria-controls="checkoutBarangayOptions"
                                disabled
                            >

                            <span class="checkout-search-select-arrow"></span>

                            <div
                                class="checkout-search-select-options"
                                id="checkoutBarangayOptions"
                                role="listbox"
                            ></div>

                            <input
                                type="hidden"
                                id="checkoutBarangay"
                            >
                        </div>
                    </div>

                    {{-- ZIP --}}
                    <div class="checkout-address-field-group">
                        <label
                            for="newAddressZip"
                            class="checkout-address-field-label"
                        >
                            Zip Code
                            <span class="required-star">*</span>
                        </label>

                        <input
                            type="text"
                            id="newAddressZip"
                            class="checkout-address-form-input"
                            placeholder="Enter zip code"
                            inputmode="numeric"
                            maxlength="4"
                            required
                        >
                    </div>
                </div>

                <div class="checkout-address-bottom-grid">

                    <div class="checkout-address-field-group">
                        <label
                            for="newAddressStreet"
                            class="checkout-address-field-label"
                        >
                            Street
                        </label>

                        <input
                            type="text"
                            id="newAddressStreet"
                            class="checkout-address-form-input"
                            placeholder="Enter street"
                        >
                    </div>

                    <div class="checkout-address-field-group">
                        <label
                            for="newAddressHouse"
                            class="checkout-address-field-label"
                        >
                            House No.
                        </label>

                        <input
                            type="text"
                            id="newAddressHouse"
                            class="checkout-address-form-input"
                            placeholder="Enter house no."
                        >
                    </div>

                    <div class="checkout-address-field-group">
                        <label
                            for="newAddressBuilding"
                            class="checkout-address-field-label"
                        >
                            Building
                        </label>

                        <input
                            type="text"
                            id="newAddressBuilding"
                            class="checkout-address-form-input"
                            placeholder="Enter building"
                        >
                    </div>

                    <div class="checkout-address-field-group">
                        <label
                            for="newAddressSubdivision"
                            class="checkout-address-field-label"
                        >
                            Subdivision
                        </label>

                        <input
                            type="text"
                            id="newAddressSubdivision"
                            class="checkout-address-form-input"
                            placeholder="Enter subdivision"
                        >
                    </div>
                </div>

                <div class="modal-actions modal-form-actions">
                    <button
                        type="button"
                        id="backToRegistrationAddress"
                        class="modal-button modal-button-secondary"
                    >
                        Back
                    </button>

                    <button
                        type="submit"
                        class="modal-button modal-button-primary"
                    >
                        Save Address
                    </button>
                </div>
            </form>
        </section>
    </div>


    {{-- =========================================================
         CHECKOUT SUCCESS MODAL
    ========================================================== --}}
    <div
        id="checkoutSuccessModal"
        class="checkout-modal-backdrop"
        hidden
    >
        <section
            class="checkout-modal checkout-success-modal"
            role="dialog"
            aria-modal="true"
            aria-labelledby="checkoutSuccessTitle"
        >
            <div class="checkout-success-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <circle
                        cx="12"
                        cy="12"
                        r="9"
                        stroke="currentColor"
                        stroke-width="1.8"
                    />
                    <path
                        d="m8 12.5 2.5 2.5L16.5 9"
                        stroke="currentColor"
                        stroke-width="1.9"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>

            <h2 id="checkoutSuccessTitle">
                Order Placed Successfully!
            </h2>

            <p class="checkout-success-text">
                Your order has been placed and is now being processed.
                You can track its status anytime in My Purchases.
            </p>

            <button
                type="button"
                id="checkoutSuccessOkay"
                class="modal-button modal-button-primary checkout-success-button"
            >
                Okay
            </button>
        </section>
    </div>

    {{-- Shared Buyer Floating Chat --}}
    @include('components.buyer.floating-chat')

</body>
</html>
