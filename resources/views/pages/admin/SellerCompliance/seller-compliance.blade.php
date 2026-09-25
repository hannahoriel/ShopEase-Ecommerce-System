{{-- resources/views/pages/admin/SellerCompliance/seller-compliance.blade.php
     HTML only. CSS  -> resources/css/admin/seller_compliance.css
                 JS   -> resources/js/admin/seller_compliance.js
                 Data -> $initialComplianceData (controller) + config/seller_compliance.php
--}}

@php
    $config  = config('admin.seller_compliance');
    $data    = $initialComplianceData ?? ['data' => [], 'summary' => []];
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

    // Everything the JS needs.
    // Read from <script type="application/json">.
    $clientConfig = [
    'apiUrl'     => url('/api/v1/admin/seller-compliance'),
    'perPage'    => $config['default_per_page'],
    'categories' => $config['categories'],
    'statuses'   => $config['statuses'],
    'initial'    => $data,
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
         resources/css/admin/seller_compliance.css
         resources/js/admin/seller_compliance.js
    --}}
    @vite([
        'resources/css/app.css',
        'resources/css/admin/seller_compliance.css',
        'resources/js/app.js',
        'resources/js/admin/seller_compliance.js',
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
                                    {{ (int) ($summary[$card['key']] ?? 0) }}
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

                    @foreach ($config['categories'] as $slug => $label)

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

                    @foreach ($config['statuses'] as $value => $status)

                        <option value="{{ $value }}">
                            {{ $status['label'] }}
                        </option>

                    @endforeach

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

                            @foreach ($config['per_page_options'] as $option)

                                <option
                                    value="{{ $option }}"
                                    @selected($option === $config['default_per_page'])
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

                 seller_modal.blade.php
                 seller_products_modal.blade.php
                 seller_decision_modal.blade.php
            ====================================================== --}}


            {{-- SELLER MODAL --}}
            @include('pages.admin.SellerCompliance.seller_modal')


            {{-- SELLER PRODUCTS MODAL --}}
            @include('pages.admin.SellerCompliance.seller_products_modal')


            {{-- REMOVE PRODUCT DECISION MODAL --}}
            @include('pages.admin.SellerCompliance.seller_decision_modal', [
                'type'    => 'remove',
                'title'   => 'Remove Product',
                'intro'   => "You are about to remove this product from the seller's store.",
                'submit'  => 'Remove',
                'reasons' => $config['remove_reasons'],
            ])


            {{-- WARNING DECISION MODAL --}}
            @include('pages.admin.SellerCompliance.seller_decision_modal', [
                'type'    => 'warn',
                'title'   => 'Issue Warning',
                'intro'   => "You are about to issue a warning for this product due to a violation of ShopEase's platform policies.",
                'submit'  => 'Issue Warning',
                'reasons' => $config['warning_reasons'],
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
        window.initialSellerComplianceData = @json($data, JSON_HEX_TAG | JSON_HEX_AMP);
    </script>

</body>

</html>
