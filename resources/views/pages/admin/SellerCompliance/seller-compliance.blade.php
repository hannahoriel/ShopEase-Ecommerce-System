{{-- resources/views/pages/admin/SellerCompliance/seller-compliance.blade.php
     HTML only. CSS  -> resources/css/admin/seller-compliance.css
                 JS   -> resources/js/admin/seller-compliance.js
                 Data -> $initialComplianceData (controller) + config/seller-compliance.php
--}}

@php
    $config = config('seller-compliance', []);

    $config = array_merge([
        'default_per_page' => 10,
        'per_page_options' => [10, 20, 50],
        'categories' => [],
        'statuses' => [],
        'remove_reasons' => [],
        'warning_reasons' => [],
    ], is_array($config) ? $config : []);
    // Keep Seller Compliance aligned with the full list in Seller Inventory.
    // Values use the exact normalized slugs expected by the existing API filter.
    $inventoryCategories = [
        'pet-supplies' => 'Pet Supplies',
        'electronics-and-gadgets' => 'Electronics & Gadgets',
        'womens-apparel' => "Women's Apparel",
        'mens-apparel' => "Men's Apparel",
        'kids-and-baby' => 'Kids & Baby',
        'home-and-garden' => 'Home & Garden',
        'sports-and-outdoors' => 'Sports & Outdoors',
        'health-and-beauty' => 'Health & Beauty',
        'books-and-media' => 'Books & Media',
        'food-and-gourmet' => 'Food & Gourmet',
        'automotive-motorcycle' => 'Automotive & Motorcycle',
        'furniture-and-office-equipment' => 'Furniture & Office Equipment',
        'jewelry-and-watches' => 'Jewelry & Watches',
        'office-and-school-supplies' => 'Office & School Supplies',
    ];

    // Also retain categories optionally added by the existing config file.
    // Normalize their values to the same slugs used by the admin API.
    $extraCategories = [];
    foreach (($config['categories'] ?? []) as $slug => $label) {
        if (!is_string($label) || trim($label) === '') continue;
        $value = is_string($slug) ? $slug : $label;
        $normalized = \Illuminate\Support\Str::slug(
            str_replace(['&', '’'], ['and', "'"], $value)
        );
        if ($normalized !== '' && !array_key_exists($normalized, $inventoryCategories)) {
            $extraCategories[$normalized] = $label;
        }
    }

    $availableCategoryOptions = $inventoryCategories + $extraCategories;
    $config['categories'] = $availableCategoryOptions;

    $data = $initialComplianceData ?? ['data' => [], 'summary' => []];
    $summary = $data['summary'] ?? [];

    $statCards = [
        [
            'key'   => 'compliant',
            'label' => 'Compliant',
            'icon'  => 'compliant',
            'trend' => 'of total sellers',
        ],
        [
            'key'   => 'warnings',
            'label' => 'Warnings Issued',
            'icon'  => 'warnings',
            'trend' => 'of total sellers',
        ],
        [
            'key'   => 'under_review',
            'label' => 'Under Review',
            'icon'  => 'under-review',
            'trend' => 'of total sellers',
        ],
        [
            'key'   => 'suspended',
            'label' => 'Suspended',
            'icon'  => 'suspended',
            'trend' => 'of total sellers',
        ],
        [
            'key'   => 'total_sellers',
            'label' => 'Total Sellers',
            'icon'  => 'sellers',
            'trend' => 'from last month',
        ],
    ];

    $clientConfig = [
        'apiUrl'     => url('/admin/seller-compliance/data'),
        'perPage'    => $config['default_per_page'] ?? 10,
        'categories' => $config['categories'] ?? [],
        'statuses'   => array_intersect_key($config['statuses'] ?? [], array_flip(['compliant', 'warning', 'suspended'])),
        'initial'    => ['data' => [], 'summary' => []],
    ];
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>ShopEase - Seller Compliance</title>

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{-- Vite --}}
    {{-- IMPORTANT:
         Actual filenames:
         resources/css/admin/seller-compliance.css
         resources/js/admin/seller-compliance.js
    --}}
    @vite([
        'resources/css/app.css',
        'resources/css/admin/seller-compliance.css',
        'resources/js/app.js',
        'resources/js/admin/seller-compliance.js',
    ])
