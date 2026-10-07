{{-- =========================================================
     SELLER REPORTS PAGE
     resources/views/pages/seller/reports.blade.php

     DASHBOARD-MATCHED VERSION
     - Same Poppins typography as Seller Dashboard
     - Same page spacing / sidebar behavior
     - Same card sizing, radius, shadows and hover UX
     - Same title hierarchy
     - Same stat icon treatment: transparent, 56px PNG
========================================================= --}}

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        ShopEase - Seller Reports
    </title>


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
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    {{-- =====================================================
         APP CSS
    ====================================================== --}}

    @vite([
        'resources/css/app.css',
        'resources/css/seller/reports.css',
        'resources/js/seller/reports.js',
    ])


    {{-- =====================================================
         CHART.JS
    ====================================================== --}}

    <script
        src="https://cdn.jsdelivr.net/npm/chart.js"
    ></script>


    

</head>

@section('page-title', 'Reports')
<body>


    {{-- =====================================================
         SELLER SIDEBAR
    ====================================================== --}}

    <x-seller.sidebar />


    {{-- =====================================================
         SELLER NAVBAR
    ====================================================== --}}

    <x-seller.navbar />


    {{-- =====================================================
         REPORTS PAGE
    ====================================================== --}}

    <main
        id="sellerReportsPage"
        class="seller-reports-page"
    >

        <div class="seller-reports-content">


            {{-- =================================================
                 HEADER
            ================================================== --}}

            <div class="reports-header reports-header-tools-only">

                <div class="reports-filter-actions">

                    <label class="reports-date-field">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="16"
                                rx="2"
                            />

                            <path d="M16 3v4"/>
                            <path d="M8 3v4"/>
                            <path d="M3 10h18"/>
                        </svg>

                        <input
                            type="date"
                            id="reportStartDate"
                            aria-label="Select from date"
                        >

                    </label>


                    <label class="reports-date-field">

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.7"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="5"
                                width="18"
                                height="16"
                                rx="2"
                            />

                            <path d="M16 3v4"/>
                            <path d="M8 3v4"/>
                            <path d="M3 10h18"/>
                        </svg>

                        <input
                            type="date"
                            id="reportEndDate"
                            aria-label="Select to date"
                        >

                    </label>


                    <button
                        type="button"
                        id="refreshReports"
                        class="reports-refresh"
                        aria-label="Refresh reports"
                        title="Refresh reports"
                    >

                        <svg
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M20 11a8 8 0 0 0-14.7-4.4L3 9"
                            />

                            <path
                                d="M3 4v5h5"
                            />

                            <path
                                d="M4 13a8 8 0 0 0 14.7 4.4L21 15"
                            />

                            <path
                                d="M21 20v-5h-5"
                            />
                        </svg>

                    </button>

                </div>

            </div>

            <p id="reportFeedback" class="text-sm text-red-700" role="status" aria-live="polite"></p>

            {{-- =================================================
                 STAT CARDS
            ================================================== --}}

            <section class="reports-stats-grid">


                {{-- TOTAL SALES --}}
                <article class="report-stat-card">

                    <div class="report-stat-top">

                        <div class="report-stat-icon">

                            <img
                                src="{{ asset('icons/seller/reports/total-sales.png') }}"
                                alt="Total Sales"
                            >

                        </div>


                        <div class="report-stat-info">

                            <span class="report-stat-value" id="reportTotalSales">
                                ₱{{ number_format($report['stats']['total_sales'], 2) }}
                            </span>

                            <span class="report-stat-label">
                                Total Sales
                            </span>

                        </div>

                    </div>


                    <div class="report-stat-bottom">

                        <span class="report-stat-change" id="reportTotalSalesChange">
                            —
                        </span>

                        <span class="report-comparison-label">
                            {{ $report['stats']['comparison_label'] }}
                        </span>

                    </div>

                </article>


                {{-- TOTAL ORDERS --}}
                <article class="report-stat-card">

                    <div class="report-stat-top">

                        <div class="report-stat-icon">

                            <img
                                src="{{ asset('icons/seller/reports/total-orders.png') }}"
                                alt="Total Orders"
                            >

                        </div>


                        <div class="report-stat-info">

                            <span class="report-stat-value" id="reportTotalOrders">
                                {{ number_format($report['stats']['total_orders']) }}
                            </span>

                            <span class="report-stat-label">
                                Total Orders
                            </span>

                        </div>

                    </div>


                    <div class="report-stat-bottom">

                        <span class="report-stat-change" id="reportTotalOrdersChange">
                            —
                        </span>

                        <span class="report-comparison-label">
                            {{ $report['stats']['comparison_label'] }}
                        </span>

                    </div>

                </article>


                {{-- TOTAL PROFIT --}}
                <article class="report-stat-card">

                    <div class="report-stat-top">

                        <div class="report-stat-icon">

                            <img
                                src="{{ asset('icons/seller/reports/total-profit.png') }}"
                                alt="Total Profit"
                            >

                        </div>


                        <div class="report-stat-info">

                            <span class="report-stat-value" id="reportTotalProfit">
                                ₱{{ number_format($report['stats']['total_profit'], 2) }}
                            </span>

                            <span class="report-stat-label">
                                Total Profit
                            </span>

                        </div>

                    </div>


                    <div class="report-stat-bottom">

                        <span class="report-stat-change" id="reportTotalProfitChange">
                            —
                        </span>

                        <span class="report-comparison-label">
                            {{ $report['stats']['comparison_label'] }}
                        </span>

                    </div>

                </article>


                {{-- GROSS PROFIT --}}
                <article class="report-stat-card">

                    <div class="report-stat-top">

                        <div class="report-stat-icon">

                            <img
                                src="{{ asset('icons/seller/reports/gross-profit.png') }}"
                                alt="Gross Profit Margin"
                            >

                        </div>


                        <div class="report-stat-info">

                            <span class="report-stat-value" id="reportGrossProfitMargin">
                                {{ number_format($report['stats']['gross_profit_margin'], 1) }}%
                            </span>

                            <span class="report-stat-label">
                                Gross Profit Margin
                            </span>

                        </div>

                    </div>


                    <div class="report-stat-bottom">

                        <span class="report-stat-change" id="reportGrossProfitMarginChange">
                            —
                        </span>

                        <span class="report-comparison-label">
                            {{ $report['stats']['comparison_label'] }}
                        </span>

                    </div>

                </article>

            </section>


            {{-- =================================================
                 MAIN REPORT GRID
            ================================================== --}}

            <section class="reports-main-grid">


                <div>


                    {{-- =============================================
                         SALES OVERVIEW
                    ============================================== --}}

                    <article
                        class="
                            reports-card
                            sales-report-card
                        "
                    >

                        <div class="reports-card-header">

                            <h2 class="reports-card-title">
                                Sales Overview
                            </h2>


                            <select
                                class="reports-card-action"
                                id="reportSalesPeriod"
                            >

                                <option value="month">
                                    This month
                                </option>

                                <option value="week">
                                    This week
                                </option>

                                <option value="year">
                                    This year
                                </option>

                            </select>

                        </div>


                        <div class="report-sales-legend">

                            <div class="report-legend-item">

                                <span
                                    class="
                                        report-legend-dot
                                        previous
                                    "
                                ></span>

                                Previous Period

                            </div>


                            <div class="report-legend-item">

                                <span
                                    class="
                                        report-legend-dot
                                        current
                                    "
                                ></span>

                                This Period

                            </div>

                        </div>


                        <div class="report-sales-chart-container">

                            <canvas
                                id="reportSalesChart"
                            ></canvas>

                        </div>

                    </article>


                    {{-- =============================================
                         PERFORMANCE SUMMARY
                    ============================================== --}}

                    <article
                        class="
                            reports-card
                            performance-card
                        "
                    >

                        <div class="reports-card-header">

                            <h2 class="reports-card-title">
                                Performance Summary
                            </h2>

                        </div>


                        <div class="performance-content">

                            <div class="performance-row">

                                <span>
                                    Total Orders
                                </span>

                                <strong id="reportPerformanceOrders">
                                    {{ number_format($report['performance']['total_orders']) }}
                                </strong>

                            </div>


                            <div class="performance-row">

                                <span>
                                    Gross Sales
                                </span>

                                <strong id="reportPerformanceSales">
                                    ₱{{ number_format($report['performance']['gross_sales'], 2) }}
                                </strong>

                            </div>


                            <div class="performance-row">

                                <span>
                                    Admin Commission
                                </span>

                                <strong id="reportPerformanceCommission">
                                    -₱{{ number_format($report['performance']['admin_commission'], 2) }}
                                </strong>

                            </div>


                            <div
                                class="
                                    performance-row
                                    profit
                                "
                            >

                                <span>
                                    Profit
                                </span>

                                <strong id="reportPerformanceProfit">
                                    ₱{{ number_format($report['performance']['profit'], 2) }}
                                </strong>

                            </div>

                        </div>

                    </article>

                </div>


                {{-- =============================================
                     TOP SELLING PRODUCTS
                ============================================== --}}

                <article
                    class="
                        reports-card
                        top-products-card
                    "
                >

                    <div class="reports-card-header">

                        <h2 class="reports-card-title">
                            Top Selling Products
                        </h2>

                    </div>


                    <div class="top-products-content">

                        <div class="top-products-table">


                            <div class="top-products-head">

                                <div>
                                    Product
                                </div>

                                <div>
                                    Quantity Sold
                                </div>

                            </div>


                            <div id="reportTopProducts">
                            @forelse ($report['top_products'] as $product)

                                <div class="top-product-row">

                                    <div class="top-product-info">


                                        <div class="top-product-image">

                                            @if ($product['photo'])
                                                <img src="{{ $product['photo'] }}" alt="">
                                            @else
                                            <svg
                                                viewBox="0 0 24 24"
                                                fill="none"
                                                stroke="currentColor"
                                                stroke-width="1.7"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                aria-hidden="true"
                                            >
                                                <path
                                                    d="
                                                        M4 14
                                                        v-3
                                                        a8 8 0 0 1
                                                        16 0
                                                        v3
                                                    "
                                                />

                                                <path
                                                    d="
                                                        M18 19
                                                        h1
                                                        a1 1 0 0 0
                                                        1-1
                                                        v-5
                                                        a1 1 0 0 0
                                                        -1-1
                                                        h-1
                                                        z
                                                    "
                                                />

                                                <path
                                                    d="
                                                        M6 19
                                                        H5
                                                        a1 1 0 0 1
                                                        -1-1
                                                        v-5
                                                        a1 1 0 0 1
                                                        1-1
                                                        h1
                                                        z
                                                    "
                                                />
                                            </svg>
                                            @endif

                                        </div>


                                        <span class="top-product-name">

                                            {{ $product['name'] }}

                                        </span>

                                    </div>


                                    <div class="top-product-qty">

                                        {{ number_format($product['quantity']) }}

                                    </div>

                                </div>

                            @empty
                                <div class="reports-empty-state top-products-empty-state">
                                    <strong>No product sales found.</strong>
                                    <span>Product sales will appear here for the selected period.</span>
                                </div>
                            @endforelse
                            </div>


                        </div>

                    </div>

                </article>

            </section>


            {{-- =================================================
                 ORDER SUMMARY
            ================================================== --}}

            <section
                class="
                    reports-card
                    order-summary-card
                "
            >

                <div class="reports-card-header">

                    <div class="order-summary-title-wrap">

                        <h2 class="reports-card-title" id="reportOrderSummaryTitle">
                            Order Summary
                        </h2>

                        <span class="order-summary-sort">
                            ↓
                        </span>

                    </div>

                </div>


                <div class="order-summary-table-wrap">

                    <table class="order-summary-table">

                        <thead>

                            <tr>

                                <th>
                                    Order ID
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Payment Method
                                </th>

                                <th>
                                    Sales
                                </th>

                                <th>
                                    Admin Commission
                                </th>

                                <th>
                                    Profit
                                </th>

                            </tr>

                        </thead>


                        <tbody id="reportOrderRows">
                            @forelse ($report['orders']['data'] as $order)
                                <tr>
                                    <td class="order-id-cell">#{{ $order['id'] }}</td>
                                    <td>{{ \Illuminate\Support\Carbon::parse($order['date'])->format('M d, Y') }}</td>
                                    <td>{{ $order['payment_method'] }}</td>
                                    <td>₱{{ number_format($order['sales'], 2) }}</td>
                                    <td>₱{{ number_format($order['commission'], 2) }}</td>
                                    <td>₱{{ number_format($order['profit'], 2) }}</td>
                                </tr>
                            @empty
                                <tr class="report-empty-row">
                                    <td colspan="6">
                                        <div class="reports-empty-state order-summary-empty-state">
                                            <strong>No completed orders found.</strong>
                                            <span>Completed orders will appear here for the selected period.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>

                </div>


                <div class="order-summary-footer">

                    <p class="order-summary-count" id="reportOrderCount">
                        Showing {{ $report['orders']['from'] ?? 0 }} to {{ $report['orders']['to'] ?? 0 }} of {{ $report['orders']['total'] }} entries
                    </p>


                    <div class="order-summary-pagination">

                        <button
                            type="button"
                            class="report-page-button"
                            aria-label="Previous page"
                            data-page-direction="previous"
                        >
                            ‹
                        </button>


                        <div id="reportPaginationPages" class="flex items-center gap-1">
                        @for ($page = 1; $page <= $report['orders']['last_page']; $page++)
                        <button
                            type="button"
                            class="
                                report-page-button
                                {{ $page === $report['orders']['current_page'] ? 'active' : '' }}
                            "
                            data-page="{{ $page }}"
                        >
                            {{ $page }}
                        </button>
                        @endfor
                        </div>

                        <button
                            type="button"
                            class="report-page-button"
                            aria-label="Next page"
                            data-page-direction="next"
                        >
                            ›
                        </button>


                        <select class="report-items-select" id="reportItemsPerPage">

                            <option value="7">
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

            </section>

        </div>

    </main>

    <script id="sellerReportsData" type="application/json">@json($report)</script>
    <script id="sellerReportsConfig" type="application/json">@json(['dataUrl' => $reportDataUrl])</script>

    


    {{-- =====================================================
         REPORT SALES GRAPH JAVASCRIPT
         Mirrors Seller Dashboard graph behavior
    ====================================================== --}}

    


</body>

</html>
