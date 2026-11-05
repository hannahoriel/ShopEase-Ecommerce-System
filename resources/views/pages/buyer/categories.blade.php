<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop by Category | ShopEase</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite([
        'resources/css/app.css',
        'resources/css/buyer/pages/dashboard.css',
        'resources/css/buyer/pages/categories.css',
        'resources/js/buyer/pages/categories.js',
    ])
    @vite('resources/js/buyer/components/buyer-likes.js')
    <meta name="buyer-likes-user-id" content="{{ auth()->id() ?? 'guest' }}">
    <script id="buyerCategoriesConfig" type="application/json">{!! json_encode([
        'productsUrl' => '/api/v1/buyer/dashboard/products',
        'productUrl' => '/buyer/product',
        'shopUrl' => '/buyer/shop',
        'apiToken' => $apiToken ?? '',
        'cartIconUrl' => asset('icons/buyer/product-cart.png'),
    ]) !!}</script>
</head>
<body class="buyer-page buyer-categories-page">
    @include('components.buyer.navbar')
    @php
        $categories = [
            ['name' => 'Pet Supplies', 'slug' => 'pet-supplies', 'icon' => 'pets.png'],
            ['name' => 'Electronics & Gadgets', 'slug' => 'electronics', 'icon' => 'gadgets.png'],
            ['name' => "Women's Apparel", 'slug' => 'womens-apparel', 'icon' => 'women.png'],
            ['name' => "Men's Apparel", 'slug' => 'mens-apparel', 'icon' => 'men.png'],
            ['name' => 'Kids & Baby', 'slug' => 'kids-and-baby', 'icon' => 'kids.png'],
            ['name' => 'Home & Garden', 'slug' => 'home-and-garden', 'icon' => 'garden.png'],
            ['name' => 'Sports & Outdoors', 'slug' => 'sports-and-outdoors', 'icon' => 'sports.png'],
            ['name' => 'Health & Beauty', 'slug' => 'health-and-beauty', 'icon' => 'health.png'],
            ['name' => 'Books & Media', 'slug' => 'books-and-media', 'icon' => 'books.png'],
            ['name' => 'Food & Gourmet', 'slug' => 'food-and-gourmet', 'icon' => 'gourmet.png'],
            ['name' => 'Automotive & Motorcycle', 'slug' => 'automotive', 'icon' => 'automotive.png'],
            ['name' => 'Furniture & Office Equipment', 'slug' => 'furniture', 'icon' => 'furniture.png'],
            ['name' => 'Jewelry & Watches', 'slug' => 'jewelry-and-watches', 'icon' => 'jewelry.png'],
            ['name' => 'Office & School Supplies', 'slug' => 'office-supplies', 'icon' => 'school.png'],
        ];
    @endphp
    <main class="categories-main">
        <div class="categories-container">
            <nav class="categories-breadcrumb" aria-label="Breadcrumb"><a href="{{ route('buyer.dashboard') }}">Home</a><span>›</span><span>Shop by Category</span></nav>
            <section class="categories-hero">
                <div>
                    <span class="categories-eyebrow"><span class="categories-eyebrow-dot"></span> YOUR NEXT FAVORITE IS HERE</span>
                    <h1>Discover what you <em>love.</em></h1>
                    <p>Explore products across your favorite categories. Find the little things, the big upgrades, and everything in between.</p>
                    <div class="categories-hero-stats"><span><strong>{{ count($categories) }}</strong> curated categories</span><span class="categories-stat-divider"></span><span>Finds from ShopEase sellers</span></div>
                </div>
                <div class="categories-hero-art" aria-hidden="true"><span class="hero-glow"></span><span class="hero-orbit orbit-one"></span><span class="hero-orbit orbit-two"></span><span class="hero-art-bag">✦</span><span class="hero-art-sparkle">✧</span></div>
            </section>
            <section class="category-picker-panel" aria-label="Browse category shortcuts">
                <div class="category-picker-header"><div><span class="categories-section-kicker">EXPLORE BY INTEREST</span><h2>Shop categories</h2></div><button type="button" id="clearCategoryPill" class="categories-text-button">View all products <span>→</span></button></div>
                <div class="categories-shortcuts" id="categoriesShortcuts">
                    @foreach($categories as $category)
                        <button type="button" class="categories-shortcut" data-category="{{ $category['slug'] }}" aria-pressed="false">
                            <span class="categories-shortcut-icon"><img src="{{ asset('icons/buyer/' . $category['icon']) }}" alt="" loading="lazy"></span>
                            <span>{{ $category['name'] }}</span>
                        </button>
                    @endforeach
                </div>
            </section>
            <div class="categories-shop-layout">
                <aside class="categories-filter-panel" id="categoriesFilters" aria-label="Product filters">
                    <div class="categories-filter-heading"><h2>Filters</h2><button type="button" id="resetFilters" class="categories-reset">Clear all</button></div>
                    <div class="categories-filter-group"><h3>Price range</h3><div class="categories-price-inputs"><label><span>Min (₱)</span><input id="priceMin" type="number" min="0" step="1" inputmode="numeric" placeholder="0"></label><span class="price-separator">—</span><label><span>Max (₱)</span><input id="priceMax" type="number" min="0" step="1" inputmode="numeric" placeholder="Any"></label></div><p class="categories-filter-hint">Filters products currently loaded. Use Load more to see additional matches.</p></div>
                </aside>
                <section class="categories-results" aria-labelledby="categoryResultsTitle">
                    <div class="categories-results-top"><div><span class="categories-section-kicker">SHOP THE COLLECTION</span><h2 id="categoryResultsTitle">All products</h2></div></div><div class="categories-tools"><label class="categories-search"><svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="11" cy="11" r="6.5" stroke="currentColor" stroke-width="1.7"/><path d="m16 16 4.5 4.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg><input id="categoriesSearch" type="search" placeholder="Search products in this category" autocomplete="off" aria-label="Search products"></label><div class="categories-sort-controls" role="group" aria-label="Sort product prices">
                            <span class="categories-sort-title">Sort by</span>
                            <button type="button" class="categories-sort-button is-active" data-sort="latest" aria-pressed="true">Latest</button>
                            <button type="button" class="categories-sort-button" data-sort="price-low" aria-pressed="false">₱ Low to High <span aria-hidden="true">↑</span></button>
                            <button type="button" class="categories-sort-button" data-sort="price-high" aria-pressed="false">₱ High to Low <span aria-hidden="true">↓</span></button>
                        </div><button type="button" id="mobileFilterToggle" class="categories-mobile-filter">☷ Filters</button></div>
                    <div class="categories-active-filters" id="categoriesActiveFilters" aria-live="polite"></div>
                    <div class="product-grid categories-products-grid" id="categoriesProducts"><div class="categories-loading">Finding your next favorite…</div></div>
                    <div class="categories-loadmore"><button type="button" id="categoriesLoadMore" hidden>Load more products <span>↓</span></button></div>
                </section>
            </div>
        </div>
    </main>
</body>
</html>
