<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Shop | ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/css/buyer/pages/shop.css', 'resources/js/buyer/pages/shop.js'])
    <script id="buyerShopConfig" type="application/json">
        {!! json_encode([
            'apiUrl' => '/api/v1/buyer/shops',
            'messagesUrl' => '/api/v1/buyer/messages',
            'sellerId' => request()->integer('seller') ?: null,
            'apiToken' => $apiToken ?? '',
        ]) !!}
    </script>
</head>
<body class="buyer-shop-page">
    @include('components.buyer.navbar', ['hideCategories' => true])
    <main class="bs-main">
        <div class="bs-container">
            <nav class="bs-breadcrumb" aria-label="Breadcrumb"><a href="{{ url('/buyer/dashboard') }}">Home</a><span>›</span><span id="bsBreadcrumbName">Shop</span></nav>
            <section class="bs-store-hero" aria-labelledby="bsShopName">
                <div class="bs-cover" id="bsCover"><span class="bs-cover-glow bs-cover-glow-one"></span><span class="bs-cover-glow bs-cover-glow-two"></span><div class="bs-cover-label"><span class="bs-live-dot"></span> SHOP ON SHOPEASE</div></div>
                <div class="bs-store-summary">
                    <div class="bs-store-identity">
                        <div class="bs-store-avatar" id="bsAvatar"><span>SE</span></div>
                        <div class="bs-store-copy"><span class="bs-store-kicker">OFFICIAL SHOP</span><h1 id="bsShopName">Loading shop…</h1><p id="bsShopLocation">A ShopEase seller</p></div>
                    </div>
                    <div class="bs-store-actions"><button type="button" id="bsChatButton" class="bs-chat-button" disabled><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3h11A2.5 2.5 0 0 1 20 5.5v8a2.5 2.5 0 0 1-2.5 2.5H10l-5.5 4v-4.5A2.5 2.5 0 0 1 4 13.5z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/><path d="M8 8h8M8 11h5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg> Chat with shop</button></div>
                </div>
                <div class="bs-store-stats" aria-label="Shop information">
                    <div class="bs-stat"><span class="bs-stat-icon">▦</span><span><small>Products</small><strong id="bsProductCount">—</strong></span></div>
                    <div class="bs-stat"><span class="bs-stat-icon">★</span><span><small>Store rating</small><strong><span id="bsRating">—</span> <em id="bsRatingCount"></em></strong></span></div>
                    <div class="bs-stat"><span class="bs-stat-icon">✓</span><span><small>Compliance score</small><strong id="bsComplianceScore">Not assessed</strong></span></div>
                    <div class="bs-stat"><span class="bs-stat-icon">◷</span><span><small>On ShopEase</small><strong id="bsJoined">—</strong></span></div>
                </div>
            </section>

            <section class="bs-shop-content">
                <div class="bs-shop-intro"><div><span class="bs-eyebrow">MADE FOR YOU</span><h2>Find something you’ll love.</h2><p>Explore this shop’s collection, handpicked for your next favorite.</p></div><div class="bs-shop-search"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.8" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg><input id="bsSearch" type="search" placeholder="Search products in this shop" aria-label="Search products in this shop"><kbd>⌕</kbd></div></div>
                <div class="bs-category-bar"><div id="bsCategories" class="bs-category-list" role="tablist" aria-label="Shop categories"><button type="button" class="bs-category-chip is-active" data-category="" role="tab" aria-selected="true">All products</button></div><span class="bs-category-accent">A little joy, delivered.</span></div>
                <section class="bs-featured-section" id="bsFeaturedSection" hidden><div class="bs-section-heading"><div><span class="bs-eyebrow">CUSTOMER FAVORITES</span><h2>Popular in this shop</h2></div><span class="bs-section-note">Loved by ShopEase buyers</span></div><div id="bsFeaturedGrid" class="bs-product-grid bs-featured-grid product-grid"></div></section>
                <section class="bs-products-section"><div class="bs-results-heading"><div><span class="bs-eyebrow">THE COLLECTION</span><h2 id="bsProductsHeading">All products</h2><p id="bsResultsCount">Loading products…</p></div><label class="bs-sort-label"><span>Sort by</span><select id="bsSort"><option value="popular">Popular</option><option value="latest">Latest</option><option value="top_sales">Top sales</option><option value="price_low">Price: low to high</option><option value="price_high">Price: high to low</option></select></label></div><div id="bsProductGrid" class="bs-product-grid product-grid" aria-live="polite"></div><div id="bsEmpty" class="bs-empty-state" hidden></div><div id="bsError" class="bs-error-state" hidden></div></section>
            </section>

            {{-- Separate recommendation section, outside the seller shop's white product panel. --}}
            <section id="bsYouMayLikeSection" class="bs-you-may-like" hidden aria-labelledby="bsYouMayLikeTitle">
                <div class="bs-section-heading"><div><span class="bs-eyebrow">DISCOVER MORE</span><h2 id="bsYouMayLikeTitle">You May Also Like</h2><p class="bs-recommend-subtitle">Explore products from other ShopEase sellers</p></div></div>
                <div id="bsYouMayLikeGrid" class="bs-recommendation-grid product-grid" aria-live="polite"></div>
            </section>
            <footer class="bs-shop-footer"><span>Thoughtfully shopped. Happily delivered.</span><strong>ShopEase <i>♥</i></strong></footer>
        </div>
    </main>
    @include('components.buyer.footer')
    @include('components.buyer.floating-chat')
</body>
</html>