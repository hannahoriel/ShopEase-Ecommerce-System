<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopEase - Product Details</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite([
        'resources/css/app.css',
        'resources/css/buyer/pages/product.css',
        'resources/js/buyer/pages/product.js',
    ])
    <script id="buyerProductConfig" type="application/json">
        {!! json_encode([
            'cartUrl'    => '/api/v1/buyer/cart',
            'productUrl' => '/api/v1/buyer/products',
            'reviewsUrl' => '/api/v1/buyer/products',
            'productId'  => request()->integer('id') ?: null,
            'apiToken'   => $apiToken ?? '',
        ]) !!}
    </script>
    <style>
        .product-skeleton { background: linear-gradient(90deg,#f0e8e5 25%,#faf4f2 50%,#f0e8e5 75%); background-size: 200% 100%; animation: shimmer 1.4s infinite; border-radius: 6px; }
        @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
        .product-loading-state { padding: 60px 0; text-align: center; color: #A09896; font-size: 13px; }
        .product-not-found { padding: 80px 0; text-align: center; color: #A09896; font-size: 14px; }
    </style>
</head>
<body class="buyer-product-page">

    @include('components.buyer.navbar', ['hideCategories' => true])

    <main class="product-main">
        <div class="product-container">

            {{-- BREADCRUMB --}}
            <nav class="product-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/buyer/dashboard') }}" class="breadcrumb-home">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M3 10.5 12 3l9 7.5V21h-6v-6H9v6H3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                    </svg>
                    Home
                </a>
                <span class="breadcrumb-separator" aria-hidden="true">›</span>
                <span class="breadcrumb-current" id="breadcrumbProduct" aria-current="page">Product</span>
            </nav>

            {{-- PRODUCT HERO --}}
            <section class="product-hero-card" id="productHeroCard">
                <div class="product-gallery">
                    <div class="gallery-main">
                        <button type="button" class="gallery-arrow gallery-arrow-left" id="galleryPrev" aria-label="Previous image">‹</button>
                        <div class="main-product-visual" id="mainProductVisual">
                            <div class="product-skeleton" style="width:100%;height:100%;"></div>
                        </div>
                        <button type="button" class="gallery-arrow gallery-arrow-right" id="galleryNext" aria-label="Next image">›</button>
                    </div>
                    <div class="gallery-thumbnails" id="galleryThumbnails"></div>
                </div>

                <div class="product-info">
                    <span class="preferred-badge">Preferred</span>
                    <h1 id="productName"><span class="product-skeleton" style="display:block;height:24px;width:90%;margin-top:6px;"></span></h1>

                    <div class="product-meta-row" id="productMeta">
                        <span class="product-skeleton" style="display:inline-block;height:14px;width:220px;"></span>
                    </div>

                    <div class="price-panel" id="pricePanel">
                        <span class="product-skeleton" style="display:inline-block;height:30px;width:120px;"></span>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Shipping</div>
                        <div class="shipping-copy">
                            <div class="shipping-main">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M3 6h11v10H3zM14 9h4l3 3v4h-7z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><circle cx="7" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/><circle cx="18" cy="18" r="1.5" stroke="currentColor" stroke-width="1.7"/></svg>
                                <span>5 - 7 Days</span>
                            </div>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Shipping Guarantee</div>
                        <div class="guarantee-copy">
                            <svg viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v5c0 4.5 2.8 8 7 10 4.2-2 7-5.5 7-10V6Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/><path d="m8.5 11.5 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            <span>Fulfilled by ShopEase · Merchandise Protection ›</span>
                        </div>
                    </div>

                    <div class="info-row variations-row" id="variationsRow" style="display:none;">
                        <div class="info-label" id="variationsLabel">Variations</div>
                        <div class="variations-wrap" id="variationsWrap"></div>
                    </div>

                    <div class="info-row quantity-row">
                        <div class="info-label">Quantity</div>
                        <div class="quantity-wrap">
                            <div class="quantity-control">
                                <button type="button" id="quantityMinus">−</button>
                                <input type="number" id="productQuantity" value="1" min="1" max="99" readonly>
                                <button type="button" id="quantityPlus">+</button>
                            </div>
                            <span id="stockLabel">— In Stock</span>
                        </div>
                    </div>

                    <div class="product-action-row">
                        <button type="button" class="add-cart-button" id="addToCartButton" data-product-id="">
                            <img src="{{ asset('icons/buyer/product-cart.png') }}" alt="">
                            Add to Cart
                        </button>
                        <button type="button" class="buy-now-button" id="buyNowButton">Buy Now</button>
                    </div>
                </div>

                <div class="product-trust-row">
                    <div><svg viewBox="0 0 24 24" fill="none"><path d="M12 3 5 6v5c0 4.5 2.8 8 7 10 4.2-2 7-5.5 7-10V6Z" stroke="currentColor" stroke-width="1.7"/><path d="m8.5 11.5 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.7"/></svg><span>100% Authentic</span></div>
                    <div><svg viewBox="0 0 24 24" fill="none"><rect x="4" y="7" width="16" height="13" rx="2" stroke="currentColor" stroke-width="1.7"/><path d="M8 7V5h8v2M8 12h8" stroke="currentColor" stroke-width="1.7"/></svg><span>Secure Payments</span></div>
                    <div><svg viewBox="0 0 24 24" fill="none"><path d="M5 16v-5a7 7 0 0 1 14 0v5" stroke="currentColor" stroke-width="1.7"/><path d="M5 15H3v4h4v-4ZM19 15h2v4h-4v-4ZM9 21h6" stroke="currentColor" stroke-width="1.7"/></svg><span>Customer Support</span></div>
                </div>
            </section>

            {{-- SELLER CARD --}}
            <section class="seller-card" id="sellerCard">
                <div class="seller-left">
                    <div class="seller-logo" id="sellerLogo">—</div>
                    <div class="seller-main-copy">
                        <h2 id="sellerName"><span class="product-skeleton" style="display:inline-block;height:16px;width:120px;"></span></h2>
                        <div class="seller-buttons">
                            <button type="button" class="chat-now-button">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M5 5h14v10H9l-4 4Z" stroke="currentColor" stroke-width="1.7"/></svg>
                                Chat Now
                            </button>
                            <button type="button" class="view-shop-button">
                                <svg viewBox="0 0 24 24" fill="none"><path d="M4 9h16l-1-5H5L4 9ZM5 9v10h14V9" stroke="currentColor" stroke-width="1.7"/></svg>
                                View Shop
                            </button>
                        </div>
                    </div>
                </div>
                <div class="seller-stats">
                    <div><span>Ratings</span><strong id="sellerRatings">0%</strong></div>
                    <div><span>Response Rate</span><strong id="sellerResponseRate">—</strong></div>
                    <div><span>Joined</span><strong id="sellerJoined">—</strong></div>
                    <div><span>Products</span><strong id="sellerProducts">0</strong></div>
                    <div><span>Response Time</span><strong id="sellerResponseTime">—</strong></div>
                    <div><span>Followers</span><strong id="sellerFollowers">0</strong></div>
                </div>
            </section>

            {{-- SPECIFICATIONS --}}
            <section class="product-section-card" id="specsCard" style="display:none;">
                <div class="product-section-heading"><h2>Product Specifications</h2></div>
                <div class="spec-grid" id="specGrid"></div>
            </section>

            {{-- DESCRIPTION --}}
            <section class="product-section-card" id="descCard" style="display:none;">
                <div class="product-section-heading"><h2>Product Description</h2></div>
                <div class="product-description" id="productDescription"></div>
            </section>

            {{-- RATINGS --}}
            <section class="product-section-card ratings-card">
                <div class="product-section-heading"><h2>Product Ratings</h2></div>
                <div class="ratings-summary">
                    <div class="rating-score-box">
                        <strong id="productRatingAverage">0.0 <small>out of 5</small></strong>
                        <span id="productRatingStars">☆☆☆☆☆</span>
                        <small id="productReviewCount">0 reviews</small>
                    </div>
                    <div class="rating-filter-list">
                        <button type="button" class="rating-filter active" data-rating-filter="all">All</button>
                        <button type="button" class="rating-filter" data-rating-filter="5">5 Star</button>
                        <button type="button" class="rating-filter" data-rating-filter="4">4 Star</button>
                        <button type="button" class="rating-filter" data-rating-filter="3">3 Star</button>
                        <button type="button" class="rating-filter" data-rating-filter="2">2 Star</button>
                        <button type="button" class="rating-filter" data-rating-filter="1">1 Star</button>
                    </div>
                </div>
                <form id="productReviewForm" class="product-review-form">
                    <h3>Share your feedback</h3>
                    <label for="reviewRating">Your rating</label>
                    <select id="reviewRating" name="rating" required>
                        <option value="">Choose a rating</option>
                        <option value="5">5 stars - Excellent</option>
                        <option value="4">4 stars - Good</option>
                        <option value="3">3 stars - Average</option>
                        <option value="2">2 stars - Poor</option>
                        <option value="1">1 star - Very poor</option>
                    </select>
                    <label for="reviewTitle">Title (optional)</label>
                    <input id="reviewTitle" name="title" type="text" maxlength="120" placeholder="Summarize your experience">
                    <label for="reviewBody">Your review</label>
                    <textarea id="reviewBody" name="body" rows="3" maxlength="2000" minlength="2" required placeholder="What did you think of this product?"></textarea>
                    <button type="submit" id="submitProductReview">Submit review</button>
                    <p id="productReviewMessage" role="status" aria-live="polite"></p>
                </form>
                <div class="review-list">
                    <div id="ratingsReviewList"></div>
                    <div id="ratingsFilterEmpty" class="ratings-filter-empty">Loading reviews…</div>
                </div>
            </section>

            {{-- RECOMMENDED --}}
            <section class="recommended-section">
                <div class="recommended-heading"><h2>You May Also Like</h2></div>
                <div class="product-grid" id="recommendedProducts">
                    <div class="product-loading-state" style="grid-column:1/-1;">Loading recommendations…</div>
                </div>
            </section>

        </div>
    </main>

    @include('components.buyer.footer')
    @include('components.buyer.floating-chat')

</body>
</html>
