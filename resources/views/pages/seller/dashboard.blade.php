{{-- =========================================================
     SELLER DASHBOARD
     resources/views/pages/seller/dashboard.blade.php
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
        ShopEase - Seller Dashboard
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
        'resources/css/seller/dashboard.css',
        'resources/js/seller/dashboard.js',
    ])


    {{-- =====================================================
         CHART.JS
    ====================================================== --}}

    <script
        src="https://cdn.jsdelivr.net/npm/chart.js"
    ></script>


    

</head>


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
         DASHBOARD
    ====================================================== --}}

    <div
        class="seller-dashboard-page"
        id="sellerDashboardPage"
    >

        <main
            class="seller-dashboard-content"
            id="sellerDashboardContent"
        >


            {{-- =================================================
                 WELCOME
            ================================================== --}}

            <section class="dashboard-welcome">

                <h2>
                    Welcome, {{ $dashboard['seller']['store_name'] }}!
                </h2>

            </section>


            {{-- =================================================
                 STATISTICS
            ================================================== --}}

            <section class="stats-grid">


                {{-- =================================================
                     TOTAL SALES
                ================================================== --}}

                <div class="stat-card">

                    <div class="stat-top">

                        <div class="stat-icon">

                            <img
                                src="{{ asset('icons/seller/dashboard/total-sales.png') }}"
                                class="stat-icon-image"
                                alt="Total Sales"
                            >

                        </div>


                        <div class="stat-info">

                            <span class="stat-value">
                                ₱{{ number_format($dashboard['stats']['total_sales'], 2) }}
                            </span>

                            <span class="stat-label">
                                Total Sales
                            </span>

                        </div>

                    </div>


                    <div class="stat-bottom">

                        <span class="stat-growth-arrow">
                            {{ $dashboard['stats']['changes']['total_sales']['direction'] === 'up' ? '↑' : '↓' }}
                        </span>

                        <span class="stat-change">
                            {{ $dashboard['stats']['changes']['total_sales']['value'] }}
                        </span>

                        <span>
                            from yesterday
                        </span>

                    </div>

                </div>


                {{-- =================================================
                     TOTAL ORDERS
                ================================================== --}}

                <div class="stat-card">

                    <div class="stat-top">

                        <div class="stat-icon">

                            <img
                                src="{{ asset('icons/seller/dashboard/total-orders.png') }}"
                                class="stat-icon-image"
                                alt="Total Orders"
                            >

                        </div>


                        <div class="stat-info">

                            <span class="stat-value">
                                {{ number_format($dashboard['stats']['total_orders']) }}
                            </span>

                            <span class="stat-label">
                                Total Orders
                            </span>

                        </div>

                    </div>


                    <div class="stat-bottom">

                        <span class="stat-growth-arrow">
                            {{ $dashboard['stats']['changes']['total_orders']['direction'] === 'up' ? '↑' : '↓' }}
                        </span>

                        <span class="stat-change">
                            {{ $dashboard['stats']['changes']['total_orders']['value'] }}
                        </span>

                        <span>
                            from yesterday
                        </span>

                    </div>

                </div>


                {{-- =================================================
                     UNITS SOLD
                ================================================== --}}

                <div class="stat-card">

                    <div class="stat-top">

                        <div class="stat-icon">

                            <img
                                src="{{ asset('icons/seller/dashboard/units-sold.png') }}"
                                class="stat-icon-image"
                                alt="Units Sold"
                            >

                        </div>


                        <div class="stat-info">

                            <span class="stat-value">
                                {{ number_format($dashboard['stats']['pending_orders']) }}
                            </span>

                            <span class="stat-label">
                                Pending Orders
                            </span>

                        </div>

                    </div>


                    <div class="stat-bottom">

                        <span class="stat-growth-arrow">
                            {{ $dashboard['stats']['changes']['pending_orders']['direction'] === 'up' ? '↑' : '↓' }}
                        </span>

                        <span class="stat-change">
                            {{ $dashboard['stats']['changes']['pending_orders']['value'] }}
                        </span>

                        <span>
                            from yesterday
                        </span>

                    </div>

                </div>


                {{-- =================================================
                     NET PROFIT
                ================================================== --}}

                <div class="stat-card">

                    <div class="stat-top">

                        <div class="stat-icon">

                            <img
                                src="{{ asset('icons/seller/dashboard/net-profit.png') }}"
                                class="stat-icon-image"
                                alt="Net Profit"
                            >

                        </div>


                        <div class="stat-info">

                            <span class="stat-value">
                                {{ number_format($dashboard['stats']['low_stock_products']) }}
                            </span>

                            <span class="stat-label">
                                Low Stock Products
                            </span>

                        </div>

                    </div>


                    <div class="stat-bottom">

                        <span class="stat-growth-arrow">
                            {{ $dashboard['stats']['changes']['low_stock_products']['direction'] === 'up' ? '↑' : '↓' }}
                        </span>

                        <span class="stat-change">
                            {{ $dashboard['stats']['changes']['low_stock_products']['value'] }}
                        </span>

                        <span>
                            from yesterday
                        </span>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 MAIN DASHBOARD ROW
            ================================================== --}}

            <section class="dashboard-main-grid">


                {{-- =================================================
                     SALES OVERVIEW
                ================================================== --}}

                <div class="dashboard-card sales-card">

                    <div class="dashboard-card-header">

                        <h3 class="dashboard-card-title">
                            Sales Overview
                        </h3>


                        <select
                            class="dashboard-card-action"
                            id="salesPeriod"
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


                    <div class="sales-legend">

                        <div class="legend-item">

                            <span
                                class="legend-dot previous"
                            ></span>

                            Previous Period

                        </div>


                        <div class="legend-item">

                            <span
                                class="legend-dot current"
                            ></span>

                            This Period

                        </div>

                    </div>


                    <div class="sales-chart-container">

                        <canvas
                            id="salesChart"
                        ></canvas>

                    </div>

                </div>


                {{-- =================================================
                     ORDERS BY STATUS
                ================================================== --}}

                <div class="dashboard-card status-card">

                    <div class="dashboard-card-header">

                        <h3 class="dashboard-card-title">
                            Orders by Status
                        </h3>


                        <select
                            class="dashboard-card-action"
                            id="statusPeriod"
                        >

                            <option value="week">
                                This week
                            </option>

                            <option value="month">
                                This month
                            </option>

                            <option value="year">
                                This year
                            </option>

                        </select>

                    </div>


                    <div class="status-content">


                        <div class="status-chart-wrap">

                            <canvas
                                id="statusChart"
                            ></canvas>


                            <div class="status-center">

                                <span
                                    class="status-total"
                                    id="statusTotal"
                                >
                                    129
                                </span>


                                <span
                                    class="status-total-label"
                                >
                                    orders
                                </span>

                            </div>

                        </div>


                        <div class="status-legend">


                            <div class="status-legend-item">

                                <span
                                    class="status-legend-dot"
                                    style="background:#FF6B76;"
                                ></span>

                                <span class="status-name">
                                    New Orders
                                </span>

                                <span class="status-count">
                                    23
                                </span>

                            </div>


                            <div class="status-legend-item">

                                <span
                                    class="status-legend-dot"
                                    style="background:#FF9F43;"
                                ></span>

                                <span class="status-name">
                                    Preparing
                                </span>

                                <span class="status-count">
                                    31
                                </span>

                            </div>


                            <div class="status-legend-item">

                                <span
                                    class="status-legend-dot"
                                    style="background:#FFCA5C;"
                                ></span>

                                <span class="status-name">
                                    To Ship
                                </span>

                                <span class="status-count">
                                    28
                                </span>

                            </div>


                            <div class="status-legend-item">

                                <span
                                    class="status-legend-dot"
                                    style="background:#79A6E8;"
                                ></span>

                                <span class="status-name">
                                    In Transit
                                </span>

                                <span class="status-count">
                                    34
                                </span>

                            </div>


                            <div class="status-legend-item">

                                <span
                                    class="status-legend-dot"
                                    style="background:#6FC29A;"
                                ></span>

                                <span class="status-name">
                                    Delivered
                                </span>

                                <span class="status-count">
                                    13
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- =================================================
                 LOWER DASHBOARD
            ================================================== --}}

            <section class="dashboard-lower-grid">


                {{-- =================================================
                     RECENT ORDERS
                ================================================== --}}

                <div
                    class="dashboard-card recent-orders-card"
                >

                    <div class="dashboard-card-header">

                        <h3 class="dashboard-card-title">
                            Recent Orders
                        </h3>


                        <a
                            href="#"
                            class="card-header-link"
                        >
                            View all
                        </a>

                    </div>


                    <div class="orders-list">


                        {{-- =================================================
                             ORDER 1 - NEW ORDER
                        ================================================== --}}

                        <div class="order-row">

                            <div class="order-status-icon">

                                <img
                                    src="{{ asset('icons/seller/dashboard/new-order.png') }}"
                                    class="order-status-image"
                                    alt="New Order"
                                >

                            </div>


                            <div class="order-info">

                                <div class="order-id-row">

                                    <span class="order-id">
                                        ORD-2089
                                    </span>

                                    <span class="status-pill new">
                                        New Order
                                    </span>

                                </div>


                                <span class="order-date">
                                    May 26, 2026
                                    &nbsp;&nbsp;
                                    10:30 AM
                                </span>

                            </div>


                            <div class="order-price">

                                ₱1,250.00

                                <span class="order-payment">
                                    COD
                                </span>

                            </div>


                            <div class="order-arrow">

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >

                                    <path
                                        d="M9 18l6-6-6-6"
                                    />

                                </svg>

                            </div>

                        </div>


                        {{-- =================================================
                             ORDER 2 - PROCESSING
                        ================================================== --}}

                        <div class="order-row">

                            <div class="order-status-icon">

                                <img
                                    src="{{ asset('icons/seller/dashboard/processing.png') }}"
                                    class="order-status-image"
                                    alt="Preparing"
                                >

                            </div>


                            <div class="order-info">

                                <div class="order-id-row">

                                    <span class="order-id">
                                        ORD-2089
                                    </span>

                                    <span
                                        class="status-pill preparing"
                                    >
                                        Preparing
                                    </span>

                                </div>


                                <span class="order-date">
                                    May 26, 2026
                                    &nbsp;&nbsp;
                                    10:30 AM
                                </span>

                            </div>


                            <div class="order-price">

                                ₱1,250.00

                                <span class="order-payment">
                                    COD
                                </span>

                            </div>


                            <div class="order-arrow">

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >

                                    <path
                                        d="M9 18l6-6-6-6"
                                    />

                                </svg>

                            </div>

                        </div>


                        {{-- =================================================
                             ORDER 3 - TO SHIP
                        ================================================== --}}

                        <div class="order-row">

                            <div class="order-status-icon">

                                <img
                                    src="{{ asset('icons/seller/dashboard/to-ship.png') }}"
                                    class="order-status-image"
                                    alt="To Ship"
                                >

                            </div>


                            <div class="order-info">

                                <div class="order-id-row">

                                    <span class="order-id">
                                        ORD-2026
                                    </span>

                                    <span
                                        class="status-pill to-ship"
                                    >
                                        To Ship
                                    </span>

                                </div>


                                <span class="order-date">
                                    May 26, 2026
                                    &nbsp;&nbsp;
                                    10:30 AM
                                </span>

                            </div>


                            <div class="order-price">

                                ₱1,250.00

                                <span class="order-payment">
                                    COD
                                </span>

                            </div>


                            <div class="order-arrow">

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >

                                    <path
                                        d="M9 18l6-6-6-6"
                                    />

                                </svg>

                            </div>

                        </div>


                        {{-- =================================================
                             ORDER 4 - IN TRANSIT
                        ================================================== --}}

                        <div class="order-row">

                            <div class="order-status-icon">

                                <img
                                    src="{{ asset('icons/seller/dashboard/in-transit.png') }}"
                                    class="order-status-image"
                                    alt="In Transit"
                                >

                            </div>


                            <div class="order-info">

                                <div class="order-id-row">

                                    <span class="order-id">
                                        ORD-2026
                                    </span>

                                    <span
                                        class="status-pill in-transit"
                                    >
                                        In Transit
                                    </span>

                                </div>


                                <span class="order-date">
                                    May 26, 2026
                                    &nbsp;&nbsp;
                                    10:30 AM
                                </span>

                            </div>


                            <div class="order-price">

                                ₱1,250.00

                                <span class="order-payment">
                                    COD
                                </span>

                            </div>


                            <div class="order-arrow">

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >

                                    <path
                                        d="M9 18l6-6-6-6"
                                    />

                                </svg>

                            </div>

                        </div>


                        {{-- =================================================
                             ORDER 5 - DELIVERED
                        ================================================== --}}

                        <div class="order-row">

                            <div class="order-status-icon">

                                <img
                                    src="{{ asset('icons/seller/dashboard/delivered.png') }}"
                                    class="order-status-image"
                                    alt="Delivered"
                                >

                            </div>


                            <div class="order-info">

                                <div class="order-id-row">

                                    <span class="order-id">
                                        ORD-2026
                                    </span>

                                    <span
                                        class="status-pill delivered"
                                    >
                                        Delivered
                                    </span>

                                </div>


                                <span class="order-date">
                                    May 26, 2026
                                    &nbsp;&nbsp;
                                    10:30 AM
                                </span>

                            </div>


                            <div class="order-price">

                                ₱1,250.00

                                <span class="order-payment">
                                    COD
                                </span>

                            </div>


                            <div class="order-arrow">

                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                >

                                    <path
                                        d="M9 18l6-6-6-6"
                                    />

                                </svg>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     LOW STOCK ALERT
                ================================================== --}}

                <div
                    class="dashboard-card low-stock-card"
                >

                    <div class="dashboard-card-header">

                        <h3 class="dashboard-card-title">
                            Low Stock Alert
                        </h3>


                        <a
                            href="#"
                            class="card-header-link"
                        >
                            View all
                        </a>

                    </div>


                    <div class="stock-list">


                        {{-- PRODUCT 1 --}}

                        <div class="stock-row">

                            <div class="stock-image">

                                <span
                                    class="stock-placeholder"
                                >
                                    🎧
                                </span>

                            </div>


                            <div class="stock-info">

                                <p class="stock-name">
                                    Wireless Headphones
                                </p>

                                <span class="stock-number">
                                    Stock: 10
                                </span>

                            </div>

                        </div>


                        {{-- PRODUCT 2 --}}

                        <div class="stock-row">

                            <div class="stock-image">

                                <span
                                    class="stock-placeholder"
                                >
                                    🎧
                                </span>

                            </div>


                            <div class="stock-info">

                                <p class="stock-name">
                                    Wireless Headphones
                                </p>

                                <span class="stock-number">
                                    Stock: 10
                                </span>

                            </div>

                        </div>


                        {{-- PRODUCT 3 --}}

                        <div class="stock-row">

                            <div class="stock-image">

                                <span
                                    class="stock-placeholder"
                                >
                                    🎧
                                </span>

                            </div>


                            <div class="stock-info">

                                <p class="stock-name">
                                    Wireless Headphones
                                </p>

                                <span class="stock-number">
                                    Stock: 10
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                     ANNOUNCEMENT
                ================================================== --}}

                <div class="announcement-card">

                    <p class="announcement-label">
                        Announcement
                    </p>


                    <h3 class="announcement-title">
                        AugZtu Sale 2026!
                    </h3>


                    <p class="announcement-text">
                        Abangan ang mga katangahan
                        ngayong August
                    </p>


                    <div class="announcement-icon">

                        <svg
                            viewBox="0 0 24 24"
                            fill="currentColor"
                        >

                            <path
                                d="M4 9v6h4l5 4V5L8 9H4z"
                            />

                            <path
                                d="M16 8.5a4.5 4.5 0 0 1 0 7"
                            />

                            <path
                                d="M18 6a8 8 0 0 1 0 12"
                            />

                        </svg>

                    </div>

                </div>

            </section>

        </main>

    </div>


    {{-- =====================================================
         DASHBOARD JAVASCRIPT
    ====================================================== --}}

    


</body>

</html>
