@extends('layouts.admin')

@section('page-title', 'Commission')

@section('content')

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

<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

    #admin-content,
    #admin-content * {
        font-family: 'Poppins', sans-serif;
    }

    #admin-content {
        color: #17120F;
    }

    /* =========================================================
       STAT CARDS
       Same visual system used by User Management / Complaints.
    ========================================================== */
    .commission-stat-card {
        min-height: 90px;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(42, 20, 15, 0.05);
        padding: 16px;
        box-sizing: border-box;
        transition: transform .3s ease, box-shadow .3s ease;
    }

    .commission-stat-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(42, 20, 15, 0.07);
    }

    .commission-stat-main {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /*
     * IMPORTANT:
     * The PNG itself already contains the pink rounded background,
     * so there is NO extra icon wrapper / pink square here.
     */
    .commission-stat-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        object-fit: contain;
        display: block;
    }

    .commission-stat-copy {
        min-width: 0;
    }

    .commission-stat-number {
        margin: 0;
        color: #17120F;
        font-size: 23px;
        line-height: 1;
        font-weight: 600;
        white-space: nowrap;
    }

    .commission-stat-label {
        margin: 5px 0 0;
        color: #8C8784;
        font-size: 13px;
        line-height: 1.15;
        font-weight: 400;
        white-space: nowrap;
    }

    /* =========================================================
       FILTER BAR
       Same text/border/focus colors as Seller Compliance.
    ========================================================== */
    .commission-filter-card {
        margin-bottom: 20px;
        padding: 16px;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
    }

    .commission-filter-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
    }

    .commission-search-wrap {
        position: relative;
        width: 330px;
        flex: 0 0 330px;
    }

    .commission-search {
        width: 100%;
        height: 36px;
        border: 1px solid #D9D6D4;
        border-radius: 8px;
        background: #FFFFFF;
        padding: 0 42px 0 13px;
        outline: none;
        color: #76716E;
        font-size: 13px;
        font-weight: 400;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .commission-search::placeholder {
        color: #76716E;
        opacity: 1;
    }

    .commission-search:focus,
    .commission-date-trigger:focus,
    .commission-date-wrap.is-open .commission-date-trigger {
        border-color: #7B1B1B;
        box-shadow: 0 0 0 2px rgba(123, 27, 27, .10);
    }

    .commission-search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        width: 18px;
        height: 18px;
        transform: translateY(-50%);
        color: #76716E;
        pointer-events: none;
    }

    .commission-date-wrap {
        position: relative;
        width: 195px;
        flex: 0 0 195px;
        height: 36px;
    }

    .commission-date-trigger {
        width: 100%;
        height: 36px;
        border: 1px solid #D9D6D4;
        border-radius: 8px;
        background: #FFFFFF;
        padding: 0 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        outline: none;
        color: #76716E;
        cursor: pointer;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .commission-date-trigger:hover {
        border-color: #C9C4C1;
    }

    .commission-date-trigger svg {
        width: 15px;
        height: 15px;
        flex: 0 0 15px;
        color: #76716E;
    }

    .commission-date-text {
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #76716E;
        font-size: 12px;
        line-height: 1;
        font-weight: 400;
        text-align: left;
    }

    .commission-date-text.has-value {
        color: #76716E;
    }

    .commission-date-native {
        position: absolute;
        right: 8px;
        bottom: 0;
        width: 1px;
        height: 1px;
        opacity: 0;
        border: 0;
        padding: 0;
        pointer-events: none;
    }

    .commission-reload {
        width: 40px;
        height: 36px;
        flex: 0 0 40px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #17120F;
        cursor: pointer;
        transition: background .15s ease, transform .15s ease;
    }

    .commission-reload:hover {
        background: #FFF0EC;
    }

    .commission-reload:active {
        transform: scale(.95);
    }

    .commission-reload svg {
        width: 20px;
        height: 20px;
    }

    .commission-reload.is-spinning svg {
        animation: commissionReloadSpin .5s linear;
    }

    @keyframes commissionReloadSpin {
        to {
            transform: rotate(360deg);
        }
    }

    /* =========================================================
       TABLE
    ========================================================== */
    .commission-table-card {
        width: 100%;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, .04);
        overflow: hidden;
    }

    .commission-table-scroll {
        overflow-x: auto;
    }

    .commission-table {
        width: 100%;
        min-width: 900px;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .commission-table thead tr {
        border-bottom: 1px solid #E5E7EB;
    }

    .commission-table th {
        padding: 12px 16px;
        text-align: left;
        color: #8C8784;
        font-size: 12px;
        line-height: 1.2;
        font-weight: 500;
        white-space: nowrap;
    }

    .commission-table th:nth-child(1) { width: 15%; }
    .commission-table th:nth-child(2) { width: 18%; }
    .commission-table th:nth-child(3) { width: 24%; }
    .commission-table th:nth-child(4) { width: 19%; }
    .commission-table th:nth-child(5) { width: 24%; }

    .commission-table tbody tr {
        border-bottom: 1px solid #E5E7EB;
        transition: background .15s ease;
    }

    .commission-table tbody tr:hover {
        background: #FFF9F7;
    }

    .commission-table td {
        padding: 10px 16px;
        vertical-align: middle;
        color: #17120F;
        font-size: 12px;
        line-height: 1.25;
        font-weight: 400;
    }

    .commission-date-cell {
        color: #17120F;
        font-size: 12px;
        line-height: 1.35;
        font-weight: 400;
        white-space: nowrap;
    }

    .commission-order-id {
        color: #17120F;
        font-size: 12px;
        line-height: 1.25;
        font-weight: 500;
        white-space: nowrap;
    }

    .commission-seller-cell {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 0;
    }

    .commission-seller-avatar {
        width: 30px;
        height: 30px;
        flex: 0 0 30px;
        border-radius: 50%;
        background: #D9D9D9;
    }

    .commission-seller-copy {
        min-width: 0;
        line-height: 1.05;
    }

    .commission-seller-name {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #17120F;
        font-size: 12px;
        font-weight: 600;
    }

    .commission-seller-owner {
        display: block;
        margin-top: 2px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #8C8784;
        font-size: 10px;
        font-weight: 400;
    }

    .commission-money {
        color: #17120F;
        font-size: 12px;
        line-height: 1.25;
        font-weight: 400;
        white-space: nowrap;
    }

    .commission-empty {
        display: none;
        padding: 42px 16px;
        text-align: center;
        color: #8C8784;
        font-size: 12px;
    }

    /* =========================================================
       TABLE FOOTER / PAGINATION
    ========================================================== */
    .commission-table-footer {
        min-height: 50px;
        padding: 10px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        border-top: 1px solid #E5E7EB;
    }

    .commission-count {
        margin: 0;
        color: #8C8784;
        font-size: 12px;
        font-weight: 400;
    }

    .commission-pagination {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .commission-page-button {
        width: 28px;
        height: 28px;
        border: 0;
        border-radius: 6px;
        background: transparent;
        color: #374151;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 400;
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
    }

    .commission-page-button:hover:not(:disabled) {
        background: #F3F4F6;
    }

    .commission-page-button.active {
        background: #FFD1C2;
        color: #7B1B1B;
        font-weight: 600;
    }

    .commission-page-button:disabled {
        opacity: .35;
        cursor: default;
    }

    .commission-page-button svg {
        width: 14px;
        height: 14px;
    }

    .commission-items {
        height: 28px;
        margin-left: 4px;
        border: 1px solid #F0B9AC;
        border-radius: 6px;
        background: #FFF5F1;
        padding: 0 28px 0 10px;
        outline: none;
        color: #374151;
        font-size: 12px;
        font-weight: 400;
        cursor: pointer;
    }


    /* Final typography lock — mirrors Complaints table content */
    .commission-table tbody td,
    .commission-table tbody td * {
        font-size: 12px;
    }

    .commission-table tbody td .commission-seller-owner {
        font-size: 10px;
    }

    .commission-page-button {
        font-size: 12px;
    }

    .commission-items {
        height: 28px;
        font-size: 12px;
    }

    /* =========================================================
       RESPONSIVE
    ========================================================== */
    @media (max-width: 1280px) {
        .commission-filter-row {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 1024px) {
        .commission-search-wrap {
            width: 100%;
            flex: 1 1 100%;
        }
    }

    @media (max-width: 640px) {

        .commission-stat-card {
            min-height: 104px;
        }

        .commission-filter-row {
            display: grid;
            grid-template-columns: 1fr;
        }

        .commission-search-wrap,
        .commission-date-wrap {
            width: 100%;
            max-width: none;
            flex: none;
        }

        .commission-reload {
            justify-self: end;
        }

        .commission-table-footer {
            align-items: flex-start;
            flex-direction: column;
        }

        .commission-pagination {
            width: 100%;
            flex-wrap: wrap;
        }
    }
</style>

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

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       DATA
    ========================================================== */
    const commissionRows =
        @json($commissionRows);

    const originalTotalEntries =
        Number(@json($commissionTotalEntries));

    /* =========================================================
       ELEMENTS
    ========================================================== */
    const search =
        document.getElementById('commission-search');

    const fromWrap =
        document.getElementById('commission-from-wrap');

    const fromTrigger =
        document.getElementById('commission-from-trigger');

    const fromDate =
        document.getElementById('commission-from-date');

    const fromText =
        document.getElementById('commission-from-text');

    const toWrap =
        document.getElementById('commission-to-wrap');

    const toTrigger =
        document.getElementById('commission-to-trigger');

    const toDate =
        document.getElementById('commission-to-date');

    const toText =
        document.getElementById('commission-to-text');

    const reload =
        document.getElementById('commission-reload');

    const tbody =
        document.getElementById('commission-table-body');

    const empty =
        document.getElementById('commission-empty');

    const count =
        document.getElementById('commission-count');

    const prev =
        document.getElementById('commission-prev');

    const next =
        document.getElementById('commission-next');

    const pages =
        document.getElementById('commission-pages');

    const items =
        document.getElementById('commission-items');

    /* =========================================================
       STATE
    ========================================================== */
    let currentPage = 1;
    let itemsPerPage = Number(items.value);

    /* =========================================================
       FORMATTERS
    ========================================================== */
    function formatMoney(value) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(Number(value || 0));
    }

    function formatCompactAmount(value) {
        return `₱ ${new Intl.NumberFormat('en-PH', {
            maximumFractionDigits: 0
        }).format(Number(value || 0))}`;
    }

    function formatDateInput(value) {
        if (!value) {
            return '';
        }

        const [year, month, day] =
            value.split('-').map(Number);

        const date =
            new Date(
                year,
                month - 1,
                day
            );

        return new Intl.DateTimeFormat('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        }).format(date);
    }

    function normalize(value) {
        return String(value || '')
            .trim()
            .toLowerCase();
    }

    /* =========================================================
       DATE PICKER
    ========================================================== */
    function openDatePicker(input, wrap) {
        wrap.classList.add('is-open');

        try {
            if (typeof input.showPicker === 'function') {
                input.showPicker();
            } else {
                input.focus();
                input.click();
            }
        } catch (error) {
            input.focus();
            input.click();
        }
    }

    function syncDateLabel(
        input,
        label,
        emptyLabel
    ) {
        if (input.value) {
            label.textContent =
                formatDateInput(input.value);

            label.classList.add('has-value');
        } else {
            label.textContent =
                emptyLabel;

            label.classList.remove('has-value');
        }
    }

    fromTrigger.addEventListener(
        'click',
        function () {
            openDatePicker(
                fromDate,
                fromWrap
            );
        }
    );

    toTrigger.addEventListener(
        'click',
        function () {
            openDatePicker(
                toDate,
                toWrap
            );
        }
    );

    fromDate.addEventListener(
        'change',
        function () {
            fromWrap.classList.remove('is-open');

            syncDateLabel(
                fromDate,
                fromText,
                'Select From Date'
            );

            /*
             * Keep the date range valid.
             */
            if (
                fromDate.value &&
                toDate.value &&
                fromDate.value > toDate.value
            ) {
                toDate.value =
                    fromDate.value;

                syncDateLabel(
                    toDate,
                    toText,
                    'Select To Date'
                );
            }

            currentPage = 1;
            render();
        }
    );

    toDate.addEventListener(
        'change',
        function () {
            toWrap.classList.remove('is-open');

            syncDateLabel(
                toDate,
                toText,
                'Select To Date'
            );

            if (
                fromDate.value &&
                toDate.value &&
                toDate.value < fromDate.value
            ) {
                fromDate.value =
                    toDate.value;

                syncDateLabel(
                    fromDate,
                    fromText,
                    'Select From Date'
                );
            }

            currentPage = 1;
            render();
        }
    );

    [fromDate, toDate].forEach(input => {
        input.addEventListener(
            'blur',
            function () {
                fromWrap.classList.remove('is-open');
                toWrap.classList.remove('is-open');
            }
        );
    });

    /* =========================================================
       FILTERING
    ========================================================== */
    function getFilteredRows() {
        const query =
            normalize(search.value);

        const start =
            fromDate.value || '';

        const end =
            toDate.value || '';

        return commissionRows.filter(row => {

            const searchable =
                normalize(
                    [
                        row.order_id,
                        row.seller,
                        row.seller_owner,
                        row.date_label,
                        row.time
                    ].join(' ')
                );

            const matchesSearch =
                !query ||
                searchable.includes(query);

            const matchesFrom =
                !start ||
                row.date >= start;

            const matchesTo =
                !end ||
                row.date <= end;

            return (
                matchesSearch &&
                matchesFrom &&
                matchesTo
            );
        });
    }

    /* =========================================================
       TABLE
    ========================================================== */
    function renderRows(filteredRows) {

        const startIndex =
            (currentPage - 1) * itemsPerPage;

        const pageRows =
            filteredRows.slice(
                startIndex,
                startIndex + itemsPerPage
            );

        tbody.innerHTML =
            pageRows.map(row => `
                <tr>
                    <td>
                        <div class="commission-date-cell">
                            ${row.date_label}<br>
                            ${row.time}
                        </div>
                    </td>

                    <td>
                        <span class="commission-order-id">
                            ${row.order_id}
                        </span>
                    </td>

                    <td>
                        <div class="commission-seller-cell">
                            <span class="commission-seller-avatar"></span>

                            <span class="commission-seller-copy">
                                <span class="commission-seller-name">
                                    ${row.seller}
                                </span>

                                <span class="commission-seller-owner">
                                    ${row.seller_owner}
                                </span>
                            </span>
                        </div>
                    </td>

                    <td>
                        <span class="commission-money">
                            ${formatCompactAmount(row.order_amount)}
                        </span>
                    </td>

                    <td>
                        <span class="commission-money">
                            ${formatMoney(row.commission)}
                        </span>
                    </td>
                </tr>
            `).join('');

        const hasRows =
            pageRows.length > 0;

        tbody.style.display =
            hasRows ? '' : 'none';

        empty.style.display =
            hasRows ? 'none' : 'block';
    }

    /* =========================================================
       PAGINATION
    ========================================================== */
    function getPageNumbers(totalPages) {

        if (totalPages <= 5) {
            return Array.from(
                { length: totalPages },
                (_, index) => index + 1
            );
        }

        if (currentPage <= 3) {
            return [1, 2, 3, 4, totalPages];
        }

        if (currentPage >= totalPages - 2) {
            return [
                1,
                totalPages - 3,
                totalPages - 2,
                totalPages - 1,
                totalPages
            ];
        }

        return [
            1,
            currentPage - 1,
            currentPage,
            currentPage + 1,
            totalPages
        ];
    }

    function renderPagination(filteredRows) {

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    itemsPerPage
                )
            );

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        prev.disabled =
            currentPage === 1;

        next.disabled =
            currentPage === totalPages;

        const pageNumbers =
            getPageNumbers(totalPages);

        pages.innerHTML = '';

        let previousPageNumber = 0;

        pageNumbers.forEach(pageNumber => {

            if (
                previousPageNumber &&
                pageNumber - previousPageNumber > 1
            ) {
                const ellipsis =
                    document.createElement('span');

                ellipsis.textContent = '…';

                ellipsis.className =
                    'px-1 text-[11px] text-[#8C8784]';

                pages.appendChild(
                    ellipsis
                );
            }

            const button =
                document.createElement('button');

            button.type = 'button';

            button.className =
                'commission-page-button' +
                (
                    pageNumber === currentPage
                        ? ' active'
                        : ''
                );

            button.textContent =
                pageNumber;

            button.addEventListener(
                'click',
                function () {
                    currentPage =
                        pageNumber;

                    render();
                }
            );

            pages.appendChild(
                button
            );

            previousPageNumber =
                pageNumber;
        });
    }

    function renderCount(filteredRows) {

        const visibleStart =
            filteredRows.length
                ? (
                    (currentPage - 1) *
                    itemsPerPage
                ) + 1
                : 0;

        const visibleEnd =
            Math.min(
                currentPage * itemsPerPage,
                filteredRows.length
            );

        /*
         * Keep 378 as the untouched-state total, matching the design.
         * Once a filter is used, show the actual filtered demo result.
         */
        const hasActiveFilter =
            Boolean(
                search.value.trim() ||
                fromDate.value ||
                toDate.value
            );

        const total =
            hasActiveFilter
                ? filteredRows.length
                : originalTotalEntries;

        const showing =
            filteredRows.length
                ? visibleEnd - visibleStart + 1
                : 0;

        count.textContent =
            `Showing ${showing} out of ${total.toLocaleString()} entries`;
    }

    /* =========================================================
       MAIN RENDER
    ========================================================== */
    function render() {

        const filteredRows =
            getFilteredRows();

        const totalPages =
            Math.max(
                1,
                Math.ceil(
                    filteredRows.length /
                    itemsPerPage
                )
            );

        if (currentPage > totalPages) {
            currentPage =
                totalPages;
        }

        renderRows(
            filteredRows
        );

        renderPagination(
            filteredRows
        );

        renderCount(
            filteredRows
        );
    }

    /* =========================================================
       EVENTS
    ========================================================== */
    search.addEventListener(
        'input',
        function () {
            currentPage = 1;
            render();
        }
    );

    items.addEventListener(
        'change',
        function () {
            itemsPerPage =
                Number(items.value);

            currentPage = 1;
            render();
        }
    );

    prev.addEventListener(
        'click',
        function () {
            if (currentPage <= 1) {
                return;
            }

            currentPage--;
            render();
        }
    );

    next.addEventListener(
        'click',
        function () {

            const filteredRows =
                getFilteredRows();

            const totalPages =
                Math.max(
                    1,
                    Math.ceil(
                        filteredRows.length /
                        itemsPerPage
                    )
                );

            if (currentPage >= totalPages) {
                return;
            }

            currentPage++;
            render();
        }
    );

    reload.addEventListener(
        'click',
        function () {

            search.value = '';

            fromDate.value = '';

            toDate.value = '';

            syncDateLabel(
                fromDate,
                fromText,
                'Select From Date'
            );

            syncDateLabel(
                toDate,
                toText,
                'Select To Date'
            );

            items.value = '7';
            itemsPerPage = 7;
            currentPage = 1;

            reload.classList.add(
                'is-spinning'
            );

            window.setTimeout(
                function () {
                    reload.classList.remove(
                        'is-spinning'
                    );
                },
                520
            );

            render();
        }
    );

    /* =========================================================
       INITIALIZE
    ========================================================== */
    syncDateLabel(
        fromDate,
        fromText,
        'Select From Date'
    );

    syncDateLabel(
        toDate,
        toText,
        'Select To Date'
    );

    render();
});
</script>
@endpush
