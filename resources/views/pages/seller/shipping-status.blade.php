{{-- =========================================================
     SELLER SHIPPING STATUS PAGE
     resources/views/pages/seller/shipping-status.blade.php

     UPDATED VERSION
     ---------------------------------------------------------
     - Exact shipping table layout
     - All / In Transit / Delivered filtering
     - Shipping status PNGs
     - Clickable shipping rows
     - Order Details modal
     - Ordered At beside Order Number
     - Estimated Delivery beside Ordered At
     - Tracking Number UNDER Estimated Delivery
     - Tracking Number smaller
     - Tracking Progress
     - Extra spacing before Tracking History
     - Tracking History:
         Order Prepared
         Ready to Ship
         Picked Up
         Sorting Center(s)
         In Transit
         Out for Delivery
         Delivered
     - Fade-only modal closing
    - Scrollable shipping details modal
     - Responsive sidebar adjustment
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
        ShopEase - Shipping Status
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
        href="https://fonts.googleapis.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    {{-- =====================================================
         VITE
    ====================================================== --}}

    @vite([
        'resources/css/app.css',
        'resources/css/seller/shipping-status.css',
        'resources/js/seller/shipping-status.js'
    ])

</head>


<body
    class="
        m-0
        p-0
        bg-[#FCF8F6]
        font-[Poppins,sans-serif]
        text-[#17120F]
    "
>


    {{-- =====================================================
         SELLER SIDEBAR
    ====================================================== --}}

    @include(
        'components.seller.sidebar'
    )



    {{-- =====================================================
         SELLER NAVBAR
    ====================================================== --}}

    @section(
        'page-title',
        'Shipping Status'
    )

    @include(
        'components.seller.navbar'
    )



    {{-- =====================================================
         SHIPPING STATUS PAGE
    ====================================================== --}}

    <main
        id="shipping-status-page"

        class="
            min-h-screen
            bg-[#FCF8F6]
            pt-[104px]
            ml-[288px]

            transition-[margin]
            duration-500
            ease-in-out
        "
    >

        <div
            class="
                px-[36px]
                pt-[42px]
                pb-[22px]
            "
        >


            {{-- =================================================
                 PAGE HEADER
            ================================================== --}}

            <div
                class="
                    mb-[18px]
                "
            >

                <h1
                    class="
                        text-[28px]
                        leading-tight
                        font-semibold
                        text-[#17120F]
                    "
                >
                    Shipping Status
                </h1>


                <p
                    class="
                        mt-[3px]
                        text-[19px]
                        leading-tight
                        text-[#999393]
                    "
                >
                    Track every order’s shipping status.
                </p>

            </div>



            {{-- =================================================
                 SHIPPING CARD
            ================================================== --}}

            <section
                class="
                    overflow-hidden
                    bg-white
                    rounded-[12px]
                    border
                    border-[#F0E9E6]
                    shadow-[0_2px_12px_rgba(42,20,15,0.04)]
                "
            >


                {{-- =================================================
                     TABS + SEARCH
                ================================================== --}}

                <div
                    class="
                        shipping-controls

                        flex
                        items-center
                        justify-between

                        min-h-[58px]

                        px-[12px]
                        pl-[14px]

                        gap-4

                        border-b
                        border-[#E8E2DF]
                    "
                >

                    <div
                        id="shippingStatusTabs"

                        class="
                            flex
                            items-center
                            self-stretch
                            shrink-0
                        "
                    >

                        <button
                            type="button"
                            data-tab="all"

                            class="
                                shipping-status-tab
                                active

                                relative
                                h-full

                                px-[8px]
                                mr-[48px]

                                text-[14px]
                                font-medium
                                text-[#95908E]
                            "
                        >
                            All
                        </button>


                        <button
                            type="button"
                            data-tab="in-transit"

                            class="
                                shipping-status-tab

                                relative
                                h-full

                                px-[8px]
                                mr-[48px]

                                text-[14px]
                                font-medium
                                text-[#95908E]
                            "
                        >
                            In Transit
                        </button>


                        <button
                            type="button"
                            data-tab="delivered"

                            class="
                                shipping-status-tab

                                relative
                                h-full

                                px-[8px]

                                text-[14px]
                                font-medium
                                text-[#95908E]
                            "
                        >
                            Delivered
                        </button>

                    </div>



                    {{-- SEARCH --}}

                    <div
                        class="
                            relative
                            w-[290px]
                            shrink-0
                        "
                    >

                        <input
                            type="text"
                            id="shippingSearch"

                            placeholder="Search order ID, customer name, or tracking number"

                            autocomplete="off"

                            class="
                                w-full
                                h-[35px]

                                rounded-[8px]

                                border
                                border-[#D9D6D4]

                                bg-white

                                pl-[12px]
                                pr-[38px]

                                text-[12px]
                                text-[#45403E]

                                outline-none

                                transition-all
                                duration-200

                                placeholder:text-[#B2AFAD]

                                focus:border-[#B8B0AC]

                                focus:ring-[3px]

                                focus:ring-[#7B1B1B]/5
                            "
                        >


                        <svg
                            class="
                                absolute
                                right-[10px]
                                top-1/2
                                -translate-y-1/2

                                w-[18px]
                                h-[18px]

                                text-[#B7B3B1]

                                pointer-events-none
                            "

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

                            <path
                                d="M20 20l-4-4"
                            />

                        </svg>

                    </div>

                </div>



                {{-- =================================================
                     SHIPPING TABLE
                ================================================== --}}

                <div
                    class="
                        shipping-table-wrapper
                    "
                >

                    <div
                        id="shippingTable"
                        class="shipping-table"
                    >


                        {{-- =================================================
                             ROW 1 — IN TRANSIT
                        ================================================== --}}

                        <article
                            class="shipping-row"

                            data-status="in-transit"

                            data-search="
                                #ORD-2025
                                Juan Dela Cruz
                                0917-123-4567
                                T23430583RHEFBW
                            "

                            data-order-id="#ORD-2025"
                            data-customer="Juan Dela Cruz"
                            data-phone="0917-123-4567"

                            data-order-date="May 22, 2026"
                            data-order-time="10:34 AM"

                            data-prepared-date="May 22, 2026"
                            data-prepared-time="11:45 AM"

                            data-ready-date="May 22, 2026"
                            data-ready-time="12:05 PM"

                            data-picked-date="May 23, 2026"
                            data-picked-time="9:15 AM"

                            data-sorting1-date="May 23, 2026"
                            data-sorting1-time="2:10 PM"

                            data-sorting2-date="May 24, 2026"
                            data-sorting2-time="8:35 AM"

                            data-transit-date="May 24, 2026"
                            data-transit-time="1:20 PM"

                            data-out-date="May 25, 2026"
                            data-out-time="8:05 AM"

                            data-delivered-date="May 25, 2026"
                            data-delivered-time="9:32 AM"

                            data-estimated="May 25, 2026"

                            data-tracking="T23430583RHEFBW"

                            data-price="₱559.00"
                            data-payment="COD"
                        >

                            <div class="order-info">

                                <div
                                    class="
                                        order-product-icon
                                        blue-bg
                                    "
                                >

                                    <img
                                        src="{{ asset('icons/seller/shipping-status/in-transit.png') }}"
                                        alt="In Transit"
                                    >

                                </div>


                                <div class="order-details">

                                    <h3>
                                        #ORD-2025
                                    </h3>

                                    <p class="customer-name">
                                        Juan Dela Cruz
                                    </p>

                                    <p class="customer-phone">
                                        0917-123-4567
                                    </p>


                                    <div class="order-date-row">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <rect
                                                x="4"
                                                y="3"
                                                width="16"
                                                height="18"
                                                rx="2"
                                            />

                                            <path d="M8 7h8"/>
                                            <path d="M8 11h8"/>

                                        </svg>

                                        <span>
                                            May 22, 2026
                                        </span>

                                    </div>


                                    <div class="order-time-row">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="8"
                                            />

                                            <path
                                                d="M12 7v5l3 2"
                                            />

                                        </svg>

                                        <span>
                                            10:34 AM
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div class="delivery-info">

                                <span class="status-pill transit">
                                    In Transit
                                </span>

                                <span class="delivery-label">
                                    Estimated Delivery
                                </span>

                                <strong class="delivery-date">
                                    May 25, 2026
                                </strong>

                            </div>


                            <div class="tracking-and-courier">

                                <div class="tracking-block">

                                    <div class="tracker">

                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/in-transit-blue.png') }}"
                                                alt="In Transit"
                                            >

                                            <span>
                                                In Transit
                                            </span>

                                        </div>


                                        <div class="tracker-line"></div>


                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/out-for-delivery-gray.png') }}"
                                                alt="Out for Delivery"
                                            >

                                            <span>
                                                Out for Delivery
                                            </span>

                                        </div>


                                        <div class="tracker-line"></div>


                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/delivered-gray.png') }}"
                                                alt="Delivered"
                                            >

                                            <span>
                                                Delivered
                                            </span>

                                        </div>

                                    </div>


                                    <p class="tracking-number">

                                        Tracking Number:

                                        <span>
                                            T23430583RHEFBW
                                        </span>

                                    </p>

                                </div>


                                <div class="courier-info">

                                    <h3 class="courier-name blue">
                                        Ease Express
                                    </h3>

                                    <p class="shipping-price">
                                        ₱559.00
                                    </p>

                                    <p class="payment-line">
                                        Payment:
                                        <span>COD</span>
                                    </p>

                                    <p class="payment-description">
                                        Payment upon Delivery
                                    </p>

                                </div>

                            </div>

                        </article>



                        {{-- =================================================
                             ROW 2 — OUT FOR DELIVERY
                        ================================================== --}}

                        <article
                            class="shipping-row"

                            data-status="out-for-delivery"

                            data-search="
                                #ORD-2026
                                Maria Santos
                                0918-555-7777
                                T99830583RHEFBW
                            "

                            data-order-id="#ORD-2026"
                            data-customer="Maria Santos"
                            data-phone="0918-555-7777"

                            data-order-date="May 23, 2026"
                            data-order-time="2:15 PM"

                            data-prepared-date="May 23, 2026"
                            data-prepared-time="3:10 PM"

                            data-ready-date="May 23, 2026"
                            data-ready-time="3:25 PM"

                            data-picked-date="May 24, 2026"
                            data-picked-time="9:15 AM"

                            data-sorting1-date="May 24, 2026"
                            data-sorting1-time="2:10 PM"

                            data-sorting2-date="May 25, 2026"
                            data-sorting2-time="7:40 AM"

                            data-transit-date="May 25, 2026"
                            data-transit-time="6:20 AM"

                            data-out-date="May 25, 2026"
                            data-out-time="8:05 AM"

                            data-delivered-date="May 25, 2026"
                            data-delivered-time="9:32 AM"

                            data-estimated="May 26, 2026"

                            data-tracking="T99830583RHEFBW"

                            data-price="₱559.00"
                            data-payment="COD"
                        >

                            <div class="order-info">

                                <div
                                    class="
                                        order-product-icon
                                        blue-bg
                                    "
                                >

                                    <img
                                        src="{{ asset('icons/seller/shipping-status/in-transit.png') }}"
                                        alt="Out for Delivery"
                                    >

                                </div>


                                <div class="order-details">

                                    <h3>
                                        #ORD-2026
                                    </h3>

                                    <p class="customer-name">
                                        Maria Santos
                                    </p>

                                    <p class="customer-phone">
                                        0918-555-7777
                                    </p>


                                    <div class="order-date-row">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <rect
                                                x="4"
                                                y="3"
                                                width="16"
                                                height="18"
                                                rx="2"
                                            />

                                            <path d="M8 7h8"/>
                                            <path d="M8 11h8"/>

                                        </svg>

                                        <span>
                                            May 23, 2026
                                        </span>

                                    </div>


                                    <div class="order-time-row">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="8"
                                            />

                                            <path
                                                d="M12 7v5l3 2"
                                            />

                                        </svg>

                                        <span>
                                            2:15 PM
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div class="delivery-info">

                                <span class="status-pill transit">
                                    In Transit
                                </span>

                                <span class="delivery-label">
                                    Estimated Delivery
                                </span>

                                <strong class="delivery-date">
                                    May 26, 2026
                                </strong>

                            </div>


                            <div class="tracking-and-courier">

                                <div class="tracking-block">

                                    <div class="tracker">

                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/in-transit-check.png') }}"
                                                alt="In Transit"
                                            >

                                            <span>
                                                In Transit
                                            </span>

                                        </div>


                                        <div
                                            class="
                                                tracker-line
                                                completed
                                            "
                                        ></div>


                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/out-for-delivery.png') }}"
                                                alt="Out for Delivery"
                                            >

                                            <span>
                                                Out for Delivery
                                            </span>

                                        </div>


                                        <div class="tracker-line"></div>


                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/delivered-gray.png') }}"
                                                alt="Delivered"
                                            >

                                            <span>
                                                Delivered
                                            </span>

                                        </div>

                                    </div>


                                    <p class="tracking-number">

                                        Tracking Number:

                                        <span>
                                            T99830583RHEFBW
                                        </span>

                                    </p>

                                </div>


                                <div class="courier-info">

                                    <h3 class="courier-name blue">
                                        Ease Express
                                    </h3>

                                    <p class="shipping-price">
                                        ₱559.00
                                    </p>

                                    <p class="payment-line">
                                        Payment:
                                        <span>COD</span>
                                    </p>

                                    <p class="payment-description">
                                        Payment upon Delivery
                                    </p>

                                </div>

                            </div>

                        </article>



                        {{-- =================================================
                             ROW 3 — DELIVERED
                        ================================================== --}}

                        <article
                            class="shipping-row"

                            data-status="delivered"

                            data-search="
                                #ORD-2027
                                Carlo Reyes
                                0919-222-8899
                                T56730583RHEFBW
                            "

                            data-order-id="#ORD-2027"
                            data-customer="Carlo Reyes"
                            data-phone="0919-222-8899"

                            data-order-date="May 24, 2026"
                            data-order-time="9:20 AM"

                            data-prepared-date="May 24, 2026"
                            data-prepared-time="10:10 AM"

                            data-ready-date="May 24, 2026"
                            data-ready-time="10:35 AM"

                            data-picked-date="May 24, 2026"
                            data-picked-time="1:15 PM"

                            data-sorting1-date="May 24, 2026"
                            data-sorting1-time="4:20 PM"

                            data-sorting2-date="May 25, 2026"
                            data-sorting2-time="8:10 AM"

                            data-transit-date="May 25, 2026"
                            data-transit-time="1:20 PM"

                            data-out-date="May 26, 2026"
                            data-out-time="8:05 AM"

                            data-delivered-date="May 26, 2026"
                            data-delivered-time="9:32 AM"

                            data-estimated="May 26, 2026"

                            data-tracking="T56730583RHEFBW"

                            data-price="₱559.00"
                            data-payment="COD"
                        >

                            <div class="order-info">

                                <div
                                    class="
                                        order-product-icon
                                        green-bg
                                    "
                                >

                                    <img
                                        src="{{ asset('icons/seller/shipping-status/delivered.png') }}"
                                        alt="Delivered"
                                    >

                                </div>


                                <div class="order-details">

                                    <h3>
                                        #ORD-2027
                                    </h3>

                                    <p class="customer-name">
                                        Carlo Reyes
                                    </p>

                                    <p class="customer-phone">
                                        0919-222-8899
                                    </p>


                                    <div class="order-date-row">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <rect
                                                x="4"
                                                y="3"
                                                width="16"
                                                height="18"
                                                rx="2"
                                            />

                                            <path d="M8 7h8"/>
                                            <path d="M8 11h8"/>

                                        </svg>

                                        <span>
                                            May 24, 2026
                                        </span>

                                    </div>


                                    <div class="order-time-row">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="8"
                                            />

                                            <path d="M12 7v5l3 2"/>
                                        </svg>

                                        <span>
                                            9:20 AM
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div class="delivery-info">

                                <span class="status-pill delivered">
                                    Delivered
                                </span>

                                <span class="delivery-label">
                                    Delivered on
                                </span>

                                <strong class="delivery-date delivered">

                                    May 26, 2026

                                    <span>
                                        9:32 AM
                                    </span>

                                </strong>

                            </div>


                            <div class="tracking-and-courier">

                                <div class="tracking-block">

                                    <div class="tracker">

                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/delivered-check.png') }}"
                                                alt="In Transit"
                                            >

                                            <span>
                                                In Transit
                                            </span>

                                        </div>


                                        <div
                                            class="
                                                tracker-line
                                                completed
                                            "
                                        ></div>


                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/delivered-check.png') }}"
                                                alt="Out for Delivery"
                                            >

                                            <span>
                                                Out for Delivery
                                            </span>

                                        </div>


                                        <div
                                            class="
                                                tracker-line
                                                completed
                                            "
                                        ></div>


                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/delivered-check.png') }}"
                                                alt="Delivered"
                                            >

                                            <span>
                                                Delivered
                                            </span>

                                        </div>

                                    </div>


                                    <p class="tracking-number">

                                        Tracking Number:

                                        <span>
                                            T56730583RHEFBW
                                        </span>

                                    </p>

                                </div>


                                <div class="courier-info">

                                    <h3 class="courier-name green">
                                        Ease Express
                                    </h3>

                                    <p class="shipping-price">
                                        ₱559.00
                                    </p>

                                    <p class="payment-line">
                                        Payment:
                                        <span>COD</span>
                                    </p>

                                    <p class="payment-description">
                                        Payment upon Delivery
                                    </p>

                                </div>

                            </div>

                        </article>



                        {{-- =================================================
                             ROW 4 — IN TRANSIT
                        ================================================== --}}

                        <article
                            class="
                                shipping-row
                                last-row
                            "

                            data-status="in-transit"

                            data-search="
                                #ORD-2028
                                Ana Garcia
                                0920-333-1212
                                T77830583RHEFBW
                            "

                            data-order-id="#ORD-2028"
                            data-customer="Ana Garcia"
                            data-phone="0920-333-1212"

                            data-order-date="May 24, 2026"
                            data-order-time="4:30 PM"

                            data-prepared-date="May 24, 2026"
                            data-prepared-time="5:10 PM"

                            data-ready-date="May 24, 2026"
                            data-ready-time="5:25 PM"

                            data-picked-date="May 25, 2026"
                            data-picked-time="9:15 AM"

                            data-sorting1-date="May 25, 2026"
                            data-sorting1-time="2:10 PM"

                            data-sorting2-date="May 26, 2026"
                            data-sorting2-time="8:35 AM"

                            data-transit-date="May 26, 2026"
                            data-transit-time="1:20 PM"

                            data-out-date="May 27, 2026"
                            data-out-time="8:05 AM"

                            data-delivered-date="May 27, 2026"
                            data-delivered-time="9:32 AM"

                            data-estimated="May 27, 2026"

                            data-tracking="T77830583RHEFBW"

                            data-price="₱559.00"
                            data-payment="COD"
                        >

                            <div class="order-info">

                                <div
                                    class="
                                        order-product-icon
                                        blue-bg
                                    "
                                >

                                    <img
                                        src="{{ asset('icons/seller/shipping-status/in-transit.png') }}"
                                        alt="In Transit"
                                    >

                                </div>


                                <div class="order-details">

                                    <h3>
                                        #ORD-2028
                                    </h3>

                                    <p class="customer-name">
                                        Ana Garcia
                                    </p>

                                    <p class="customer-phone">
                                        0920-333-1212
                                    </p>


                                    <div class="order-date-row">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <rect
                                                x="4"
                                                y="3"
                                                width="16"
                                                height="18"
                                                rx="2"
                                            />

                                            <path d="M8 7h8"/>
                                            <path d="M8 11h8"/>

                                        </svg>

                                        <span>
                                            May 24, 2026
                                        </span>

                                    </div>


                                    <div class="order-time-row">

                                        <svg
                                            viewBox="0 0 24 24"
                                            fill="none"
                                            stroke="currentColor"
                                            stroke-width="1.7"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        >

                                            <circle
                                                cx="12"
                                                cy="12"
                                                r="8"
                                            />

                                            <path
                                                d="M12 7v5l3 2"
                                            />

                                        </svg>

                                        <span>
                                            4:30 PM
                                        </span>

                                    </div>

                                </div>

                            </div>


                            <div class="delivery-info">

                                <span class="status-pill transit">
                                    In Transit
                                </span>

                                <span class="delivery-label">
                                    Estimated Delivery
                                </span>

                                <strong class="delivery-date">
                                    May 27, 2026
                                </strong>

                            </div>


                            <div class="tracking-and-courier">

                                <div class="tracking-block">

                                    <div class="tracker">

                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/in-transit-blue.png') }}"
                                                alt="In Transit"
                                            >

                                            <span>
                                                In Transit
                                            </span>

                                        </div>


                                        <div class="tracker-line"></div>


                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/out-for-delivery-gray.png') }}"
                                                alt="Out for Delivery"
                                            >

                                            <span>
                                                Out for Delivery
                                            </span>

                                        </div>


                                        <div class="tracker-line"></div>


                                        <div class="tracker-step">

                                            <img
                                                src="{{ asset('icons/seller/shipping-status/delivered-gray.png') }}"
                                                alt="Delivered"
                                            >

                                            <span>
                                                Delivered
                                            </span>

                                        </div>

                                    </div>


                                    <p class="tracking-number">

                                        Tracking Number:

                                        <span>
                                            T77830583RHEFBW
                                        </span>

                                    </p>

                                </div>


                                <div class="courier-info">

                                    <h3 class="courier-name blue">
                                        Ease Express
                                    </h3>

                                    <p class="shipping-price">
                                        ₱559.00
                                    </p>

                                    <p class="payment-line">
                                        Payment:
                                        <span>COD</span>
                                    </p>

                                    <p class="payment-description">
                                        Payment upon Delivery
                                    </p>

                                </div>

                            </div>

                        </article>

                    </div>

                </div>



                {{-- =================================================
                     NO RESULTS
                ================================================== --}}

                <div
                    id="shippingNoResults"

                    class="
                        hidden
                        px-[20px]
                        py-[55px]
                        text-center
                    "
                >

                    <p
                        class="
                            text-[15px]
                            font-medium
                            text-[#7D7774]
                        "
                    >
                        No shipping orders found.
                    </p>

                    <p
                        class="
                            mt-[4px]
                            text-[12px]
                            text-[#AAA6A4]
                        "
                    >
                        Try another search or shipping status.
                    </p>

                </div>



                {{-- =================================================
                     PAGINATION
                ================================================== --}}

                <div
                    class="
                        flex
                        items-center
                        justify-between

                        min-h-[58px]

                        px-[18px]

                        border-t
                        border-[#E4DFDD]
                    "
                >

                    <p
                        class="
                            text-[12px]
                            text-[#8E8885]
                        "
                    >

                        Showing

                        <span id="shippingShowingCount">
                            0
                        </span>

                        out of

                        <span id="shippingTotalEntriesCount">
                            0
                        </span>

                        entries

                    </p>


                    <div
                        class="
                            flex
                            items-center
                            gap-[3px]
                        "
                    >

                        <button
                            type="button"
                            id="shippingPreviousPage"
                            class="pagination-button disabled"
                        >
                            ‹
                        </button>


                        <button
                            type="button"
                            data-page="1"
                            class="pagination-button current"
                        >
                            1
                        </button>


                        <button
                            type="button"
                            data-page="2"
                            class="pagination-button"
                        >
                            2
                        </button>


                        <button
                            type="button"
                            data-page="3"
                            class="pagination-button"
                        >
                            3
                        </button>


                        <button
                            type="button"
                            id="shippingNextPage"
                            class="pagination-button"
                        >
                            ›
                        </button>


                        <select
                            id="shippingItemsPerPage"

                            class="
                                ml-[10px]
                                h-[31px]

                                rounded-[7px]

                                border
                                border-[#ECC8C1]

                                bg-[#FFF5F2]

                                px-[8px]

                                text-[11px]
                                text-[#8C4B44]

                                outline-none
                                cursor-pointer
                            "
                        >

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



    {{-- =========================================================
         SHIPPING DETAILS MODAL
    ========================================================== --}}

    <div
        id="shippingDetailsModal"
        class="shipping-modal"
        aria-hidden="true"
    >

        <div
            id="shippingDetailsModalPanel"
            class="shipping-modal-panel"

            role="dialog"
            aria-modal="true"
            aria-labelledby="shippingModalTitle"
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
                    id="shippingModalTitle"
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
                                Calamba, Laguna
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
                                    COD
                                </span>
                            </p>


                            <small>
                                Payment upon Delivery
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


                <div
                    class="
                        shipping-item-header
                    "
                >

                    <span>
                        Item
                    </span>

                    <span>
                        Price
                    </span>

                    <span>
                        Quantity
                    </span>

                    <span>
                        Subtotal
                    </span>

                </div>



                {{-- ITEM 1 --}}

                <div
                    class="
                        shipping-item-row
                    "
                >

                    <div
                        class="
                            shipping-item-name
                        "
                    >

                        <div
                            class="
                                shipping-item-image
                            "
                        >

                            <div class="modal-product-bag"></div>

                        </div>


                        <span>
                            Wireless Bag
                        </span>

                    </div>


                    <span>
                        ₱559.00
                    </span>


                    <span>
                        1
                    </span>


                    <span>
                        ₱559.00
                    </span>

                </div>



                {{-- ITEM 2 --}}

                <div
                    class="
                        shipping-item-row
                    "
                >

                    <div
                        class="
                            shipping-item-name
                        "
                    >

                        <div
                            class="
                                shipping-item-image
                            "
                        >

                            <div class="modal-product-bag"></div>

                        </div>


                        <span>
                            Wireless Bag
                        </span>

                    </div>


                    <span>
                        ₱559.00
                    </span>


                    <span>
                        1
                    </span>


                    <span>
                        ₱559.00
                    </span>

                </div>



                {{-- TOTALS --}}

                <div
                    class="
                        shipping-modal-totals
                    "
                >

                    <div>

                        <span>
                            Subtotal
                        </span>

                        <strong>
                            ₱559.00
                        </strong>

                    </div>


                    <div>

                        <span>
                            Shipping Fee
                        </span>

                        <strong>
                            ₱60.00
                        </strong>

                    </div>


                    <div>

                        <span>
                            Total Amount
                        </span>

                        <b>
                            ₱6,020.00
                        </b>

                    </div>

                </div>

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
                    Please Handle with care. Thank you!
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
                    id="closeShippingModal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>




</body>
</html>
