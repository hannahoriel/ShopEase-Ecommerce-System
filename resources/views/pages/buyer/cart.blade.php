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

    <script id="buyerCartConfig" type="application/json">
        {!! json_encode([
            'cartUrl'  => '/api/v1/buyer/cart',
            'apiToken' => $apiToken ?? '',
        ]) !!}
    </script>
</head>

<body
    class="buyer-cart-page"
    data-checkout-url="{{ route('buyer.checkout') }}"
>

    {{-- Existing Buyer Navbar --}}
    @include('components.buyer.navbar', ['hideCategories' => true])

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

                    {{-- SHOP GROUPS — populated by cart.js via API --}}
                    <div id="cartGroups" class="cart-groups"></div>
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



        </div>
    </main>


    {{-- Shared Buyer Footer --}}
    @include('components.buyer.footer')


    {{-- Shared Buyer Floating Chat --}}
    @include('components.buyer.floating-chat')

</body>
</html>