</head>


<body class="seller-compliance-body">

    {{-- =========================================================
         SIDEBAR
    ========================================================== --}}
    @include('components.admin.sidebar')


    {{-- =========================================================
         NAVBAR
    ========================================================== --}}
    @section('page-title', 'Seller Compliance')

    @include('components.admin.navbar')


    {{-- =========================================================
         MAIN CONTENT
    ========================================================== --}}
    <main
        id="admin-content"
        class="seller-compliance-page"
    >

        <div class="seller-compliance-content">


            {{-- =====================================================
                 SUMMARY CARDS
            ====================================================== --}}
            <div class="sc-summary-grid">

                @foreach ($statCards as $card)

                    <div class="compliance-stat-card">

                        <div class="compliance-stat-main">

                            <img
                                src="{{ asset('icons/admin/seller-compliance/' . $card['icon'] . '.png') }}"
                                alt="{{ $card['label'] }}"
                                class="compliance-stat-icon"
                            >

                            <div class="compliance-stat-content">

                                <div
                                    class="compliance-stat-number"
                                    data-summary="{{ $card['key'] }}"
                                >
                                    —
                                </div>

                                <div class="compliance-stat-label">
                                    {{ $card['label'] }}
                                </div>

                            </div>

                        </div>


                        {{-- Filled by JS only when
                             summary.trends.{key} is provided by API --}}
                        <div
                            class="compliance-stat-growth"
                            data-trend="{{ $card['key'] }}"
                            data-trend-label="{{ $card['trend'] }}"
                            hidden
                        ></div>

                    </div>

                @endforeach

            </div>


            {{-- =====================================================
                 FILTER BAR
            ====================================================== --}}
            <section class="compliance-filter-card">

                {{-- SEARCH --}}
                <div class="compliance-search-wrap">

                    <input
                        type="text"
                        id="sellerSearch"
                        class="compliance-search"
                        placeholder="Search name, email, and phone"
                        autocomplete="off"
                    >

                    <svg
                        class="compliance-search-icon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <circle
                            cx="11"
                            cy="11"
                            r="7"
                        />

                        <path d="M20 20l-4-4" />
                    </svg>

                </div>


                {{-- CATEGORY FILTER --}}
                <select
                    id="sellerTypeFilter"
                    class="compliance-filter filter-user-type"
                    aria-label="Filter by category"
                >

                    <option value="all">
                        All Categories
                    </option>

                    @foreach ($availableCategoryOptions as $slug => $label)

                        <option value="{{ $slug }}">
                            {{ $label }}
                        </option>

                    @endforeach

                </select>


                {{-- COMPLIANCE FILTER --}}
                <select
                    id="complianceFilter"
                    class="compliance-filter filter-compliance"
                    aria-label="Filter by compliance"
                >

                    <option value="all">
                        All Compliance
                    </option>

                    <option value="compliant">Compliant</option>
                    <option value="warning">Warning</option>
                    <option value="suspended">Suspended</option>

                </select>


                {{-- REFRESH BUTTON --}}
                <button
                    type="button"
                    id="refreshCompliance"
                    class="compliance-refresh"
                    data-action="refresh"
                    aria-label="Refresh seller compliance"
                >

                    <svg
                        id="complianceRefreshIcon"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >

                        <path d="M20 11a8.1 8.1 0 0 0-15.5-2" />

                        <path d="M4 5v4h4" />

                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2" />

                        <path d="M20 19v-4h-4" />

                    </svg>

                </button>

            </section>


            {{-- =====================================================
                 SELLER TABLE
            ====================================================== --}}
            <section class="compliance-table-card">

                {{-- TABLE HEADER --}}
                <div class="compliance-table-header">

                    <div>
                        Sellers
                    </div>

                    <div>
                        Category
                    </div>

                    <div>
                        Products
                    </div>

                    <div>
                        Compliance score
                    </div>

                    <div>
                        Status
                    </div>

                </div>


                {{-- TABLE BODY
                     Rows are rendered by JavaScript --}}
                <div
                    id="sellerComplianceTable"
                    class="compliance-table-body"
                ></div>


                {{-- NO RESULTS --}}
                <div
                    id="sellerComplianceNoResults"
                    class="compliance-empty"
                    hidden
                >

                    <p>
                        No sellers found.
                    </p>

                    <p>
                        Try another search or filter.
                    </p>

                </div>


                {{-- PAGINATION --}}
                <div class="compliance-pagination">

                    <p id="sellerShowingText">
                        Showing 0-0 out of 0 entries
                    </p>


                    <div class="compliance-pagination-controls">

                        {{-- PREVIOUS PAGE --}}
                        <button
                            type="button"
                            class="pagination-button"
                            data-action="page-prev"
                            id="sellerPreviousPage"
                            aria-label="Previous page"
                        >
                            ‹
                        </button>


                        {{-- PAGE NUMBERS --}}
                        <span
                            id="sellerPageNumbers"
                            class="compliance-page-numbers"
                        ></span>


                        {{-- NEXT PAGE --}}
                        <button
                            type="button"
                            class="pagination-button"
                            data-action="page-next"
                            id="sellerNextPage"
                            aria-label="Next page"
                        >
                            ›
                        </button>


                        {{-- ITEMS PER PAGE --}}
                        <select
                            id="sellerItemsPerPage"
                            class="compliance-per-page"
                            aria-label="Items per page"
                        >

                            @foreach (($config['per_page_options'] ?? [10, 20, 50]) as $option)

                                <option
                                    value="{{ $option }}"
                                    @selected($option === ($config['default_per_page'] ?? 10))
                                >
                                    Items per page: {{ $option }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </section>


            {{-- =====================================================
                 MODALS

                 Actual files:
                 resources/views/pages/admin/SellerCompliance/

                 seller-modal.blade.php
                 seller-products-modal.blade.php
                 seller-decision-modal.blade.php
            ====================================================== }}


            {{-- SELLER MODAL --}}
            @include('pages.admin.SellerCompliance.seller-modal')


            {{-- SELLER PRODUCTS MODAL --}}
            @include('pages.admin.SellerCompliance.seller-products-modal')


            {{-- REMOVE PRODUCT DECISION MODAL --}}
            @include('pages.admin.SellerCompliance.seller-decision-modal', [
                'type'    => 'remove',
                'title'   => 'Remove Product',
                'intro'   => "You are about to remove this product from the seller's store.",
                'submit'  => 'Remove',
                'reasons' => $config['remove_reasons'] ?? [],
            ])


            {{-- WARNING DECISION MODAL --}}
            @include('pages.admin.SellerCompliance.seller-decision-modal', [
                'type'    => 'warn',
                'title'   => 'Issue Warning',
                'intro'   => "You are about to issue a warning for this product due to a violation of ShopEase's platform policies.",
                'submit'  => 'Issue Warning',
                'reasons' => $config['warning_reasons'] ?? [],
            ])


            {{-- =====================================================
                 FLASH MESSAGE
            ====================================================== --}}
            <div
                id="productDecisionFlash"
                class="product-decision-flash"
                role="status"
                aria-live="polite"
                aria-atomic="true"
            >

                {{-- FLASH ICON --}}
                <div
                    class="product-decision-flash-icon"
                    aria-hidden="true"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path d="M5 12l4 4L19 6" />
                    </svg>

                </div>


                {{-- FLASH TEXT --}}
                <div class="product-decision-flash-copy">

                    <strong data-flash="title"></strong>

                    <span data-flash="message"></span>

                </div>


                {{-- CLOSE FLASH --}}
                <button
                    type="button"
                    class="product-decision-flash-close"
                    data-action="flash-close"
                    aria-label="Close notification"
                >

                    <svg
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                    >

                        <path d="M6 6l12 12" />

                        <path d="M18 6L6 18" />

                    </svg>

                </button>

            </div>

        </div>

    </main>


    {{-- =========================================================
         JAVASCRIPT CONFIG
    ========================================================== --}}
    <script
        type="application/json"
        id="sellerComplianceConfig"
    >@json($clientConfig, JSON_HEX_TAG | JSON_HEX_AMP)</script>

    <script>
        window.initialSellerComplianceData = null;
    </script>

</body>

</html>
