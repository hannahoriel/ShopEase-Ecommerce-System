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

    @vite(['resources/css/app.css'])


    {{-- =====================================================
         CHART.JS
    ====================================================== --}}

    <script
        src="https://cdn.jsdelivr.net/npm/chart.js"
    ></script>


    <style>

        /* =====================================================
           ROOT
        ====================================================== */

        :root {

            --seller-maroon-dark:
                #52070B;

            --seller-maroon:
                #7B1B1B;

            --seller-maroon-light:
                #A52A2A;

            --seller-peach:
                #FFE8E0;

            --seller-peach-soft:
                #FFF3F0;

            --seller-background:
                #FBF8F6;

            --seller-white:
                #FFFFFF;

            --seller-text:
                #161616;

            --seller-muted:
                #929292;

            --seller-border:
                #ECE6E2;

            --seller-green:
                #2E8B35;

            --seller-red:
                #D32F2F;
        }


        /* =====================================================
           RESET
        ====================================================== */

        * {
            box-sizing: border-box;
        }


        html {
            min-height: 100%;
        }


        body {

            margin: 0;
            padding: 0;

            min-height: 100vh;

            font-family:
                'Poppins',
                sans-serif;

            background:
                var(--seller-background);

            color:
                var(--seller-text);

            overflow-x:
                hidden;
        }


        button,
        input,
        select {

            font-family:
                'Poppins',
                sans-serif;
        }


        /* =====================================================
           MAIN PAGE
        ====================================================== */

        .seller-dashboard-page {

            min-height: 100vh;

            padding-top:
                104px;

            padding-left:
                288px;

            background:
                var(--seller-background);

            transition:
                padding-left
                0.18s
                ease-out;
        }


        /* =====================================================
           COLLAPSED SIDEBAR STATE
        ====================================================== */

        .seller-dashboard-page.sidebar-collapsed {

            padding-left:
                68px;
        }


        body.seller-sidebar-collapsed
        .seller-dashboard-page {

            padding-left:
                68px;
        }


        /* =====================================================
           DASHBOARD CONTENT
        ====================================================== */

        .seller-dashboard-content {

            width: 100%;

            max-width: 100%;

            padding:
                34px
                38px
                38px;

            transition:
                padding
                0.18s
                ease-out;
        }


        /* =====================================================
           WELCOME
        ====================================================== */

        .dashboard-welcome {

            margin-bottom:
                24px;
        }


        .dashboard-welcome h2 {

            margin: 0;

            font-size:
                22px;

            line-height:
                1.2;

            font-weight:
                700;

            color:
                #111111;
        }


        /* =====================================================
           STAT CARDS
        ====================================================== */

        .stats-grid {

            display:
                grid;

            grid-template-columns:
                repeat(
                    4,
                    minmax(
                        0,
                        1fr
                    )
                );

            gap:
                20px;

            margin-bottom:
                24px;
        }


        .stat-card {

            min-width:
                0;

            min-height:
                132px;

            position:
                relative;

            display:
                flex;

            flex-direction:
                column;

            justify-content:
                space-between;

            background:
                var(--seller-white);

            border:
                1px solid
                rgba(
                    82,
                    7,
                    11,
                    0.04
                );

            border-radius:
                12px;

            padding:
                18px;

            box-shadow:
                0 3px 14px
                rgba(
                    74,
                    10,
                    10,
                    0.04
                );

            transition:
                transform
                0.18s
                ease-out,

                box-shadow
                0.18s
                ease-out;
        }


        .stat-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 8px 22px
                rgba(
                    74,
                    10,
                    10,
                    0.08
                );
        }


        .stat-top {

            display:
                flex;

            align-items:
                center;

            gap:
                14px;
        }


        /* =====================================================
           STAT ICON
        ====================================================== */

        .stat-icon {

            width:
                56px;

            height:
                56px;

            flex:
                0 0 56px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                transparent;

            border:
                none;

            border-radius:
                0;

            overflow:
                hidden;
        }


        .stat-icon-image {

            width:
                56px;

            height:
                56px;

            display:
                block;

            object-fit:
                contain;
        }


        .stat-info {

            min-width:
                0;

            flex:
                1;
        }


        /* =====================================================
           STAT VALUE
           LESS HEAVY THAN BEFORE
        ====================================================== */

        .stat-value {

            display:
                block;

            margin:
                1px
                0
                3px;

            font-size:
                26px;

            line-height:
                1;

            font-weight:
                600;

            color:
                #111111;

            white-space:
                nowrap;

            overflow:
                hidden;

            text-overflow:
                ellipsis;
        }


        /* =====================================================
           STAT LABEL
        ====================================================== */

        .stat-label {

            display:
                block;

            font-size:
                14px;

            line-height:
                1.25;

            color:
                #999999;

            white-space:
                nowrap;

            overflow:
                hidden;

            text-overflow:
                ellipsis;
        }


        /* =====================================================
           STAT CHANGE
        ====================================================== */

        .stat-bottom {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                5px;

            margin-top:
                14px;

            font-size:
                13px;

            line-height:
                1.2;

            color:
                #8A8A8A;
        }


        .stat-change {

            color:
                var(--seller-green);

            font-size:
                15px;

            font-weight:
                600;
        }


        /* =====================================================
           PRIMARY DASHBOARD GRID
        ====================================================== */

        .dashboard-main-grid {

            display:
                grid;

            grid-template-columns:
                minmax(
                    0,
                    1.65fr
                )
                minmax(
                    320px,
                    1fr
                );

            gap:
                20px;

            margin-bottom:
                20px;
        }


        /* =====================================================
           DASHBOARD CARD
        ====================================================== */

        .dashboard-card {

            min-width:
                0;

            background:
                var(--seller-white);

            border:
                1px solid
                rgba(
                    82,
                    7,
                    11,
                    0.04
                );

            border-radius:
                12px;

            box-shadow:
                0 3px 14px
                rgba(
                    74,
                    10,
                    10,
                    0.04
                );
        }


        /* =====================================================
           CARD HEADER
        ====================================================== */

        .dashboard-card-header {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            gap:
                15px;

            padding:
                20px
                22px
                8px;
        }


        .dashboard-card-title {

            margin:
                0;

            font-size:
                18px;

            line-height:
                1.3;

            font-weight:
                700;

            color:
                #171717;
        }


        .dashboard-card-action {

            border:
                1px solid
                #E8E3E0;

            border-radius:
                6px;

            height:
                32px;

            padding:
                0 11px;

            background:
                #FFFFFF;

            color:
                #444444;

            font-size:
                12px;

            cursor:
                pointer;

            outline:
                none;

            transition:
                border-color
                0.18s
                ease;
        }


        .dashboard-card-action:hover {

            border-color:
                #C9C2BE;
        }


        .dashboard-card-action:focus {

            border-color:
                #AFA7A3;
        }


        /* =====================================================
           SALES CHART
        ====================================================== */

        .sales-card {

            min-height:
                356px;
        }


        .sales-legend {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            gap:
                18px;

            margin:
                10px
                0
                4px;
        }


        .legend-item {

            display:
                flex;

            align-items:
                center;

            gap:
                6px;

            font-size:
                12px;

            color:
                #262626;
        }


        .legend-dot {

            width:
                11px;

            height:
                11px;

            border-radius:
                50%;
        }


        .legend-dot.previous {

            background:
                #5D0B11;
        }


        .legend-dot.current {

            background:
                #E00000;
        }


        .sales-chart-container {

            height:
                250px;

            padding:
                0
                18px
                14px;
        }


        .sales-chart-container canvas {

            width:
                100% !important;

            height:
                100% !important;
        }


        /* =====================================================
           STATUS CARD
        ====================================================== */

        .status-card {

            min-height:
                356px;
        }


        .status-content {

            display:
                grid;

            grid-template-columns:
                185px
                minmax(
                    0,
                    1fr
                );

            align-items:
                center;

            gap:
                18px;

            padding:
                24px
                20px
                25px;
        }


        .status-chart-wrap {

            position:
                relative;

            width:
                160px;

            height:
                160px;

            margin:
                0 auto;
        }


        .status-center {

            position:
                absolute;

            inset:
                0;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;

            justify-content:
                center;

            pointer-events:
                none;
        }


        .status-total {

            font-size:
                18px;

            line-height:
                1;

            font-weight:
                700;

            color:
                #111111;
        }


        .status-total-label {

            margin-top:
                4px;

            font-size:
                10px;

            color:
                #929292;
        }


        .status-legend {

            display:
                flex;

            flex-direction:
                column;

            gap:
                13px;
        }


        .status-legend-item {

            display:
                grid;

            grid-template-columns:
                11px
                minmax(
                    0,
                    1fr
                )
                auto;

            align-items:
                center;

            gap:
                8px;

            font-size:
                12px;

            color:
                #292929;
        }


        .status-legend-dot {

            width:
                10px;

            height:
                10px;

            border-radius:
                50%;
        }


        .status-name {

            overflow:
                hidden;

            text-overflow:
                ellipsis;

            white-space:
                nowrap;
        }


        .status-count {

            font-size:
                12px;

            font-weight:
                600;

            color:
                #171717;
        }


        /* =====================================================
           LOWER GRID
        ====================================================== */

        .dashboard-lower-grid {

            display:
                grid;

            grid-template-columns:
                minmax(
                    0,
                    1.18fr
                )
                minmax(
                    0,
                    1fr
                )
                minmax(
                    260px,
                    0.95fr
                );

            gap:
                20px;
        }


        /* =====================================================
           RECENT ORDERS / LOW STOCK
        ====================================================== */

        .recent-orders-card,
        .low-stock-card {

            min-height:
                257px;
        }


        .card-header-link {

            text-decoration:
                none;

            color:
                var(--seller-maroon-light);

            font-size:
                12px;

            font-weight:
                500;

            transition:
                color
                0.18s
                ease;
        }


        .card-header-link:hover {

            color:
                var(--seller-maroon-dark);
        }


        /* =====================================================
           RECENT ORDERS
        ====================================================== */

        .orders-list {

            display:
                flex;

            flex-direction:
                column;

            padding:
                7px
                14px
                12px;
        }


        .order-row {

            display:
                grid;

            grid-template-columns:
                31px
                minmax(
                    0,
                    1fr
                )
                82px
                18px;

            align-items:
                center;

            gap:
                9px;

            min-height:
                47px;

            border-bottom:
                1px solid
                #F0ECE9;

            transition:
                background
                0.16s
                ease;
        }


        .order-row:hover {

            background:
                #FFFAF8;
        }


        .order-row:last-child {

            border-bottom:
                none;
        }


        /* =====================================================
           ORDER ICON
        ====================================================== */

        .order-status-icon {

            width:
                31px;

            height:
                31px;

            flex:
                0 0 31px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            background:
                transparent;

            border:
                none;

            border-radius:
                0;

            overflow:
                hidden;
        }


        .order-status-image {

            width:
                31px;

            height:
                31px;

            display:
                block;

            object-fit:
                contain;
        }


        /* =====================================================
           ORDER INFO
        ====================================================== */

        .order-info {

            min-width:
                0;
        }


        .order-id-row {

            display:
                flex;

            align-items:
                center;

            gap:
                5px;

            min-width:
                0;
        }


        .order-id {

            font-size:
                13px;

            font-weight:
                500;

            color:
                #292929;
        }


        .status-pill {

            display:
                inline-flex;

            align-items:
                center;

            height:
                16px;

            padding:
                0
                6px;

            border-radius:
                8px;

            font-size:
                8px;

            font-weight:
                500;

            white-space:
                nowrap;
        }


        .status-pill.new {

            background:
                #FFDADA;

            color:
                #D52525;
        }


        .status-pill.preparing {

            background:
                #FFE9C3;

            color:
                #D88208;
        }


        .status-pill.to-ship {

            background:
                #DFF0CF;

            color:
                #378F37;
        }


        .status-pill.in-transit {

            background:
                #D8EAF8;

            color:
                #236A9C;
        }


        .status-pill.delivered {

            background:
                #DCF0D5;

            color:
                #2E7D32;
        }


        .order-date {

            display:
                block;

            margin-top:
                2px;

            font-size:
                10px;

            color:
                #999999;
        }


        .order-price {

            text-align:
                right;

            font-size:
                12px;

            font-weight:
                600;

            color:
                #161616;

            white-space:
                nowrap;
        }


        .order-payment {

            display:
                block;

            margin-top:
                1px;

            font-size:
                9px;

            color:
                #888888;
        }


        .order-arrow {

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            color:
                #444444;
        }


        .order-arrow svg {

            width:
                16px;

            height:
                16px;
        }


        /* =====================================================
           LOW STOCK
        ====================================================== */

        .stock-list {

            display:
                flex;

            flex-direction:
                column;

            padding:
                8px
                14px
                12px;
        }


        .stock-row {

            display:
                flex;

            align-items:
                center;

            gap:
                10px;

            min-height:
                64px;

            border-bottom:
                1px solid
                #F0ECE9;

            transition:
                background
                0.16s
                ease;
        }


        .stock-row:hover {

            background:
                #FFFAF8;
        }


        .stock-row:last-child {

            border-bottom:
                none;
        }


        .stock-image {

            width:
                51px;

            height:
                51px;

            flex:
                0 0 51px;

            border:
                1px solid
                #DADADA;

            border-radius:
                9px;

            overflow:
                hidden;

            background:
                #FFFFFF;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;
        }


        .stock-image img {

            width:
                100%;

            height:
                100%;

            object-fit:
                cover;
        }


        .stock-placeholder {

            font-size:
                22px;

            color:
                #777777;
        }


        .stock-info {

            min-width:
                0;
        }


        .stock-name {

            margin:
                0
                0
                3px;

            font-size:
                13px;

            line-height:
                1.3;

            color:
                #252525;

            white-space:
                nowrap;

            overflow:
                hidden;

            text-overflow:
                ellipsis;
        }


        .stock-number {

            font-size:
                11px;

            color:
                #F00000;

            font-weight:
                500;
        }


        /* =====================================================
           ANNOUNCEMENT
        ====================================================== */

        .announcement-card {

            position:
                relative;

            overflow:
                hidden;

            min-height:
                257px;

            padding:
                25px
                23px;

            border-radius:
                12px;

            background:
                #84241F;

            color:
                white;

            box-shadow:
                0 3px 14px
                rgba(
                    74,
                    10,
                    10,
                    0.06
                );
        }


        .announcement-card::after {

            content:
                "";

            position:
                absolute;

            width:
                180px;

            height:
                180px;

            right:
                -70px;

            bottom:
                -92px;

            border-radius:
                50%;

            background:
                rgba(
                    255,
                    135,
                    110,
                    0.12
                );
        }


        .announcement-label {

            margin:
                0;

            font-size:
                15px;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.67
                );
        }


        .announcement-title {

            margin:
                31px
                0
                20px;

            font-size:
                24px;

            line-height:
                1.2;

            font-weight:
                700;

            color:
                #FFFFFF;
        }


        .announcement-text {

            margin:
                0;

            max-width:
                215px;

            font-size:
                13px;

            line-height:
                1.55;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.82
                );
        }


        .announcement-icon {

            position:
                absolute;

            right:
                17px;

            bottom:
                26px;

            width:
                58px;

            height:
                58px;

            z-index:
                2;

            color:
                #FF8E79;
        }


        .announcement-icon svg {

            width:
                100%;

            height:
                100%;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1280px) {

            .seller-dashboard-page {

                padding-left:
                    288px;
            }


            body.seller-sidebar-collapsed
            .seller-dashboard-page {

                padding-left:
                    68px;
            }


            .dashboard-main-grid {

                grid-template-columns:
                    minmax(
                        0,
                        1.4fr
                    )
                    minmax(
                        300px,
                        1fr
                    );
            }


            .dashboard-lower-grid {

                grid-template-columns:
                    minmax(
                        0,
                        1fr
                    )
                    minmax(
                        0,
                        1fr
                    );
            }


            .announcement-card {

                grid-column:
                    1 / -1;
            }

        }


        @media (max-width: 1100px) {

            .stats-grid {

                grid-template-columns:
                    repeat(
                        2,
                        minmax(
                            0,
                            1fr
                        )
                    );
            }


            .dashboard-main-grid {

                grid-template-columns:
                    1fr;
            }


            .status-content {

                grid-template-columns:
                    190px
                    1fr;
            }

        }


        @media (max-width: 760px) {

            .seller-dashboard-page {

                padding-left:
                    0 !important;

                padding-top:
                    78px;
            }


            .seller-dashboard-content {

                padding:
                    24px
                    16px
                    30px;
            }


            .dashboard-welcome {

                margin-bottom:
                    22px;
            }


            .dashboard-welcome h2 {

                font-size:
                    22px;
            }


            .stats-grid {

                grid-template-columns:
                    1fr;

                gap:
                    13px;
            }


            .stat-card {

                padding:
                    18px;
            }


            .stat-value {

                font-size:
                    25px;

                font-weight:
                    600;
            }


            .stat-label {

                font-size:
                    13px;
            }


            .dashboard-main-grid {

                gap:
                    14px;
            }


            .sales-card,
            .status-card {

                min-height:
                    auto;
            }


            .status-content {

                grid-template-columns:
                    1fr;

                justify-items:
                    center;
            }


            .status-legend {

                width:
                    100%;
            }


            .dashboard-lower-grid {

                grid-template-columns:
                    1fr;

                gap:
                    14px;
            }


            .announcement-card {

                grid-column:
                    auto;
            }


            .sales-chart-container {

                height:
                    220px;

                padding:
                    0
                    10px
                    12px;
            }


            .dashboard-card-title {

                font-size:
                    17px;
            }


            .order-id {

                font-size:
                    12px;
            }


            .order-date {

                font-size:
                    9px;
            }


            .order-price {

                font-size:
                    11px;
            }


            .stock-name {

                font-size:
                    12px;
            }

        }

    </style>

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
                    Welcome, Seller!
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
                                ₱236,500.00
                            </span>

                            <span class="stat-label">
                                Total Sales
                            </span>

                        </div>

                    </div>


                    <div class="stat-bottom">

                        <span class="stat-change">
                            ↑ 12%
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
                                1,245
                            </span>

                            <span class="stat-label">
                                Total Orders
                            </span>

                        </div>

                    </div>


                    <div class="stat-bottom">

                        <span class="stat-change">
                            ↑ 8%
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
                                352
                            </span>

                            <span class="stat-label">
                                Units Sold
                            </span>

                        </div>

                    </div>


                    <div class="stat-bottom">

                        <span class="stat-change">
                            ↑ 5%
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
                                ₱45,680.00
                            </span>

                            <span class="stat-label">
                                Net Profit
                            </span>

                        </div>

                    </div>


                    <div class="stat-bottom">

                        <span class="stat-change">
                            ↑ 5%
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

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {


                /* =================================================
                   SALES DATA
                ================================================== */

                const salesData = {

                    month: {

                        labels: [
                            'May 1',
                            'May 6',
                            'May 11',
                            'May 16',
                            'May 21',
                            'May 26',
                            'May 31'
                        ],

                        previous: [
                            2400,
                            5600,
                            2300,
                            5500,
                            4200,
                            5800,
                            14000
                        ],

                        current: [
                            4700,
                            8900,
                            5100,
                            13200,
                            7900,
                            11800,
                            21600
                        ]

                    },


                    week: {

                        labels: [
                            'Mon',
                            'Tue',
                            'Wed',
                            'Thu',
                            'Fri',
                            'Sat',
                            'Sun'
                        ],

                        previous: [
                            1800,
                            2500,
                            2100,
                            2900,
                            3200,
                            4100,
                            3900
                        ],

                        current: [
                            2600,
                            3400,
                            2950,
                            4300,
                            4700,
                            5900,
                            5200
                        ]

                    },


                    year: {

                        labels: [
                            'Jan',
                            'Mar',
                            'May',
                            'Jul',
                            'Sep',
                            'Nov'
                        ],

                        previous: [
                            12000,
                            15000,
                            18000,
                            21000,
                            23000,
                            27000
                        ],

                        current: [
                            15000,
                            19000,
                            24000,
                            26000,
                            30000,
                            35000
                        ]

                    }

                };


                /* =================================================
                   SALES CHART
                ================================================== */

                const salesCanvas =
                    document.getElementById(
                        'salesChart'
                    );


                let salesChart =
                    null;


                function renderSalesChart(
                    period
                ) {

                    if (!salesCanvas) {
                        return;
                    }


                    const data =
                        salesData[period];


                    const ctx =
                        salesCanvas.getContext(
                            '2d'
                        );


                    if (salesChart) {

                        salesChart.destroy();

                    }


                    const previousGradient =
                        ctx.createLinearGradient(
                            0,
                            0,
                            0,
                            260
                        );


                    previousGradient.addColorStop(
                        0,
                        'rgba(93, 11, 17, 0.42)'
                    );


                    previousGradient.addColorStop(
                        1,
                        'rgba(93, 11, 17, 0)'
                    );


                    const currentGradient =
                        ctx.createLinearGradient(
                            0,
                            0,
                            0,
                            260
                        );


                    currentGradient.addColorStop(
                        0,
                        'rgba(224, 0, 0, 0.48)'
                    );


                    currentGradient.addColorStop(
                        1,
                        'rgba(224, 0, 0, 0)'
                    );


                    salesChart =
                        new Chart(
                            ctx,
                            {

                                type:
                                    'line',

                                data: {

                                    labels:
                                        data.labels,

                                    datasets: [

                                        {

                                            label:
                                                'Previous Period',

                                            data:
                                                data.previous,

                                            borderColor:
                                                '#5D0B11',

                                            backgroundColor:
                                                previousGradient,

                                            borderWidth:
                                                1.7,

                                            fill:
                                                true,

                                            tension:
                                                0.3,

                                            pointRadius:
                                                0,

                                            pointHoverRadius:
                                                4

                                        },

                                        {

                                            label:
                                                'This Period',

                                            data:
                                                data.current,

                                            borderColor:
                                                '#E00000',

                                            backgroundColor:
                                                currentGradient,

                                            borderWidth:
                                                1.7,

                                            fill:
                                                true,

                                            tension:
                                                0.3,

                                            pointRadius:
                                                0,

                                            pointHoverRadius:
                                                4

                                        }

                                    ]

                                },

                                options: {

                                    responsive:
                                        true,

                                    maintainAspectRatio:
                                        false,

                                    interaction: {

                                        intersect:
                                            false,

                                        mode:
                                            'index'

                                    },

                                    animation: {

                                        duration:
                                            450,

                                        easing:
                                            'easeOutQuart'

                                    },

                                    plugins: {

                                        legend: {

                                            display:
                                                false

                                        },

                                        tooltip: {

                                            backgroundColor:
                                                '#FFFFFF',

                                            titleColor:
                                                '#161616',

                                            bodyColor:
                                                '#444444',

                                            borderColor:
                                                '#E7E1DE',

                                            borderWidth:
                                                1,

                                            padding:
                                                10,

                                            titleFont: {

                                                family:
                                                    'Poppins',

                                                size:
                                                    13,

                                                weight:
                                                    '600'

                                            },

                                            bodyFont: {

                                                family:
                                                    'Poppins',

                                                size:
                                                    12

                                            },

                                            callbacks: {

                                                label:
                                                    function (
                                                        context
                                                    ) {

                                                        return (
                                                            ' ₱' +
                                                            Number(
                                                                context.parsed.y
                                                            ).toLocaleString(
                                                                'en-PH',
                                                                {
                                                                    minimumFractionDigits:
                                                                        2
                                                                }
                                                            )
                                                        );

                                                    }

                                            }

                                        }

                                    },

                                    scales: {

                                        y: {

                                            beginAtZero:
                                                true,

                                            border: {

                                                display:
                                                    false

                                            },

                                            grid: {

                                                color:
                                                    'rgba(0,0,0,0.05)',

                                                drawTicks:
                                                    false

                                            },

                                            ticks: {

                                                color:
                                                    '#777777',

                                                font: {

                                                    family:
                                                        'Poppins',

                                                    size:
                                                        12

                                                },

                                                padding:
                                                    7,

                                                callback:
                                                    function (
                                                        value
                                                    ) {

                                                        if (
                                                            value === 0
                                                        ) {

                                                            return '0';

                                                        }


                                                        return (
                                                            '₱' +
                                                            (
                                                                value /
                                                                1000
                                                            ) +
                                                            'k'
                                                        );

                                                    }

                                            }

                                        },


                                        x: {

                                            border: {

                                                display:
                                                    false

                                            },

                                            grid: {

                                                display:
                                                    false

                                            },

                                            ticks: {

                                                color:
                                                    '#777777',

                                                font: {

                                                    family:
                                                        'Poppins',

                                                    size:
                                                        12

                                                },

                                                maxRotation:
                                                    0

                                            }

                                        }

                                    }

                                }

                            }
                        );

                }


                renderSalesChart(
                    'month'
                );


                /* =================================================
                   SALES PERIOD
                ================================================== */

                const salesPeriod =
                    document.getElementById(
                        'salesPeriod'
                    );


                if (salesPeriod) {

                    salesPeriod.addEventListener(
                        'change',
                        function () {

                            renderSalesChart(
                                this.value
                            );

                        }
                    );

                }


                /* =================================================
                   STATUS CHART
                ================================================== */

                const statusCanvas =
                    document.getElementById(
                        'statusChart'
                    );


                const statusTotal =
                    document.getElementById(
                        'statusTotal'
                    );


                let statusChart =
                    null;


                const statusData = {

                    week: [
                        23,
                        31,
                        28,
                        34,
                        13
                    ],

                    month: [
                        74,
                        96,
                        87,
                        121,
                        63
                    ],

                    year: [
                        311,
                        420,
                        395,
                        502,
                        277
                    ]

                };


                function renderStatusChart(
                    period
                ) {

                    if (!statusCanvas) {
                        return;
                    }


                    const data =
                        statusData[period];


                    const total =
                        data.reduce(
                            function (
                                sum,
                                value
                            ) {

                                return sum + value;

                            },
                            0
                        );


                    if (statusTotal) {

                        statusTotal.textContent =
                            total.toLocaleString();

                    }


                    const ctx =
                        statusCanvas.getContext(
                            '2d'
                        );


                    if (statusChart) {

                        statusChart.destroy();

                    }


                    statusChart =
                        new Chart(
                            ctx,
                            {

                                type:
                                    'doughnut',

                                data: {

                                    labels: [

                                        'New Orders',
                                        'Preparing',
                                        'To Ship',
                                        'In Transit',
                                        'Delivered'

                                    ],

                                    datasets: [

                                        {

                                            data:
                                                data,

                                            backgroundColor: [

                                                '#FF6B76',
                                                '#FF9F43',
                                                '#FFCA5C',
                                                '#79A6E8',
                                                '#6FC29A'

                                            ],

                                            borderWidth:
                                                0,

                                            hoverOffset:
                                                3

                                        }

                                    ]

                                },

                                options: {

                                    responsive:
                                        true,

                                    maintainAspectRatio:
                                        false,

                                    cutout:
                                        '62%',

                                    animation: {

                                        duration:
                                            450,

                                        easing:
                                            'easeOutQuart'

                                    },

                                    plugins: {

                                        legend: {

                                            display:
                                                false

                                        },

                                        tooltip: {

                                            backgroundColor:
                                                '#FFFFFF',

                                            titleColor:
                                                '#161616',

                                            bodyColor:
                                                '#444444',

                                            borderColor:
                                                '#E7E1DE',

                                            borderWidth:
                                                1,

                                            padding:
                                                10,

                                            titleFont: {

                                                family:
                                                    'Poppins',

                                                size:
                                                    12,

                                                weight:
                                                    '600'

                                            },

                                            bodyFont: {

                                                family:
                                                    'Poppins',

                                                size:
                                                    12

                                            }

                                        }

                                    }

                                }

                            }
                        );

                }


                renderStatusChart(
                    'week'
                );


                /* =================================================
                   STATUS PERIOD
                ================================================== */

                const statusPeriod =
                    document.getElementById(
                        'statusPeriod'
                    );


                if (statusPeriod) {

                    statusPeriod.addEventListener(
                        'change',
                        function () {

                            renderStatusChart(
                                this.value
                            );

                        }
                    );

                }


                /* =================================================
                   SELLER SIDEBAR SYNC
                ================================================== */

                const sellerSidebar =
                    document.getElementById(
                        'sellerSidebar'
                    );


                const sellerDashboardPage =
                    document.getElementById(
                        'sellerDashboardPage'
                    );


                const sellerMenuButton =
                    document.getElementById(
                        'sellerNavbarMenuButton'
                    );


                function syncSellerSidebarState() {

                    if (
                        !sellerSidebar ||
                        !sellerDashboardPage
                    ) {

                        return;
                    }


                    const collapsed =
                        sellerSidebar.classList.contains(
                            'sidebar-collapsed'
                        )
                        ||
                        sellerSidebar.classList.contains(
                            'seller-sidebar-collapsed'
                        )
                        ||
                        sellerSidebar.classList.contains(
                            'collapsed'
                        );


                    sellerDashboardPage.classList.toggle(
                        'sidebar-collapsed',
                        collapsed
                    );


                    document.body.classList.toggle(
                        'seller-sidebar-collapsed',
                        collapsed
                    );


                    setTimeout(
                        function () {

                            if (salesChart) {

                                salesChart.resize();

                            }


                            if (statusChart) {

                                statusChart.resize();

                            }

                        },
                        190
                    );

                }


                /* =================================================
                   WATCH SIDEBAR STATE
                ================================================== */

                if (sellerSidebar) {

                    const sidebarObserver =
                        new MutationObserver(
                            function () {

                                syncSellerSidebarState();

                            }
                        );


                    sidebarObserver.observe(
                        sellerSidebar,
                        {
                            attributes:
                                true,

                            attributeFilter: [
                                'class'
                            ]
                        }
                    );


                    syncSellerSidebarState();

                }


                /* =================================================
                   BURGER BUTTON
                ================================================== */

                if (sellerMenuButton) {

                    sellerMenuButton.addEventListener(
                        'click',
                        function () {

                            setTimeout(
                                function () {

                                    syncSellerSidebarState();

                                },
                                30
                            );

                        }
                    );

                }


                /* =================================================
                   WINDOW RESIZE
                ================================================== */

                let resizeTimer =
                    null;


                window.addEventListener(
                    'resize',
                    function () {

                        clearTimeout(
                            resizeTimer
                        );


                        resizeTimer =
                            setTimeout(
                                function () {

                                    if (salesChart) {

                                        salesChart.resize();

                                    }


                                    if (statusChart) {

                                        statusChart.resize();

                                    }


                                    syncSellerSidebarState();

                                },
                                100
                            );

                    }
                );

            }
        );

    </script>


</body>

</html>