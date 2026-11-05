<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Products | ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite([
        'resources/css/app.css',
        'resources/css/buyer/pages/dashboard.css',
        'resources/css/buyer/pages/search.css',
        'resources/js/buyer/pages/search.js',
    ])
    @vite('resources/js/buyer/components/buyer-likes.js')
    <meta name="buyer-likes-user-id" content="{{ auth()->id() ?? 'guest' }}">
    <script id="buyerSearchConfig" type="application/json">{!! json_encode([
        'apiUrl' => '/api/v1/buyer/search',
        'token' => $apiToken ?? '',
        'productUrl' => url('/buyer/product'),
        'shopUrl' => url('/buyer/shop'),
        'cartIcon' => asset('icons/buyer/product-cart.png'),
    ]) !!}</script>
</head>
<body class="buyer-page buyer-search-page">
    @include('components.buyer.navbar')
    <main class="search-page-wrap">
        <div class="search-page-shell">
            <nav class="search-breadcrumb"><a href="{{ route('buyer.dashboard') }}">Home</a><span>›</span><span>Search results</span></nav>
            <div class="search-page-heading">
                <span class="search-kicker">DISCOVER THE BEST FINDS</span>
                <h1>Search results for <span id="searchedTerm">your search</span></h1>
                <p>Explore products from verified ShopEase sellers and refine your results.</p>
            </div>
            <div class="search-workspace">
                <aside class="search-sidebar" id="searchSidebar">
                    <div class="search-sidebar-top"><h2>☷ &nbsp; SEARCH FILTER</h2><button type="button" id="clearAllFilters">Clear all</button></div>
                    <section class="search-filter-group">
                        <h3>Shipped From</h3>
                        <p class="search-helper">Seller's registered province</p>
                        <div id="provinceOptions" class="search-filter-options"><span class="search-filter-loading">Loading locations…</span></div>
                    </section>
                    <section class="search-filter-group">
                        <h3>Brand</h3>
                        <div id="brandOptions" class="search-filter-options"><span class="search-filter-loading">Loading brands…</span></div>
                    </section>
                    <section class="search-filter-group">
                        <h3>Price Range</h3>
                        <div class="search-price-inputs"><input type="number" min="0" id="minPrice" placeholder="₱ MIN" aria-label="Minimum price"><span>—</span><input type="number" min="0" id="maxPrice" placeholder="₱ MAX" aria-label="Maximum price"></div>
                        <button type="button" id="applyPrice" class="search-apply-btn">APPLY</button>
                        <p class="search-filter-error" id="priceError" role="alert" hidden></p>
                    </section>
                    <section class="search-filter-group">
                        <h3>Rating</h3>
                        <div id="ratingOptions" class="search-filter-options">
                            @foreach([5, 4, 3, 2, 1] as $rating)
                                <label class="search-check-option"><input type="radio" name="minRating" value="{{ $rating }}"><span class="search-stars">{{ str_repeat('★', $rating) }}{{ str_repeat('☆', 5 - $rating) }}</span> @if($rating < 5) &amp; Up @endif</label>
                            @endforeach
                        </div>
                    </section>
                    <section class="search-filter-group">
                        <h3>By Category <span class="search-category-hint">Subcategories</span></h3>
                        <div id="subcategoryOptions" class="search-filter-options"><span class="search-filter-loading">Loading subcategories…</span></div>
                    </section>
                    <button type="button" class="search-clear-bottom" id="clearBottomFilters">CLEAR ALL</button>
                </aside>
                <section class="search-results-section" aria-label="Products matching search">
                    <div id="relatedShopsSection" class="search-related-shops" hidden>
                        <div class="search-shops-title"><h2>SHOPS RELATED TO YOUR SEARCH</h2><span>Explore ShopEase sellers</span></div>
                        <div id="relatedShopsList" class="search-shops-list"></div>
                    </div>
                    <div class="search-results-intro"><span class="search-results-icon">⌕</span> Search result for <strong id="resultsQuery">your search</strong></div>
                    <div class="search-sortbar">
                        <span class="search-sortbar-caption">Sort by</span>
                        <div class="search-sort-buttons" role="group" aria-label="Sort products">
                            <button type="button" data-sort="relevance" class="is-selected">Relevance</button>
                            <button type="button" data-sort="latest">Latest</button>
                            <button type="button" data-sort="top-sales">Top Sales</button>
                        </div>
                        <select id="priceSort" aria-label="Sort by price"><option value="">Price</option><option value="price-low">Price: Low to High</option><option value="price-high">Price: High to Low</option></select>
                        <span id="resultTotal" class="search-result-total" aria-live="polite"></span>
                        <button type="button" id="toggleMobileFilters" class="search-mobile-filters">☷ Filters</button>
                    </div>
                    <div id="activeFilterChips" class="search-active-chips" aria-live="polite"></div>
                    <div id="searchProductGrid" class="product-grid search-product-grid" aria-live="polite"><div class="search-feedback">Searching ShopEase products…</div></div>
                    <div class="search-pager" id="searchPager" aria-label="Search pagination"></div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
