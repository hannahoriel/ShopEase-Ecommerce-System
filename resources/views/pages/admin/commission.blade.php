@extends('layouts.admin')

@section('page-title', 'Commission')

@section('content')

@vite('resources/css/admin/commission.css')

@php
    /*
    |--------------------------------------------------------------------------
    | DEMO / FALLBACK DATA
    |--------------------------------------------------------------------------
    | Replace this with data from your CommissionController when the backend
    | is ready. The Blade remains usable now for the UI prototype.
    */
    $commissionRows = $commissionRows ?? [
        [
            'date' => '2026-05-31',
            'date_label' => 'May 31, 2026',
            'time' => '10:30 AM',
            'order_id' => '#ORD-2025',
            'seller' => 'DelaCruzShop',
            'seller_owner' => 'Juan Dela Cruz',
            'order_amount' => 2500,
            'commission' => 250,
        ],
        [
            'date' => '2026-05-31',
            'date_label' => 'May 31, 2026',
            'time' => '10:30 AM',
            'order_id' => '#ORD-2025',
            'seller' => 'DelaCruzShop',
            'seller_owner' => 'Juan Dela Cruz',
            'order_amount' => 2500,
            'commission' => 250,
        ],
        [
            'date' => '2026-05-31',
            'date_label' => 'May 31, 2026',
            'time' => '10:30 AM',
            'order_id' => '#ORD-2025',
            'seller' => 'DelaCruzShop',
            'seller_owner' => 'Juan Dela Cruz',
            'order_amount' => 2500,
            'commission' => 250,
        ],
        [
            'date' => '2026-05-31',
            'date_label' => 'May 31, 2026',
            'time' => '10:30 AM',
            'order_id' => '#ORD-2025',
            'seller' => 'DelaCruzShop',
            'seller_owner' => 'Juan Dela Cruz',
            'order_amount' => 2500,
            'commission' => 250,
        ],
        [
            'date' => '2026-05-31',
            'date_label' => 'May 31, 2026',
            'time' => '10:30 AM',
            'order_id' => '#ORD-2025',
            'seller' => 'DelaCruzShop',
            'seller_owner' => 'Juan Dela Cruz',
            'order_amount' => 2500,
            'commission' => 250,
        ],
        [
            'date' => '2026-05-31',
            'date_label' => 'May 31, 2026',
            'time' => '10:30 AM',
            'order_id' => '#ORD-2025',
            'seller' => 'DelaCruzShop',
            'seller_owner' => 'Juan Dela Cruz',
            'order_amount' => 2500,
            'commission' => 250,
        ],
        [
            'date' => '2026-05-31',
            'date_label' => 'May 31, 2026',
            'time' => '10:30 AM',
            'order_id' => '#ORD-2025',
            'seller' => 'DelaCruzShop',
            'seller_owner' => 'Juan Dela Cruz',
            'order_amount' => 2500,
            'commission' => 250,
        ],

        /* Extra demo rows so pagination / filters already work. */
        [
            'date' => '2026-05-30',
            'date_label' => 'May 30, 2026',
            'time' => '3:15 PM',
            'order_id' => '#ORD-2024',
            'seller' => 'FaithFinds',
            'seller_owner' => 'Faith Oriel',
            'order_amount' => 1890,
            'commission' => 189,
        ],
        [
            'date' => '2026-05-30',
            'date_label' => 'May 30, 2026',
            'time' => '1:20 PM',
            'order_id' => '#ORD-2023',
            'seller' => 'TechCornerPH',
            'seller_owner' => 'Mark Santos',
            'order_amount' => 3200,
            'commission' => 320,
        ],
        [
            'date' => '2026-05-29',
            'date_label' => 'May 29, 2026',
            'time' => '6:45 PM',
            'order_id' => '#ORD-2022',
            'seller' => 'DailyNeeds',
            'seller_owner' => 'Maria Cruz',
            'order_amount' => 1450,
            'commission' => 145,
        ],
        [
            'date' => '2026-05-29',
            'date_label' => 'May 29, 2026',
            'time' => '11:10 AM',
            'order_id' => '#ORD-2021',
            'seller' => 'StyleStation',
            'seller_owner' => 'Ana Reyes',
            'order_amount' => 2750,
            'commission' => 275,
        ],
        [
            'date' => '2026-05-28',
            'date_label' => 'May 28, 2026',
            'time' => '9:45 AM',
            'order_id' => '#ORD-2020',
            'seller' => 'HomeEssentials',
            'seller_owner' => 'Leo Garcia',
            'order_amount' => 4100,
            'commission' => 410,
        ],
        [
            'date' => '2026-05-28',
            'date_label' => 'May 28, 2026',
            'time' => '8:25 AM',
            'order_id' => '#ORD-2019',
            'seller' => 'GadgetHub',
            'seller_owner' => 'Carlo Mendoza',
            'order_amount' => 5990,
            'commission' => 599,
        ],
        [
            'date' => '2026-05-27',
            'date_label' => 'May 27, 2026',
            'time' => '7:10 PM',
            'order_id' => '#ORD-2018',
            'seller' => 'DelaCruzShop',
            'seller_owner' => 'Juan Dela Cruz',
            'order_amount' => 2200,
            'commission' => 220,
        ],
        [
            'date' => '2026-05-27',
            'date_label' => 'May 27, 2026',
            'time' => '4:35 PM',
            'order_id' => '#ORD-2017',
            'seller' => 'FaithFinds',
            'seller_owner' => 'Faith Oriel',
            'order_amount' => 3100,
            'commission' => 310,
        ],
        [
            'date' => '2026-05-26',
            'date_label' => 'May 26, 2026',
            'time' => '2:15 PM',
            'order_id' => '#ORD-2016',
            'seller' => 'TechCornerPH',
            'seller_owner' => 'Mark Santos',
            'order_amount' => 850,
            'commission' => 85,
        ],
        [
            'date' => '2026-05-25',
            'date_label' => 'May 25, 2026',
            'time' => '5:40 PM',
            'order_id' => '#ORD-2015',
            'seller' => 'DailyNeeds',
            'seller_owner' => 'Maria Cruz',
            'order_amount' => 1780,
            'commission' => 178,
        ],
        [
            'date' => '2026-05-24',
            'date_label' => 'May 24, 2026',
            'time' => '10:05 AM',
            'order_id' => '#ORD-2014',
            'seller' => 'StyleStation',
            'seller_owner' => 'Ana Reyes',
            'order_amount' => 2890,
            'commission' => 289,
        ],
        [
            'date' => '2026-05-23',
            'date_label' => 'May 23, 2026',
            'time' => '12:20 PM',
            'order_id' => '#ORD-2013',
            'seller' => 'HomeEssentials',
            'seller_owner' => 'Leo Garcia',
            'order_amount' => 3600,
            'commission' => 360,
        ],
        [
            'date' => '2026-05-22',
            'date_label' => 'May 22, 2026',
            'time' => '4:50 PM',
            'order_id' => '#ORD-2012',
            'seller' => 'GadgetHub',
            'seller_owner' => 'Carlo Mendoza',
            'order_amount' => 7250,
            'commission' => 725,
        ],
        [
            'date' => '2026-05-21',
            'date_label' => 'May 21, 2026',
            'time' => '1:05 PM',
            'order_id' => '#ORD-2011',
            'seller' => 'DelaCruzShop',
            'seller_owner' => 'Juan Dela Cruz',
            'order_amount' => 1990,
            'commission' => 199,
        ],
    ];

    $commissionStats = $commissionStats ?? [
        'rate' => 10,
        'total_commission' => 32800,
        'total_orders' => 105,
        'total_sellers_charged' => 105,
    ];

    /*
     * This keeps the screenshot's total count while the fallback demo data
     * only contains enough rows to demonstrate the UI.
     */
    $commissionTotalEntries = $commissionTotalEntries ?? 378;
