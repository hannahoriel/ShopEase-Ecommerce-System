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
        'resources/css/components/dashboard-announcement-carousel.css',
        'resources/js/components/dashboard-announcement-carousel.js',
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
                            →
                        </span>

                        <span class="stat-change">
                            {{ $dashboard['stats']['changes']['low_stock_products']['value'] }}
                        </span>

                        <span>
                            current inventory
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
                                    {{ array_sum($dashboard['orders_by_status_period']['week']) }}
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

                                <span class="status-count" data-status="new_orders">
                                    {{ $dashboard['orders_by_status_period']['week']['new_orders'] }}
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

                                <span class="status-count" data-status="preparing">
                                    {{ $dashboard['orders_by_status_period']['week']['preparing'] }}
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

                                <span class="status-count" data-status="to_ship">
                                    {{ $dashboard['orders_by_status_period']['week']['to_ship'] }}
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

                                <span class="status-count" data-status="in_transit">
                                    {{ $dashboard['orders_by_status_period']['week']['in_transit'] }}
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

                                <span class="status-count" data-status="delivered">
                                    {{ $dashboard['orders_by_status_period']['week']['delivered'] }}
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
                            href="{{ route('seller.order.status') }}"
                            class="card-header-link"
                        >
                            View all
                        </a>

                    </div>


                    <div class="orders-list">


                        @forelse ($dashboard['recent_orders'] as $order)
                            @php
                                $orderStatus = strtolower($order['status']);
                                $statusPresentation = match ($orderStatus) {
                                    'pending', 'new' => ['New Order', 'new', 'new-order'],
                                    'preparing' => ['Preparing', 'preparing', 'processing'],
                                    'to_ship', 'ready_to_ship' => ['To Ship', 'to-ship', 'to-ship'],
                                    'in_transit', 'out_for_delivery' => ['In Transit', 'in-transit', 'in-transit'],
                                    'delivered', 'completed' => ['Delivered', 'delivered', 'delivered'],
                                    default => [str($orderStatus)->replace('_', ' ')->title(), 'new', 'new-order'],
                                };
                            @endphp
                            <a class="order-row" href="{{ route('seller.order.status') }}">
                                <div class="order-status-icon">
                                    <img
                                        src="{{ asset('icons/seller/dashboard/' . $statusPresentation[2] . '.png') }}"
                                        class="order-status-image"
                                        alt="{{ $statusPresentation[0] }}"
                                    >
                                </div>
                                <div class="order-info">
                                    <div class="order-id-row">
                                        <span class="order-id">{{ $order['order_number'] }}</span>
                                        <span class="status-pill {{ $statusPresentation[1] }}">{{ $statusPresentation[0] }}</span>
                                    </div>
                                    <span class="order-date">
                                        {{ \Illuminate\Support\Carbon::parse($order['created_at'])->format('M d, Y') }}
                                        &nbsp;&nbsp;
                                        {{ \Illuminate\Support\Carbon::parse($order['created_at'])->format('g:i A') }}
                                    </span>
                                </div>
                                <div class="order-price">
                                    ₱{{ number_format($order['total'], 2) }}
                                    <span class="order-payment">{{ $order['payment_method'] }}</span>
                                </div>
                                <div class="order-arrow" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path d="M9 18l6-6-6-6" />
                                    </svg>
                                </div>
                            </a>
                        @empty
                            <div class="dashboard-empty-state dashboard-empty-state--orders">
                                <strong>No recent orders found.</strong>
                                <span>New customer orders will appear here.</span>
                            </div>
                        @endforelse

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
                            href="{{ route('seller.inventory') }}"
                            class="card-header-link"
                        >
                            View all
                        </a>

                    </div>


                    <div class="stock-list">


                        @forelse ($dashboard['low_stock_products'] as $product)
                            <div class="stock-row">
                                <div class="stock-image">
                                    @if ($product['photo'])
                                        <img src="{{ $product['photo'] }}" alt="{{ $product['name'] }}" class="stock-product-image">
                                    @else
                                        <span class="stock-placeholder" aria-hidden="true">📦</span>
                                    @endif
                                </div>
                                <div class="stock-info">
                                    <p class="stock-name">{{ $product['name'] }}</p>
                                    <span class="stock-number">Stock: {{ $product['stock'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="dashboard-empty-state dashboard-empty-state--stock">
                                <strong>No low-stock products found.</strong>
                                <span>Products running low on stock will appear here.</span>
                            </div>
                        @endforelse

                    </div>

                </div>


                {{-- =================================================
                     ANNOUNCEMENT
                ================================================== --}}

                <div class="dashboard-announcement-card bg-maroon-900 text-white rounded-2xl p-4 shadow-sm relative overflow-hidden">

                    <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-peach-dark/10"></div>
                    <div class="absolute -right-5 -bottom-12 w-28 h-28 rounded-full bg-peach-dark/10"></div>
                    <div class="absolute right-6 top-6 w-2 h-2 rounded-full bg-peach-dark/50"></div>
                    <div class="absolute right-12 top-12 w-1.5 h-1.5 rounded-full bg-peach-dark/40"></div>

                    <div class="relative z-10">
                        @include('components.dashboard-announcement-carousel', [
                            'dashboardAnnouncementVariant' => 'admin',
                        ])

                        <div class="mt-4 flex items-center gap-2">
                            <span class="w-8 h-1 rounded-full bg-peach-dark"></span>
                            <span class="w-2 h-1 rounded-full bg-white/30"></span>
                            <span class="w-2 h-1 rounded-full bg-white/20"></span>
                        </div>
                    </div>
                </div>

            </section>

        </main>

    </div>


    <script id="sellerDashboardData" type="application/json">@json([
        'sales' => $dashboard['sales_chart'],
        'statuses' => $dashboard['orders_by_status_period'],
    ])</script>


</body>

</html>
