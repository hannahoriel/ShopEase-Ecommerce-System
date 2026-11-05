@extends('layouts.admin')

@section('page-title', 'Commission')

@section('content')

@vite(['resources/css/admin/commission.css', 'resources/css/admin/commission-order-modal.css'])

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
    <p id="commission-api-error" class="mb-4 text-sm text-red-700" role="alert" hidden></p>
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
                    <p class="commission-stat-number" data-commission-stat="rate">
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
                    <p class="commission-stat-number" data-commission-stat="total_commission">
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
                    <p class="commission-stat-number" data-commission-stat="total_orders">
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
                    <p class="commission-stat-number" data-commission-stat="total_sellers_charged">
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



    {{-- Same markup/sections as Seller Shipping Status Order Details (read-only in Admin) --}}
<div
        id="commissionDetailsModal"
        class="shipping-modal"
        aria-hidden="true"
    >

        <div
            id="commissionDetailsModalPanel"
            class="shipping-modal-panel"

            role="dialog"
            aria-modal="true"
            aria-labelledby="commissionModalTitle"
        >


            {{-- =================================================
                 MODAL HEADER
            ================================================== --}}

            <div
                class="
                    shipping-modal-header
                "
            >

                <h2
                    id="commissionModalTitle"
                >
                    Order Details
                </h2>


                <div
                    class="
                        ease-express-brand
                    "
                >

                    <svg
                        viewBox="0 0 48 48"
                        fill="none"
                        aria-hidden="true"
                    >

                        <path
                            d="M6 28h4V17h19v11h4"
                            stroke="#D90000"
                            stroke-width="4"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        />

                        <path
                            d="M29 20h7l6 7v7h-6"
                            fill="#D90000"
                            stroke="#D90000"
                            stroke-width="3"
                            stroke-linejoin="round"
                        />

                        <circle
                            cx="15"
                            cy="35"
                            r="4"
                            fill="#D90000"
                        />

                        <circle
                            cx="36"
                            cy="35"
                            r="4"
                            fill="#D90000"
                        />

                        <path
                            d="M4 35h7m16-7h10"
                            stroke="#D90000"
                            stroke-width="4"
                            stroke-linecap="round"
                        />

                    </svg>


                    <span>
                        Ease Express
                    </span>

                </div>

            </div>



            {{-- =================================================
                 MODAL SUMMARY
            ================================================== --}}

            <div
                class="
                    shipping-modal-summary
                "
            >


                {{-- ORDER --}}

                <div
                    class="
                        shipping-modal-order-main
                    "
                >

                    <div
                        id="shippingModalIconBox"

                        class="
                            shipping-modal-status-box
                            blue-modal
                        "
                    >

                        <img
                            id="shippingModalOrderIcon"

                            src="{{ asset('icons/seller/shipping-status/in-transit.png') }}"

                            alt="Shipping Status"
                        >

                    </div>


                    <div>

                        <p
                            id="shippingModalOrderId"

                            class="
                                modal-order-id
                            "
                        >
                            #ORD-2025
                        </p>


                        <p
                            id="shippingModalStatus"

                            class="
                                modal-status-text
                            "
                        >

                            <span
                                id="shippingModalStatusDot"

                                class="
                                    modal-status-dot
                                    blue-dot
                                "
                            ></span>


                            <span
                                id="shippingModalStatusLabel"
                            >
                                In Transit
                            </span>

                        </p>

                    </div>

                </div>



                {{-- ORDERED AT --}}

                <div
                    class="
                        modal-summary-column
                    "
                >

                    <p class="modal-summary-label">
                        Ordered at
                    </p>


                    <span
                        id="shippingModalOrderDate"
                        class="modal-summary-value"
                    >
                        May 22, 2026
                    </span>


                    <span
                        id="shippingModalOrderTime"
                        class="modal-summary-subvalue"
                    >
                        10:34 AM
                    </span>

                </div>



                {{-- ESTIMATED + TRACKING --}}

                <div
                    class="
                        modal-summary-column
                        modal-estimated-tracking-column
                    "
                >

                    <p
                        class="
                            modal-summary-label
                        "
                    >
                        Estimated Delivery
                    </p>


                    <span
                        id="shippingModalEstimated"

                        class="
                            modal-summary-value
                            estimated-value
                        "
                    >
                        May 25, 2026
                    </span>


                    <p
                        class="
                            modal-tracking-label
                        "
                    >
                        Tracking Number
                    </p>


                    <span
                        id="shippingModalTrackingNumber"

                        class="
                            modal-tracking-value
                        "
                    >
                        T23430583RHEFBW
                    </span>
                    <p class="modal-tracking-label">Current Parcel Location</p>
                    <span id="shippingModalCurrentLocation" class="modal-tracking-value">Not scanned yet</span>

                </div>

            </div>



            {{-- =================================================
                 CUSTOMER + PAYMENT
            ================================================== --}}

            <div
                class="
                    shipping-modal-customer-payment
                "
            >

                {{-- CUSTOMER --}}

                <div>

                    <h3>
                        Customer Information
                    </h3>


                    <div
                        class="
                            shipping-customer-list
                        "
                    >

                        <div>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                            >

                                <circle
                                    cx="8"
                                    cy="8"
                                    r="3"
                                    fill="#000"
                                />

                                <circle
                                    cx="16"
                                    cy="8"
                                    r="3"
                                    fill="#000"
                                />

                                <path
                                    d="M2 19c.8-3 3-5 6-5s5.2 2 6 5H2Z"
                                    fill="#000"
                                />

                                <path
                                    d="M12 19c.7-2.5 2.5-4 5-4 2.7 0 4.5 1.5 5 4h-10Z"
                                    fill="#000"
                                />

                            </svg>


                            <span
                                id="shippingModalCustomer"
                            >
                                Juan Dela Cruz
                            </span>

                        </div>


                        <div>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#000"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <rect
                                    x="2"
                                    y="5"
                                    width="20"
                                    height="14"
                                    rx="2"
                                />

                                <path
                                    d="M3 7l9 7 9-7"
                                />

                            </svg>


                            <span>
                                juandelacruz@gmail.com
                            </span>

                        </div>


                        <div>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#000"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path
                                    d="M7.5 4.5c.5 0 1 .3 1.3.8l1.7 3.1c.2.4.2 1-.1 1.4l-1.2 1.4a13 13 0 0 0 3.6 3.6l1.4-1.2c.4-.3 1-.4 1.4-.1l3.1 1.7c.5.3.8.8.8 1.3V19c0 .8-.7 1.5-1.5 1.5C10.6 20.5 3.5 13.4 3.5 4.5 3.5 3.7 4.2 3 5 3h2.5c0 .5 0 1 0 1.5Z"
                                />

                            </svg>


                            <span
                                id="shippingModalPhone"
                            >
                                0917 123 4567
                            </span>

                        </div>


                        <div>

                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="#000"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >

                                <path
                                    d="M12 21s7-5.6 7-12a7 7 0 1 0-14 0c0 6.4 7 12 7 12Z"
                                />

                                <circle
                                    cx="12"
                                    cy="9"
                                    r="2.3"
                                />

                            </svg>


                            <span>
                                <span id="shippingModalAddress">—</span>
                            </span>

                        </div>

                    </div>

                </div>



                {{-- PAYMENT --}}

                <div>

                    <h3>
                        Payment Method
                    </h3>


                    <div
                        class="
                            shipping-payment
                        "
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#000"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >

                            <rect
                                x="3"
                                y="6"
                                width="18"
                                height="13"
                                rx="2"
                            />

                            <path d="M3 10h18"/>

                            <path d="M7 14h4"/>

                        </svg>


                        <div>

                            <p>
                                Payment:
                                <span>
                                    <span id="shippingModalPaymentMethod">COD</span>
                                </span>
                            </p>


                            <small>
                                <span id="shippingModalPaymentDescription">Payment upon Delivery</span>
                            </small>

                        </div>

                    </div>

                </div>

            </div>



            {{-- =================================================
                 ITEMS ORDERED
            ================================================== --}}

            <div
                class="
                    shipping-modal-items
                "
            >

                <h3>
                    Items Ordered
                </h3>

                <div id="shippingModalItemsList" aria-live="polite">Loading order items…</div>

            </div>



            {{-- =================================================
                 CUSTOMER NOTES
            ================================================== --}}

            <div
                class="
                    shipping-modal-notes
                "
            >

                <h3>
                    Customer Notes
                </h3>


                <div>
                    <span id="shippingModalNotes">No customer notes provided.</span>
                </div>

            </div>



            {{-- =================================================
                 TRACKING PROGRESS
            ================================================== --}}

            <section
                class="
                    shipping-tracking-progress
                "
            >

                <h3>
                    Tracking Progress
                </h3>


                <div
                    class="
                        modal-progress-tracker
                    "
                >

                    {{-- IN TRANSIT --}}

                    <div
                        id="modalProgressTransit"
                        class="modal-progress-step"
                    >

                        <img
                            src="{{ asset('icons/seller/shipping-status/in-transit-blue.png') }}"
                            alt="In Transit"
                        >

                        <span>
                            In Transit
                        </span>

                    </div>


                    <div
                        id="modalProgressLine1"
                        class="modal-progress-line"
                    ></div>


                    {{-- OUT FOR DELIVERY --}}

                    <div
                        id="modalProgressOut"
                        class="modal-progress-step"
                    >

                        <img
                            src="{{ asset('icons/seller/shipping-status/out-for-delivery-gray.png') }}"
                            alt="Out for Delivery"
                        >

                        <span>
                            Out for Delivery
                        </span>

                    </div>


                    <div
                        id="modalProgressLine2"
                        class="modal-progress-line"
                    ></div>


                    {{-- DELIVERED --}}

                    <div
                        id="modalProgressDelivered"
                        class="modal-progress-step"
                    >

                        <img
                            src="{{ asset('icons/seller/shipping-status/delivered-gray.png') }}"
                            alt="Delivered"
                        >

                        <span>
                            Delivered
                        </span>

                    </div>

                </div>

            </section>



            {{-- =================================================
                 TRACKING HISTORY
            ================================================== --}}

            <section
                class="
                    shipping-history-section
                "
            >

                <h3>
                    Tracking History
                </h3>


                <div
                    id="shippingTrackingHistoryList"

                    class="
                        shipping-history-list
                    "
                ></div>

            </section>




            {{-- Admin-only additional section; the sections above reuse the Seller Shipping Status modal. --}}
            <section class="admin-commission-breakdown" aria-label="Admin platform commission">
                <div class="admin-commission-breakdown-heading">
                    <div>
                        <h3>Platform Commission</h3>
                        <p>Commission charged on this order</p>
                    </div>
                    <span class="admin-commission-earned">Earned</span>
                </div>
                <div class="admin-commission-grid">
                    <div><span>Order Amount</span><strong id="commissionDetailOrderAmount">—</strong></div>
                    <div><span>Commission Rate</span><strong id="commissionDetailRate">—</strong></div>
                    <div class="admin-commission-highlight"><span>Admin Commission</span><strong id="commissionDetailEarned">—</strong></div>
                    <div><span>Seller Amount After Commission</span><strong id="commissionDetailSellerNet">—</strong></div>
                </div>
                <p class="admin-commission-note">Based on the recorded commission transaction. Other order adjustments are shown separately.</p>
            </section>

            {{-- =================================================
                 FOOTER
            ================================================== --}}

            <div
                class="
                    shipping-modal-footer
                "
            >

                <button
                    type="button"
                    id="closeCommissionModal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

@php
    $commissionClientConfig = [
        'rows' => $commissionRows,
        'totalEntries' => $commissionTotalEntries,
        'apiUrl' => url('/api/v1/admin/commissions'),
        'detailsUrl' => url('/admin/commission/transactions'),
    ];
@endphp

<script
    type="application/json"
    id="commissionConfig"
>{!! json_encode($commissionClientConfig, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

@endsection

@push('scripts')

@vite(['resources/js/admin/commission.js', 'resources/js/admin/commission-order-modal.js'])

@endpush