@endphp



<div
    id="admin-content"
    class="ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300"
>
    <!-- =====================================================
         STAT CARDS
    ====================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-5">

        <!-- Platform Commission -->
        <div class="commission-stat-card">
            <div class="commission-stat-main">
                <img
                    src="{{ asset('icons/admin/commission/rate.png') }}"
                    class="commission-stat-icon"
                    alt="Platform Commission"
                >

                <div class="commission-stat-copy">
                    <p class="commission-stat-number">
                        {{ number_format($commissionStats['rate']) }}%
                    </p>

                    <p class="commission-stat-label">
                        Platform Commission
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Commission -->
        <div class="commission-stat-card">
            <div class="commission-stat-main">
                <img
                    src="{{ asset('icons/admin/commission/total-commission.png') }}"
                    class="commission-stat-icon"
                    alt="Total Commission"
                >

                <div class="commission-stat-copy">
                    <p class="commission-stat-number">
                        ₱ {{ number_format($commissionStats['total_commission']) }}
                    </p>

                    <p class="commission-stat-label">
                        Total Commission
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="commission-stat-card">
            <div class="commission-stat-main">
                <img
                    src="{{ asset('icons/admin/commission/total-orders.png') }}"
                    class="commission-stat-icon"
                    alt="Total Orders"
                >

                <div class="commission-stat-copy">
                    <p class="commission-stat-number">
                        {{ number_format($commissionStats['total_orders']) }}
                    </p>

                    <p class="commission-stat-label">
                        Total Orders
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Sellers Charged -->
        <div class="commission-stat-card">
            <div class="commission-stat-main">
                <img
                    src="{{ asset('icons/admin/commission/sellers-charged.png') }}"
                    class="commission-stat-icon"
                    alt="Total Sellers Charged"
                >

                <div class="commission-stat-copy">
                    <p class="commission-stat-number">
                        {{ number_format($commissionStats['total_sellers_charged']) }}
                    </p>

                    <p class="commission-stat-label">
                        Total Sellers Charged
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- =====================================================
         FILTER BAR
    ====================================================== -->
    <div class="commission-filter-card">
        <div class="commission-filter-row">

            <!-- Search -->
            <div class="commission-search-wrap">
                <input
                    id="commission-search"
                    type="text"
                    class="commission-search"
                    placeholder="Search order number, seller, date"
                    autocomplete="off"
                >

                <svg
                    class="commission-search-icon"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />
                </svg>
            </div>

            <!-- From Date -->
            <div id="commission-from-wrap" class="commission-date-wrap">
                <button
                    id="commission-from-trigger"
                    type="button"
                    class="commission-date-trigger"
                    aria-label="Select from date"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                        />
                    </svg>

                    <span
                        id="commission-from-text"
                        class="commission-date-text"
                    >
                        Select From Date
                    </span>
                </button>

                <input
                    id="commission-from-date"
                    type="date"
                    class="commission-date-native"
                    tabindex="-1"
                    aria-label="From date"
                >
            </div>

            <!-- To Date -->
            <div id="commission-to-wrap" class="commission-date-wrap">
                <button
                    id="commission-to-trigger"
                    type="button"
                    class="commission-date-trigger"
                    aria-label="Select to date"
                >
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                        />
                    </svg>

                    <span
                        id="commission-to-text"
                        class="commission-date-text"
                    >
                        Select To Date
                    </span>
                </button>

                <input
                    id="commission-to-date"
                    type="date"
                    class="commission-date-native"
                    tabindex="-1"
                    aria-label="To date"
                >
            </div>

            <!-- Reset -->
            <button
                id="commission-reload"
                type="button"
                class="commission-reload"
                title="Reset filters"
                aria-label="Reset filters"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"
                    />
                </svg>
            </button>

        </div>
    </div>

    <!-- =====================================================
         COMMISSION TABLE
    ====================================================== -->
    <div class="commission-table-card">

        <div class="commission-table-scroll">
            <table class="commission-table">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Seller</th>
                        <th>Order Amount</th>
                        <th>Platform Commission</th>
                    </tr>
                </thead>

                <tbody id="commission-table-body"></tbody>

            </table>

            <div
                id="commission-empty"
                class="commission-empty"
            >
                No commission records match the selected filters.
            </div>
        </div>

        <!-- Footer -->
        <div class="commission-table-footer">

            <p
                id="commission-count"
                class="commission-count"
            >
                Showing 7 out of {{ number_format($commissionTotalEntries) }} entries
            </p>

            <div class="commission-pagination">

                <button
                    id="commission-prev"
                    type="button"
                    class="commission-page-button"
                    aria-label="Previous page"
                >
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m15 6-6 6 6 6"
                        />
                    </svg>
                </button>

                <div
                    id="commission-pages"
                    class="flex items-center gap-2"
                ></div>

                <button
                    id="commission-next"
                    type="button"
                    class="commission-page-button"
                    aria-label="Next page"
                >
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m9 6 6 6-6 6"
                        />
                    </svg>
                </button>

                <select
                    id="commission-items"
                    class="commission-items"
                >
                    <option value="7" selected>
                        Items per page: 7
                    </option>

                    <option value="10">
                        Items per page: 10
                    </option>

                    <option value="20">
                        Items per page: 20
                    </option>
                </select>

            </div>
        </div>

    </div>
</div>

@php
    $commissionClientConfig = [
        'rows' => $commissionRows,
        'totalEntries' => $commissionTotalEntries,
    ];
@endphp

<script
    type="application/json"
    id="commissionConfig"
>{!! json_encode($commissionClientConfig, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

@endsection

@push('scripts')

@vite('resources/js/admin/commission.js')

@endpush