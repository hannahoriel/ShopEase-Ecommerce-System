{{-- =========================================================
     SELLER CUSTOMER FEEDBACK
     resources/views/pages/seller/customer-feedback.blade.php

     Matches the supplied Figma:
     - All Feedback tab
     - Product Ratings tab
     - Search + rating filter
     - Delivered truck icon
     - Product ratings cards
     - Product reviews modal
========================================================= --}}

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>ShopEase - Customer Feedback</title>


    {{-- =====================================================
         POPPINS
    ====================================================== --}}

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
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >


    @vite([
        'resources/css/app.css',
        'resources/css/seller/shipping-status.css',
        'resources/css/seller/customer-feedback.css',
    ])


    

</head>


<body>


    {{-- =====================================================
         SELLER NAVIGATION
    ====================================================== --}}

    @include(
        'components.seller.sidebar'
    )

    @section(
        'page-title',
        'Customer Feedback'
    )

    @include(
        'components.seller.navbar'
    )


    {{-- =====================================================
         PAGE
    ====================================================== --}}

    <main
        id="feedbackPage"
        class="feedback-page"
    >

        <div class="feedback-content">


            {{-- =================================================
                 TITLE
            ================================================== --}}

            <div class="feedback-heading">

                <h1 class="feedback-title">
                    Customer Feedback
                </h1>

                <p
                    id="feedbackSubtitle"
                    class="feedback-subtitle"
                >
                    View feedbacks from your customers.
                </p>

            </div>


            {{-- =================================================
                 CARD
            ================================================== --}}

            <section class="feedback-card">


                {{-- =============================================
                     TABS + TOOLS
                ============================================== --}}

                <div class="feedback-toolbar">

                    <div class="feedback-tabs">

                        <button
                            type="button"
                            class="
                                feedback-tab
                                active
                            "
                            data-tab="feedback"
                        >
                            All Feedback
                        </button>


                        <button
                            type="button"
                            class="feedback-tab"
                            data-tab="ratings"
                        >
                            Product Ratings
                        </button>

                    </div>


                    <div class="feedback-tools">

                        <div class="feedback-search">

                            <input
                                type="text"
                                id="feedbackSearch"
                                placeholder="Search order ID or customer name"
                            >

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <circle
                                    cx="11"
                                    cy="11"
                                    r="7"
                                />

                                <path
                                    d="m20 20-3.5-3.5"
                                />
                            </svg>

                        </div>


                        <select
                            id="feedbackRatingFilter"
                            class="rating-filter"
                        >

                            <option value="all">
                                Rating
                            </option>

                            <option value="5">
                                5 Stars
                            </option>

                            <option value="4">
                                4 Stars
                            </option>

                            <option value="3">
                                3 Stars
                            </option>

                            <option value="2">
                                2 Stars
                            </option>

                            <option value="1">
                                1 Star
                            </option>

                        </select>


                        <button
                            type="button"
                            id="feedbackRefresh"
                            class="feedback-refresh"
                            aria-label="Refresh"
                        >

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M20 6v6h-6"
                                />

                                <path
                                    d="M20 12a8 8 0 1 1-2.34-5.66L20 8"
                                />
                            </svg>

                        </button>

                    </div>

                </div>


                {{-- =============================================
                     ALL FEEDBACK
                ============================================== --}}

                <div
                    id="allFeedbackPanel"
                    class="feedback-table-wrap"
                >

                    <div class="feedback-table-head">

                        <div>
                            Order ID
                        </div>

                        <div>
                            Customer
                        </div>

                        <div>
                            Rating
                        </div>

                        <div>
                            Feedback
                        </div>

                        <div>
                            Date
                        </div>

                    </div>


                    @php

                        $feedbackRows = [

                            [
                                'order' =>
                                    '#ORD-2025',

                                'order_date' =>
                                    'May 25, 2026',

                                'customer' =>
                                    'Juan Dela Cruz',

                                'phone' =>
                                    '0917-123-4567',

                                'rating' =>
                                    5,

                                'sentiment' =>
                                    'positive',

                                'title' =>
                                    'Excellent and fast delivery',

                                'body' =>
                                    'They are so bait and so bilis ng delivery yahu!',

                                'date' =>
                                    'May 22,2026',

                                'time' =>
                                    '10:34 AM',
                            ],

                            [
                                'order' =>
                                    '#ORD-2025',

                                'order_date' =>
                                    'May 25, 2026',

                                'customer' =>
                                    'Juan Dela Cruz',

                                'phone' =>
                                    '0917-123-4567',

                                'rating' =>
                                    5,

                                'sentiment' =>
                                    'positive',

                                'title' =>
                                    'Excellent and fast delivery',

                                'body' =>
                                    'They are so bait and so bilis ng delivery yahu!',

                                'date' =>
                                    'May 22,2026',

                                'time' =>
                                    '10:34 AM',
                            ],

                            [
                                'order' =>
                                    '#ORD-2025',

                                'order_date' =>
                                    'May 25, 2026',

                                'customer' =>
                                    'Juan Dela Cruz',

                                'phone' =>
                                    '0917-123-4567',

                                'rating' =>
                                    5,

                                'sentiment' =>
                                    'positive',

                                'title' =>
                                    'Excellent and fast delivery',

                                'body' =>
                                    'They are so bait and so bilis ng delivery yahu!',

                                'date' =>
                                    'May 22,2026',

                                'time' =>
                                    '10:34 AM',
                            ],

                            [
                                'order' =>
                                    '#ORD-2025',

                                'order_date' =>
                                    'May 25, 2026',

                                'customer' =>
                                    'Juan Dela Cruz',

                                'phone' =>
                                    '0917-123-4567',

                                'rating' =>
                                    3,

                                'sentiment' =>
                                    'neutral',

                                'title' =>
                                    'Excellent and fast delivery',

                                'body' =>
                                    'They are so bait and so bilis ng delivery yahu!',

                                'date' =>
                                    'May 22,2026',

                                'time' =>
                                    '10:34 AM',
                            ],

                            [
                                'order' =>
                                    '#ORD-2025',

                                'order_date' =>
                                    'May 25, 2026',

                                'customer' =>
                                    'Juan Dela Cruz',

                                'phone' =>
                                    '0917-123-4567',

                                'rating' =>
                                    2,

                                'sentiment' =>
                                    'negative',

                                'title' =>
                                    'Excellent and fast delivery',

                                'body' =>
                                    'They are so bait and so bilis ng delivery yahu!',

                                'date' =>
                                    'May 22,2026',

                                'time' =>
                                    '10:34 AM',
                            ],

                        ];

                    @endphp


                    <div
                        id="feedbackList"
                        class="feedback-list"
                    >

                        @foreach ($feedbackRows as $feedback)

                            <div
                                class="feedback-row"
                                data-rating="{{ $feedback['rating'] }}"
                                data-search="{{ strtolower(
                                    $feedback['order'] .
                                    ' ' .
                                    $feedback['customer']
                                ) }}"
                            >


                                {{-- ORDER --}}
                                <div class="order-cell">

                                    <div class="truck-box">

                                        <img
                                            src="{{ asset('icons/seller/shipping-status/delivered.png') }}"
                                            alt="Delivered"
                                        >

                                    </div>


                                    <div>

                                        <p class="order-id">
                                            {{ $feedback['order'] }}
                                        </p>

                                        <div class="order-date">
                                            {{ $feedback['order_date'] }}
                                        </div>

                                        <div class="delivered-label">

                                            <span class="delivered-dot"></span>

                                            Delivered

                                        </div>

                                    </div>

                                </div>


                                {{-- CUSTOMER --}}
                                <div>

                                    <p class="customer-name">
                                        {{ $feedback['customer'] }}
                                    </p>

                                    <div class="customer-phone">
                                        {{ $feedback['phone'] }}
                                    </div>

                                </div>


                                {{-- RATING --}}
                                <div class="rating-cell">

                                    <div class="stars">

                                        @for ($i = 1; $i <= 5; $i++)

                                            <span
                                                class="{{
                                                    $i > $feedback['rating']
                                                        ? 'star-empty'
                                                        : ''
                                                }}"
                                            >
                                                ★
                                            </span>

                                        @endfor

                                    </div>


                                    <span
                                        class="
                                            sentiment
                                            {{
                                                $feedback['sentiment']
                                            }}
                                        "
                                    >

                                        {{ ucfirst(
                                            $feedback['sentiment']
                                        ) }}

                                    </span>

                                </div>


                                {{-- FEEDBACK --}}
                                <div>

                                    <p class="feedback-text-title">
                                        {{ $feedback['title'] }}
                                    </p>

                                    <div class="feedback-text-body">
                                        {{ $feedback['body'] }}
                                    </div>

                                    <div class="feedback-photo"></div>

                                </div>


                                {{-- DATE --}}
                                <div class="feedback-date">

                                    <div>
                                        {{ $feedback['date'] }}
                                    </div>

                                    <div>
                                        {{ $feedback['time'] }}
                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>

                </div>


                {{-- =============================================
                     PRODUCT RATINGS
                ============================================== --}}

                <div
                    id="productRatingsPanel"
                    class="product-ratings-panel"
                >

                    @php

                        $products = [

                            [
                                'name' =>
                                    "Men’s Graphic T-shirt for everyone",

                                'rating' =>
                                    3,

                                'image' =>
                                    null,
                            ],

                            [
                                'name' =>
                                    'Snail Solution Care Set',

                                'rating' =>
                                    5,

                                'image' =>
                                    null,
                            ],

                            [
                                'name' =>
                                    "Men’s Graphic T-shirt for everyone",

                                'rating' =>
                                    2,

                                'image' =>
                                    null,
                            ],

                            [
                                'name' =>
                                    "Men’s Graphic T-shirt for everyone",

                                'rating' =>
                                    5,

                                'image' =>
                                    null,
                            ],

                        ];

                    @endphp


                    <div
                        id="productGrid"
                        class="product-grid"
                    >

                        @foreach ($products as $index => $product)

                            <article
                                class="product-card"
                                data-product-index="{{ $index }}"
                                data-search="{{ strtolower(
                                    $product['name']
                                ) }}"
                            >

                                <div class="product-card-image">

                                    @if ($product['image'])

                                        <img
                                            src="{{ asset(
                                                $product['image']
                                            ) }}"
                                            alt="{{ $product['name'] }}"
                                        >

                                    @else

                                        <svg
                                            viewBox="0 0 200 200"
                                            width="100%"
                                            height="100%"
                                            aria-hidden="true"
                                        >

                                            <rect
                                                width="200"
                                                height="200"
                                                fill="#F2F2F2"
                                            />

                                            <path
                                                d="
                                                    M62 35
                                                    L86 23
                                                    H114
                                                    L138 35
                                                    L164 65
                                                    L142 82
                                                    L129 68
                                                    V168
                                                    H71
                                                    V68
                                                    L58 82
                                                    L36 65
                                                    Z
                                                "
                                                fill="#111"
                                            />

                                            <circle
                                                cx="100"
                                                cy="30"
                                                r="18"
                                                fill="#D6B39C"
                                            />

                                        </svg>

                                    @endif

                                </div>


                                <div class="product-card-body">

                                    <p class="product-card-name">
                                        {{ $product['name'] }}
                                    </p>

                                    <div class="product-card-stars">

                                        @for ($i = 1; $i <= 5; $i++)

                                            <span
                                                class="{{
                                                    $i > $product['rating']
                                                        ? 'star-empty'
                                                        : ''
                                                }}"
                                            >
                                                ★
                                            </span>

                                        @endfor

                                    </div>

                                </div>

                            </article>

                        @endforeach

                    </div>

                </div>


                {{-- =============================================
                     FOOTER
                ============================================== --}}

                <div class="feedback-footer">

                    <p
                        id="feedbackCount"
                        class="feedback-count"
                    >
                        Showing 7 out of 378 entries
                    </p>


                    <div class="feedback-pagination">

                        <button
                            type="button"
                            class="page-button"
                        >
                            ‹
                        </button>

                        <button
                            type="button"
                            class="
                                page-button
                                active
                            "
                        >
                            1
                        </button>

                        <button
                            type="button"
                            class="page-button"
                        >
                            2
                        </button>

                        <button
                            type="button"
                            class="page-button"
                        >
                            3
                        </button>

                        <button
                            type="button"
                            class="page-button"
                        >
                            ›
                        </button>


                        <select class="per-page">

                            <option>
                                Items per page: 7
                            </option>

                            <option>
                                Items per page: 10
                            </option>

                            <option>
                                Items per page: 20
                            </option>

                        </select>

                    </div>

                </div>

            </section>

        </div>

    </main>


    {{-- =====================================================
         PRODUCT REVIEW MODAL
    ====================================================== --}}

    <div
        id="productReviewsModal"
        class="reviews-modal"
        aria-hidden="true"
    >

        <div
            class="reviews-modal-panel"
            role="dialog"
            aria-modal="true"
            aria-labelledby="modalProductName"
        >


            {{-- PRODUCT SUMMARY --}}
            <div class="modal-product-summary">

                <div class="modal-product-image">

                    <svg
                        viewBox="0 0 200 200"
                        width="100%"
                        height="100%"
                    >

                        <rect
                            width="200"
                            height="200"
                            fill="#F5F5F5"
                        />

                        <path
                            d="
                                M62 35
                                L86 23
                                H114
                                L138 35
                                L164 65
                                L142 82
                                L129 68
                                V168
                                H71
                                V68
                                L58 82
                                L36 65
                                Z
                            "
                            fill="#111"
                        />

                        <circle
                            cx="100"
                            cy="30"
                            r="18"
                            fill="#D6B39C"
                        />

                    </svg>

                </div>


                <div>

                    <h2
                        id="modalProductName"
                        class="modal-product-name"
                    >
                        Men’s Graphic T-shirt
                    </h2>

                    <div class="modal-product-price">
                        ₱59
                    </div>

                    <div class="modal-category-row">

                        <span>
                            Category
                        </span>

                        <span class="category-pill">
                            Women’s Apparel
                        </span>

                    </div>

                    <div class="modal-rating-line">

                        <span class="modal-big-star">
                            ★
                        </span>

                        <span class="modal-rating-score">
                            4.3 out of 5
                        </span>

                        <span class="modal-review-count">
                            (24 reviews)
                        </span>

                    </div>

                </div>


                <div class="rating-breakdown">

                    @php

                        $breakdown = [

                            [
                                'rating' => 5,
                                'width' => 82,
                                'count' => 15,
                            ],

                            [
                                'rating' => 4,
                                'width' => 58,
                                'count' => 4,
                            ],

                            [
                                'rating' => 3,
                                'width' => 32,
                                'count' => 2,
                            ],

                            [
                                'rating' => 2,
                                'width' => 65,
                                'count' => 5,
                            ],

                            [
                                'rating' => 1,
                                'width' => 12,
                                'count' => 1,
                            ],

                        ];

                    @endphp


                    @foreach ($breakdown as $item)

                        <div class="rating-breakdown-row">

                            <span>
                                {{ $item['rating'] }}
                            </span>

                            <span class="star">
                                ★
                            </span>

                            <div class="rating-bar">

                                <div
                                    class="rating-bar-fill"
                                    style="
                                        width:
                                        {{ $item['width'] }}%;
                                    "
                                ></div>

                            </div>

                            <span>
                                {{ $item['count'] }}
                            </span>

                        </div>

                    @endforeach

                </div>

            </div>


            {{-- REVIEWS --}}
            <div class="modal-reviews-section">

                <h3 class="modal-reviews-title">
                    Customer Review (24)
                </h3>


                @php

                    $reviews = [

                        [
                            'name' =>
                                'Juan Dela Cruz',

                            'rating' =>
                                5,

                            'date' =>
                                'May 22, 2026',

                            'time' =>
                                '7:00 PM',

                            'text' =>
                                'Sobrang ganda ng damit haha, basta sobrang ganda haha. ang bilis pa ng shipment.',
                        ],

                        [
                            'name' =>
                                'Juan Dela Cruz',

                            'rating' =>
                                3,

                            'date' =>
                                'May 22, 2026',

                            'time' =>
                                '7:00 PM',

                            'text' =>
                                'Sobrang ganda ng damit haha, basta sobrang ganda haha. ang bilis pa ng shipment.',
                        ],

                        [
                            'name' =>
                                'Juan Dela Cruz',

                            'rating' =>
                                2,

                            'date' =>
                                'May 22, 2026',

                            'time' =>
                                '7:00 PM',

                            'text' =>
                                'Sobrang ganda ng damit haha, basta sobrang ganda haha. ang bilis pa ng shipment.',
                        ],

                    ];

                @endphp


                @foreach ($reviews as $review)

                    <article class="review-card">

                        <div class="review-avatar">

                            <svg
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                aria-hidden="true"
                            >

                                <circle
                                    cx="12"
                                    cy="7"
                                    r="4"
                                />

                                <path
                                    d="
                                        M4 21
                                        v-2
                                        a8 8 0 0 1
                                        16 0
                                        v2
                                        z
                                    "
                                />

                            </svg>

                        </div>


                        <div>

                            <h4 class="review-name">
                                {{ $review['name'] }}
                            </h4>

                            <div class="review-meta">

                                <span class="review-stars">

                                    @for ($i = 1; $i <= 5; $i++)

                                        <span
                                            class="{{
                                                $i > $review['rating']
                                                    ? 'star-empty'
                                                    : ''
                                            }}"
                                        >
                                            ★
                                        </span>

                                    @endfor

                                </span>

                                <span>
                                    {{ $review['date'] }}
                                </span>

                                <span>
                                    {{ $review['time'] }}
                                </span>

                            </div>


                            <p class="review-text">
                                {{ $review['text'] }}
                            </p>

                        </div>


                        <div class="review-photo">

                            <svg
                                viewBox="0 0 120 120"
                                width="100%"
                                height="100%"
                                aria-hidden="true"
                            >

                                <defs>

                                    <linearGradient
                                        id="reviewBg"
                                        x1="0"
                                        y1="0"
                                        x2="1"
                                        y2="1"
                                    >

                                        <stop
                                            offset="0%"
                                            stop-color="#D8D0C4"
                                        />

                                        <stop
                                            offset="100%"
                                            stop-color="#7890A5"
                                        />

                                    </linearGradient>

                                </defs>

                                <rect
                                    width="120"
                                    height="120"
                                    fill="url(#reviewBg)"
                                />

                                <circle
                                    cx="61"
                                    cy="34"
                                    r="13"
                                    fill="#C99970"
                                />

                                <rect
                                    x="45"
                                    y="47"
                                    width="32"
                                    height="46"
                                    rx="12"
                                    fill="#111"
                                />

                                <path
                                    d="
                                        M45 57
                                        L31 79
                                        M77 57
                                        L91 79
                                        M55 93
                                        L50 116
                                        M67 93
                                        L72 116
                                    "
                                    stroke="#111"
                                    stroke-width="9"
                                    stroke-linecap="round"
                                />

                            </svg>

                        </div>

                    </article>

                @endforeach

            </div>


            <div
                class="
                    shipping-modal-footer
                "
            >

                <button
                    type="button"
                    id="closeShippingModal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>
</body>

</html>
