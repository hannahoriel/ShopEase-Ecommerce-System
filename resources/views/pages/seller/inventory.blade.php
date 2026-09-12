{{-- =========================================================
     SELLER INVENTORY PAGE
     resources/views/pages/seller/inventory.blade.php

     FIGMA-MATCHED INVENTORY
     ---------------------------------------------------------
     - Seller Sidebar
     - Seller Navbar
     - Inventory UI
     - All Products
     - Policy Issues
     - Archived Items
     - Search
     - Category filter
     - Status filter
     - Refresh
     - Pagination
     - Add Product modal
     - Product Details modal
     - Click product to open Product Details
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
        ShopEase - Inventory
    </title>


    {{-- =====================================================
         FONT
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
        'Inventory'
    )

    @include(
        'components.seller.navbar'
    )



    {{-- =====================================================
         INVENTORY PAGE
    ====================================================== --}}

    <main
        id="inventory-page"

        class="
            min-h-screen

            bg-[#FCF8F6]

            pt-[104px]

            ml-[288px]

            transition-[margin-left]
            duration-300
        "
    >

        <div
            class="
                px-[36px]
                pt-[42px]
                pb-[35px]
            "
        >


            {{-- =================================================
                 PAGE HEADER
            ================================================== --}}

            <div
                class="
                    flex
                    items-end
                    justify-between

                    gap-6

                    mb-[18px]
                "
            >

                <div>

                    <h2
                        class="
                            text-[28px]
                            leading-tight
                            font-semibold
                            text-[#17120F]
                        "
                    >
                        Inventory
                    </h2>


                    <p
                        class="
                            mt-[3px]

                            text-[19px]

                            leading-tight

                            text-[#999393]
                        "
                    >
                        Manage your products, stocks, prices, and promotions.
                    </p>

                </div>



                {{-- =================================================
                     ADD PRODUCT
                ================================================== --}}

                <button
                    type="button"

                    id="openAddProductModal"

                    class="
                        shrink-0

                        inline-flex
                        items-center
                        justify-center

                        gap-2

                        h-[34px]

                        px-[16px]

                        rounded-[8px]

                        bg-[#9E241F]

                        text-white

                        text-[13px]

                        font-semibold

                        shadow-sm

                        transition-all
                        duration-200

                        hover:bg-[#861D19]
                        hover:-translate-y-[1px]
                        hover:shadow-md

                        active:scale-[0.98]
                    "
                >

                    <span
                        class="
                            text-[20px]
                            leading-none
                            font-light
                        "
                    >
                        +
                    </span>

                    <span>
                        Add Product
                    </span>

                </button>

            </div>



            {{-- =================================================
                 INVENTORY CARD
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
                     TOP CONTROL BAR
                ================================================== --}}

                <div
                    class="
                        inventory-controls

                        flex
                        items-center
                        justify-between

                        gap-4

                        min-h-[58px]

                        border-b
                        border-[#E8E2DF]

                        px-[12px]
                        pl-[14px]
                    "
                >


                    {{-- =================================================
                         TABS
                    ================================================== --}}

                    <div
                        id="inventoryTabs"

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
                                inventory-tab
                                active
                                relative

                                h-full

                                px-[8px]
                                mr-[48px]

                                text-[14px]
                                font-medium

                                text-[#95908E]

                                transition-colors
                                duration-200

                                hover:text-[#6F6A68]
                            "
                        >
                            All Products
                        </button>



                        <button
                            type="button"

                            data-tab="policy"

                            class="
                                inventory-tab

                                relative

                                h-full

                                px-[8px]
                                mr-[48px]

                                text-[14px]
                                font-medium

                                text-[#95908E]

                                transition-colors
                                duration-200

                                hover:text-[#6F6A68]
                            "
                        >
                            Policy Issues
                        </button>



                        <button
                            type="button"

                            data-tab="archived"

                            class="
                                inventory-tab

                                relative

                                h-full

                                px-[8px]

                                text-[14px]
                                font-medium

                                text-[#95908E]

                                transition-colors
                                duration-200

                                hover:text-[#6F6A68]
                            "
                        >
                            Archived Items
                        </button>

                    </div>



                    {{-- =================================================
                         FILTERS
                    ================================================== --}}

                    <div
                        class="
                            flex
                            items-center

                            gap-[10px]

                            shrink-0
                        "
                    >


                        {{-- SEARCH --}}

                        <div
                            class="
                                inventory-search-wrap

                                relative

                                w-[205px]
                            "
                        >

                            <input
                                type="text"

                                id="productSearch"

                                placeholder="Search product"

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



                        {{-- CATEGORY --}}

                        <select
                            id="categoryFilter"

                            class="
                                h-[35px]

                                w-[175px]

                                rounded-[8px]

                                border
                                border-[#D9D6D4]

                                bg-white

                                px-[10px]

                                text-[12px]
                                text-[#9B9694]

                                outline-none

                                cursor-pointer

                                transition-all
                                duration-200

                                focus:border-[#B8B0AC]

                                focus:ring-[3px]

                                focus:ring-[#7B1B1B]/5
                            "
                        >
                            <option value="all">
                                All Categories
                            </option>

                            <option value="pet-supplies">
                                Pet Supplies
                            </option>

                            <option value="electronics-and-gadgets">
                                Electronics and Gadgets
                            </option>

                            <option value="womens-apparel">
                                Women&#039;s Apparel
                            </option>

                            <option value="mens-apparel">
                                Men&#039;s Apparel
                            </option>

                            <option value="kids-and-baby">
                                Kids and Baby
                            </option>

                            <option value="home-and-garden">
                                Home and Garden
                            </option>

                            <option value="sports-and-outdoors">
                                Sports and Outdoors
                            </option>

                            <option value="health-and-beauty">
                                Health and Beauty
                            </option>

                            <option value="books-and-media">
                                Books and Media
                            </option>

                            <option value="food-and-gourmet">
                                Food and Gourmet
                            </option>

                            <option value="automotive-motorcycle">
                                Automotive &amp; Motorcycle
                            </option>

                            <option value="furniture-and-office-equipment">
                                Furniture and Office Equipment
                            </option>

                            <option value="jewelry-and-watches">
                                Jewelry and Watches
                            </option>

                            <option value="office-and-school-supplies">
                                Office and School Supplies
                            </option>
                        </select>



                        {{-- STATUS --}}

                        <select
                            id="statusFilter"

                            class="
                                h-[35px]

                                w-[97px]

                                rounded-[8px]

                                border
                                border-[#D9D6D4]

                                bg-white

                                px-[10px]

                                text-[12px]
                                text-[#9B9694]

                                outline-none

                                cursor-pointer

                                transition-all
                                duration-200

                                focus:border-[#B8B0AC]

                                focus:ring-[3px]

                                focus:ring-[#7B1B1B]/5
                            "
                        >

                            <option value="all">
                                Status
                            </option>

                            <option value="in-stock">
                                In Stock
                            </option>

                            <option value="low-stock">
                                Low Stock
                            </option>

                            <option value="out-of-stock">
                                Out of Stock
                            </option>

                        </select>



                        {{-- REFRESH --}}

                        <button
                            type="button"

                            id="refreshInventory"

                            class="
                                flex
                                items-center
                                justify-center

                                w-[28px]
                                h-[35px]

                                rounded-full

                                text-[#17120F]

                                transition-all
                                duration-200

                                hover:bg-[#FFF2EE]
                                hover:scale-105

                                active:scale-95
                            "

                            aria-label="Refresh inventory"
                        >

                            <svg
                                id="refreshIcon"

                                class="
                                    w-[22px]
                                    h-[22px]
                                "

                                viewBox="0 0 24 24"

                                fill="none"

                                stroke="currentColor"

                                stroke-width="2"

                                stroke-linecap="round"

                                stroke-linejoin="round"
                            >

                                <path
                                    d="M20 11a8.1 8.1 0 0 0-15.5-2"
                                />

                                <path
                                    d="M4 5v4h4"
                                />

                                <path
                                    d="M4 13a8.1 8.1 0 0 0 15.5 2"
                                />

                                <path
                                    d="M20 19v-4h-4"
                                />

                            </svg>

                        </button>

                    </div>

                </div>



                {{-- =================================================
                     TABLE HEADER
                ================================================== --}}

                <div
                    id="inventoryTableHeader"

                    class="
                        mx-[12px]

                        mt-[18px]

                        rounded-[10px]

                        bg-[#FBE0DD]

                        min-h-[50px]

                        px-[20px]

                        text-[13px]

                        font-medium

                        text-[#60100F]
                    "
                >


                    {{-- ALL PRODUCTS HEADER --}}

                    <div
                        id="allProductsHeader"

                        class="
                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]

                            items-center

                            min-h-[50px]
                        "
                    >

                        <div>
                            Product
                        </div>

                        <div>
                            Category
                        </div>

                        <div>
                            Price
                        </div>

                        <div>
                            Stock
                        </div>

                        <div>
                            Status
                        </div>

                        <div></div>

                    </div>



                    {{-- POLICY HEADER --}}

                    <div
                        id="policyIssuesHeader"

                        class="
                            hidden

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr]

                            items-center

                            min-h-[50px]
                        "
                    >

                        <div>
                            Product
                        </div>

                        <div>
                            Category
                        </div>

                        <div>
                            Price
                        </div>

                        <div>
                            Stock
                        </div>

                        <div>
                            Issue
                        </div>

                    </div>

                </div>



                {{-- =================================================
                     ALL PRODUCTS
                ================================================== --}}

                <div
                    id="allProductsTable"

                    class="mt-[4px]"
                >


                    {{-- =================================================
                         PRODUCT 1
                    ================================================== --}}

                    <article
                        class="
                            inventory-row
                            product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]

                            items-center

                            min-h-[90px]

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "

                        data-name="Wireless Bag haha gage"

                        data-category="pet-supplies"

                        data-status="in-stock"

                        tabindex="0"

                        role="button"
                    >

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>


                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-pet-supplies">
                                Pet Supplies
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div>

                            <span
                                class="
                                    status-badge
                                    status-in-stock
                                "
                            >
                                In Stock
                            </span>

                        </div>



                        <div></div>

                    </article>



                    {{-- PRODUCT 2 --}}

                    <article
                        class="
                            inventory-row
                            product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]

                            items-center

                            min-h-[90px]

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "

                        data-name="Wireless Bag haha gage"

                        data-category="electronics-and-gadgets"

                        data-status="in-stock"

                        data-policy="true"

                        data-issue-title="Misleading product information"

                        data-issue-date="March 24, 2026   10:00 AM"

                        tabindex="0"

                        role="button"
                    >

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-electronics-and-gadgets">
                                Electronics and Gadgets
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div>

                            <span
                                class="
                                    status-badge
                                    status-in-stock
                                "
                            >
                                In Stock
                            </span>

                        </div>



                        <div
                            class="
                                flex
                                justify-center
                            "
                        >

                            <span
                                class="stock-warning"
                                title="Review stock level"
                            >
                                !
                            </span>

                        </div>

                    </article>



                    {{-- PRODUCT 3 --}}

                    <article
                        class="
                            inventory-row
                            product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]

                            items-center

                            min-h-[90px]

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "

                        data-name="Wireless Bag haha gage"

                        data-category="womens-apparel"

                        data-status="low-stock"

                        tabindex="0"

                        role="button"
                    >

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-womens-apparel">
                                Women&#039;s Apparel
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            5
                        </div>



                        <div>

                            <span
                                class="
                                    status-badge
                                    status-low-stock
                                "
                            >
                                Low Stock
                            </span>

                        </div>



                        <div></div>

                    </article>



                    {{-- PRODUCT 4 --}}

                    <article
                        class="
                            inventory-row
                            product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]

                            items-center

                            min-h-[90px]

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "

                        data-name="Wireless Bag haha gage"

                        data-category="mens-apparel"

                        data-status="in-stock"

                        data-policy="true"

                        data-issue-title="Misleading product information"

                        data-issue-date="March 24, 2026   10:00 AM"

                        tabindex="0"

                        role="button"
                    >

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-mens-apparel">
                                Men&#039;s Apparel
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div>

                            <span
                                class="
                                    status-badge
                                    status-in-stock
                                "
                            >
                                In Stock
                            </span>

                        </div>



                        <div
                            class="
                                flex
                                justify-center
                            "
                        >

                            <span class="stock-warning">
                                !
                            </span>

                        </div>

                    </article>



                    {{-- PRODUCT 5 --}}

                    <article
                        class="
                            inventory-row
                            product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]

                            items-center

                            min-h-[90px]

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "

                        data-name="Wireless Bag haha gage"

                        data-category="kids-and-baby"

                        data-status="out-of-stock"

                        tabindex="0"

                        role="button"
                    >

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-kids-and-baby">
                                Kids and Baby
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            0
                        </div>



                        <div>

                            <span
                                class="
                                    status-badge
                                    status-out-stock
                                "
                            >
                                Out of Stock
                            </span>

                        </div>



                        <div></div>

                    </article>



                    {{-- PRODUCT 6 --}}

                    <article
                        class="
                            inventory-row
                            product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]

                            items-center

                            min-h-[90px]

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "

                        data-name="Wireless Bag haha gage"

                        data-category="home-and-garden"

                        data-status="in-stock"

                        tabindex="0"

                        role="button"
                    >

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-home-and-garden">
                                Home and Garden
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div>

                            <span
                                class="
                                    status-badge
                                    status-in-stock
                                "
                            >
                                In Stock
                            </span>

                        </div>



                        <div></div>

                    </article>



                    {{-- PRODUCT 7 --}}

                    <article
                        class="
                            inventory-row
                            product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]

                            items-center

                            min-h-[90px]

                            px-[20px]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "

                        data-name="Wireless Bag haha gage"

                        data-category="sports-and-outdoors"

                        data-status="in-stock"

                        tabindex="0"

                        role="button"
                    >

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-sports-and-outdoors">
                                Sports and Outdoors
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div>

                            <span
                                class="
                                    status-badge
                                    status-in-stock
                                "
                            >
                                In Stock
                            </span>

                        </div>



                        <div></div>

                    </article>

                </div>



                {{-- =================================================
                     POLICY ISSUES
                ================================================== --}}

                <div
                    id="policyIssuesTable"

                    class="hidden"
                >


                    {{-- POLICY 1 --}}

                    <article
                        class="
                            policy-row
                            policy-product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr]

                            items-center

                            min-h-[90px]

                            cursor-pointer

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "
                    
                        data-policy="true"

                        data-category="health-and-beauty"

                        data-issue-title="Misleading product information"

                        data-issue-date="March 24, 2026   10:00 AM"

                        tabindex="0"

                        role="button">

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-health-and-beauty">
                                Health and Beauty
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div class="policy-issue">

                            <span class="stock-warning">
                                !
                            </span>

                            <div>

                                <p class="issue-title">
                                    Misleading product information...
                                </p>

                                <p class="issue-date">
                                    March 24, 2026&nbsp;&nbsp;10:00 AM
                                </p>

                            </div>

                        </div>

                    </article>



                    {{-- POLICY 2 --}}

                    <article
                        class="
                            policy-row
                            policy-product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr]

                            items-center

                            min-h-[90px]

                            cursor-pointer

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "
                    
                        data-policy="true"

                        data-category="books-and-media"

                        data-issue-title="Misleading product information"

                        data-issue-date="March 24, 2026   10:00 AM"

                        tabindex="0"

                        role="button">

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-books-and-media">
                                Books and Media
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div class="policy-issue">

                            <span class="stock-warning">
                                !
                            </span>

                            <div>

                                <p class="issue-title">
                                    Misleading product information...
                                </p>

                                <p class="issue-date">
                                    March 24, 2026&nbsp;&nbsp;10:00 AM
                                </p>

                            </div>

                        </div>

                    </article>



                    {{-- POLICY 3 --}}

                    <article
                        class="
                            policy-row
                            policy-product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr]

                            items-center

                            min-h-[90px]

                            cursor-pointer

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "
                    
                        data-policy="true"

                        data-category="food-and-gourmet"

                        data-issue-title="Misleading product information"

                        data-issue-date="March 24, 2026   10:00 AM"

                        tabindex="0"

                        role="button">

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-food-and-gourmet">
                                Food and Gourmet
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            5
                        </div>



                        <div class="policy-issue">

                            <span class="stock-warning">
                                !
                            </span>

                            <div>

                                <p class="issue-title">
                                    Misleading product information...
                                </p>

                                <p class="issue-date">
                                    March 24, 2026&nbsp;&nbsp;10:00 AM
                                </p>

                            </div>

                        </div>

                    </article>



                    {{-- POLICY 4 --}}

                    <article
                        class="
                            policy-row
                            policy-product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr]

                            items-center

                            min-h-[90px]

                            cursor-pointer

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "
                    
                        data-policy="true"

                        data-category="automotive-motorcycle"

                        data-issue-title="Misleading product information"

                        data-issue-date="March 24, 2026   10:00 AM"

                        tabindex="0"

                        role="button">

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-automotive-motorcycle">
                                Automotive &amp; Motorcycle
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div class="policy-issue">

                            <span class="stock-warning">
                                !
                            </span>

                            <div>

                                <p class="issue-title">
                                    Misleading product information...
                                </p>

                                <p class="issue-date">
                                    March 24, 2026&nbsp;&nbsp;10:00 AM
                                </p>

                            </div>

                        </div>

                    </article>



                    {{-- POLICY 5 --}}

                    <article
                        class="
                            policy-row
                            policy-product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr]

                            items-center

                            min-h-[90px]

                            cursor-pointer

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "
                    
                        data-policy="true"

                        data-category="furniture-and-office-equipment"

                        data-issue-title="Misleading product information"

                        data-issue-date="March 24, 2026   10:00 AM"

                        tabindex="0"

                        role="button">

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-furniture-and-office-equipment">
                                Furniture and Office Equipment
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            0
                        </div>



                        <div class="policy-issue">

                            <span class="stock-warning">
                                !
                            </span>

                            <div>

                                <p class="issue-title">
                                    Misleading product information...
                                </p>

                                <p class="issue-date">
                                    March 24, 2026&nbsp;&nbsp;10:00 AM
                                </p>

                            </div>

                        </div>

                    </article>



                    {{-- POLICY 6 --}}

                    <article
                        class="
                            policy-row
                            policy-product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr]

                            items-center

                            min-h-[90px]

                            cursor-pointer

                            px-[20px]

                            border-b
                            border-[#DDD9D7]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "
                    
                        data-policy="true"

                        data-category="jewelry-and-watches"

                        data-issue-title="Misleading product information"

                        data-issue-date="March 24, 2026   10:00 AM"

                        tabindex="0"

                        role="button">

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-jewelry-and-watches">
                                Jewelry and Watches
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div class="policy-issue">

                            <span class="stock-warning">
                                !
                            </span>

                            <div>

                                <p class="issue-title">
                                    Misleading product information...
                                </p>

                                <p class="issue-date">
                                    March 24, 2026&nbsp;&nbsp;10:00 AM
                                </p>

                            </div>

                        </div>

                    </article>



                    {{-- POLICY 7 --}}

                    <article
                        class="
                            policy-row
                            policy-product-clickable

                            grid

                            grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr]

                            items-center

                            min-h-[90px]

                            cursor-pointer

                            px-[20px]

                            transition-all
                            duration-200

                            hover:bg-[#FFFBF9]
                        "
                    
                        data-policy="true"

                        data-category="office-and-school-supplies"

                        data-issue-title="Misleading product information"

                        data-issue-date="March 24, 2026   10:00 AM"

                        tabindex="0"

                        role="button">

                        <div class="product-main">

                            <div class="product-thumb">

                                <div class="product-bag"></div>

                            </div>


                            <div class="min-w-0">

                                <h3 class="product-name">
                                    Wireless Bag haha gage
                                </h3>

                                <p class="product-sold">
                                    10 sold
                                </p>

                            </div>

                        </div>



                        <div>

                            <span class="category-badge category-office-and-school-supplies">
                                Office and School Supplies
                            </span>

                        </div>



                        <div class="product-number">
                            ₱559.00
                        </div>



                        <div class="product-number">
                            20
                        </div>



                        <div class="policy-issue">

                            <span class="stock-warning">
                                !
                            </span>

                            <div>

                                <p class="issue-title">
                                    Misleading product information...
                                </p>

                                <p class="issue-date">
                                    March 24, 2026&nbsp;&nbsp;10:00 AM
                                </p>

                            </div>

                        </div>

                    </article>

                </div>





                {{-- =================================================
                     ARCHIVED ITEMS
                ================================================== --}}

                <div
                    id="archivedItemsTable"
                    class="hidden"
                >

                </div>

                <div
                    id="archivedItemsEmpty"
                    class="hidden px-[20px] py-[50px] text-center"
                >
                    <p class="text-[15px] font-medium text-[#7D7774]">
                        No archived products.
                    </p>
                    <p class="mt-[4px] text-[12px] text-[#AAA6A4]">
                        Removed products will appear here.
                    </p>
                </div>

                {{-- =================================================
                     NO RESULTS
                ================================================== --}}

                <div
                    id="inventoryNoResults"

                    class="
                        hidden

                        px-[20px]
                        py-[50px]

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
                        No products found.
                    </p>


                    <p
                        class="
                            mt-[4px]
                            text-[12px]
                            text-[#AAA6A4]
                        "
                    >
                        Try another search or filter.
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

                        min-h-[42px]

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

                        <span id="showingCount">
                            7
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

                            id="previousPage"

                            class="
                                pagination-button
                                disabled
                            "

                            aria-label="Previous page"
                        >
                            ‹
                        </button>


                        <button
                            type="button"

                            data-page="1"

                            class="
                                pagination-button
                                current
                            "
                        >
                            1
                        </button>


                        <button
                            type="button"

                            data-page="2"

                            class="
                                pagination-button
                            "
                        >
                            2
                        </button>


                        <button
                            type="button"

                            data-page="3"

                            class="
                                pagination-button
                            "
                        >
                            3
                        </button>


                        <button
                            type="button"

                            id="nextPage"

                            class="
                                pagination-button
                            "

                            aria-label="Next page"
                        >
                            ›
                        </button>



                        <select
                            id="itemsPerPage"

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
         ADD PRODUCT MODAL
    ========================================================== --}}

    <div
        id="addProductModal"

        class="
            fixed
            inset-0

            z-[100]

            hidden

            items-center
            justify-center

            bg-black/30

            backdrop-blur-[2px]

            p-5
        "
    >

        <div
            id="addProductModalPanel"

            class="
                w-full

                max-w-[560px]

                rounded-[18px]

                bg-white

                shadow-2xl

                overflow-hidden

                opacity-0

                transition-all
                duration-200
            "
        >

            <div
                class="
                    flex
                    items-center
                    justify-between

                    px-[24px]
                    py-[18px]

                    border-b
                    border-[#ECE7E5]
                "
            >

                <div>

                    <h3
                        class="
                            text-[20px]
                            font-semibold
                            text-[#17120F]
                        "
                    >
                        Add Product
                    </h3>


                    <p
                        class="
                            mt-[2px]
                            text-[12px]
                            text-[#999393]
                        "
                    >
                        Add a new product to your inventory.
                    </p>

                </div>


                <button
                    type="button"

                    id="closeAddProductModal"

                    class="
                        flex
                        items-center
                        justify-center

                        w-[34px]
                        h-[34px]

                        rounded-full

                        text-[#77716E]

                        transition-all
                        duration-200

                        hover:bg-[#FFF2EE]

                        hover:text-[#6A1616]
                    "
                >

                    <svg
                        class="w-5 h-5"

                        viewBox="0 0 24 24"

                        fill="none"

                        stroke="currentColor"

                        stroke-width="2"

                        stroke-linecap="round"
                    >

                        <path d="M6 6l12 12"/>

                        <path d="M18 6L6 18"/>

                    </svg>

                </button>

            </div>



            <form
                id="addProductForm"

                class="
                    p-[24px]
                "
            >

                <div
                    class="
                        grid
                        grid-cols-2
                        gap-[16px]
                    "
                >

                    <div class="col-span-2">

                        <label
                            class="
                                block
                                mb-[6px]

                                text-[13px]
                                font-medium
                                text-[#2F2926]
                            "
                        >
                            Product Name
                        </label>


                        <input
                            type="text"

                            required

                            placeholder="Enter product name"

                            class="
                                w-full
                                h-[44px]

                                rounded-[8px]

                                border
                                border-[#D9D3D0]

                                px-[12px]

                                text-[13px]

                                outline-none

                                focus:border-[#A52A2A]
                                focus:ring-[3px]
                                focus:ring-[#A52A2A]/5
                            "
                        >

                    </div>



                    <div>

                        <label
                            class="
                                block
                                mb-[6px]

                                text-[13px]
                                font-medium
                                text-[#2F2926]
                            "
                        >
                            Category
                        </label>


                        <select
                            required

                            class="
                                w-full
                                h-[44px]

                                rounded-[8px]

                                border
                                border-[#D9D3D0]

                                px-[12px]

                                text-[13px]

                                outline-none
                            "
                        >
                            <option value="">
                                Select category
                            </option>

                            <option value="pet-supplies">
                                Pet Supplies
                            </option>

                            <option value="electronics-and-gadgets">
                                Electronics and Gadgets
                            </option>

                            <option value="womens-apparel">
                                Women&#039;s Apparel
                            </option>

                            <option value="mens-apparel">
                                Men&#039;s Apparel
                            </option>

                            <option value="kids-and-baby">
                                Kids and Baby
                            </option>

                            <option value="home-and-garden">
                                Home and Garden
                            </option>

                            <option value="sports-and-outdoors">
                                Sports and Outdoors
                            </option>

                            <option value="health-and-beauty">
                                Health and Beauty
                            </option>

                            <option value="books-and-media">
                                Books and Media
                            </option>

                            <option value="food-and-gourmet">
                                Food and Gourmet
                            </option>

                            <option value="automotive-motorcycle">
                                Automotive &amp; Motorcycle
                            </option>

                            <option value="furniture-and-office-equipment">
                                Furniture and Office Equipment
                            </option>

                            <option value="jewelry-and-watches">
                                Jewelry and Watches
                            </option>

                            <option value="office-and-school-supplies">
                                Office and School Supplies
                            </option>
                        </select>

                    </div>



                    <div>

                        <label
                            class="
                                block
                                mb-[6px]

                                text-[13px]
                                font-medium
                                text-[#2F2926]
                            "
                        >
                            Price
                        </label>


                        <input
                            type="number"

                            min="0"

                            step="0.01"

                            required

                            placeholder="₱0.00"

                            class="
                                w-full
                                h-[44px]

                                rounded-[8px]

                                border
                                border-[#D9D3D0]

                                px-[12px]

                                text-[13px]

                                outline-none
                            "
                        >

                    </div>



                    <div>

                        <label
                            class="
                                block
                                mb-[6px]

                                text-[13px]
                                font-medium
                                text-[#2F2926]
                            "
                        >
                            Stock
                        </label>


                        <input
                            type="number"

                            min="0"

                            required

                            placeholder="0"

                            class="
                                w-full
                                h-[44px]

                                rounded-[8px]

                                border
                                border-[#D9D3D0]

                                px-[12px]

                                text-[13px]

                                outline-none
                            "
                        >

                    </div>



                    <div>

                        <label
                            class="
                                block
                                mb-[6px]

                                text-[13px]
                                font-medium
                                text-[#2F2926]
                            "
                        >
                            SKU
                        </label>


                        <input
                            type="text"

                            placeholder="Optional SKU"

                            class="
                                w-full
                                h-[44px]

                                rounded-[8px]

                                border
                                border-[#D9D3D0]

                                px-[12px]

                                text-[13px]

                                outline-none
                            "
                        >

                    </div>

                </div>



                <div
                    class="
                        flex
                        justify-end

                        gap-[10px]

                        mt-[24px]
                    "
                >

                    <button
                        type="button"

                        id="cancelAddProduct"

                        class="
                            h-[42px]

                            px-[18px]

                            rounded-[9px]

                            border
                            border-[#D9D3D0]

                            bg-white

                            text-[13px]

                            font-medium

                            text-[#625D5A]

                            transition-all
                            duration-200

                            hover:bg-[#FAF7F5]
                        "
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"

                        class="
                            h-[42px]

                            px-[20px]

                            rounded-[9px]

                            bg-[#9E241F]

                            text-[13px]

                            font-semibold

                            text-white

                            transition-all
                            duration-200

                            hover:bg-[#861D19]

                            active:scale-[0.98]
                        "
                    >
                        Add Product
                    </button>

                </div>

            </form>

        </div>

    </div>



    {{-- =========================================================
         PRODUCT DETAILS MODAL
         FIGMA MATCHED
    ========================================================== --}}

    <div
        id="productDetailsModal"

        class="
            fixed
            inset-0

            z-[110]

            hidden

            items-center
            justify-center

            bg-black/20

            backdrop-blur-[3px]

            opacity-0

            transition-opacity
            duration-200
            ease-out

            p-0
            sm:p-[8px]
        "
    >


        {{-- =====================================================
             MODAL PANEL
        ====================================================== --}}

        <div
            id="productDetailsModalPanel"

            class="
                product-details-panel

                relative

                w-full

                max-w-[916px]

                h-[calc(100vh-16px)]

                max-h-[calc(100vh-16px)]

                overflow-y-auto

                overflow-x-hidden

                rounded-[28px]

                bg-white

                shadow-[0_12px_40px_rgba(0,0,0,0.10)]

                opacity-0

                transition-opacity
                duration-200
                ease-out
            "
        >


            {{-- =================================================
                 MODAL TITLE
            ================================================== --}}

            <div
                class="
                    shrink-0

                    px-[35px]
                    pt-[20px]
                    pb-[4px]
                "
            >

                <h2
                    class="
                        text-[20px]
                        font-semibold
                        leading-tight
                        text-[#17120F]
                    "
                >
                    Product Details
                </h2>

            </div>


            {{-- =================================================
                 PRODUCT SUMMARY
            ================================================== --}}

            <div
                class="
                    px-[35px]
                    pt-[12px]
                    pb-[20px]
                "
            >

                <div
                    class="
                        border
                        border-[#D8D8D8]

                        rounded-[19px]

                        px-[42px]
                        pt-[17px]
                        pb-[15px]

                        min-h-[286px]
                    "
                >

                    <div
                        class="
                            grid

                            grid-cols-[225px_1fr]

                            gap-[18px]
                        "
                    >


                        {{-- =================================
                             IMAGE SIDE
                        ================================== --}}

                        <div>

                            <div
                                class="
                                    w-[188px]
                                    h-[188px]

                                    rounded-[9px]

                                    border
                                    border-[#BBBBBB]

                                    bg-white

                                    flex
                                    items-center
                                    justify-center

                                    overflow-hidden

                                    mx-auto
                                "
                            >

                                <div
                                    class="
                                        product-real-image-wrap

                                        relative

                                        w-full
                                        h-full
                                    "
                                >

                                    <img
                                        src="{{ asset('images/products/graphic-tshirt.png') }}"

                                        alt="Men's Graphic T-shirt"

                                        class="
                                            product-real-image

                                            absolute
                                            inset-0

                                            w-full
                                            h-full

                                            object-contain

                                            bg-white
                                        "

                                        onerror="
                                            this.style.display='none';
                                            this.nextElementSibling.style.display='flex';
                                        "
                                    >


                                    <div
                                        class="
                                            product-shirt-fallback

                                            absolute
                                            inset-0

                                            items-center
                                            justify-center

                                            bg-white
                                        "
                                        style="display:none;"
                                    >

                                        <svg
                                            width="150"
                                            height="170"
                                            viewBox="0 0 150 170"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                        >

                                            <path
                                                d="M49 21
                                                   C55 14 62 10 75 10
                                                   C88 10 95 14 101 21
                                                   L137 38
                                                   L120 69
                                                   L108 62
                                                   L108 158
                                                   L42 158
                                                   L42 62
                                                   L30 69
                                                   L13 38
                                                   L49 21Z"
                                                fill="#111111"
                                            />

                                            <path
                                                d="M57 16
                                                   C60 24 66 28 75 28
                                                   C84 28 90 24 93 16"
                                                stroke="#2B2B2B"
                                                stroke-width="4"
                                                stroke-linecap="round"
                                            />

                                            <path
                                                d="M42 57L42 157"
                                                stroke="#232323"
                                                stroke-width="3"
                                            />

                                            <path
                                                d="M108 57L108 157"
                                                stroke="#232323"
                                                stroke-width="3"
                                            />

                                        </svg>

                                    </div>

                                </div>

                            </div>



                            {{-- THUMBNAILS --}}

                            <div
                                class="
                                    mt-[9px]

                                    flex
                                    items-center
                                    justify-center

                                    gap-[7px]
                                "
                            >

                                <div class="product-thumbnail">
                                    <div class="mini-shirt"></div>
                                </div>

                                <div class="product-thumbnail">
                                    <div class="mini-shirt"></div>
                                </div>

                                <div class="product-thumbnail">
                                    <div class="mini-shirt"></div>
                                </div>

                                <div class="product-thumbnail">
                                    <div class="mini-shirt"></div>
                                </div>

                                <div class="product-thumbnail">
                                    <div class="mini-shirt"></div>
                                </div>

                                <span
                                    class="
                                        text-[31px]

                                        leading-none

                                        text-[#17120F]

                                        ml-[2px]
                                    "
                                >
                                    ›
                                </span>

                            </div>

                        </div>



                        {{-- =================================
                             PRODUCT INFO
                        ================================== --}}

                        <div
                            class="
                                pt-[23px]
                            "
                        >

                            <div
                                class="
                                    grid
                                    grid-cols-[1fr_110px]
                                    items-start

                                    gap-4
                                "
                            >

                                <div>

                                    <h2
                                        class="
                                            text-[23px]

                                            font-semibold

                                            leading-tight

                                            text-[#080808]
                                        "
                                    >
                                        Men’s Graphic T-shirt
                                    </h2>


                                    <p
                                        class="
                                            mt-[8px]

                                            text-[21px]

                                            leading-none

                                            font-semibold

                                            text-[#6D1010]
                                        "
                                    >
                                        ₱59
                                    </p>

                                </div>


                                <div
                                    class="
                                        pt-[42px]

                                        text-[11px]

                                        text-[#3D3937]
                                    "
                                >
                                    0 sold
                                </div>

                            </div>



                            {{-- CATEGORY --}}

                            <div
                                class="
                                    mt-[24px]
                                "
                            >

                                <p
                                    class="
                                        text-[14px]

                                        text-[#8F8B89]
                                    "
                                >
                                    Category
                                </p>


                                <span
                                    id="productDetailsCategory"
                                    class="
                                        inline-flex
                                        items-center

                                        mt-[7px]

                                        rounded-full

                                        px-[13px]
                                        py-[4px]

                                        text-[11px]

                                        font-medium

                                        category-badge
                                        category-womens-apparel
                                    "
                                >
                                    Women’s Apparel
                                </span>

                            </div>



                            {{-- STOCK --}}

                            <div
                                class="
                                    mt-[20px]
                                "
                            >

                                <p
                                    class="
                                        text-[14px]

                                        text-[#8F8B89]
                                    "
                                >
                                    Stock
                                </p>


                                <p
                                    class="
                                        mt-[4px]

                                        text-[15px]

                                        font-medium

                                        text-[#161414]
                                    "
                                >
                                    154 pieces
                                </p>

                            </div>

                        </div>

                    </div>



                    {{-- UPLOADED DATE --}}

                    <div
                        class="
                            flex
                            justify-end

                            mt-[2px]
                        "
                    >

                        <p
                            class="
                                text-[12px]

                                text-[#8E8885]
                            "
                        >

                            Uploaded:

                            <span
                                class="
                                    text-[#17120F]

                                    font-medium
                                "
                            >
                                September 9, 2026&nbsp;&nbsp;2:13 PM
                            </span>

                        </p>

                    </div>

                </div>

            </div>



            {{-- =================================================
                 POLICY WARNING
            ================================================== --}}

            <div
                id="productPolicyWarning"
                class="
                    hidden
                    mx-[35px]
                    mb-[20px]
                    rounded-[18px]
                    bg-[#FFE9BE]
                    px-[23px]
                    py-[17px]
                "
            >

                <div
                    class="
                        flex
                        items-start
                        gap-[15px]
                    "
                >

                    <div
                        class="
                            flex
                            items-center
                            justify-center
                            shrink-0
                            w-[42px]
                            h-[42px]
                        "
                    >
                        <svg
                            class="w-[40px] h-[40px]"
                            viewBox="0 0 48 48"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                            aria-hidden="true"
                        >
                            <path
                                d="M24 4L45 41H3L24 4Z"
                                fill="#E9A33C"
                            />
                            <path
                                d="M24 15V29"
                                stroke="white"
                                stroke-width="4"
                                stroke-linecap="round"
                            />
                            <circle
                                cx="24"
                                cy="35"
                                r="2.2"
                                fill="white"
                            />
                        </svg>
                    </div>

                    <div class="min-w-0 pt-[1px]">
                        <p
                            id="productPolicyIssueTitle"
                            class="
                                text-[18px]
                                font-semibold
                                leading-tight
                                text-[#17120F]
                            "
                        >
                            Misleading product information
                        </p>

                        <p
                            id="productPolicyIssueDate"
                            class="
                                mt-[5px]
                                text-[11px]
                                text-[#A78E6D]
                            "
                        >
                            March 24, 2026  10:00 AM
                        </p>
                    </div>

                </div>

            </div>

            {{-- =================================================
                 DESCRIPTION
            ================================================== --}}

            <div
                class="
                    px-[39px]
                "
            >

                <label
                    class="
                        block

                        mb-[9px]

                        text-[17px]

                        font-semibold

                        text-[#121212]
                    "
                >
                    Product Description
                </label>


                <textarea
                    id="productDescription"

                    maxlength="300"

                    class="
                        w-full

                        h-[78px]

                        resize-none

                        rounded-[15px]

                        border
                        border-[#D8D8D8]

                        bg-white

                        px-[22px]
                        py-[11px]

                        text-[16px]

                        leading-[1.5]

                        text-[#252220]

                        outline-none

                        transition-all
                        duration-200

                        focus:border-[#B8B0AC]

                        focus:ring-[3px]

                        focus:ring-[#B8B0AC]/10
                    "
                >High-quality cotton graphic T-shirt for everyday wear. Comfortable, breathable, and perfect for casual outfits. Available in multiple sizes.</textarea>


                <div
                    class="
                        flex
                        justify-end

                        mt-[-20px]

                        pr-[18px]

                        pointer-events-none
                    "
                >

                    <span
                        id="descriptionCount"

                        class="
                            text-[10px]

                            text-[#77716E]
                        "
                    >
                        105/300
                    </span>

                </div>

            </div>



            {{-- =================================================
                 PRODUCT DETAILS
            ================================================== --}}

            <div
                class="
                    px-[62px]

                    pt-[16px]
                "
            >

                <h3
                    class="
                        text-[17px]

                        font-semibold

                        text-[#17120F]

                        mb-[12px]
                    "
                >
                    Product Details
                </h3>



                <div
                    class="
                        grid

                        grid-cols-[190px_1fr]

                        items-center

                        gap-y-[12px]
                    "
                >


                    {{-- BRAND --}}

                    <label
                        class="
                            text-[16px]

                            text-[#8D8987]
                        "
                    >
                        Brand
                    </label>


                    <input
                        type="text"

                        value="HangLoose"

                        class="
                            w-[215px]

                            h-[38px]

                            rounded-[9px]

                            border
                            border-[#D6D2D0]

                            px-[11px]

                            text-[16px]

                            text-[#282422]

                            outline-none

                            transition-all
                            duration-200

                            focus:border-[#B8B0AC]
                            focus:ring-[3px]
                            focus:ring-[#B8B0AC]/10
                        "
                    >



                    {{-- MATERIAL --}}

                    <label
                        class="
                            text-[16px]

                            text-[#8D8987]
                        "
                    >
                        Material
                    </label>


                    <input
                        type="text"

                        value="100% Cotton"

                        class="
                            w-[215px]

                            h-[38px]

                            rounded-[9px]

                            border
                            border-[#D6D2D0]

                            px-[11px]

                            text-[16px]

                            text-[#282422]

                            outline-none

                            transition-all
                            duration-200

                            focus:border-[#B8B0AC]
                            focus:ring-[3px]
                            focus:ring-[#B8B0AC]/10
                        "
                    >



                    {{-- SIZES --}}

                    <label
                        class="
                            text-[16px]

                            text-[#8D8987]
                        "
                    >
                        Sizes
                    </label>


                    <input
                        type="text"

                        value="S, M, L, XL, XXL"

                        class="
                            w-[215px]

                            h-[38px]

                            rounded-[9px]

                            border
                            border-[#D6D2D0]

                            px-[11px]

                            text-[16px]

                            text-[#282422]

                            outline-none

                            transition-all
                            duration-200

                            focus:border-[#B8B0AC]
                            focus:ring-[3px]
                            focus:ring-[#B8B0AC]/10
                        "
                    >



                    {{-- COLORS --}}

                    <label
                        class="
                            text-[16px]

                            text-[#8D8987]
                        "
                    >
                        Colors
                    </label>


                    <input
                        type="text"

                        value="Black, White, Gray"

                        class="
                            w-[215px]

                            h-[38px]

                            rounded-[9px]

                            border
                            border-[#D6D2D0]

                            px-[11px]

                            text-[16px]

                            text-[#282422]

                            outline-none

                            transition-all
                            duration-200

                            focus:border-[#B8B0AC]
                            focus:ring-[3px]
                            focus:ring-[#B8B0AC]/10
                        "
                    >



                    {{-- WEIGHT --}}

                    <label
                        class="
                            text-[16px]

                            text-[#8D8987]
                        "
                    >
                        Weight
                    </label>


                    <input
                        type="text"

                        value="150g"

                        class="
                            w-[215px]

                            h-[38px]

                            rounded-[9px]

                            border
                            border-[#D6D2D0]

                            px-[11px]

                            text-[16px]

                            text-[#282422]

                            outline-none

                            transition-all
                            duration-200

                            focus:border-[#B8B0AC]
                            focus:ring-[3px]
                            focus:ring-[#B8B0AC]/10
                        "
                    >



                    {{-- COUNTRY --}}

                    <label
                        class="
                            text-[16px]

                            text-[#8D8987]
                        "
                    >
                        Country of Origin
                    </label>


                    <input
                        type="text"

                        value="Philippines"

                        class="
                            w-[215px]

                            h-[38px]

                            rounded-[9px]

                            border
                            border-[#D6D2D0]

                            px-[11px]

                            text-[16px]

                            text-[#282422]

                            outline-none

                            transition-all
                            duration-200

                            focus:border-[#B8B0AC]
                            focus:ring-[3px]
                            focus:ring-[#B8B0AC]/10
                        "
                    >

                </div>

            </div>



            {{-- =================================================
                 MODAL ACTIONS
            ================================================== --}}

            <div
                class="
                    flex
                    justify-end
                    items-center

                    gap-[16px]

                    px-[38px]

                    pb-[24px]

                    pt-[24px]
                "
            >

                {{-- REMOVE --}}

                <button
                    type="button"

                    id="removeProductButton"

                    class="
                        h-[48px]

                        min-w-[120px]

                        px-[22px]

                        rounded-[8px]

                        border
                        border-[#F0A276]

                        bg-[#FFF0E8]

                        text-[16px]

                        font-semibold

                        text-[#C85D00]

                        transition-all
                        duration-200

                        hover:bg-[#FFE4D8]
                        hover:-translate-y-[1px]

                        active:scale-[0.98]
                    "
                >
                    Remove
                </button>



                {{-- SAVE --}}

                <button
                    type="button"

                    id="saveProductChanges"

                    class="
                        h-[48px]

                        min-w-[165px]

                        px-[24px]

                        rounded-[8px]

                        bg-[#9E241F]

                        text-[16px]

                        font-semibold

                        text-white

                        transition-all
                        duration-200

                        hover:bg-[#861D19]
                        hover:-translate-y-[1px]

                        active:scale-[0.98]
                    "
                >
                    Save Changes
                </button>



                {{-- CANCEL --}}

                <button
                    type="button"

                    id="cancelProductDetails"

                    class="
                        h-[48px]

                        min-w-[145px]

                        px-[24px]

                        rounded-[8px]

                        border
                        border-[#242424]

                        bg-white

                        text-[16px]

                        font-semibold

                        text-[#17120F]

                        hover:bg-[#FAF7F5]
                    "
                >
                    Cancel
                </button>

            </div>

        </div>

    </div>



    {{-- =========================================================
         REMOVE PRODUCT MODAL
         FIGMA-MATCHED
    ========================================================== --}}

    <div
        id="removeProductModal"
        class="
            fixed
            inset-0
            z-[130]
            hidden
            items-center
            justify-center
            bg-black/25
            backdrop-blur-[2px]
            opacity-0
            transition-opacity
            duration-200
            ease-out
            p-[20px]
        "
        aria-hidden="true"
    >

        <div
            id="removeProductModalPanel"
            class="
                remove-product-panel
                relative
                w-full
                max-w-[610px]
                rounded-[28px]
                bg-white
                shadow-[0_12px_40px_rgba(0,0,0,0.10)]
                opacity-0
                transition-opacity
                duration-200
                ease-out
            "
        >

            <div class="px-[31px] pt-[30px] pb-[27px]">

                <div>
                    <h2
                        class="
                            text-[22px]
                            font-semibold
                            leading-tight
                            text-[#17120F]
                        "
                    >
                        Remove Product
                    </h2>

                    <p
                        class="
                            mt-[10px]
                            text-[16px]
                            leading-tight
                            text-[#A29D9A]
                        "
                    >
                        You are about to remove your product.
                    </p>
                </div>

                <div class="mt-[26px]">

                    <p
                        class="
                            mb-[10px]
                            text-[17px]
                            font-medium
                            text-[#17120F]
                        "
                    >
                        Reason<span class="text-red-500">*</span>
                    </p>

                    <div
                        id="removeReasonOptions"
                        class="space-y-[8px]"
                    >

                        @php
                            $removeReasons = [
                                'no-longer-selling' => 'No longer selling this product',
                                'updating-listing' => 'Updating or replacing the product listing',
                                'pricing-changes' => 'Pricing or cost changes',
                                'supplier-changes' => 'Supplier or sourcing changes',
                                'low-demand' => 'Product has low customer demand',
                                'other' => 'Other (please specify)',
                            ];
                        @endphp

                        @foreach ($removeReasons as $value => $label)
                            <label
                                class="
                                    remove-reason-option
                                    flex
                                    items-center
                                    gap-[12px]
                                    min-h-[39px]
                                    px-[11px]
                                    rounded-[6px]
                                    border
                                    border-[#E2E0DF]
                                    bg-[#FAFAFA]
                                    cursor-pointer
                                    transition-colors
                                    duration-150
                                    hover:bg-[#F7F5F4]
                                "
                            >
                                <input
                                    type="radio"
                                    name="remove_reason"
                                    value="{{ $value }}"
                                    class="remove-reason-radio"
                                >

                                <span class="remove-radio-circle"></span>

                                <span
                                    class="
                                        text-[14px]
                                        text-[#25211F]
                                    "
                                >
                                    {{ $label }}
                                </span>
                            </label>
                        @endforeach

                    </div>

                </div>

                <div class="mt-[27px]">

                    <label
                        for="removeAdditionalDetails"
                        class="
                            block
                            mb-[9px]
                            text-[17px]
                            font-medium
                            text-[#17120F]
                        "
                    >
                        Additional Details (Optional)
                    </label>

                    <div class="relative">
                        <textarea
                            id="removeAdditionalDetails"
                            maxlength="300"
                            rows="2"
                            placeholder=""
                            class="
                                w-full
                                h-[57px]
                                resize-none
                                rounded-[6px]
                                border
                                border-[#DDDBDA]
                                bg-white
                                px-[12px]
                                py-[9px]
                                pr-[52px]
                                text-[14px]
                                leading-[1.35]
                                text-[#2A2624]
                                outline-none
                                transition-all
                                duration-200
                                focus:border-[#B8B0AC]
                                focus:ring-[3px]
                                focus:ring-[#B8B0AC]/10
                            "
                        ></textarea>

                        <span
                            id="removeDetailsCount"
                            class="
                                absolute
                                right-[10px]
                                bottom-[6px]
                                text-[10px]
                                text-[#77716E]
                            "
                        >
                            0/300
                        </span>
                    </div>

                </div>

                <div
                    class="
                        flex
                        items-center
                        justify-end
                        gap-[10px]
                        mt-[34px]
                    "
                >

                    <button
                        type="button"
                        id="cancelRemoveProduct"
                        class="
                            h-[42px]
                            min-w-[105px]
                            px-[18px]
                            rounded-[8px]
                            border
                            border-[#D6D2D0]
                            bg-white
                            text-[14px]
                            font-semibold
                            text-[#8A2522]
                            transition-all
                            duration-200
                            hover:bg-[#FAF7F5]
                            active:scale-[0.98]
                        "
                    >
                        Cancel
                    </button>

                    <button
                        type="button"
                        id="confirmRemoveProduct"
                        class="
                            h-[42px]
                            min-w-[128px]
                            px-[20px]
                            rounded-[8px]
                            border
                            border-[#D82E2E]
                            bg-[#FFD9D9]
                            text-[15px]
                            font-semibold
                            text-[#AF0000]
                            transition-all
                            duration-200
                            hover:bg-[#FFCACA]
                            active:scale-[0.98]
                        "
                    >
                        Remove
                    </button>

                </div>

            </div>

        </div>

    </div>

    {{-- =========================================================
         INVENTORY STYLES
    ========================================================== --}}


    {{-- =========================================================
         FLASH MESSAGE
    ========================================================== --}}
    <div
        id="inventoryFlashMessage"
        class="
            inventory-flash-message
            fixed
            right-[24px]
            bottom-[24px]
            z-[250]
            hidden
            items-center
            gap-[11px]
            min-w-[300px]
            max-w-[390px]
            rounded-[12px]
            border
            border-[#DCE8D8]
            bg-white
            px-[16px]
            py-[13px]
            shadow-[0_10px_30px_rgba(42,20,15,0.14)]
            opacity-0
            translate-y-[8px]
            pointer-events-none
            transition-all
            duration-200
            ease-out
        "
        role="status"
        aria-live="polite"
    >
        <span
            class="
                flex items-center justify-center
                w-[28px] h-[28px] shrink-0
                rounded-full bg-[#E3F2DF] text-[#317A2A]
            "
        >
            <svg
                class="w-[16px] h-[16px]"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
                aria-hidden="true"
            >
                <path d="M5 12.5l4.2 4.2L19 7"/>
            </svg>
        </span>

        <div class="min-w-0">
            <p class="text-[13px] font-semibold text-[#26211F]">
                Success
            </p>
            <p
                id="inventoryFlashText"
                class="mt-[1px] text-[12px] text-[#77716E] leading-[1.4]"
            >
                Product archived successfully.
            </p>
        </div>
    </div>

    <style>


        /* =====================================================
           FADE-ONLY MODALS
        ====================================================== */

        #productDetailsModal,
        #addProductModal,
        #removeProductModal {

            transform: none !important;
            will-change: opacity;

        }


        #productDetailsModalPanel,
        #addProductModalPanel,
        #removeProductModalPanel {

            transform: none !important;
            will-change: opacity;

        }


        #productDetailsModal.modal-open,
        #addProductModal.modal-open,
        #removeProductModal.modal-open {

            display: flex;
            opacity: 1;

        }


        #productDetailsModal.modal-closing,
        #addProductModal.modal-closing,
        #removeProductModal.modal-closing {

            display: flex;
            opacity: 0;

        }


        #productDetailsModal.modal-open #productDetailsModalPanel,
        #addProductModal.modal-open #addProductModalPanel,
        #removeProductModal.modal-open #removeProductModalPanel {

            opacity: 1;
            transform: none !important;

        }


        #productDetailsModal.modal-closing #productDetailsModalPanel,
        #addProductModal.modal-closing #addProductModalPanel,
        #removeProductModal.modal-closing #removeProductModalPanel {

            opacity: 0;
            transform: none !important;

        }


        #productDetailsModalPanel,
        #addProductModalPanel,
        #removeProductModalPanel {

            transition:
                opacity
                0.2s
                ease-out !important;

        }


        /* =====================================================
           POLICY ROW CLICK
        ====================================================== */

        .policy-product-clickable {

            cursor: pointer;
            outline: none;

        }


        .policy-product-clickable:focus-visible {

            outline:
                2px solid #B8B0AC;

            outline-offset:
                -2px;

        }


        /* =====================================================
           REMOVE REASON MODAL
        ====================================================== */

        .remove-reason-radio {

            position: absolute;
            opacity: 0;
            pointer-events: none;

        }


        .remove-radio-circle {

            width: 18px;
            height: 18px;
            flex: 0 0 18px;
            border: 1px solid #D7D4D2;
            border-radius: 999px;
            background: #FFFFFF;
            position: relative;

        }


        .remove-reason-radio:checked + .remove-radio-circle {

            border-color: #A52A2A;

        }


        .remove-reason-radio:checked + .remove-radio-circle::after {

            content: "";
            position: absolute;
            inset: 4px;
            border-radius: 999px;
            background: #A52A2A;

        }


        .remove-reason-option:has(.remove-reason-radio:checked) {

            border-color: #D7C8C5;
            background: #FFFDFC;

        }



        /* =====================================================
           PRODUCT ROW CLICK UX
        ====================================================== */

        .product-clickable {

            cursor:
                pointer;

        }


        .product-clickable:focus-visible {

            outline:
                2px solid #A52A2A;

            outline-offset:
                -2px;

        }



        /* =====================================================
           PRODUCT MAIN
        ====================================================== */

        .product-main {

            display:
                flex;

            align-items:
                center;

            gap:
                16px;

            min-width:
                0;

        }


        .product-thumb {

            width:
                57px;

            height:
                57px;

            flex-shrink:
                0;

            border:
                1px solid #DDDAD8;

            border-radius:
                10px;

            background:
                #F8F8F8;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            overflow:
                hidden;

        }


        .product-name {

            margin:
                0;

            overflow:
                hidden;

            text-overflow:
                ellipsis;

            white-space:
                nowrap;

            font-size:
                15px;

            font-weight:
                600;

            color:
                #181514;

        }


        .product-sold {

            margin:
                2px 0 0;

            font-size:
                12px;

            color:
                #AAA6A4;

        }


        .product-number {

            font-size:
                14px;

            font-weight:
                500;

            color:
                #17120F;

        }



        /* =====================================================
           PRODUCT BAG
        ====================================================== */

        .product-bag {

            position:
                relative;

            width:
                38px;

            height:
                28px;

            background:
                #292929;

            border-radius:
                5px 5px 7px 7px;

            box-shadow:
                inset
                0
                -3px
                0
                rgba(
                    0,
                    0,
                    0,
                    0.15
                );

        }


        .product-bag::before {

            content:
                "";

            position:
                absolute;

            left:
                9px;

            top:
                -7px;

            width:
                20px;

            height:
                10px;

            border:
                3px solid #292929;

            border-bottom:
                none;

            border-radius:
                10px 10px 0 0;

        }


        .product-bag::after {

            content:
                "";

            position:
                absolute;

            left:
                5px;

            top:
                5px;

            width:
                28px;

            height:
                4px;

            border-radius:
                999px;

            background:
                #353535;

        }



        /* =====================================================
           CATEGORY
        ====================================================== */

        .category-badge,
        .category-default {

            display:
                inline-flex;

            align-items:
                center;

            border-radius:
                999px;

            background:
                #D8E6FF;

            padding:
                5px 12px;

            font-size:
                11px;

            font-weight:
                500;

            color:
                #07588A;

            white-space:
                nowrap;

        }




        /* =====================================================
           UNIQUE CATEGORY PILL COLORS
        ====================================================== */

        .category-pet-supplies { background: #E7F5E9; color: #2F6B3A; }
        .category-electronics-and-gadgets { background: #DDEBFF; color: #185FA3; }
        .category-womens-apparel { background: #F9DFEA; color: #A12763; }
        .category-mens-apparel { background: #E6E2F8; color: #5A4A9A; }
        .category-kids-and-baby { background: #FFE5B8; color: #9A5B00; }
        .category-home-and-garden { background: #DDF3E4; color: #27704A; }
        .category-sports-and-outdoors { background: #DDECF2; color: #23627A; }
        .category-health-and-beauty { background: #FFE0DC; color: #A63B2C; }
        .category-books-and-media { background: #E6E8F2; color: #3F4A68; }
        .category-food-and-gourmet { background: #FFF0C7; color: #8A5A00; }
        .category-automotive-motorcycle { background: #E3E3E3; color: #434343; }
        .category-furniture-and-office-equipment { background: #EBDCCF; color: #795548; }
        .category-jewelry-and-watches { background: #F8E2B8; color: #946B00; }
        .category-office-and-school-supplies { background: #E2F0F7; color: #2B617D; }


        /* =====================================================
           STATUS
        ====================================================== */

        .status-badge {

            display:
                inline-flex;

            align-items:
                center;

            border-radius:
                999px;

            padding:
                4px 12px;

            font-size:
                10px;

            font-weight:
                500;

            white-space:
                nowrap;

        }


        .status-in-stock {

            background:
                #D9EED3;

            color:
                #27721F;

        }


        .status-low-stock {

            background:
                #FFE6CF;

            color:
                #B95F0A;

        }


        .status-out-stock {

            background:
                #FFD7D7;

            color:
                #BD3131;

        }



        /* =====================================================
           WARNING
        ====================================================== */

        .stock-warning {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            width:
                20px;

            height:
                20px;

            flex-shrink:
                0;

            clip-path:
                polygon(
                    50% 0,
                    100% 100%,
                    0 100%
                );

            background:
                #E9A33C;

            color:
                white;

            font-size:
                11px;

            font-weight:
                700;

            padding-top:
                5px;

        }



        /* =====================================================
           POLICY ISSUE
        ====================================================== */

        .policy-issue {

            display:
                flex;

            align-items:
                flex-start;

            gap:
                8px;

            min-width:
                0;

        }


        .issue-title {

            margin:
                0;

            font-size:
                13px;

            line-height:
                1.2;

            color:
                #2B2725;

        }


        .issue-date {

            margin:
                5px 0 0;

            font-size:
                11px;

            color:
                #B0AAA7;

            white-space:
                nowrap;

        }





        /* =====================================================
           ARCHIVED ITEMS
        ====================================================== */

        .archived-reason-title {

            margin: 0;
            font-size: 12px;
            font-weight: 600;
            color: #7B1B1B;

        }

        .archived-reason-detail {

            margin: 3px 0 0;
            font-size: 10px;
            line-height: 1.25;
            color: #999393;

        }

        /* =====================================================
           INVENTORY TABS
        ====================================================== */

        .inventory-tab::after {

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
                0.2s
                ease;

        }


        .inventory-tab.active {

            color:
                #9E241F;

        }


        .inventory-tab.active::after {

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
                0.18s
                ease;

        }


        .pagination-button:hover {

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
                0.35;

            pointer-events:
                none;

        }



        /* =====================================================
           ADD PRODUCT MODAL
        ====================================================== */

        #addProductModal.modal-open {

            display:
                flex;

        }


        #addProductModal.modal-open
        #addProductModalPanel {

            opacity:
                1;

            transform:
                none !important;

        }



        /* =====================================================
           PRODUCT DETAILS MODAL
        ====================================================== */

        /* Prevent the page scrollbar from changing the modal's
           horizontal position when the modal opens/closes. */

        html {

            scrollbar-gutter:
                stable;

        }


        #productDetailsModal {

            transform:
                none !important;

            will-change:
                opacity;

        }


        #productDetailsModalPanel {

            transform:
                none !important;

            will-change:
                opacity;

            transition:
                opacity
                0.2s
                ease-out !important;

        }


        #productDetailsModal.modal-open {

            display:
                flex;

            opacity:
                1;

        }


        #productDetailsModal.modal-open
        #productDetailsModalPanel {

            opacity:
                1;

            transform:
                none !important;

        }


        .product-details-panel {

            /* Keep the modal scrollable without showing a scrollbar. */

            scrollbar-width:
                none;

            -ms-overflow-style:
                none;

            -webkit-overflow-scrolling:
                touch;

        }


        .product-details-panel::-webkit-scrollbar {

            width:
                0;

            height:
                0;

            display:
                none;

        }



        /* =====================================================
           PRODUCT THUMBNAILS
        ====================================================== */

        .product-thumbnail {

            width:
                36px;

            height:
                39px;

            border:
                1px solid #D4D0CE;

            border-radius:
                6px;

            background:
                white;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            overflow:
                hidden;

        }


        .mini-shirt {

            position:
                relative;

            width:
                20px;

            height:
                24px;

            background:
                #151515;

            clip-path:
                polygon(
                    35% 0,
                    65% 0,
                    76% 17%,
                    100% 28%,
                    85% 45%,
                    73% 38%,
                    73% 100%,
                    27% 100%,
                    27% 38%,
                    15% 45%,
                    0 28%,
                    24% 17%
                );

        }



        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1100px) {

            #inventory-page {

                margin-left:
                    288px;

            }


            .inventory-search-wrap {

                width:
                    175px;

            }

        }



        @media (max-width: 900px) {

            .inventory-controls {

                flex-wrap:
                    wrap;

                padding-top:
                    8px;

                padding-bottom:
                    8px;

            }

        }



        @media (max-width: 760px) {

            #inventory-page {

                margin-left:
                    0 !important;

            }


            #inventory-page > div {

                padding-left:
                    18px;

                padding-right:
                    18px;

            }


            .inventory-tab {

                margin-right:
                    18px;

                padding-left:
                    3px;

                padding-right:
                    3px;

                font-size:
                    12px;

            }


            #inventoryTableHeader,

            #allProductsTable,

            #policyIssuesTable {

                min-width:
                    900px;

            }


            .product-details-panel {

                border-radius:
                    0;

                max-height:
                    100vh;

            }

        }



        /* =====================================================
           REDUCED MOTION
        ====================================================== */

        @media (prefers-reduced-motion: reduce) {

            .product-clickable,
            .inventory-tab,
            .inventory-tab::after,
            .pagination-button {

                transition:
                    none !important;

            }

        }


        /* =====================================================
           FLASH MESSAGE
        ====================================================== */

        .inventory-flash-message.flash-visible {

            display:
                flex;

            opacity:
                1;

            transform:
                translateY(0);

        }

    </style>



    {{-- =========================================================
         INVENTORY JAVASCRIPT
    ========================================================== --}}

    <script>

        document.addEventListener(
            'DOMContentLoaded',
            function () {


                /* =================================================
                   FAST SIDEBAR / PAGE ALIGNMENT
                   Matches the seller pages with quick response:
                   - Expanded sidebar = 288px
                   - Collapsed sidebar = 80px
                   - Mobile = 0px
                   - 200ms margin transition
                ================================================== */

                const inventoryPage =
                    document.getElementById(
                        'inventory-page'
                    );

                const inventorySidebar =
                    document.getElementById(
                        'sellerSidebar'
                    );


                function syncInventoryPageOffset() {

                    if (!inventoryPage) {
                        return;
                    }


                    if (window.innerWidth <= 760) {

                        inventoryPage.style.marginLeft =
                            '0px';

                        return;
                    }


                    const sidebarCollapsed =
                        inventorySidebar &&
                        inventorySidebar.classList.contains(
                            'seller-sidebar-collapsed'
                        );


                    inventoryPage.style.marginLeft =
                        sidebarCollapsed
                            ? '80px'
                            : '288px';

                }


                /*
                 * Initial alignment.
                 */
                syncInventoryPageOffset();


                /*
                 * Recalculate on viewport changes.
                 */
                window.addEventListener(
                    'resize',
                    syncInventoryPageOffset
                );


                /*
                 * Watch the exact class used by the seller sidebar
                 * when it collapses/expands so Inventory responds
                 * immediately instead of waiting for a layout measurement.
                 */
                if (
                    inventorySidebar &&
                    typeof MutationObserver !== 'undefined'
                ) {

                    const inventorySidebarObserver =
                        new MutationObserver(
                            function () {

                                requestAnimationFrame(
                                    syncInventoryPageOffset
                                );

                            }
                        );


                    inventorySidebarObserver.observe(
                        inventorySidebar,
                        {
                            attributes: true,
                            attributeFilter: ['class']
                        }
                    );

                }


                /*
                 * Compatibility with the seller navbar toggle event.
                 */
                document.body.addEventListener(
                    'toggle-seller-sidebar',
                    function () {

                        requestAnimationFrame(
                            syncInventoryPageOffset
                        );

                    }
                );




                /* =================================================
                   FLASH MESSAGE
                ================================================== */

                const inventoryFlashMessage =
                    document.getElementById(
                        'inventoryFlashMessage'
                    );

                const inventoryFlashText =
                    document.getElementById(
                        'inventoryFlashText'
                    );

                let inventoryFlashTimer = null;

                function showInventoryFlash(message) {

                    if (!inventoryFlashMessage || !inventoryFlashText) {
                        return;
                    }

                    if (inventoryFlashTimer) {
                        window.clearTimeout(inventoryFlashTimer);
                    }

                    inventoryFlashText.textContent = message;

                    inventoryFlashMessage.classList.remove('hidden');

                    void inventoryFlashMessage.offsetWidth;

                    inventoryFlashMessage.classList.add('flash-visible');

                    inventoryFlashTimer = window.setTimeout(function () {

                        inventoryFlashMessage.classList.remove('flash-visible');

                        window.setTimeout(function () {

                            inventoryFlashMessage.classList.add('hidden');

                        }, 200);

                    }, 3000);

                }

                /* =================================================
                   INVENTORY ELEMENTS
                ================================================== */

                const tabs =
                    document.querySelectorAll(
                        '.inventory-tab'
                    );


                const allHeader =
                    document.getElementById(
                        'allProductsHeader'
                    );


                const policyHeader =
                    document.getElementById(
                        'policyIssuesHeader'
                    );


                const allTable =
                    document.getElementById(
                        'allProductsTable'
                    );


                const policyTable =
                    document.getElementById(
                        'policyIssuesTable'
                    );


                const archivedTable =
                    document.getElementById(
                        'archivedItemsTable'
                    );


                const archivedEmpty =
                    document.getElementById(
                        'archivedItemsEmpty'
                    );


                const searchInput =
                    document.getElementById(
                        'productSearch'
                    );


                const categoryFilter =
                    document.getElementById(
                        'categoryFilter'
                    );


                const statusFilter =
                    document.getElementById(
                        'statusFilter'
                    );


                const showingCount =
                    document.getElementById(
                        'showingCount'
                    );


                const noResults =
                    document.getElementById(
                        'inventoryNoResults'
                    );


                const refreshButton =
                    document.getElementById(
                        'refreshInventory'
                    );


                const refreshIcon =
                    document.getElementById(
                        'refreshIcon'
                    );



                /* =================================================
                   STATE
                ================================================== */

                let activeTab =
                    'all';



                /* =================================================
                   FILTER PRODUCTS
                ================================================== */

                function filterProducts() {

                    const rows =
                        Array.from(
                            document.querySelectorAll(
                                '#allProductsTable .inventory-row'
                            )
                        );


                    const search =
                        (
                            searchInput?.value ||
                            ''
                        )
                        .trim()
                        .toLowerCase();


                    const category =
                        categoryFilter?.value ||
                        'all';


                    const status =
                        statusFilter?.value ||
                        'all';


                    let visible =
                        0;


                    rows.forEach(
                        function (row) {

                            const name =
                                (
                                    row.dataset.name ||
                                    ''
                                )
                                .toLowerCase();


                            const rowCategory =
                                row.dataset.category ||
                                '';


                            const rowStatus =
                                row.dataset.status ||
                                '';


                            const matchesSearch =
                                !search ||
                                name.includes(
                                    search
                                );


                            const matchesCategory =
                                category === 'all' ||
                                rowCategory === category;


                            const matchesStatus =
                                status === 'all' ||
                                rowStatus === status;


                            const shouldShow =
                                activeTab === 'all' &&
                                matchesSearch &&
                                matchesCategory &&
                                matchesStatus;


                            row.classList.toggle(
                                'hidden',
                                !shouldShow
                            );


                            if (
                                shouldShow
                            ) {

                                visible++;

                            }

                        }
                    );


                    if (showingCount) {

                        showingCount.textContent =
                            visible;

                    }


                    if (noResults) {

                        noResults.classList.toggle(
                            'hidden',
                            visible > 0
                        );

                    }

                }



                /* =================================================
                   TAB VIEW
                ================================================== */

                function updateTabView() {

                    allHeader?.classList.add(
                        'hidden'
                    );

                    policyHeader?.classList.add(
                        'hidden'
                    );

                    allTable?.classList.add(
                        'hidden'
                    );

                    policyTable?.classList.add(
                        'hidden'
                    );

                    archivedTable?.classList.add(
                        'hidden'
                    );

                    archivedEmpty?.classList.add(
                        'hidden'
                    );

                    statusFilter?.classList.remove(
                        'hidden'
                    );

                    if (activeTab === 'policy') {

                        policyHeader?.classList.remove(
                            'hidden'
                        );

                        policyTable?.classList.remove(
                            'hidden'
                        );

                        statusFilter?.classList.add(
                            'hidden'
                        );

                        if (showingCount) {
                            showingCount.textContent =
                                document.querySelectorAll(
                                    '#policyIssuesTable .policy-row'
                                ).length;
                        }

                        noResults?.classList.add(
                            'hidden'
                        );

                        return;
                    }


                    if (activeTab === 'archived') {

                        archivedTable?.classList.remove(
                            'hidden'
                        );

                        const archivedRows =
                            document.querySelectorAll(
                                '#archivedItemsTable .archived-row'
                            );

                        if (archivedRows.length === 0) {

                            archivedEmpty?.classList.remove(
                                'hidden'
                            );

                        }

                        if (showingCount) {
                            showingCount.textContent =
                                archivedRows.length;
                        }

                        noResults?.classList.add(
                            'hidden'
                        );

                        return;
                    }


                    allHeader?.classList.remove(
                        'hidden'
                    );

                    allTable?.classList.remove(
                        'hidden'
                    );

                    filterProducts();

                }


                /* =================================================
                   TAB EVENTS
                ================================================== */

                tabs.forEach(
                    function (tab) {

                        tab.addEventListener(
                            'click',
                            function () {

                                tabs.forEach(
                                    function (item) {

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


                                updateTabView();

                            }
                        );

                    }
                );



                /* =================================================
                   FILTER EVENTS
                ================================================== */

                searchInput?.addEventListener(
                    'input',
                    filterProducts
                );


                categoryFilter?.addEventListener(
                    'change',
                    filterProducts
                );


                statusFilter?.addEventListener(
                    'change',
                    filterProducts
                );



                /* =================================================
                   REFRESH
                ================================================== */

                refreshButton?.addEventListener(
                    'click',
                    function () {

                        refreshIcon?.classList.add(
                            'animate-spin'
                        );


                        setTimeout(
                            function () {

                                if (searchInput) {

                                    searchInput.value =
                                        '';

                                }


                                if (categoryFilter) {

                                    categoryFilter.value =
                                        'all';

                                }


                                if (statusFilter) {

                                    statusFilter.value =
                                        'all';

                                }


                                tabs.forEach(
                                    function (tab) {

                                        tab.classList.toggle(
                                            'active',

                                            tab.dataset.tab ===
                                                'all'
                                        );

                                    }
                                );


                                activeTab =
                                    'all';


                                updateTabView();


                                refreshIcon?.classList.remove(
                                    'animate-spin'
                                );

                            },
                            450
                        );

                    }
                );



                /* =================================================
                   PRODUCT DETAILS / POLICY / REMOVE MODALS
                ================================================== */

                const productDetailsModal =
                    document.getElementById(
                        'productDetailsModal'
                    );


                const productDetailsPanel =
                    document.getElementById(
                        'productDetailsModalPanel'
                    );


                const productRows =
                    document.querySelectorAll(
                        '#allProductsTable .product-clickable, #policyIssuesTable .policy-product-clickable'
                    );


                const cancelProductDetails =
                    document.getElementById(
                        'cancelProductDetails'
                    );


                const saveProductChanges =
                    document.getElementById(
                        'saveProductChanges'
                    );


                const removeProductButton =
                    document.getElementById(
                        'removeProductButton'
                    );


                const productDescription =
                    document.getElementById(
                        'productDescription'
                    );


                const descriptionCount =
                    document.getElementById(
                        'descriptionCount'
                    );


                const productPolicyWarning =
                    document.getElementById(
                        'productPolicyWarning'
                    );

                const productDetailsCategory =
                    document.getElementById(
                        'productDetailsCategory'
                    );


                const productPolicyIssueTitle =
                    document.getElementById(
                        'productPolicyIssueTitle'
                    );


                const productPolicyIssueDate =
                    document.getElementById(
                        'productPolicyIssueDate'
                    );


                const removeProductModal =
                    document.getElementById(
                        'removeProductModal'
                    );


                const removeProductModalPanel =
                    document.getElementById(
                        'removeProductModalPanel'
                    );


                const cancelRemoveProduct =
                    document.getElementById(
                        'cancelRemoveProduct'
                    );


                const confirmRemoveProduct =
                    document.getElementById(
                        'confirmRemoveProduct'
                    );


                const removeAdditionalDetails =
                    document.getElementById(
                        'removeAdditionalDetails'
                    );


                const removeDetailsCount =
                    document.getElementById(
                        'removeDetailsCount'
                    );


                let currentProductRow = null;


                let closeDetailsTimer = null;


                let closeRemoveTimer = null;


                /* =================================================
                   FADE OPEN / CLOSE HELPERS
                ================================================== */

                function fadeOpenModal(modal, panel) {

                    if (!modal || !panel) {
                        return;
                    }

                    modal.classList.remove(
                        'hidden',
                        'modal-closing'
                    );

                    modal.classList.add(
                        'modal-open'
                    );

                    modal.setAttribute(
                        'aria-hidden',
                        'false'
                    );

                    panel.style.transform =
                        'none';

                }


                function fadeCloseModal(
                    modal,
                    panel,
                    onComplete = null
                ) {

                    if (!modal || !panel) {
                        return;
                    }

                    modal.classList.remove(
                        'modal-open'
                    );

                    modal.classList.add(
                        'modal-closing'
                    );

                    modal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    panel.style.transform =
                        'none';

                    window.setTimeout(
                        function () {

                            modal.classList.remove(
                                'modal-closing'
                            );

                            modal.classList.add(
                                'hidden'
                            );

                            if (typeof onComplete === 'function') {
                                onComplete();
                            }

                        },
                        200
                    );

                }


                /* =================================================
                   UPDATE PRODUCT DETAILS MODAL CONTENT
                ================================================== */

                function updateProductDetailsModal(row) {

                    if (!row) {
                        return;
                    }

                    const isPolicy =
                        row.dataset.policy === 'true' ||
                        row.classList.contains(
                            'policy-product-clickable'
                        );

                    if (productDetailsCategory) {

                        const rowCategoryBadge =
                            row.querySelector(
                                '.category-badge'
                            );

                        const category =
                            rowCategoryBadge?.textContent?.trim() ||
                            '—';

                        productDetailsCategory.textContent =
                            category;

                        productDetailsCategory.className =
                            `inline-flex items-center mt-[7px] rounded-full px-[13px] py-[4px] text-[11px] font-medium category-badge ${categoryBadgeClass(category)}`;

                    }

                    if (productPolicyWarning) {

                        productPolicyWarning.classList.toggle(
                            'hidden',
                            !isPolicy
                        );

                    }

                    if (isPolicy) {

                        if (productPolicyIssueTitle) {

                            productPolicyIssueTitle.textContent =
                                row.dataset.issueTitle ||
                                'Policy issue';

                        }

                        if (productPolicyIssueDate) {

                            productPolicyIssueDate.textContent =
                                row.dataset.issueDate ||
                                'Review required';

                        }

                    }

                }


                /* =================================================
                   OPEN PRODUCT DETAILS
                ================================================== */

                function openProductDetails(row) {

                    if (!productDetailsModal) {
                        return;
                    }

                    currentProductRow =
                        row ||
                        null;

                    updateProductDetailsModal(
                        currentProductRow
                    );

                    if (closeDetailsTimer) {
                        clearTimeout(closeDetailsTimer);
                        closeDetailsTimer = null;
                    }

                    fadeOpenModal(
                        productDetailsModal,
                        productDetailsPanel
                    );

                    document.body.classList.add(
                        'overflow-hidden'
                    );

                }


                /* =================================================
                   CLOSE PRODUCT DETAILS
                ================================================== */

                function closeProductDetails(
                    callback = null
                ) {

                    if (!productDetailsModal) {
                        return;
                    }

                    if (closeDetailsTimer) {
                        clearTimeout(closeDetailsTimer);
                    }

                    productDetailsModal.classList.remove(
                        'modal-open'
                    );

                    productDetailsModal.classList.add(
                        'modal-closing'
                    );

                    productDetailsModal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    if (productDetailsPanel) {
                        productDetailsPanel.style.transform =
                            'none';
                    }

                    closeDetailsTimer =
                        window.setTimeout(
                            function () {

                                productDetailsModal.classList.remove(
                                    'modal-closing'
                                );

                                productDetailsModal.classList.add(
                                    'hidden'
                                );

                                document.body.classList.remove(
                                    'overflow-hidden'
                                );

                                currentProductRow =
                                    null;

                                if (typeof callback === 'function') {
                                    callback();
                                }

                            },
                            200
                        );

                }


                /* =================================================
                   PRODUCT ROW CLICK
                ================================================== */

                productRows.forEach(
                    function (row) {

                        row.addEventListener(
                            'click',
                            function (event) {

                                if (
                                    event.target.closest(
                                        'button, input, select, textarea, a'
                                    )
                                ) {
                                    return;
                                }

                                openProductDetails(row);

                            }
                        );


                        row.addEventListener(
                            'keydown',
                            function (event) {

                                if (
                                    event.key === 'Enter' ||
                                    event.key === ' '
                                ) {

                                    event.preventDefault();

                                    openProductDetails(row);

                                }

                            }
                        );

                    }
                );


                /* =================================================
                   CLOSE PRODUCT DETAILS
                ================================================== */

                cancelProductDetails?.addEventListener(
                    'click',
                    function () {
                        closeProductDetails();
                    }
                );


                /* =================================================
                   CLICK OUTSIDE PRODUCT DETAILS
                ================================================== */

                productDetailsModal?.addEventListener(
                    'click',
                    function (event) {

                        if (
                            event.target ===
                            productDetailsModal
                        ) {

                            closeProductDetails();

                        }

                    }
                );


                /* =================================================
                   PRODUCT DETAILS ESC
                ================================================== */

                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (event.key !== 'Escape') {
                            return;
                        }

                        if (
                            removeProductModal?.classList.contains(
                                'modal-open'
                            )
                        ) {

                            closeRemoveProductModal();
                            return;

                        }

                        if (
                            productDetailsModal?.classList.contains(
                                'modal-open'
                            )
                        ) {

                            closeProductDetails();

                        }

                    }
                );


                /* =================================================
                   DESCRIPTION COUNTER
                ================================================== */

                function updateDescriptionCount() {

                    if (
                        !productDescription ||
                        !descriptionCount
                    ) {

                        return;

                    }

                    descriptionCount.textContent =
                        `${productDescription.value.length}/300`;

                }


                productDescription?.addEventListener(
                    'input',
                    updateDescriptionCount
                );


                updateDescriptionCount();


                /* =================================================
                   SAVE CHANGES
                ================================================== */

                saveProductChanges?.addEventListener(
                    'click',
                    function () {

                        const originalText =
                            saveProductChanges.textContent;

                        saveProductChanges.disabled =
                            true;

                        saveProductChanges.textContent =
                            'Saving...';

                        window.setTimeout(
                            function () {

                                saveProductChanges.disabled =
                                    false;

                                saveProductChanges.textContent =
                                    originalText;

                                closeProductDetails(
                                    function () {

                                        window.setTimeout(
                                            function () {

                                                alert(
                                                    'Product changes saved successfully.'
                                                );

                                            },
                                            60
                                        );

                                    }
                                );

                            },
                            650
                        );

                    }
                );


                /* =================================================
                   OPEN REMOVE PRODUCT REASON MODAL
                ================================================== */

                function openRemoveProductModal() {

                    if (
                        !removeProductModal ||
                        !removeProductModalPanel
                    ) {
                        return;
                    }

                    document.querySelectorAll(
                        'input[name="remove_reason"]'
                    ).forEach(
                        function (radio) {
                            radio.checked = false;
                        }
                    );

                    if (removeAdditionalDetails) {
                        removeAdditionalDetails.value = '';
                    }

                    updateRemoveDetailsCount();

                    fadeOpenModal(
                        removeProductModal,
                        removeProductModalPanel
                    );

                }


                /* =================================================
                   CLOSE REMOVE PRODUCT REASON MODAL
                ================================================== */

                function closeRemoveProductModal() {

                    if (
                        !removeProductModal ||
                        !removeProductModalPanel
                    ) {
                        return;
                    }

                    if (closeRemoveTimer) {
                        clearTimeout(closeRemoveTimer);
                    }

                    removeProductModal.classList.remove(
                        'modal-open'
                    );

                    removeProductModal.classList.add(
                        'modal-closing'
                    );

                    removeProductModal.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    removeProductModalPanel.style.transform =
                        'none';

                    closeRemoveTimer =
                        window.setTimeout(
                            function () {

                                removeProductModal.classList.remove(
                                    'modal-closing'
                                );

                                removeProductModal.classList.add(
                                    'hidden'
                                );

                            },
                            200
                        );

                }


                /* =================================================
                   REMOVE BUTTON -> REASON MODAL
                ================================================== */

                removeProductButton?.addEventListener(
                    'click',
                    function () {

                        if (!currentProductRow) {
                            return;
                        }

                        openRemoveProductModal();

                    }
                );


                /* =================================================
                   REMOVE DETAILS COUNTER
                ================================================== */

                function updateRemoveDetailsCount() {

                    if (
                        !removeAdditionalDetails ||
                        !removeDetailsCount
                    ) {
                        return;
                    }

                    removeDetailsCount.textContent =
                        `${removeAdditionalDetails.value.length}/300`;

                }


                removeAdditionalDetails?.addEventListener(
                    'input',
                    updateRemoveDetailsCount
                );

                updateRemoveDetailsCount();


                /* =================================================
                   CANCEL REMOVE REASON MODAL
                ================================================== */

                cancelRemoveProduct?.addEventListener(
                    'click',
                    closeRemoveProductModal
                );


                removeProductModal?.addEventListener(
                    'click',
                    function (event) {

                        if (
                            event.target ===
                            removeProductModal
                        ) {
                            closeRemoveProductModal();
                        }

                    }
                );


                /* =================================================
                   MOVE REMOVED PRODUCT TO ARCHIVED TAB
                ================================================== */


                /* =================================================
                   CATEGORY BADGE CLASS HELPER
                ================================================== */

                function categoryBadgeClass(category) {

                    const map = {
                        'Pet Supplies': 'category-pet-supplies',
                        'Electronics and Gadgets': 'category-electronics-and-gadgets',
                        'Electronics & Gadgets': 'category-electronics-and-gadgets',
                        "Women's Apparel": 'category-womens-apparel',
                        "Women’s Apparel": 'category-womens-apparel',
                        "Men's Apparel": 'category-mens-apparel',
                        "Men’s Apparel": 'category-mens-apparel',
                        'Kids and Baby': 'category-kids-and-baby',
                        'Home and Garden': 'category-home-and-garden',
                        'Sports and Outdoors': 'category-sports-and-outdoors',
                        'Health and Beauty': 'category-health-and-beauty',
                        'Books and Media': 'category-books-and-media',
                        'Food and Gourmet': 'category-food-and-gourmet',
                        'Automotive & Motorcycle': 'category-automotive-motorcycle',
                        'Furniture and Office Equipment': 'category-furniture-and-office-equipment',
                        'Jewelry and Watches': 'category-jewelry-and-watches',
                        'Office and School Supplies': 'category-office-and-school-supplies'
                    };

                    return map[category] || 'category-default';
                }


                function archiveCurrentProduct(reason, details) {

                    if (!currentProductRow) {
                        return;
                    }

                    const row = currentProductRow;

                    const name =
                        row.dataset.name ||
                        'Product';

                    const category =
                        row.querySelector('.category-badge')?.textContent?.trim() ||
                        '—';

                    const price =
                        row.querySelector('.product-number')?.textContent?.trim() ||
                        '—';

                    const stock =
                        row.querySelectorAll('.product-number')[1]?.textContent?.trim() ||
                        '—';

                    const archivedTable =
                        document.getElementById(
                            'archivedItemsTable'
                        );

                    const archivedEmpty =
                        document.getElementById(
                            'archivedItemsEmpty'
                        );

                    if (!archivedTable) {
                        return;
                    }

                    const archivedRow =
                        document.createElement(
                            'article'
                        );

                    archivedRow.className =
                        'archived-row grid grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1.1fr] items-center min-h-[90px] px-[20px] border-b border-[#DDD9D7]';

                    archivedRow.innerHTML = `
                        <div class="product-main">
                            <div class="product-thumb">
                                <div class="product-bag"></div>
                            </div>
                            <div class="min-w-0">
                                <h3 class="product-name">${escapeHtml(name)}</h3>
                                <p class="product-sold">${reasonLabel(reason)}</p>
                            </div>
                        </div>

                        <div>
                            <span class="category-badge ${categoryBadgeClass(category)}">${escapeHtml(category)}</span>
                        </div>

                        <div class="product-number">${escapeHtml(price)}</div>

                        <div class="product-number">${escapeHtml(stock)}</div>

                        <div class="archived-reason">
                            <p class="archived-reason-title">Archived</p>
                            <p class="archived-reason-detail">${escapeHtml(details || reasonLabel(reason))}</p>
                        </div>
                    `;

                    archivedTable.appendChild(
                        archivedRow
                    );

                    archivedTable.classList.remove(
                        'hidden'
                    );

                    archivedEmpty?.classList.add(
                        'hidden'
                    );

                    row.remove();

                }


                function reasonLabel(reason) {

                    const labels = {
                        'no-longer-selling': 'No longer selling this product',
                        'updating-listing': 'Updating or replacing the product listing',
                        'pricing-changes': 'Pricing or cost changes',
                        'supplier-changes': 'Supplier or sourcing changes',
                        'low-demand': 'Product has low customer demand',
                        'other': 'Other',
                    };

                    return labels[reason] || 'Archived product';

                }


                function escapeHtml(value) {

                    return String(value)
                        .replaceAll('&', '&amp;')
                        .replaceAll('<', '&lt;')
                        .replaceAll('>', '&gt;')
                        .replaceAll('"', '&quot;')
                        .replaceAll("'", '&#039;');

                }


                /* =================================================
                   CONFIRM REMOVE
                ================================================== */

                confirmRemoveProduct?.addEventListener(
                    'click',
                    function () {

                        const selectedReason =
                            document.querySelector(
                                'input[name="remove_reason"]:checked'
                            );

                        if (!selectedReason) {

                            alert(
                                'Please select a reason for removing the product.'
                            );

                            return;

                        }

                        const reason =
                            selectedReason.value;

                        const details =
                            removeAdditionalDetails?.value?.trim() ||
                            '';

                        confirmRemoveProduct.disabled =
                            true;

                        confirmRemoveProduct.textContent =
                            'Removing...';

                        window.setTimeout(
                            function () {

                                archiveCurrentProduct(
                                    reason,
                                    details
                                );

                                confirmRemoveProduct.disabled =
                                    false;

                                confirmRemoveProduct.textContent =
                                    'Remove';

                                closeRemoveProductModal();

                                closeProductDetails(
                                    function () {

                                        currentProductRow =
                                            null;

                                        window.setTimeout(
                                            function () {

                                                showInventoryFlash(
                                                    'Product archived successfully and moved to Archived Items.'
                                                );

                                            },
                                            60
                                        );

                                    }
                                );

                            },
                            450
                        );

                    }
                );

                /* =================================================
                   ADD PRODUCT MODAL
                ================================================== */

                const addProductModal =
                    document.getElementById(
                        'addProductModal'
                    );


                const addProductModalPanel =
                    document.getElementById(
                        'addProductModalPanel'
                    );


                const openAddProductModal =
                    document.getElementById(
                        'openAddProductModal'
                    );


                const closeAddProductModal =
                    document.getElementById(
                        'closeAddProductModal'
                    );


                const cancelAddProduct =
                    document.getElementById(
                        'cancelAddProduct'
                    );


                const addProductForm =
                    document.getElementById(
                        'addProductForm'
                    );



                /* OPEN ADD PRODUCT */

                function openAddModal() {

                    if (!addProductModal) {

                        return;

                    }


                    addProductModal.classList.remove(
                        'hidden'
                    );


                    addProductModal.classList.add(
                        'modal-open'
                    );


                    document.body.classList.add(
                        'overflow-hidden'
                    );

                }



                /* CLOSE ADD PRODUCT */

                function closeAddModal() {

                    if (!addProductModal || !addProductModalPanel) {

                        return;

                    }

                    fadeCloseModal(
                        addProductModal,
                        addProductModalPanel,
                        function () {
                            document.body.classList.remove(
                                'overflow-hidden'
                            );
                        }
                    );

                }

                openAddProductModal?.addEventListener(
                    'click',
                    openAddModal
                );


                closeAddProductModal?.addEventListener(
                    'click',
                    closeAddModal
                );


                cancelAddProduct?.addEventListener(
                    'click',
                    closeAddModal
                );


                addProductModal?.addEventListener(
                    'click',
                    function (event) {

                        if (
                            event.target ===
                            addProductModal
                        ) {

                            closeAddModal();

                        }

                    }
                );



                /* =================================================
                   ADD PRODUCT FORM
                ================================================== */

                addProductForm?.addEventListener(
                    'submit',
                    function (event) {

                        event.preventDefault();


                        const submitButton =
                            addProductForm.querySelector(
                                'button[type="submit"]'
                            );


                        if (submitButton) {

                            submitButton.disabled =
                                true;

                            submitButton.textContent =
                                'Adding...';

                        }


                        setTimeout(
                            function () {

                                if (submitButton) {

                                    submitButton.disabled =
                                        false;

                                    submitButton.textContent =
                                        'Add Product';

                                }


                                addProductForm.reset();


                                closeAddModal();


                                setTimeout(
                                    function () {

                                        alert(
                                            'Product added successfully.'
                                        );

                                    },
                                    250
                                );

                            },
                            700
                        );

                    }
                );



                /* =================================================
                   PAGINATION
                ================================================== */

                const paginationButtons =
                    document.querySelectorAll(
                        '.pagination-button[data-page]'
                    );


                const previousPage =
                    document.getElementById(
                        'previousPage'
                    );


                const nextPage =
                    document.getElementById(
                        'nextPage'
                    );


                let currentPage =
                    1;


                paginationButtons.forEach(
                    function (button) {

                        button.addEventListener(
                            'click',
                            function () {

                                currentPage =
                                    parseInt(
                                        button.dataset.page,
                                        10
                                    ) || 1;


                                paginationButtons.forEach(
                                    function (item) {

                                        item.classList.toggle(
                                            'current',

                                            parseInt(
                                                item.dataset.page,
                                                10
                                            ) ===
                                            currentPage
                                        );

                                    }
                                );

                            }
                        );

                    }
                );


                previousPage?.addEventListener(
                    'click',
                    function () {

                        if (
                            currentPage <= 1
                        ) {

                            return;

                        }


                        currentPage--;


                        paginationButtons.forEach(
                            function (item) {

                                item.classList.toggle(
                                    'current',

                                    parseInt(
                                        item.dataset.page,
                                        10
                                    ) ===
                                    currentPage
                                );

                            }
                        );

                    }
                );


                nextPage?.addEventListener(
                    'click',
                    function () {

                        if (
                            currentPage >= 3
                        ) {

                            return;

                        }


                        currentPage++;


                        paginationButtons.forEach(
                            function (item) {

                                item.classList.toggle(
                                    'current',

                                    parseInt(
                                        item.dataset.page,
                                        10
                                    ) ===
                                    currentPage
                                );

                            }
                        );

                    }
                );



                /* =================================================
                   INITIALIZE
                ================================================== */

                updateTabView();

            }
        );

    </script>

</body>

</html>