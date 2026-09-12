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
     - Hidden modal scrollbar
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
        'resources/css/app.css'
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
                            4
                        </span>

                        out of 378 entries

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



    <style>

        /* =====================================================
           SHIPPING TABLE
        ====================================================== */

        .shipping-table-wrapper {

            width:
                100%;

            overflow-x:
                auto;

            scrollbar-width:
                none;

            -ms-overflow-style:
                none;
        }


        .shipping-table-wrapper::-webkit-scrollbar {

            display:
                none;
        }


        .shipping-table {

            width:
                100%;

            min-width:
                1080px;
        }


        .shipping-row {

            display:
                grid;

            grid-template-columns:
                320px
                175px
                minmax(0, 1fr);

            min-height:
                167px;

            align-items:
                center;

            padding:
                0 18px;

            border-bottom:
                1px solid
                #DDD9D7;

            background:
                #FFFFFF;

            box-sizing:
                border-box;

            transition:
                background-color
                .20s
                ease;

            cursor:
                pointer;
        }


        .shipping-row:hover {

            background:
                #FFFBF9;
        }


        .shipping-row:last-child {

            border-bottom:
                none;
        }



        /* =====================================================
           ORDER INFO
        ====================================================== */

        .order-info {

            display:
                flex;

            align-items:
                center;

            gap:
                28px;

            min-width:
                0;
        }


        .order-product-icon {

            width:
                65px;

            height:
                65px;

            flex:
                0 0 65px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                10px;

            overflow:
                hidden;
        }


        .order-product-icon.blue-bg {

            background:
                #E2EFFB;
        }


        .order-product-icon.green-bg {

            background:
                #DDEFD5;
        }


        .order-product-icon img {

            width:
                65px;

            height:
                65px;

            object-fit:
                contain;

            display:
                block;
        }


        .order-details {

            min-width:
                0;
        }


        .order-details h3 {

            margin:
                0;

            font-size:
                17px;

            line-height:
                1.2;

            font-weight:
                600;

            color:
                #17120F;

            white-space:
                nowrap;
        }


        .customer-name {

            margin:
                6px 0 0;

            font-size:
                14px;

            line-height:
                1.2;

            color:
                #17120F;

            white-space:
                nowrap;
        }


        .customer-phone {

            margin:
                3px 0 0;

            font-size:
                13px;

            line-height:
                1.2;

            color:
                #77716E;

            white-space:
                nowrap;
        }


        .order-date-row,
        .order-time-row {

            display:
                flex;

            align-items:
                center;

            gap:
                7px;

            color:
                #77716E;
        }


        .order-date-row {

            margin-top:
                14px;
        }


        .order-time-row {

            margin-top:
                4px;
        }


        .order-date-row svg,
        .order-time-row svg {

            width:
                15px;

            height:
                15px;

            flex:
                0 0 15px;
        }


        .order-date-row span,
        .order-time-row span {

            font-size:
                12px;

            line-height:
                1.2;

            white-space:
                nowrap;
        }



        /* =====================================================
           DELIVERY
        ====================================================== */

        .delivery-info {

            display:
                flex;

            flex-direction:
                column;

            align-items:
                flex-start;

            justify-content:
                center;
        }


        .status-pill {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            min-height:
                27px;

            padding:
                0 14px;

            border-radius:
                999px;

            font-size:
                11px;

            line-height:
                1;

            font-weight:
                500;

            white-space:
                nowrap;
        }


        .status-pill.transit {

            background:
                #DCEBFA;

            color:
                #07588A;
        }


        .status-pill.delivered {

            background:
                #D9EED3;

            color:
                #27721F;
        }


        .delivery-label {

            margin-top:
                7px;

            font-size:
                11px;

            color:
                #8A8582;
        }


        .delivery-date {

            margin-top:
                2px;

            font-size:
                13px;

            font-weight:
                600;

            color:
                #07588A;
        }


        .delivery-date.delivered {

            color:
                #08751B;
        }


        .delivery-date.delivered span {

            margin-left:
                5px;
        }



        /* =====================================================
           TRACKING
        ====================================================== */

        .tracking-and-courier {

            display:
                grid;

            grid-template-columns:
                minmax(380px, 1fr)
                175px;

            align-items:
                center;

            gap:
                22px;

            min-width:
                0;
        }


        .tracking-block {

            min-width:
                0;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;
        }


        .tracker {

            width:
                100%;

            display:
                flex;

            align-items:
                flex-start;

            justify-content:
                center;
        }


        .tracker-step {

            width:
                70px;

            flex:
                0 0 70px;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;
        }


        .tracker-step img {

            width:
                36px;

            height:
                36px;

            object-fit:
                contain;
        }


        .tracker-step span {

            margin-top:
                7px;

            font-size:
                10px;

            line-height:
                1.2;

            color:
                #77716E;

            white-space:
                nowrap;

            text-align:
                center;
        }


        .tracker-line {

            width:
                72px;

            height:
                1px;

            flex:
                0 0 72px;

            margin-top:
                17px;

            background:
                #BEBEBE;
        }


        .tracker-line.completed {

            background:
                #8CC57E;
        }


        .tracking-number {

            margin:
                11px 0 0;

            font-size:
                10px;

            color:
                #8A8582;

            white-space:
                nowrap;

            text-align:
                center;
        }


        .tracking-number span {

            color:
                #17120F;
        }



        /* =====================================================
           COURIER
        ====================================================== */

        .courier-name {

            margin:
                0;

            font-size:
                17px;

            line-height:
                1.2;

            font-weight:
                700;

            white-space:
                nowrap;
        }


        .courier-name.blue {

            color:
                #135B89;
        }


        .courier-name.green {

            color:
                #08751B;
        }


        .shipping-price {

            margin:
                27px 0 0;

            font-size:
                18px;

            font-weight:
                600;

            white-space:
                nowrap;
        }


        .payment-line {

            margin:
                10px 0 0;

            font-size:
                13px;

            white-space:
                nowrap;
        }


        .payment-line span {

            color:
                #F07E23;
        }


        .payment-description {

            margin:
                4px 0 0;

            font-size:
                12px;

            color:
                #77716E;

            white-space:
                nowrap;
        }



        /* =====================================================
           TABS
        ====================================================== */

        .shipping-status-tab {

            position:
                relative;

            transition:
                color
                .20s
                ease;
        }


        .shipping-status-tab::after {

            content:
                "";

            position:
                absolute;

            left:
                0;

            right:
                0;

            bottom:
                0;

            height:
                4px;

            border-radius:
                999px
                999px
                0
                0;

            background:
                transparent;

            transition:
                background
                .20s
                ease;
        }


        .shipping-status-tab.active {

            color:
                #9E241F;
        }


        .shipping-status-tab.active::after {

            background:
                #9E241F;
        }



        /* =====================================================
           PAGINATION
        ====================================================== */

        .pagination-button {

            width:
                28px;

            height:
                28px;

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                6px;

            font-size:
                11px;

            color:
                #615A57;

            transition:
                all
                .18s
                ease;
        }


        .pagination-button:hover:not(.disabled) {

            background:
                #FFF2EE;

            color:
                #8D211D;
        }


        .pagination-button.current {

            background:
                #FFC8B9;

            color:
                #7B1B1B;

            font-weight:
                600;
        }


        .pagination-button.disabled {

            opacity:
                .35;

            pointer-events:
                none;
        }



        /* =====================================================
           MODAL
           FADE ONLY
        ====================================================== */

        .shipping-modal {

            position:
                fixed;

            inset:
                0;

            z-index:
                150;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            padding:
                10px;

            background:
                rgba(
                    0,
                    0,
                    0,
                    .25
                );

            backdrop-filter:
                blur(2px);

            opacity:
                0;

            visibility:
                hidden;

            pointer-events:
                none;

            transition:
                opacity
                .20s
                ease-out,
                visibility
                0s
                linear
                .20s;
        }


        .shipping-modal.modal-open {

            opacity:
                1;

            visibility:
                visible;

            pointer-events:
                auto;

            transition:
                opacity
                .20s
                ease-out,
                visibility
                0s
                linear
                0s;
        }


        .shipping-modal-panel {

            width:
                916px;

            max-width:
                calc(
                    100vw -
                    20px
                );

            max-height:
                calc(
                    100vh -
                    20px
                );

            overflow-y:
                auto;

            overflow-x:
                hidden;

            scrollbar-width:
                none;

            -ms-overflow-style:
                none;

            border-radius:
                28px;

            background:
                #FFFFFF;

            box-shadow:
                0 20px
                60px
                rgba(
                    35,
                    14,
                    11,
                    .18
                );

            transform:
                none !important;
        }


        .shipping-modal-panel::-webkit-scrollbar {

            display:
                none;

            width:
                0;

            height:
                0;
        }



        /* =====================================================
           MODAL HEADER
        ====================================================== */

        .shipping-modal-header {

            height:
                72px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            padding:
                0 35px;

            border-bottom:
                1px solid
                #D9D5D3;
        }


        .shipping-modal-header h2 {

            margin:
                0;

            font-size:
                19px;

            font-weight:
                500;
        }


        .ease-express-brand {

            display:
                flex;

            align-items:
                center;

            gap:
                10px;
        }


        .ease-express-brand svg {

            width:
                30px;

            height:
                30px;
        }


        .ease-express-brand span {

            font-size:
                19px;

            font-weight:
                700;
        }



        /* =====================================================
           MODAL SUMMARY
        ====================================================== */

        .shipping-modal-summary {

            min-height:
                108px;

            display:
                grid;

            grid-template-columns:
                minmax(275px, 1.45fr)
                minmax(145px, .8fr)
                minmax(235px, 1fr);

            align-items:
                center;

            gap:
                24px;

            padding:
                0 30px;

            border-bottom:
                1px solid
                #D9D5D3;
        }


        .shipping-modal-order-main {

            display:
                flex;

            align-items:
                center;

            gap:
                20px;

            min-width:
                0;
        }


        .shipping-modal-status-box {

            width:
                65px;

            height:
                65px;

            flex:
                0 0 65px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                10px;

            overflow:
                hidden;
        }


        .shipping-modal-status-box.blue-modal {

            background:
                #E2EFFB;
        }


        .shipping-modal-status-box.green-modal {

            background:
                #DDEFD5;
        }


        .shipping-modal-status-box img {

            width:
                58px;

            height:
                58px;

            object-fit:
                contain;
        }


        .modal-order-id {

            margin:
                0;

            font-size:
                17px;

            line-height:
                1.2;

            font-weight:
                500;
        }


        .modal-status-text {

            margin-top:
                7px;

            display:
                flex;

            align-items:
                center;

            gap:
                8px;

            font-size:
                12px;
        }


        .modal-status-dot {

            width:
                9px;

            height:
                9px;

            border-radius:
                999px;

            flex:
                0 0 9px;
        }


        .blue-dot {

            background:
                #176495;
        }


        .green-dot {

            background:
                #0A901C;
        }


        .orange-dot {

            background:
                #E7A000;
        }


        .modal-summary-column {

            min-width:
                0;
        }


        .modal-summary-label {

            margin:
                0;

            font-size:
                12px;

            line-height:
                1.2;

            font-weight:
                500;

            color:
                #8A8582;

            white-space:
                nowrap;
        }


        .modal-summary-value {

            display:
                block;

            margin-top:
                8px;

            font-size:
                13px;

            line-height:
                1.2;

            color:
                #17120F;

            white-space:
                nowrap;
        }


        .modal-summary-subvalue {

            display:
                block;

            margin-top:
                4px;

            font-size:
                13px;

            line-height:
                1.2;

            color:
                #17120F;

            white-space:
                nowrap;
        }


        .estimated-value {

            font-size:
                14px;

            font-weight:
                600;

            color:
                #07588A;
        }


        .modal-tracking-label {

            margin:
                8px 0 0;

            font-size:
                9px;

            line-height:
                1.2;

            color:
                #A29D9A;

            white-space:
                nowrap;
        }


        .modal-tracking-value {

            display:
                block;

            margin-top:
                3px;

            font-size:
                10px;

            line-height:
                1.2;

            color:
                #17120F;

            white-space:
                nowrap;

            overflow:
                hidden;

            text-overflow:
                ellipsis;

            max-width:
                190px;
        }



        /* =====================================================
           CUSTOMER + PAYMENT
        ====================================================== */

        .shipping-modal-customer-payment {

            display:
                grid;

            grid-template-columns:
                1fr 1fr;

            gap:
                45px;

            padding:
                20px
                35px
                14px;
        }


        .shipping-modal-customer-payment h3 {

            margin:
                0;

            font-size:
                16px;

            font-weight:
                600;
        }


        .shipping-customer-list {

            margin-top:
                13px;

            display:
                flex;

            flex-direction:
                column;

            gap:
                9px;
        }


        .shipping-customer-list > div {

            display:
                flex;

            align-items:
                center;

            gap:
                10px;
        }


        .shipping-customer-list svg {

            width:
                19px;

            height:
                19px;

            flex:
                0 0 19px;
        }


        .shipping-customer-list span {

            font-size:
                12px;
        }


        .shipping-payment {

            margin-top:
                18px;

            display:
                flex;

            align-items:
                flex-start;

            gap:
                15px;
        }


        .shipping-payment svg {

            width:
                19px;

            height:
                19px;
        }


        .shipping-payment p {

            margin:
                0;

            font-size:
                14px;
        }


        .shipping-payment p span {

            color:
                #F07E23;
        }


        .shipping-payment small {

            display:
                block;

            margin-top:
                7px;

            font-size:
                13px;

            color:
                #77716E;
        }



        /* =====================================================
           ITEMS
        ====================================================== */

        .shipping-modal-items {

            padding:
                0
                35px
                10px;
        }


        .shipping-modal-items > h3 {

            margin:
                0;

            font-size:
                15px;

            font-weight:
                600;
        }


        .shipping-item-header {

            margin-top:
                5px;

            height:
                42px;

            display:
                grid;

            grid-template-columns:
                2.25fr
                1fr
                1fr
                1fr;

            align-items:
                center;

            padding:
                0 24px;

            border-radius:
                11px;

            background:
                #FBEDED;

            font-size:
                12px;

            font-weight:
                500;

            color:
                #60100F;
        }


        .shipping-item-row {

            height:
                66px;

            display:
                grid;

            grid-template-columns:
                2.25fr
                1fr
                1fr
                1fr;

            align-items:
                center;

            padding:
                0 19px;

            border-bottom:
                1px solid
                #D9D5D3;

            font-size:
                13px;
        }


        .shipping-item-name {

            display:
                flex;

            align-items:
                center;

            gap:
                16px;
        }


        .shipping-item-name span {

            font-size:
                14px;

            font-weight:
                600;
        }


        .shipping-item-image {

            width:
                48px;

            height:
                48px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                1px solid
                #DDDAD8;

            border-radius:
                10px;

            background:
                #F8F8F8;
        }


        .modal-product-bag {

            position:
                relative;

            width:
                34px;

            height:
                24px;

            background:
                #292929;

            border-radius:
                4px
                4px
                6px
                6px;
        }


        .modal-product-bag::before {

            content:
                "";

            position:
                absolute;

            left:
                8px;

            top:
                -6px;

            width:
                18px;

            height:
                9px;

            border:
                2px solid
                #292929;

            border-bottom:
                none;

            border-radius:
                9px
                9px
                0
                0;
        }


        .modal-product-bag::after {

            content:
                "";

            position:
                absolute;

            left:
                4px;

            top:
                5px;

            width:
                26px;

            height:
                3px;

            border-radius:
                999px;

            background:
                #363636;
        }


        .shipping-modal-totals {

            padding-top:
                10px;

            margin-left:
                auto;

            width:
                320px;
        }


        .shipping-modal-totals > div {

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;
        }


        .shipping-modal-totals > div + div {

            margin-top:
                10px;
        }


        .shipping-modal-totals span {

            font-size:
                13px;

            color:
                #85807D;
        }


        .shipping-modal-totals strong {

            font-size:
                13px;

            font-weight:
                400;
        }


        .shipping-modal-totals > div:last-child {

            margin-top:
                12px;
        }


        .shipping-modal-totals > div:last-child span {

            color:
                #17120F;

            font-weight:
                500;
        }


        .shipping-modal-totals b {

            font-size:
                20px;

            color:
                #721313;
        }



        /* =====================================================
           CUSTOMER NOTES
        ====================================================== */

        .shipping-modal-notes {

            padding:
                12px
                35px;
        }


        .shipping-modal-notes h3 {

            margin:
                0;

            font-size:
                15px;

            font-weight:
                600;
        }


        .shipping-modal-notes > div {

            margin-top:
                8px;

            min-height:
                42px;

            display:
                flex;

            align-items:
                center;

            padding:
                11px 20px;

            border-radius:
                10px;

            background:
                #ECECEC;

            font-size:
                13px;

            color:
                #77716E;
        }



        /* =====================================================
           TRACKING PROGRESS
        ====================================================== */

        .shipping-tracking-progress {

            padding:
                16px
                35px
                22px;
        }


        .shipping-tracking-progress h3 {

            margin:
                0;

            font-size:
                16px;

            font-weight:
                600;
        }


        .modal-progress-tracker {

            margin-top:
                15px;

            display:
                flex;

            align-items:
                flex-start;

            justify-content:
                center;
        }


        .modal-progress-step {

            width:
                75px;

            flex:
                0 0 75px;

            display:
                flex;

            flex-direction:
                column;

            align-items:
                center;

            text-align:
                center;
        }


        .modal-progress-step img {

            width:
                36px;

            height:
                36px;

            object-fit:
                contain;
        }


        .modal-progress-step span {

            margin-top:
                7px;

            font-size:
                10px;

            color:
                #77716E;

            white-space:
                nowrap;
        }


        .modal-progress-line {

            width:
                90px;

            height:
                1px;

            flex:
                0 0 90px;

            margin-top:
                18px;

            background:
                #BDBDBD;
        }


        .modal-progress-line.completed {

            background:
                #8CC57E;
        }



        /* =====================================================
           TRACKING HISTORY
        ====================================================== */

        .shipping-history-section {

            padding:
                6px
                35px
                10px;

            margin-top:
                4px;
        }


        .shipping-history-section h3 {

            margin:
                0;

            font-size:
                16px;

            font-weight:
                600;
        }


        .shipping-history-list {

            position:
                relative;

            margin-top:
                15px;

            padding-bottom:
                5px;
        }


        .shipping-history-list::before {

            content:
                "";

            position:
                absolute;

            left:
                15px;

            top:
                15px;

            bottom:
                18px;

            width:
                1px;

            background:
                #D5D5D5;
        }


        .shipping-history-item {

            position:
                relative;

            display:
                grid;

            grid-template-columns:
                32px
                95px
                minmax(0, 1fr);

            column-gap:
                12px;

            min-height:
                57px;
        }


        .shipping-history-icon {

            position:
                relative;

            z-index:
                2;

            width:
                30px;

            height:
                30px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border-radius:
                999px;

            background:
                #E8F1FB;
        }


        .shipping-history-icon.active {

            background:
                #185B8C;
        }


        .shipping-history-icon.green-active {

            background:
                #2A8B36;
        }


        .shipping-history-icon svg {

            width:
                16px;

            height:
                16px;
        }


        .shipping-history-time {

            padding-top:
                1px;

            text-align:
                right;
        }


        .shipping-history-time .date,
        .shipping-history-time .time {

            display:
                block;

            font-size:
                10px;

            line-height:
                1.35;

            color:
                #17120F;
        }


        .shipping-history-content {

            padding-top:
                0;
        }


        .shipping-history-content strong {

            display:
                block;

            font-size:
                11px;

            line-height:
                1.35;

            font-weight:
                600;

            color:
                #17120F;
        }


        .shipping-history-content p {

            margin:
                2px 0 0;

            font-size:
                10px;

            line-height:
                1.45;

            color:
                #45403E;
        }



        /* =====================================================
           FOOTER
        ====================================================== */

        .shipping-modal-footer {

            display:
                flex;

            justify-content:
                flex-end;

            padding:
                8px
                35px
                18px;
        }


        .shipping-modal-footer button {

            height:
                42px;

            padding:
                0 22px;

            border:
                1px solid
                #D9D3D0;

            border-radius:
                9px;

            background:
                #FFFFFF;

            color:
                #625D5A;

            font-size:
                13px;

            font-weight:
                500;

            transition:
                background-color
                .20s
                ease;
        }


        .shipping-modal-footer button:hover {

            background:
                #FAF7F5;
        }



        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (
            max-width:
            1250px
        ) {

            .shipping-row {

                grid-template-columns:
                    295px
                    155px
                    minmax(0, 1fr);
            }


            .tracking-and-courier {

                grid-template-columns:
                    minmax(340px, 1fr)
                    155px;

                gap:
                    15px;
            }


            .tracker-step {

                width:
                    64px;

                flex-basis:
                    64px;
            }


            .tracker-line {

                width:
                    55px;

                flex-basis:
                    55px;
            }


            .shipping-modal-summary {

                grid-template-columns:
                    minmax(250px, 1.35fr)
                    minmax(135px, .8fr)
                    minmax(210px, 1fr);

                gap:
                    16px;
            }

        }



        @media (
            max-width:
            1080px
        ) {

            .shipping-row {

                grid-template-columns:
                    275px
                    145px
                    minmax(0, 1fr);
            }


            .tracking-and-courier {

                grid-template-columns:
                    minmax(315px, 1fr)
                    145px;

                gap:
                    10px;
            }


            .shipping-modal-summary {

                grid-template-columns:
                    minmax(235px, 1.25fr)
                    minmax(125px, .75fr)
                    minmax(190px, 1fr);

                gap:
                    10px;
            }

        }



        @media (
            max-width:
            900px
        ) {

            .shipping-row {

                min-width:
                    1050px;
            }


            .shipping-modal-panel {

                max-height:
                    calc(
                        100vh -
                        16px
                    );

                border-radius:
                    20px;
            }


            .shipping-modal-summary {

                grid-template-columns:
                    1fr
                    1fr;

                padding:
                    20px;

                gap:
                    18px;
            }


            .shipping-modal-order-main {

                grid-column:
                    1 / -1;
            }


            .shipping-modal-customer-payment {

                padding-left:
                    20px;

                padding-right:
                    20px;
            }


            .shipping-modal-items {

                padding-left:
                    20px;

                padding-right:
                    20px;
            }


            .shipping-modal-notes {

                padding-left:
                    20px;

                padding-right:
                    20px;
            }


            .shipping-tracking-progress {

                padding-left:
                    20px;

                padding-right:
                    20px;
            }


            .shipping-history-section {

                padding-left:
                    20px;

                padding-right:
                    20px;
            }


            .shipping-modal-footer {

                padding-left:
                    20px;

                padding-right:
                    20px;
            }

        }



        @media (
            max-width:
            760px
        ) {

            #shipping-status-page {

                margin-left:
                    0;
            }


            #shipping-status-page > div {

                padding-left:
                    16px;

                padding-right:
                    16px;
            }


            .shipping-controls {

                overflow-x:
                    auto;

                flex-wrap:
                    nowrap;

                scrollbar-width:
                    none;

                -ms-overflow-style:
                    none;
            }


            .shipping-controls::-webkit-scrollbar {

                display:
                    none;
            }


            .shipping-table {

                min-width:
                    1050px;
            }


            .shipping-row {

                min-height:
                    160px;
            }


            .shipping-modal {

                padding:
                    8px;
            }


            .shipping-modal-panel {

                width:
                    calc(
                        100vw -
                        16px
                    );

                max-width:
                    calc(
                        100vw -
                        16px
                    );

                max-height:
                    calc(
                        100vh -
                        16px
                    );
            }


            .shipping-modal-customer-payment {

                grid-template-columns:
                    1fr;

                gap:
                    22px;
            }


            .shipping-item-header,
            .shipping-item-row {

                min-width:
                    650px;
            }


            .shipping-modal-items {

                overflow-x:
                    auto;

                scrollbar-width:
                    none;

                -ms-overflow-style:
                    none;
            }


            .shipping-modal-items::-webkit-scrollbar {

                display:
                    none;
            }

        }



        /* =====================================================
           REDUCED MOTION
        ====================================================== */

        @media (
            prefers-reduced-motion:
            reduce
        ) {

            .shipping-modal,
            .shipping-row,
            .shipping-status-tab::after,
            .pagination-button {

                transition:
                    none !important;
            }

        }

    </style>



    {{-- =========================================================
         JAVASCRIPT
    ========================================================== --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {


                /* =================================================
                   PAGE REFERENCES
                ================================================== */

                const sidebar =
                    document.getElementById(
                        'sellerSidebar'
                    );


                const page =
                    document.getElementById(
                        'shipping-status-page'
                    );


                const tabs =
                    document.querySelectorAll(
                        '.shipping-status-tab'
                    );


                const rows =
                    Array.from(
                        document.querySelectorAll(
                            '.shipping-row'
                        )
                    );


                const searchInput =
                    document.getElementById(
                        'shippingSearch'
                    );


                const noResults =
                    document.getElementById(
                        'shippingNoResults'
                    );


                const showingCount =
                    document.getElementById(
                        'shippingShowingCount'
                    );


                const previousPage =
                    document.getElementById(
                        'shippingPreviousPage'
                    );


                const nextPage =
                    document.getElementById(
                        'shippingNextPage'
                    );


                const pageButtons =
                    document.querySelectorAll(
                        '#shipping-status-page .pagination-button[data-page]'
                    );


                let activeTab =
                    'all';



                /* =================================================
                   SIDEBAR OFFSET
                ================================================== */

                function syncPageOffset() {

                    if (!page) {

                        return;

                    }


                    if (
                        window.innerWidth <=
                        760
                    ) {

                        page.style.marginLeft =
                            '0px';

                        return;

                    }


                    const width =
                        sidebar
                            ? sidebar.getBoundingClientRect().width
                            : 288;


                    page.style.marginLeft =
                        width +
                        'px';

                }


                syncPageOffset();


                window.addEventListener(
                    'resize',
                    syncPageOffset
                );


                if (
                    sidebar &&
                    typeof ResizeObserver !==
                        'undefined'
                ) {

                    const sidebarObserver =
                        new ResizeObserver(
                            function () {

                                syncPageOffset();

                            }
                        );


                    sidebarObserver.observe(
                        sidebar
                    );

                }



                /* =================================================
                   FILTER
                ================================================== */

                function filterShippingRows() {

                    const query =
                        (
                            searchInput?.value ||
                            ''
                        )
                        .trim()
                        .toLowerCase();


                    let visibleCount =
                        0;


                    rows.forEach(
                        function (
                            row
                        ) {

                            const status =
                                row.dataset.status ||
                                '';


                            const searchable =
                                (
                                    row.dataset.search ||
                                    ''
                                )
                                .toLowerCase();


                            let statusMatches =
                                true;


                            /*
                             * In Transit tab includes:
                             * - In Transit
                             * - Out for Delivery
                             */

                            if (
                                activeTab ===
                                'in-transit'
                            ) {

                                statusMatches =
                                    status ===
                                        'in-transit' ||
                                    status ===
                                        'out-for-delivery';

                            }


                            if (
                                activeTab ===
                                'delivered'
                            ) {

                                statusMatches =
                                    status ===
                                    'delivered';

                            }


                            const searchMatches =
                                !query ||
                                searchable.includes(
                                    query
                                );


                            const shouldShow =
                                statusMatches &&
                                searchMatches;


                            row.classList.toggle(
                                'hidden',
                                !shouldShow
                            );


                            if (
                                shouldShow
                            ) {

                                visibleCount++;

                            }

                        }
                    );


                    if (showingCount) {

                        showingCount.textContent =
                            visibleCount;

                    }


                    if (noResults) {

                        noResults.classList.toggle(
                            'hidden',
                            visibleCount !== 0
                        );

                    }

                }



                searchInput?.addEventListener(
                    'input',
                    filterShippingRows
                );



                /* =================================================
                   TABS
                ================================================== */

                tabs.forEach(
                    function (
                        tab
                    ) {

                        tab.addEventListener(
                            'click',
                            function () {

                                tabs.forEach(
                                    function (
                                        item
                                    ) {

                                        item.classList.remove(
                                            'active'
                                        );

                                    }
                                );


                                tab.classList.add(
                                    'active'
                                );


                                activeTab =
                                    tab.dataset.tab ||
                                    'all';


                                filterShippingRows();

                            }
                        );

                    }
                );



                /* =================================================
                   PAGINATION
                ================================================== */

                function setCurrentPage(
                    targetPage
                ) {

                    pageButtons.forEach(
                        function (
                            button
                        ) {

                            button.classList.toggle(
                                'current',
                                Number(
                                    button.dataset.page
                                ) ===
                                    targetPage
                            );

                        }
                    );


                    if (previousPage) {

                        previousPage.classList.toggle(
                            'disabled',
                            targetPage ===
                                1
                        );

                    }

                }


                pageButtons.forEach(
                    function (
                        button
                    ) {

                        button.addEventListener(
                            'click',
                            function () {

                                setCurrentPage(
                                    Number(
                                        button.dataset.page
                                    )
                                );

                            }
                        );

                    }
                );


                previousPage?.addEventListener(
                    'click',
                    function () {

                        const current =
                            document.querySelector(
                                '#shipping-status-page .pagination-button.current[data-page]'
                            );


                        const currentPage =
                            Number(
                                current?.dataset.page ||
                                1
                            );


                        setCurrentPage(
                            Math.max(
                                1,
                                currentPage -
                                    1
                            )
                        );

                    }
                );


                nextPage?.addEventListener(
                    'click',
                    function () {

                        const current =
                            document.querySelector(
                                '#shipping-status-page .pagination-button.current[data-page]'
                            );


                        const currentPage =
                            Number(
                                current?.dataset.page ||
                                1
                            );


                        setCurrentPage(
                            Math.min(
                                3,
                                currentPage +
                                    1
                            )
                        );

                    }
                );



                /* =================================================
                   MODAL REFERENCES
                ================================================== */

                const modal =
                    document.getElementById(
                        'shippingDetailsModal'
                    );


                const modalOrderId =
                    document.getElementById(
                        'shippingModalOrderId'
                    );


                const modalCustomer =
                    document.getElementById(
                        'shippingModalCustomer'
                    );


                const modalPhone =
                    document.getElementById(
                        'shippingModalPhone'
                    );


                const modalOrderDate =
                    document.getElementById(
                        'shippingModalOrderDate'
                    );


                const modalOrderTime =
                    document.getElementById(
                        'shippingModalOrderTime'
                    );


                const modalEstimated =
                    document.getElementById(
                        'shippingModalEstimated'
                    );


                const modalTracking =
                    document.getElementById(
                        'shippingModalTrackingNumber'
                    );


                const modalStatusLabel =
                    document.getElementById(
                        'shippingModalStatusLabel'
                    );


                const modalStatusDot =
                    document.getElementById(
                        'shippingModalStatusDot'
                    );


                const modalIconBox =
                    document.getElementById(
                        'shippingModalIconBox'
                    );


                const modalIcon =
                    document.getElementById(
                        'shippingModalOrderIcon'
                    );


                const trackingList =
                    document.getElementById(
                        'shippingTrackingHistoryList'
                    );


                const progressTransit =
                    document.querySelector(
                        '#modalProgressTransit img'
                    );


                const progressOut =
                    document.querySelector(
                        '#modalProgressOut img'
                    );


                const progressDelivered =
                    document.querySelector(
                        '#modalProgressDelivered img'
                    );


                const progressLine1 =
                    document.getElementById(
                        'modalProgressLine1'
                    );


                const progressLine2 =
                    document.getElementById(
                        'modalProgressLine2'
                    );


                const closeButton =
                    document.getElementById(
                        'closeShippingModal'
                    );


                let selectedRow =
                    null;



                /* =================================================
                   TRACKING SVG ICONS
                ================================================== */

                function trackingIcon(
                    type,
                    active,
                    green
                ) {

                    const stroke =
                        active
                            ? '#FFFFFF'
                            : (
                                green
                                    ? '#2A8B36'
                                    : '#185B8C'
                            );


                    if (
                        type ===
                        'prepared'
                    ) {

                        return `
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="${stroke}"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M5 8h14v11H5z"
                                ></path>

                                <path
                                    d="M8 8V5h8v3"
                                ></path>

                                <path
                                    d="M9 12h6"
                                ></path>
                            </svg>
                        `;

                    }


                    if (
                        type ===
                        'ready'
                    ) {

                        return `
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="${stroke}"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M5 6h14"
                                ></path>

                                <path
                                    d="M8 6v4"
                                ></path>

                                <path
                                    d="M16 6v4"
                                ></path>

                                <path
                                    d="M5 10h14v9H5z"
                                ></path>

                                <path
                                    d="M9 14h6"
                                ></path>
                            </svg>
                        `;

                    }


                    if (
                        type ===
                        'picked'
                    ) {

                        return `
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="${stroke}"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    x="5"
                                    y="8"
                                    width="14"
                                    height="11"
                                    rx="2"
                                ></rect>

                                <path
                                    d="M8 8V5h8v3"
                                ></path>
                            </svg>
                        `;

                    }


                    if (
                        type ===
                        'sorting'
                    ) {

                        return `
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="${stroke}"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <rect
                                    x="5"
                                    y="4"
                                    width="14"
                                    height="16"
                                    rx="2"
                                ></rect>

                                <path
                                    d="M8 8h8"
                                ></path>

                                <path
                                    d="M8 12h8"
                                ></path>

                                <path
                                    d="M8 16h5"
                                ></path>
                            </svg>
                        `;

                    }


                    if (
                        type ===
                        'transit'
                    ) {

                        return `
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="${stroke}"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M3 16h14"
                                ></path>

                                <path
                                    d="M14 7h4l3 4v5h-3"
                                ></path>

                                <circle
                                    cx="7"
                                    cy="17"
                                    r="2"
                                ></circle>

                                <circle
                                    cx="18"
                                    cy="17"
                                    r="2"
                                ></circle>
                            </svg>
                        `;

                    }


                    if (
                        type ===
                        'out'
                    ) {

                        return `
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="${stroke}"
                                stroke-width="1.7"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M3 16h14"
                                ></path>

                                <path
                                    d="M14 7h4l3 4v5h-3"
                                ></path>

                                <circle
                                    cx="7"
                                    cy="17"
                                    r="2"
                                ></circle>

                                <circle
                                    cx="18"
                                    cy="17"
                                    r="2"
                                ></circle>
                            </svg>
                        `;

                    }


                    if (
                        type ===
                        'delivered'
                    ) {

                        return `
                            <svg
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="${stroke}"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path
                                    d="M5 12l4 4L19 6"
                                ></path>
                            </svg>
                        `;

                    }


                    return '';

                }



                /* =================================================
                   BUILD TRACKING HISTORY
                ================================================== */

                function getTrackingHistory(
                    row
                ) {

                    const status =
                        row.dataset.status ||
                        'in-transit';


                    const events = [

                        {
                            type:
                                'prepared',

                            date:
                                row.dataset.preparedDate ||
                                row.dataset.orderDate ||
                                'May 22, 2026',

                            time:
                                row.dataset.preparedTime ||
                                '11:45 AM',

                            title:
                                'Order Prepared',

                            description:
                                'The seller prepared the order for shipment.'
                        },


                        {
                            type:
                                'ready',

                            date:
                                row.dataset.readyDate ||
                                row.dataset.orderDate ||
                                'May 22, 2026',

                            time:
                                row.dataset.readyTime ||
                                '12:05 PM',

                            title:
                                'Ready to Ship',

                            description:
                                'The order is ready for courier pickup.'
                        },


                        {
                            type:
                                'picked',

                            date:
                                row.dataset.pickedDate ||
                                'May 23, 2026',

                            time:
                                row.dataset.pickedTime ||
                                '9:15 AM',

                            title:
                                'Picked Up',

                            description:
                                'Ease Express has picked up the parcel.'
                        },


                        {
                            type:
                                'sorting',

                            date:
                                row.dataset.sorting1Date ||
                                'May 23, 2026',

                            time:
                                row.dataset.sorting1Time ||
                                '2:10 PM',

                            title:
                                'Arrived at Sta. Cruz Sorting Center',

                            description:
                                'The parcel arrived at the sorting center.'
                        },


                        {
                            type:
                                'sorting',

                            date:
                                row.dataset.sorting2Date ||
                                'May 24, 2026',

                            time:
                                row.dataset.sorting2Time ||
                                '8:35 AM',

                            title:
                                'Arrived at Lumban Sorting Center',

                            description:
                                'The parcel was transferred to the next sorting center.'
                        },


                        {
                            type:
                                'transit',

                            date:
                                row.dataset.transitDate ||
                                'May 24, 2026',

                            time:
                                row.dataset.transitTime ||
                                '1:20 PM',

                            title:
                                'In Transit',

                            description:
                                'The parcel is currently on its way to the destination.'
                        }

                    ];


                    if (
                        status ===
                        'out-for-delivery' ||
                        status ===
                        'delivered'
                    ) {

                        events.push({

                            type:
                                'out',

                            date:
                                row.dataset.outDate ||
                                'May 25, 2026',

                            time:
                                row.dataset.outTime ||
                                '8:05 AM',

                            title:
                                'Out for Delivery',

                            description:
                                'The courier is on the way to deliver the parcel.'
                        });

                    }


                    if (
                        status ===
                        'delivered'
                    ) {

                        events.push({

                            type:
                                'delivered',

                            date:
                                row.dataset.deliveredDate ||
                                'May 25, 2026',

                            time:
                                row.dataset.deliveredTime ||
                                '9:32 AM',

                            title:
                                'Delivered',

                            description:
                                'The order has been successfully delivered.'
                        });

                    }


                    return events;

                }



                /* =================================================
                   RENDER TRACKING HISTORY
                ================================================== */

                function renderTrackingHistory(
                    row
                ) {

                    if (
                        !trackingList
                    ) {

                        return;

                    }


                    const events =
                        getTrackingHistory(
                            row
                        );


                    trackingList.innerHTML =
                        '';


                    events.forEach(
                        function (
                            event,
                            index
                        ) {

                            const isLast =
                                index ===
                                events.length -
                                1;


                            const isDelivered =
                                row.dataset.status ===
                                'delivered';


                            const item =
                                document.createElement(
                                    'div'
                                );


                            item.className =
                                'shipping-history-item';


                            const iconClass =
                                isLast
                                    ? (
                                        isDelivered
                                            ? 'active green-active'
                                            : 'active'
                                    )
                                    : '';


                            item.innerHTML = `

                                <div
                                    class="
                                        shipping-history-icon
                                        ${iconClass}
                                    "
                                >

                                    ${trackingIcon(
                                        event.type,
                                        isLast,
                                        isDelivered
                                    )}

                                </div>


                                <div
                                    class="
                                        shipping-history-time
                                    "
                                >

                                    <span class="date">
                                        ${event.date}
                                    </span>

                                    <span class="time">
                                        ${event.time}
                                    </span>

                                </div>


                                <div
                                    class="
                                        shipping-history-content
                                    "
                                >

                                    <strong>
                                        ${event.title}
                                    </strong>

                                    <p>
                                        ${event.description}
                                    </p>

                                </div>

                            `;


                            trackingList.appendChild(
                                item
                            );

                        }
                    );

                }



                /* =================================================
                   UPDATE MODAL PROGRESS
                ================================================== */

                function updateProgress(
                    status
                ) {

                    if (
                        !progressTransit ||
                        !progressOut ||
                        !progressDelivered
                    ) {

                        return;

                    }


                    /*
                     * DEFAULT — IN TRANSIT
                     */

                    if (
                        status ===
                        'in-transit'
                    ) {

                        progressTransit.src =
                            "{{ asset('icons/seller/shipping-status/in-transit-blue.png') }}";

                        progressOut.src =
                            "{{ asset('icons/seller/shipping-status/out-for-delivery-gray.png') }}";

                        progressDelivered.src =
                            "{{ asset('icons/seller/shipping-status/delivered-gray.png') }}";


                        progressLine1?.classList.remove(
                            'completed'
                        );

                        progressLine2?.classList.remove(
                            'completed'
                        );

                        return;

                    }


                    /*
                     * OUT FOR DELIVERY
                     */

                    if (
                        status ===
                        'out-for-delivery'
                    ) {

                        progressTransit.src =
                            "{{ asset('icons/seller/shipping-status/in-transit-check.png') }}";

                        progressOut.src =
                            "{{ asset('icons/seller/shipping-status/out-for-delivery.png') }}";

                        progressDelivered.src =
                            "{{ asset('icons/seller/shipping-status/delivered-gray.png') }}";


                        progressLine1?.classList.add(
                            'completed'
                        );

                        progressLine2?.classList.remove(
                            'completed'
                        );

                        return;

                    }


                    /*
                     * DELIVERED
                     */

                    if (
                        status ===
                        'delivered'
                    ) {

                        progressTransit.src =
                            "{{ asset('icons/seller/shipping-status/delivered-check.png') }}";

                        progressOut.src =
                            "{{ asset('icons/seller/shipping-status/delivered-check.png') }}";

                        progressDelivered.src =
                            "{{ asset('icons/seller/shipping-status/delivered-check.png') }}";


                        progressLine1?.classList.add(
                            'completed'
                        );

                        progressLine2?.classList.add(
                            'completed'
                        );

                    }

                }



                /* =================================================
                   OPEN MODAL
                ================================================== */

                function openShippingModal(
                    row
                ) {

                    if (
                        !modal ||
                        !row
                    ) {

                        return;

                    }


                    selectedRow =
                        row;


                    const status =
                        row.dataset.status ||
                        'in-transit';


                    const orderId =
                        row.dataset.orderId ||
                        '#ORD-2025';


                    const customer =
                        row.dataset.customer ||
                        'Juan Dela Cruz';


                    const phone =
                        row.dataset.phone ||
                        '0917 123 4567';


                    const orderDate =
                        row.dataset.orderDate ||
                        'May 22, 2026';


                    const orderTime =
                        row.dataset.orderTime ||
                        '10:34 AM';


                    const estimated =
                        row.dataset.estimated ||
                        'May 25, 2026';


                    const tracking =
                        row.dataset.tracking ||
                        'T23430583RHEFBW';



                    /* =================================================
                       BASIC VALUES
                    ================================================== */

                    modalOrderId.textContent =
                        orderId;


                    modalCustomer.textContent =
                        customer;


                    modalPhone.textContent =
                        phone;


                    modalOrderDate.textContent =
                        orderDate;


                    modalOrderTime.textContent =
                        orderTime;


                    modalEstimated.textContent =
                        estimated;


                    modalTracking.textContent =
                        tracking;



                    /* =================================================
                       STATUS
                    ================================================== */

                    if (
                        status ===
                        'delivered'
                    ) {

                        modalStatusLabel.textContent =
                            'Delivered';


                        modalStatusDot.className =
                            'modal-status-dot green-dot';


                        modalIconBox.className =
                            'shipping-modal-status-box green-modal';


                        modalIcon.src =
                            "{{ asset('icons/seller/shipping-status/delivered.png') }}";

                    }


                    else if (
                        status ===
                        'out-for-delivery'
                    ) {

                        modalStatusLabel.textContent =
                            'Out for Delivery';


                        modalStatusDot.className =
                            'modal-status-dot orange-dot';


                        modalIconBox.className =
                            'shipping-modal-status-box blue-modal';


                        modalIcon.src =
                            "{{ asset('icons/seller/shipping-status/in-transit.png') }}";

                    }


                    else {

                        modalStatusLabel.textContent =
                            'In Transit';


                        modalStatusDot.className =
                            'modal-status-dot blue-dot';


                        modalIconBox.className =
                            'shipping-modal-status-box blue-modal';


                        modalIcon.src =
                            "{{ asset('icons/seller/shipping-status/in-transit.png') }}";

                    }


                    modalIcon.alt =
                        modalStatusLabel.textContent;



                    /* =================================================
                       UPDATE PROGRESS
                    ================================================== */

                    updateProgress(
                        status
                    );



                    /* =================================================
                       UPDATE HISTORY
                    ================================================== */

                    renderTrackingHistory(
                        row
                    );



                    /* =================================================
                       OPEN MODAL
                       FADE ONLY
                    ================================================== */

                    modal.classList.add(
                        'modal-open'
                    );


                    modal.setAttribute(
                        'aria-hidden',
                        'false'
                    );


                    document.body.classList.add(
                        'overflow-hidden'
                    );

                }



                /* =================================================
                   CLOSE MODAL
                   FADE ONLY
                ================================================== */

                function closeShippingModal() {

                    if (
                        !modal
                    ) {

                        return;

                    }


                    /*
                     * IMPORTANT:
                     * No transform.
                     * No translate.
                     * No scale.
                     */

                    modal.classList.remove(
                        'modal-open'
                    );


                    modal.setAttribute(
                        'aria-hidden',
                        'true'
                    );


                    setTimeout(
                        function () {

                            document.body.classList.remove(
                                'overflow-hidden'
                            );


                            selectedRow =
                                null;

                        },
                        210
                    );

                }



                /* =================================================
                   CLICKABLE ROWS
                ================================================== */

                rows.forEach(
                    function (
                        row
                    ) {

                        row.setAttribute(
                            'tabindex',
                            '0'
                        );


                        row.setAttribute(
                            'role',
                            'button'
                        );


                        row.addEventListener(
                            'click',
                            function () {

                                openShippingModal(
                                    row
                                );

                            }
                        );


                        row.addEventListener(
                            'keydown',
                            function (
                                event
                            ) {

                                if (
                                    event.key ===
                                        'Enter' ||
                                    event.key ===
                                        ' '
                                ) {

                                    event.preventDefault();

                                    openShippingModal(
                                        row
                                    );

                                }

                            }
                        );

                    }
                );



                /* =================================================
                   CLOSE BUTTON
                ================================================== */

                closeButton?.addEventListener(
                    'click',
                    closeShippingModal
                );



                /* =================================================
                   BACKDROP
                ================================================== */

                modal?.addEventListener(
                    'click',
                    function (
                        event
                    ) {

                        if (
                            event.target ===
                            modal
                        ) {

                            closeShippingModal();

                        }

                    }
                );



                /* =================================================
                   ESCAPE
                ================================================== */

                document.addEventListener(
                    'keydown',
                    function (
                        event
                    ) {

                        if (
                            event.key ===
                                'Escape' &&
                            modal?.classList.contains(
                                'modal-open'
                            )
                        ) {

                            closeShippingModal();

                        }

                    }
                );



                /* =================================================
                   INITIAL FILTER
                ================================================== */

                filterShippingRows();

            }
        );

    </script>

</body>

</html>