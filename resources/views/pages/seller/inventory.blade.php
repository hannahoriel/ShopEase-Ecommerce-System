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



@php
    /*
     * Seller inventory categories:
     * Only categories selected during seller registration
     * should be available in Inventory filters / Add Product.
     *
     * Preferred controller variable:
     *     $sellerCategories
     *
     * Compatible with the registration page data:
     *     $sellerData['categories']
     */
    $rawSellerCategories =
        $sellerCategories
        ?? ($sellerData['categories'] ?? []);

    if (is_string($rawSellerCategories)) {
        $trimmedSellerCategories =
            trim($rawSellerCategories);

        $decodedSellerCategories =
            json_decode(
                $trimmedSellerCategories,
                true
            );

        if (is_array($decodedSellerCategories)) {
            $rawSellerCategories =
                $decodedSellerCategories;
        } elseif ($trimmedSellerCategories !== '') {
            $rawSellerCategories =
                array_values(
                    array_filter(
                        array_map(
                            'trim',
                            explode(
                                ',',
                                $trimmedSellerCategories
                            )
                        )
                    )
                );
        } else {
            $rawSellerCategories = [];
        }
    }

    if (!is_array($rawSellerCategories)) {
        $rawSellerCategories = [];
    }

    $inventoryCategoryDefinitions = [
        [
            'label' => 'Pet Supplies',
            'slug' => 'pet-supplies',
            'aliases' => [
                'Pet Supplies',
            ],
        ],
        [
            'label' => 'Electronics and Gadgets',
            'slug' => 'electronics-and-gadgets',
            'aliases' => [
                'Electronics and Gadgets',
                'Electronics & Gadgets',
            ],
        ],
        [
            'label' => "Women's Apparel",
            'slug' => 'womens-apparel',
            'aliases' => [
                "Women's Apparel",
                'Women’s Apparel',
            ],
        ],
        [
            'label' => "Men's Apparel",
            'slug' => 'mens-apparel',
            'aliases' => [
                "Men's Apparel",
                'Men’s Apparel',
            ],
        ],
        [
            'label' => 'Kids and Baby',
            'slug' => 'kids-and-baby',
            'aliases' => [
                'Kids and Baby',
                'Kids & Baby',
            ],
        ],
        [
            'label' => 'Home and Garden',
            'slug' => 'home-and-garden',
            'aliases' => [
                'Home and Garden',
                'Home & Garden',
            ],
        ],
        [
            'label' => 'Sports and Outdoors',
            'slug' => 'sports-and-outdoors',
            'aliases' => [
                'Sports and Outdoors',
                'Sports & Outdoors',
            ],
        ],
        [
            'label' => 'Health and Beauty',
            'slug' => 'health-and-beauty',
            'aliases' => [
                'Health and Beauty',
                'Health & Beauty',
            ],
        ],
        [
            'label' => 'Books and Media',
            'slug' => 'books-and-media',
            'aliases' => [
                'Books and Media',
                'Books & Media',
            ],
        ],
        [
            'label' => 'Food and Gourmet',
            'slug' => 'food-and-gourmet',
            'aliases' => [
                'Food and Gourmet',
                'Food & Gourmet',
            ],
        ],
        [
            'label' => 'Automotive & Motorcycle',
            'slug' => 'automotive-motorcycle',
            'aliases' => [
                'Automotive & Motorcycle',
                'Automotive and Motorcycle',
            ],
        ],
        [
            'label' => 'Furniture and Office Equipment',
            'slug' => 'furniture-and-office-equipment',
            'aliases' => [
                'Furniture and Office Equipment',
                'Furniture & Office Equipment',
            ],
        ],
        [
            'label' => 'Jewelry and Watches',
            'slug' => 'jewelry-and-watches',
            'aliases' => [
                'Jewelry and Watches',
                'Jewelry & Watches',
            ],
        ],
        [
            'label' => 'Office and School Supplies',
            'slug' => 'office-and-school-supplies',
            'aliases' => [
                'Office and School Supplies',
                'Office & School Supplies',
            ],
        ],
    ];

    $normalizeSellerCategory =
        function ($category) {
            $value =
                html_entity_decode(
                    (string) $category,
                    ENT_QUOTES,
                    'UTF-8'
                );

            $value =
                str_replace(
                    ['’', '&'],
                    ["'", 'and'],
                    $value
                );

            $value =
                preg_replace(
                    '/\s+/',
                    ' ',
                    trim($value)
                );

            return strtolower($value);
        };

    $sellerInventoryCategories = [];

    /*
     * Registered seller address shown as the product's "Ships From".
     * InventoryController already passes the logged-in Seller model as $seller.
     */
    $sellerShipFromAddress =
        collect([
            $seller->house_number ?? null,
            $seller->street ?? null,
            $seller->barangay ?? null,
            $seller->municipality ?? null,
            $seller->province ?? null,
        ])
        ->filter(
            fn ($value) =>
                filled($value)
        )
        ->implode(', ');

    if ($sellerShipFromAddress === '') {
        $sellerShipFromAddress =
            'Registered seller address';
    }

    foreach ($rawSellerCategories as $selectedCategory) {
        $normalizedSelected =
            $normalizeSellerCategory(
                $selectedCategory
            );

        foreach ($inventoryCategoryDefinitions as $definition) {
            $matchesCategory = false;

            foreach ($definition['aliases'] as $alias) {
                if (
                    $normalizeSellerCategory($alias)
                    === $normalizedSelected
                ) {
                    $matchesCategory = true;
                    break;
                }
            }

            if (!$matchesCategory) {
                continue;
            }

            $alreadyAdded =
                collect(
                    $sellerInventoryCategories
                )->contains(
                    'slug',
                    $definition['slug']
                );

            if (!$alreadyAdded) {
                $sellerInventoryCategories[] = [
                    'label' => $definition['label'],
                    'slug' => $definition['slug'],
                ];
            }

            break;
        }
    }
@endphp

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
                 INVENTORY ACTIONS
            ================================================== --}}

            <div
                class="
                    inventory-page-actions
                    flex
                    items-center
                    justify-end
                    mb-[12px]
                "
            >

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

                            @forelse ($sellerInventoryCategories as $category)
                                <option value="{{ $category['slug'] }}">
                                    {{ $category['label'] }}
                                </option>
                            @empty
                                <option
                                    value=""
                                    disabled
                                >
                                    No registered categories
                                </option>
                            @endforelse
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

                            <option value="pending">
                                Pending
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

                        out of
                        <span
                            id="totalEntriesCount"
                            data-base-total="378"
                        >
                            378
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
         CREATE PRODUCT MODAL
         Seller categories + category-aware specifications
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
            p-[18px]
        "
        aria-hidden="true"
    >

        <div
            id="addProductModalPanel"
            class="
                create-product-modal-panel
                w-full
                max-w-[980px]
                rounded-[20px]
                bg-white
                shadow-[0_18px_50px_rgba(0,0,0,0.16)]
                overflow-hidden
                opacity-0
                transition-all
                duration-200
            "
            role="dialog"
            aria-modal="true"
            aria-labelledby="createProductModalTitle"
        >

            {{-- HEADER --}}
            <div class="create-product-header">

                <div>
                    <h3
                        id="createProductModalTitle"
                        class="create-product-title"
                    >
                        Create Product
                    </h3>

                    <p class="create-product-subtitle">
                        Add product information, options, and category-specific specifications.
                    </p>
                </div>

                <button
                    type="button"
                    id="closeAddProductModal"
                    class="create-product-close"
                    aria-label="Close create product modal"
                >
                    <svg
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
                class="create-product-form"
            >

                <div class="create-product-scroll">

                    {{-- =====================================================
                         PRODUCT PHOTOS
                    ====================================================== --}}
                    <section class="create-product-section">

                        <div class="create-product-section-heading">
                            <div>
                                <h4>Product Photos <span class="required-mark">*</span></h4>
                                <p>
                                    Upload multiple product photos. The first photo will be used as the cover.
                                </p>
                            </div>

                            <span
                                id="productPhotoCount"
                                class="create-product-mini-count"
                            >
                                0 photos
                            </span>
                        </div>

                        <label
                            for="productPhotosInput"
                            class="create-product-upload-zone"
                            id="productPhotosDropZone"
                        >
                            <input
                                id="productPhotosInput"
                                name="product_photos[]"
                                type="file"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                                class="hidden"
                            >

                            <span class="create-product-upload-icon">
                                <svg
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.8"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M4 16.5V19a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-2.5"/>
                                    <path d="M12 4v11"/>
                                    <path d="m8 8 4-4 4 4"/>
                                </svg>
                            </span>

                            <span>
                                <strong>Click to upload</strong> or drag and drop photos
                            </span>

                            <small>
                                JPG, PNG or WEBP · multiple photos allowed
                            </small>
                        </label>

                        <div
                            id="productPhotoPreviewGrid"
                            class="create-product-photo-grid hidden"
                        ></div>

                    </section>


                    {{-- =====================================================
                         BASIC INFORMATION
                    ====================================================== --}}
                    <section class="create-product-section">

                        <div class="create-product-section-heading">
                            <div>
                                <h4>Basic Information</h4>
                                <p>
                                    Required information buyers will see on the product listing.
                                </p>
                            </div>
                        </div>

                        <div class="create-product-grid">

                            <div class="create-field col-span-2">
                                <label for="createProductTitle">
                                    Product Title <span class="required-mark">*</span>
                                </label>

                                <input
                                    id="createProductTitle"
                                    name="title"
                                    type="text"
                                    maxlength="120"
                                    required
                                    placeholder="Enter product title"
                                >
                            </div>                            <div class="create-field col-span-2 create-pricing-field">
                                <label>
                                    Price <span class="required-mark">*</span>
                                </label>

                                <input
                                    type="hidden"
                                    id="productPricingMode"
                                    name="pricing_mode"
                                    value="fixed"
                                >

                                <div class="create-pricing-mode-row">

                                    <button
                                        type="button"
                                        class="create-pricing-mode-button is-selected"
                                        data-pricing-mode="fixed"
                                    >
                                        Fixed Price
                                    </button>

                                    <button
                                        type="button"
                                        class="create-pricing-mode-button"
                                        data-pricing-mode="varies"
                                    >
                                        Price Varies
                                    </button>

                                </div>

                                <div
                                    id="fixedPricePanel"
                                    class="create-fixed-price-panel"
                                >
                                    <div class="create-product-prefix-field">
                                        <span>₱</span>

                                        <input
                                            id="createProductPrice"
                                            name="price"
                                            type="number"
                                            min="0"
                                            step="0.01"
                                            required
                                            placeholder="0.00"
                                        >
                                    </div>

                                    <small>
                                        One price applies to every buyer option.
                                    </small>
                                </div>

                                <div
                                    id="variablePricePanel"
                                    class="create-variable-price-note hidden"
                                >
                                    <strong>Price will be based on buyer options.</strong>
                                    <span>
                                        Choose which option sets the actual product price. Other options can add an extra amount.
                                    </span>
                                </div>
                            </div>

                            <div class="create-field">
                                <label for="createProductStock">
                                    Stock <span class="required-mark">*</span>
                                </label>

                                <input
                                    id="createProductStock"
                                    name="stock"
                                    type="number"
                                    min="0"
                                    step="1"
                                    required
                                    placeholder="0"
                                >
                            </div>

                            <div class="create-field">
                                <label for="createProductSku">
                                    SKU
                                </label>

                                <input
                                    id="createProductSku"
                                    name="sku"
                                    type="text"
                                    maxlength="80"
                                    placeholder="Optional SKU"
                                >
                            </div>

                        </div>

                    </section>


                    {{-- =====================================================
                         BUYER OPTIONS
                    ====================================================== --}}
                    <section class="create-product-section">

                        <div class="create-product-section-heading">
                            <div>
                                <h4>Buyer Options</h4>
                                <p>
                                    Optional. Add variations, colors, or sizes that buyers can choose from.
                                </p>
                            </div>
                        </div>

                        <div
                            id="variablePricingSetup"
                            class="create-variable-pricing-setup hidden"
                        >
                            <div class="create-variable-pricing-copy">
                                <strong>Variable Pricing</strong>
                                <span>
                                    Select which buyer option carries the actual item price.
                                </span>
                            </div>

                            <input
                                type="hidden"
                                id="productPricingSource"
                                name="pricing_source"
                                value=""
                            >

                            <div class="create-pricing-source-buttons">
                                <button
                                    type="button"
                                    class="create-pricing-source-button"
                                    data-pricing-source="variations"
                                >
                                    Variations
                                </button>

                                <button
                                    type="button"
                                    class="create-pricing-source-button"
                                    data-pricing-source="colors"
                                >
                                    Colors
                                </button>

                                <button
                                    type="button"
                                    class="create-pricing-source-button"
                                    data-pricing-source="sizes"
                                >
                                    Sizes
                                </button>
                            </div>

                            <p id="variablePricingRule">
                                The selected group uses actual prices. Other groups use additional price (+₱).
                            </p>
                        </div>

                        <div class="create-product-three-options">

                            {{-- VARIATIONS --}}
                            <div class="create-option-block">

                                <div class="create-option-block-head">
                                    <div>
                                        <strong>Variations</strong>
                                        <span>
                                            Optional. Each variation can have its own photo and price when Price Varies is selected.
                                        </span>
                                    </div>
                                </div>

                                <div class="create-variation-entry">

                                    <input
                                        id="productVariationEntry"
                                        type="text"
                                        placeholder="e.g. Classic"
                                    >

                                    <label
                                        for="productVariationPhotoInput"
                                        id="variationPhotoPickerLabel"
                                        class="create-variation-photo-picker"
                                        title="Add optional variation photo"
                                    >
                                        <input
                                            id="productVariationPhotoInput"
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="hidden"
                                        >

                                        <span id="variationPhotoPickerText">
                                            Photo
                                        </span>
                                    </label>

                                    <button
                                        type="button"
                                        id="addProductVariationButton"
                                    >
                                        Add
                                    </button>

                                </div>

                                <div
                                    id="productVariationsContainer"
                                    class="create-variation-chip-list"
                                ></div>

                            </div>


                            {{-- COLORS --}}
                            <div class="create-option-block">

                                <div class="create-option-block-head">
                                    <div>
                                        <strong>Colors</strong>
                                        <span>Optional buyer color choices and price adjustments</span>
                                    </div>
                                </div>

                                <div class="create-chip-entry">
                                    <input
                                        id="productColorEntry"
                                        type="text"
                                        placeholder="e.g. Black"
                                    >

                                    <button
                                        type="button"
                                        id="addProductColorButton"
                                    >
                                        Add
                                    </button>
                                </div>

                                <div
                                    id="productColorsContainer"
                                    class="create-chip-list"
                                ></div>

                            </div>


                            {{-- SIZES --}}
                            <div class="create-option-block">

                                <div class="create-option-block-head">
                                    <div>
                                        <strong>Sizes</strong>
                                        <span>Optional buyer size choices and price adjustments</span>
                                    </div>
                                </div>

                                <div class="create-chip-entry">
                                    <input
                                        id="productSizeEntry"
                                        type="text"
                                        placeholder="e.g. Medium"
                                    >

                                    <button
                                        type="button"
                                        id="addProductSizeButton"
                                    >
                                        Add
                                    </button>
                                </div>

                                <div
                                    id="productSizesContainer"
                                    class="create-chip-list"
                                ></div>

                            </div>

                        </div>

                    </section>


                    {{-- =====================================================
                         PRODUCT CATEGORY
                         Only categories selected during seller registration
                    ====================================================== --}}
                    <section class="create-product-section">

                        <div class="create-product-section-heading">
                            <div>
                                <h4>
                                    Product Category
                                    <span class="required-mark">*</span>
                                </h4>

                                <p>
                                    Choose where this product belongs. Only your registered seller categories are available.
                                </p>
                            </div>
                        </div>

                        <div class="create-product-category-picker">

                            <input
                                type="hidden"
                                id="addProductCategory"
                                name="category"
                                value=""
                            >

                            <div
                                id="createProductCategoryPills"
                                class="create-product-category-pills"
                            >
                                @forelse ($sellerInventoryCategories as $category)
                                    <button
                                        type="button"
                                        class="create-category-pill"
                                        data-category-slug="{{ $category['slug'] }}"
                                        data-category-label="{{ $category['label'] }}"
                                    >
                                        {{ $category['label'] }}
                                    </button>
                                @empty
                                    <div class="create-category-empty">
                                        No registered categories available
                                    </div>
                                @endforelse
                            </div>

                            <small class="create-category-help">
                                Select one category. Product specifications will appear after selection.
                            </small>

                        </div>

                    </section>


                    {{-- =====================================================
                         PRODUCT SPECIFICATIONS
                         Hidden until category selection
                    ====================================================== --}}
                    <section
                        id="productSpecificationsSection"
                        class="create-product-section hidden"
                    >

                        <div class="create-product-section-heading">
                            <div>
                                <h4 id="categorySpecificationsTitle">
                                    Product Specifications
                                </h4>

                                <p>
                                    Optional. These fields change depending on the product category you selected.
                                </p>
                            </div>
                        </div>

                        <div
                            id="categorySpecificationsFields"
                            class="create-product-grid"
                        ></div>

                    </section>


                    {{-- =====================================================
                         PRODUCT DESCRIPTION
                    ====================================================== --}}
                    <section class="create-product-section">

                        <div class="create-product-section-heading">
                            <div>
                                <h4>Product Description <span class="required-mark">*</span></h4>
                                <p>
                                    Describe the product clearly for buyers.
                                </p>
                            </div>

                            <span
                                id="createProductDescriptionCount"
                                class="create-product-mini-count"
                            >
                                0/2000
                            </span>
                        </div>

                        <textarea
                            id="createProductDescription"
                            name="description"
                            maxlength="2000"
                            rows="6"
                            required
                            class="create-product-description"
                            placeholder="Write the full product description here..."
                        ></textarea>

                    </section>

                </div>


                {{-- FOOTER --}}
                <div class="create-product-footer">

                    <button
                        type="button"
                        id="cancelAddProduct"
                        class="create-product-cancel"
                    >
                        Cancel
                    </button>

                    <button
                        type="submit"
                        class="create-product-submit"
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
                                        id="productDetailsMainImage"
                                        src="{{ asset('images/products/graphic-tshirt.png') }}"

                                        alt="Product image"

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
                                        id="productDetailsImageFallback"
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
                                id="productDetailsThumbnails"
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
                                        id="productDetailsName"
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
                                        id="productDetailsPrice"
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
                                    id="productDetailsSold"
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
                                    id="productDetailsStock"
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


                            {{-- STATUS --}}
                            <div class="mt-[16px]">

                                <p class="text-[14px] text-[#8F8B89]">
                                    Status
                                </p>

                                <span
                                    id="productDetailsStatus"
                                    class="
                                        status-badge
                                        status-pending
                                        mt-[6px]
                                    "
                                >
                                    Pending
                                </span>

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
                                id="productDetailsUploaded"
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

                        id="productDetailBrand"

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

                        id="productDetailMaterial"

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

                        id="productDetailSizes"

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

                        id="productDetailColors"

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
                        Quantity per Pack
                    </label>


                    <input
                        type="text"

                        id="productDetailQuantity"

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

                        id="productDetailCountry"

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
                 CREATED PRODUCT BUYER OPTIONS
            ================================================== --}}

            <div
                id="productDetailsBuyerOptionsSection"
                class="
                    hidden
                    px-[62px]
                    pt-[16px]
                "
            >

                <h3
                    class="
                        text-[17px]
                        font-semibold
                        text-[#17120F]
                        mb-[10px]
                    "
                >
                    Buyer Options
                </h3>

                <div
                    id="productDetailsBuyerOptions"
                    class="product-created-details-grid"
                ></div>

            </div>


            {{-- =================================================
                 CREATED PRODUCT SPECIFICATIONS
            ================================================== --}}

            <div
                id="productDetailsSpecificationsSection"
                class="
                    hidden
                    px-[62px]
                    pt-[18px]
                "
            >

                <h3
                    class="
                        text-[17px]
                        font-semibold
                        text-[#17120F]
                        mb-[10px]
                    "
                >
                    Product Specifications
                </h3>

                <div
                    id="productDetailsSpecifications"
                    class="product-created-details-grid"
                ></div>

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

    

        /* =====================================================
           INVENTORY — MATCH CURRENT SELLER DASHBOARD
           -----------------------------------------------------
           Matches dashboard.blade:
           - 270px expanded content offset
           - 100px collapsed content offset
           - 120px top coverage
           - 20px left / 25px right content spacing
           - Poppins hierarchy
           - 16px card radius
           - compact Dashboard-sized controls/content
           - subtle dashboard-style hover behavior
           - ALL TABLE TEXT = 12px
        ====================================================== */

        /* =====================================================
           PAGE COVERAGE / SPACING
        ====================================================== */

        #inventory-page {
            margin-left: 270px;
            padding-top: 120px;
            background: #FBF8F6;
            transition:
                margin-left .30s ease,
                padding .30s ease;
        }

        #inventory-page > div {
            padding:
                0
                25px
                28px
                20px !important;
        }


        /* =====================================================
           TOP ACTION ROW
        ====================================================== */

        .inventory-page-actions {
            width: 100%;
            margin-bottom: 12px !important;
        }


        /* =====================================================
           ADD PRODUCT BUTTON
        ====================================================== */

        #openAddProductModal {
            height: 34px !important;
            padding-left: 14px !important;
            padding-right: 14px !important;
            border-radius: 8px !important;
            background: #9E241F !important;
            color: #FFFFFF !important;
            font-size: 12px !important;
            font-weight: 500 !important;
            box-shadow: 0 1px 2px rgba(0,0,0,.05) !important;
            transform: translateY(0);
            transition:
                transform .22s ease,
                background .22s ease,
                box-shadow .22s ease !important;
        }

        #openAddProductModal:hover {
            background: #861D19 !important;
            transform: translateY(-2px) !important;
            box-shadow:
                0 5px 14px rgba(42,20,15,.07),
                0 2px 5px rgba(42,20,15,.04) !important;
        }

        #openAddProductModal:active {
            transform: scale(.98) !important;
        }

        #openAddProductModal > span:first-child {
            font-size: 18px !important;
        }


        /* =====================================================
           MAIN INVENTORY CARD — DASHBOARD CARD TREATMENT
        ====================================================== */

        #inventory-page section {
            overflow: hidden;
            background: #FFFFFF !important;
            border: 1px solid #F0E9E6 !important;
            border-radius: 16px !important;
            box-shadow: 0 2px 12px rgba(42,20,15,.05) !important;
        }


        /* =====================================================
           CONTROL BAR / TABS / FILTERS
        ====================================================== */

        .inventory-controls {
            min-height: 56px !important;
            padding-left: 14px !important;
            padding-right: 12px !important;
            gap: 16px !important;
            border-bottom-color: #E8E2DF !important;
        }

        .inventory-tab {
            font-size: 13px !important;
            line-height: 1 !important;
            font-weight: 500 !important;
        }

        .inventory-tab::after {
            height: 2px !important;
        }

        #productSearch,
        #categoryFilter,
        #statusFilter {
            height: 35px !important;
            border-radius: 8px !important;
            font-size: 12px !important;
            font-weight: 400 !important;
        }

        #refreshInventory {
            transition:
                background .20s ease,
                transform .20s ease !important;
        }

        #refreshInventory:hover {
            background: #FFF9F7 !important;
            transform: scale(1.03) !important;
        }


        /* =====================================================
           TABLE HEADER — ALL 12PX
        ====================================================== */

        #inventoryTableHeader {
            min-height: 44px !important;
            margin-top: 14px !important;
            margin-left: 12px !important;
            margin-right: 12px !important;
            padding-left: 16px !important;
            padding-right: 16px !important;
            border-radius: 10px !important;
            font-size: 12px !important;
            line-height: 1.2 !important;
            font-weight: 500 !important;
        }

        #allProductsHeader,
        #policyIssuesHeader {
            min-height: 44px !important;
            font-size: 12px !important;
        }


        /* =====================================================
           TABLE ROWS — COMPACT LIKE DASHBOARD CONTENT
        ====================================================== */

        #allProductsTable .inventory-row,
        #policyIssuesTable .policy-product-clickable,
        #archivedItemsTable .archived-row {
            min-height: 66px !important;
            padding-left: 16px !important;
            padding-right: 16px !important;
            color: #292929;
            font-family: 'Poppins', sans-serif !important;
            font-size: 12px !important;
            line-height: 1.3 !important;
            transition:
                background .18s ease,
                box-shadow .18s ease !important;
        }

        #allProductsTable .inventory-row:hover,
        #policyIssuesTable .policy-product-clickable:hover,
        #archivedItemsTable .archived-row:hover {
            background: #FFF9F7 !important;
            box-shadow:
                0 3px 10px rgba(42,20,15,.035) !important;
        }


        /* =====================================================
           TABLE PRODUCT CONTENT — ALL 12PX
        ====================================================== */

        .product-main {
            gap: 12px !important;
        }

        .product-thumb {
            width: 44px !important;
            height: 44px !important;
            flex: 0 0 44px !important;
            border-radius: 8px !important;
        }

        .product-name {
            margin: 0 !important;
            color: #181514 !important;
            font-size: 12px !important;
            line-height: 1.3 !important;
            font-weight: 500 !important;
        }

        .product-sold,
        .product-number,
        .category-badge,
        .category-default,
        .status-badge,
        .issue-title,
        .issue-date,
        .archived-reason-title,
        .archived-reason-detail {
            font-size: 12px !important;
        }

        .product-sold {
            margin-top: 2px !important;
            color: #AAA6A4 !important;
            font-weight: 400 !important;
        }

        .product-number {
            color: #17120F !important;
            font-weight: 500 !important;
        }

        .category-badge,
        .category-default {
            padding: 4px 10px !important;
            font-weight: 500 !important;
            line-height: 1.2 !important;
        }

        .status-badge {
            padding: 4px 10px !important;
            font-weight: 500 !important;
            line-height: 1.2 !important;
        }

        .issue-title {
            color: #2B2725 !important;
            line-height: 1.3 !important;
            font-weight: 400 !important;
        }

        .issue-date {
            margin-top: 3px !important;
            color: #B0AAA7 !important;
            line-height: 1.25 !important;
            font-weight: 400 !important;
        }

        .archived-reason-title {
            font-weight: 500 !important;
        }

        .archived-reason-detail {
            margin-top: 3px !important;
            line-height: 1.25 !important;
            font-weight: 400 !important;
        }


        /* =====================================================
           PRODUCT THUMB ICON — RESIZED WITH COMPACT ROWS
        ====================================================== */

        .product-bag {
            width: 30px !important;
            height: 22px !important;
            border-radius: 4px 4px 6px 6px !important;
        }

        .product-bag::before {
            left: 7px !important;
            top: -6px !important;
            width: 16px !important;
            height: 8px !important;
            border-width: 2px !important;
        }

        .product-bag::after {
            left: 4px !important;
            top: 4px !important;
            width: 22px !important;
            height: 3px !important;
        }


        /* =====================================================
           TABLE FOOTER / PAGINATION — ALL 12PX
        ====================================================== */

        #inventory-page section > div:last-child,
        #inventory-page section > div:last-child * {
            font-size: 12px !important;
        }

        .pagination-button {
            width: 28px !important;
            height: 28px !important;
            border-radius: 6px !important;
            font-size: 12px !important;
            transition:
                background .18s ease,
                color .18s ease,
                transform .18s ease !important;
        }

        .pagination-button:hover {
            background: #FFF9F7 !important;
            color: #7B1B1B !important;
            transform: translateY(-1px);
        }

        .pagination-button.current {
            font-size: 12px !important;
            font-weight: 600 !important;
        }

        #itemsPerPage {
            font-size: 12px !important;
        }


        /* =====================================================
           EMPTY STATES INSIDE TABLE CARD
        ====================================================== */

        #inventoryNoResults p,
        #archivedItemsEmpty p {
            font-size: 12px !important;
        }


        /* =====================================================
           COLLAPSED SIDEBAR
        ====================================================== */

        body.seller-sidebar-collapsed #inventory-page {
            margin-left: 100px;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1279px) {
            #inventory-page {
                margin-left: 270px;
            }
        }

        @media (max-width: 760px) {
            #inventory-page,
            body.seller-sidebar-collapsed #inventory-page {
                margin-left: 0 !important;
                padding-top: 78px !important;
            }

            #inventory-page > div {
                padding:
                    16px
                    16px
                    28px !important;
            }

            .inventory-page-actions {
                justify-content: flex-end !important;
                margin-bottom: 10px !important;
            }

            .inventory-tab {
                font-size: 12px !important;
            }
        }


    

        /* =====================================================
           SMALLER PRODUCT DETAILS MODAL
        ====================================================== */

        #productDetailsModal {
            padding: 18px !important;
        }

        #productDetailsModalPanel {
            width: min(
                760px,
                calc(100vw - 36px)
            ) !important;

            max-width: 760px !important;

            height: auto !important;
            max-height: 82vh !important;

            border-radius: 22px !important;

            box-shadow:
                0 14px 34px
                rgba(0, 0, 0, .12) !important;
        }

        #productDetailsModalPanel
        > div:first-of-type {
            padding:
                16px
                24px
                2px !important;
        }

        #productDetailsModalPanel
        > div:first-of-type
        h2 {
            font-size: 18px !important;
            font-weight: 600 !important;
        }

        #productDetailsModalPanel
        > div:nth-of-type(2) {
            padding:
                10px
                24px
                14px !important;
        }

        #productDetailsModalPanel
        > div:nth-of-type(2)
        > div {
            min-height: 0 !important;
            padding:
                14px
                22px !important;
            border-radius: 16px !important;
        }

        #productDetailsModalPanel
        #productDescription {
            height: 66px !important;
            border-radius: 11px !important;
            padding:
                9px
                14px !important;
            font-size: 13px !important;
            line-height: 1.45 !important;
        }

        #productDetailsModalPanel
        input[type="text"] {
            width: 190px !important;
            height: 34px !important;
            border-radius: 8px !important;
            padding: 0 10px !important;
            font-size: 13px !important;
        }

        #productDetailsModalPanel
        label {
            font-size: 13px !important;
        }

        #productDetailsModalPanel
        #removeProductButton,
        #productDetailsModalPanel
        #saveProductChanges,
        #productDetailsModalPanel
        #cancelProductDetails {
            height: 40px !important;
            padding-left: 16px !important;
            padding-right: 16px !important;
            font-size: 13px !important;
            border-radius: 8px !important;
        }

        #productDetailsModalPanel
        #removeProductButton {
            min-width: 96px !important;
        }

        #productDetailsModalPanel
        #saveProductChanges {
            min-width: 132px !important;
        }

        #productDetailsModalPanel
        #cancelProductDetails {
            min-width: 100px !important;
        }

        #productDetailsModalPanel
        > div:last-of-type {
            gap: 10px !important;
            padding:
                16px
                24px
                18px !important;
        }

        @media (max-width: 800px) {
            #productDetailsModal {
                padding: 10px !important;
            }

            #productDetailsModalPanel {
                width:
                    calc(100vw - 20px)
                    !important;

                max-height: 88vh !important;
            }

            #productDetailsModalPanel
            input[type="text"] {
                width: 100% !important;
            }
        }



        /* =====================================================
           CREATE PRODUCT MODAL
        ====================================================== */

        #addProductModal {
            font-family: 'Poppins', sans-serif;
        }

        .create-product-modal-panel {
            height: min(88vh, 830px);
            max-height: calc(100vh - 36px);
            display: flex;
            flex-direction: column;
        }

        .create-product-header {
            flex: 0 0 auto;
            min-height: 72px;
            padding: 14px 20px 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-bottom: 1px solid #ECE7E5;
            background: #FFFFFF;
        }

        .create-product-title {
            margin: 0;
            color: #17120F;
            font-size: 18px;
            line-height: 1.2;
            font-weight: 600;
        }

        .create-product-subtitle {
            margin: 4px 0 0;
            color: #999393;
            font-size: 11px;
            line-height: 1.35;
            font-weight: 400;
        }

        .create-product-close {
            width: 32px;
            height: 32px;
            flex: 0 0 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: #77716E;
            cursor: pointer;
            transition:
                background .18s ease,
                color .18s ease,
                transform .18s ease;
        }

        .create-product-close:hover {
            background: #FFF2EE;
            color: #6A1616;
            transform: scale(1.03);
        }

        .create-product-close svg {
            width: 18px;
            height: 18px;
        }

        .create-product-form {
            min-height: 0;
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
        }

        .create-product-scroll {
            min-height: 0;
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 16px 20px 20px;
            background: #FCFAF9;
        }

        .create-product-section {
            padding: 16px;
            margin-bottom: 14px;
            border: 1px solid #EEE8E5;
            border-radius: 14px;
            background: #FFFFFF;
            box-shadow: 0 1px 4px rgba(42,20,15,.025);
        }

        .create-product-section:last-child {
            margin-bottom: 0;
        }

        .create-product-section-heading {
            margin-bottom: 13px;
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .create-product-section-heading h4 {
            margin: 0;
            color: #231E1C;
            font-size: 13px;
            line-height: 1.3;
            font-weight: 600;
        }

        .create-product-section-heading p {
            margin: 3px 0 0;
            color: #999393;
            font-size: 10px;
            line-height: 1.4;
            font-weight: 400;
        }

        .required-mark {
            color: #C92727;
        }

        .create-product-mini-count {
            flex: 0 0 auto;
            color: #9B9694;
            font-size: 10px;
            line-height: 1.2;
            font-weight: 400;
        }

        .create-product-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 14px;
        }

        .create-field {
            min-width: 0;
        }

        .create-field label {
            display: block;
            margin-bottom: 5px;
            color: #39322F;
            font-size: 11px;
            line-height: 1.2;
            font-weight: 500;
        }

        .create-field input,
        .create-field select,
        .create-field textarea,
        .create-product-description,
        .create-chip-entry input,
        .create-variation-row input {
            width: 100%;
            border: 1px solid #D9D3D0;
            border-radius: 8px;
            background: #FFFFFF;
            color: #2F2926;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 400;
            outline: none;
            transition:
                border-color .18s ease,
                box-shadow .18s ease,
                background .18s ease;
        }

        .create-field input,
        .create-field select {
            height: 36px;
            padding: 0 10px;
        }

        .create-field textarea {
            min-height: 68px;
            padding: 9px 10px;
            resize: vertical;
        }

        .create-field input:focus,
        .create-field select:focus,
        .create-field textarea:focus,
        .create-product-description:focus,
        .create-chip-entry input:focus,
        .create-variation-row input:focus {
            border-color: #A52A2A;
            box-shadow: 0 0 0 3px rgba(165,42,42,.05);
        }

        .create-field small {
            display: block;
            margin-top: 4px;
            color: #A29C99;
            font-size: 9px;
            line-height: 1.35;
        }

        .create-product-readonly {
            background: #F8F5F3 !important;
            color: #706A67 !important;
            cursor: default;
        }

        .create-product-prefix-field {
            position: relative;
        }

        .create-product-prefix-field > span {
            position: absolute;
            left: 10px;
            top: 50%;
            z-index: 2;
            transform: translateY(-50%);
            color: #6B6562;
            font-size: 11px;
            pointer-events: none;
        }

        .create-product-prefix-field input {
            padding-left: 26px !important;
        }

        /* Photos */
        .create-product-upload-zone {
            min-height: 92px;
            padding: 14px;
            border: 1.5px dashed #D9C1BC;
            border-radius: 12px;
            background: #FFF9F7;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 3px;
            color: #6D6561;
            text-align: center;
            font-size: 10px;
            cursor: pointer;
            transition:
                border-color .18s ease,
                background .18s ease;
        }

        .create-product-upload-zone:hover,
        .create-product-upload-zone.is-dragging {
            border-color: #A52A2A;
            background: #FFF4F0;
        }

        .create-product-upload-zone strong {
            color: #7B1B1B;
            font-weight: 600;
        }

        .create-product-upload-zone small {
            color: #A39B98;
            font-size: 9px;
        }

        .create-product-upload-icon {
            width: 30px;
            height: 30px;
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #F7DFDA;
            color: #8C211D;
        }

        .create-product-upload-icon svg {
            width: 16px;
            height: 16px;
        }

        .create-product-photo-grid {
            margin-top: 10px;
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 8px;
        }

        .create-product-photo {
            position: relative;
            aspect-ratio: 1 / 1;
            overflow: hidden;
            border: 1px solid #E6DEDA;
            border-radius: 9px;
            background: #F7F5F4;
        }

        .create-product-photo img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .create-product-photo-cover {
            position: absolute;
            left: 4px;
            bottom: 4px;
            padding: 2px 5px;
            border-radius: 999px;
            background: rgba(92,20,20,.88);
            color: #FFFFFF;
            font-size: 7px;
            font-weight: 500;
        }

        .create-product-photo-remove {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 999px;
            background: rgba(20,15,13,.72);
            color: #FFFFFF;
            font-size: 13px;
            line-height: 1;
            cursor: pointer;
        }

        /* Optional buyer options */
        .create-product-two-options {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-top: 12px;
        }

        .create-option-block {
            padding: 12px;
            border: 1px solid #EEE7E3;
            border-radius: 11px;
            background: #FEFCFB;
        }

        .create-option-block-head {
            margin-bottom: 9px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }

        .create-option-block-head strong {
            display: block;
            color: #332D2A;
            font-size: 11px;
            font-weight: 600;
        }

        .create-option-block-head span {
            display: block;
            margin-top: 2px;
            color: #AAA29E;
            font-size: 9px;
            font-weight: 400;
        }

        .create-product-add-small {
            min-height: 28px;
            padding: 0 9px;
            border: 1px solid #D7CAC5;
            border-radius: 7px;
            background: #FFFFFF;
            color: #7B1B1B;
            font-size: 9px;
            font-weight: 500;
            white-space: nowrap;
            cursor: pointer;
            transition:
                background .18s ease,
                border-color .18s ease;
        }

        .create-product-add-small:hover {
            background: #FFF4F0;
            border-color: #CFA9A1;
        }

        .create-variation-list {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .create-variation-row {
            display: grid;
            grid-template-columns: .65fr 1.35fr 26px;
            gap: 7px;
            align-items: center;
        }

        .create-variation-row input {
            height: 32px;
            padding: 0 9px;
        }

        .create-variation-remove {
            width: 26px;
            height: 26px;
            border: 0;
            border-radius: 999px;
            background: #FFF0EE;
            color: #9A2C27;
            font-size: 15px;
            cursor: pointer;
        }

        .create-option-empty {
            padding: 8px 0 1px;
            color: #AAA29E;
            font-size: 9px;
            text-align: center;
        }

        .create-chip-entry {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 52px;
            gap: 7px;
        }

        .create-chip-entry input {
            height: 32px;
            padding: 0 9px;
        }

        .create-chip-entry button {
            height: 32px;
            border: 0;
            border-radius: 7px;
            background: #7B1B1B;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: 500;
            cursor: pointer;
        }

        .create-chip-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }

        .create-option-chip {
            min-height: 25px;
            padding: 4px 7px 4px 9px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: 1px solid #E9D8D3;
            border-radius: 999px;
            background: #FFF5F2;
            color: #69413B;
            font-size: 9px;
        }

        .create-option-chip button {
            border: 0;
            background: transparent;
            color: #A24C43;
            font-size: 12px;
            line-height: 1;
            cursor: pointer;
        }

        /* Category specs */
        .category-specifications-block {
            margin-top: 14px;
            padding-top: 14px;
            border-top: 1px solid #EEE8E5;
        }

        .category-specifications-empty {
            padding: 13px;
            border: 1px dashed #DDD4D0;
            border-radius: 9px;
            background: #FAF8F7;
            color: #9B9491;
            font-size: 10px;
            text-align: center;
        }

        .category-specifications-title-row {
            margin-bottom: 10px;
        }

        .category-specifications-title-row strong {
            display: block;
            color: #332D2A;
            font-size: 11px;
            font-weight: 600;
        }

        .category-specifications-title-row span {
            display: block;
            margin-top: 2px;
            color: #AAA29E;
            font-size: 9px;
        }

        /* Description */
        .create-product-description {
            min-height: 116px;
            padding: 10px 11px;
            resize: vertical;
            line-height: 1.5;
        }

        /* Footer */
        .create-product-footer {
            flex: 0 0 auto;
            min-height: 64px;
            padding: 11px 20px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 9px;
            border-top: 1px solid #ECE7E5;
            background: #FFFFFF;
        }

        .create-product-cancel,
        .create-product-submit {
            min-width: 104px;
            height: 36px;
            padding: 0 14px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 500;
            cursor: pointer;
            transition:
                transform .18s ease,
                background .18s ease,
                border-color .18s ease;
        }

        .create-product-cancel {
            border: 1px solid #D9D3D0;
            background: #FFFFFF;
            color: #625D5A;
        }

        .create-product-cancel:hover {
            background: #FAF7F5;
        }

        .create-product-submit {
            border: 0;
            background: #9E241F;
            color: #FFFFFF;
        }

        .create-product-submit:hover {
            background: #861D19;
            transform: translateY(-1px);
        }

        .create-product-submit:disabled {
            opacity: .6;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 820px) {
            #addProductModal {
                padding: 10px;
            }

            .create-product-modal-panel {
                height: calc(100vh - 20px);
                max-height: calc(100vh - 20px);
            }

            .create-product-scroll {
                padding: 12px;
            }

            .create-product-grid,
            .create-product-two-options {
                grid-template-columns: 1fr;
            }

            .create-product-grid .col-span-2 {
                grid-column: span 1 / span 1;
            }

            .create-product-photo-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }



        /* =====================================================
           CREATE PRODUCT — BUYER OPTIONS / CATEGORY / SPECS V2
        ====================================================== */

        .create-product-three-options {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }

        .create-product-category-picker {
            max-width: 480px;
        }

        .create-variation-entry {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 58px 48px;
            gap: 6px;
            align-items: center;
        }

        .create-variation-entry > input[type="text"] {
            width: 100%;
            height: 32px;
            padding: 0 9px;
            border: 1px solid #D9D3D0;
            border-radius: 8px;
            background: #FFFFFF;
            color: #2F2926;
            font-family: 'Poppins', sans-serif;
            font-size: 9px;
            outline: none;
        }

        .create-variation-entry > input[type="text"]:focus {
            border-color: #A52A2A;
            box-shadow: 0 0 0 3px rgba(165,42,42,.05);
        }

        .create-variation-photo-picker {
            height: 32px;
            padding: 0 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid #D9D3D0;
            border-radius: 7px;
            background: #FFFFFF;
            color: #7B1B1B;
            font-size: 8px;
            font-weight: 500;
            text-overflow: ellipsis;
            white-space: nowrap;
            cursor: pointer;
            transition:
                border-color .18s ease,
                background .18s ease;
        }

        .create-variation-photo-picker:hover,
        .create-variation-photo-picker.has-photo {
            border-color: #CFA9A1;
            background: #FFF5F2;
        }

        #addProductVariationButton {
            height: 32px;
            border: 0;
            border-radius: 7px;
            background: #7B1B1B;
            color: #FFFFFF;
            font-size: 9px;
            font-weight: 500;
            cursor: pointer;
        }

        .create-variation-chip-list {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-top: 8px;
        }

        .create-variation-chip {
            min-height: 34px;
            padding: 4px 6px;
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr) 20px;
            gap: 7px;
            align-items: center;
            border: 1px solid #E9D8D3;
            border-radius: 9px;
            background: #FFF8F6;
            color: #69413B;
            font-size: 9px;
        }

        .create-variation-chip.no-photo {
            grid-template-columns: minmax(0, 1fr) 20px;
        }

        .create-variation-thumb {
            width: 28px;
            height: 28px;
            overflow: hidden;
            border: 1px solid #E3D9D5;
            border-radius: 6px;
            background: #FFFFFF;
        }

        .create-variation-thumb img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .create-variation-chip-name {
            overflow: hidden;
            color: #4E3732;
            font-size: 9px;
            font-weight: 500;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .create-variation-chip-remove {
            width: 20px;
            height: 20px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: #A24C43;
            font-size: 13px;
            line-height: 1;
            cursor: pointer;
        }

        #productSpecificationsSection.hidden {
            display: none !important;
        }

        .category-spec-readonly {
            background: #F8F5F3 !important;
            color: #706A67 !important;
        }

        .category-spec-full {
            grid-column: span 2 / span 2;
        }

        @media (max-width: 900px) {
            .create-product-three-options {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .create-variation-entry {
                grid-template-columns: minmax(0, 1fr) 58px;
            }

            #addProductVariationButton {
                grid-column: span 2 / span 2;
            }

            .category-spec-full {
                grid-column: span 1 / span 1;
            }
        }



        /* =====================================================
           CREATE PRODUCT CATEGORY PILLS
        ====================================================== */

        .create-product-category-picker {
            max-width: none !important;
        }

        .create-product-category-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .create-category-pill {
            min-height: 32px;
            padding: 6px 12px;
            border: 1px solid #DDD9D7;
            border-radius: 999px;
            background: #F1F1F1;
            color: #8D8987;
            font-family: 'Poppins', sans-serif;
            font-size: 10px;
            line-height: 1.2;
            font-weight: 500;
            cursor: pointer;
            transform: translateY(0);
            transition:
                background .18s ease,
                color .18s ease,
                border-color .18s ease,
                transform .18s ease,
                box-shadow .18s ease;
        }

        .create-category-pill:hover {
            background: #EAE8E7;
            color: #625D5A;
            transform: translateY(-1px);
        }

        .create-category-pill.is-selected {
            border-color: transparent;
            box-shadow: 0 2px 7px rgba(42,20,15,.06);
        }

        .create-category-pill.is-selected[data-category-slug="pet-supplies"] {
            background: #E7F5E9;
            color: #2F6B3A;
        }

        .create-category-pill.is-selected[data-category-slug="electronics-and-gadgets"] {
            background: #DDEBFF;
            color: #185FA3;
        }

        .create-category-pill.is-selected[data-category-slug="womens-apparel"] {
            background: #F9DFEA;
            color: #A12763;
        }

        .create-category-pill.is-selected[data-category-slug="mens-apparel"] {
            background: #E6E2F8;
            color: #5A4A9A;
        }

        .create-category-pill.is-selected[data-category-slug="kids-and-baby"] {
            background: #FFE5B8;
            color: #9A5B00;
        }

        .create-category-pill.is-selected[data-category-slug="home-and-garden"] {
            background: #DDF3E4;
            color: #27704A;
        }

        .create-category-pill.is-selected[data-category-slug="sports-and-outdoors"] {
            background: #DDECF2;
            color: #23627A;
        }

        .create-category-pill.is-selected[data-category-slug="health-and-beauty"] {
            background: #FFE0DC;
            color: #A63B2C;
        }

        .create-category-pill.is-selected[data-category-slug="books-and-media"] {
            background: #E6E8F2;
            color: #3F4A68;
        }

        .create-category-pill.is-selected[data-category-slug="food-and-gourmet"] {
            background: #FFF0C7;
            color: #8A5A00;
        }

        .create-category-pill.is-selected[data-category-slug="automotive-motorcycle"] {
            background: #E3E3E3;
            color: #434343;
        }

        .create-category-pill.is-selected[data-category-slug="furniture-and-office-equipment"] {
            background: #EBDCCF;
            color: #795548;
        }

        .create-category-pill.is-selected[data-category-slug="jewelry-and-watches"] {
            background: #F8E2B8;
            color: #946B00;
        }

        .create-category-pill.is-selected[data-category-slug="office-and-school-supplies"] {
            background: #E2F0F7;
            color: #2B617D;
        }

        .create-category-help {
            display: block;
            margin-top: 8px;
            color: #A29C99;
            font-size: 9px;
            line-height: 1.35;
        }

        .create-category-empty {
            width: 100%;
            padding: 11px 12px;
            border: 1px dashed #D9D3D0;
            border-radius: 9px;
            background: #F7F5F4;
            color: #9B9694;
            font-size: 10px;
            text-align: center;
        }

        /* =====================================================
           CATEGORY / SUBCATEGORY SPECIFICATION GROUPS
        ====================================================== */

        .category-spec-group-title {
            grid-column: span 2 / span 2;
            margin-top: 4px;
            padding: 8px 0 2px;
            color: #3A322F;
            font-size: 11px;
            font-weight: 600;
        }

        .category-spec-group-title small {
            display: block;
            margin-top: 2px;
            color: #A49D99;
            font-size: 9px;
            line-height: 1.35;
            font-weight: 400;
        }

        .subcategory-specifications-wrap {
            grid-column: span 2 / span 2;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px 14px;
            padding-top: 2px;
        }

        .subcategory-specifications-wrap.hidden {
            display: none !important;
        }

        @media (max-width: 640px) {
            .category-spec-group-title,
            .subcategory-specifications-wrap {
                grid-column: span 1 / span 1;
            }

            .subcategory-specifications-wrap {
                grid-template-columns: 1fr;
            }
        }


        /* =====================================================
           CREATE PRODUCT — PRICING MODE
        ====================================================== */
        .create-pricing-field {
            padding: 12px;
            border: 1px solid #EEE7E3;
            border-radius: 11px;
            background: #FEFCFB;
        }

        .create-pricing-mode-row,
        .create-pricing-source-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 7px;
        }

        .create-pricing-mode-row {
            margin-bottom: 10px;
        }

        .create-pricing-mode-button,
        .create-pricing-source-button {
            min-height: 30px;
            padding: 5px 11px;
            border: 1px solid #DCD6D3;
            border-radius: 999px;
            background: #F3F1F0;
            color: #817B78;
            font-family: 'Poppins', sans-serif;
            font-size: 9px;
            font-weight: 500;
            cursor: pointer;
            transition: all .18s ease;
        }

        .create-pricing-mode-button:hover,
        .create-pricing-source-button:hover {
            background: #ECE8E6;
            color: #5E5754;
            transform: translateY(-1px);
        }

        .create-pricing-mode-button.is-selected,
        .create-pricing-source-button.is-selected {
            border-color: #8F231F;
            background: #FFF0EC;
            color: #8F231F;
            box-shadow: 0 2px 7px rgba(88,20,17,.06);
        }

        .create-fixed-price-panel {
            max-width: 280px;
        }

        .create-fixed-price-panel small {
            display: block;
            margin-top: 4px;
            color: #A29C99;
            font-size: 9px;
        }

        .create-variable-price-note {
            padding: 9px 11px;
            border: 1px solid #F0D7D0;
            border-radius: 9px;
            background: #FFF8F5;
        }

        .create-variable-price-note strong,
        .create-variable-price-note span {
            display: block;
        }

        .create-variable-price-note strong {
            color: #6F1A17;
            font-size: 10px;
            font-weight: 600;
        }

        .create-variable-price-note span {
            margin-top: 2px;
            color: #928986;
            font-size: 9px;
            line-height: 1.4;
        }

        .create-variable-pricing-setup {
            margin-bottom: 12px;
            padding: 11px 12px;
            border: 1px solid #EADBD6;
            border-radius: 10px;
            background: #FFF9F7;
        }

        .create-variable-pricing-copy strong,
        .create-variable-pricing-copy span {
            display: block;
        }

        .create-variable-pricing-copy strong {
            color: #3B302D;
            font-size: 10px;
            font-weight: 600;
        }

        .create-variable-pricing-copy span {
            margin-top: 2px;
            color: #9A918D;
            font-size: 9px;
        }

        .create-pricing-source-buttons {
            margin-top: 9px;
        }

        #variablePricingRule {
            margin: 7px 0 0;
            color: #8F8783;
            font-size: 9px;
            line-height: 1.4;
        }

        .create-option-price-wrap {
            min-width: 95px;
        }

        .create-option-price-label {
            display: block;
            margin-bottom: 3px;
            color: #9A8D88;
            font-size: 7px;
            line-height: 1;
            font-weight: 500;
        }

        .create-option-price-box {
            position: relative;
        }

        .create-option-price-box span {
            position: absolute;
            left: 7px;
            top: 50%;
            transform: translateY(-50%);
            color: #8B7470;
            font-size: 8px;
            pointer-events: none;
        }

        .create-option-price-input {
            width: 100%;
            height: 27px;
            padding: 0 6px 0 25px;
            border: 1px solid #DDD3CF;
            border-radius: 6px;
            background: #fff;
            color: #423A37;
            font-family: 'Poppins', sans-serif;
            font-size: 8px;
            font-weight: 500;
            outline: none;
        }

        .create-option-price-input.is-primary-price {
            border-color: #D5AEA6;
            background: #FFFCFB;
        }

        .create-option-price-input:focus {
            border-color: #A52A2A;
            box-shadow: 0 0 0 2px rgba(165,42,42,.05);
        }

        .create-variation-chip.has-option-price {
            grid-template-columns: 28px minmax(0,1fr) 105px 20px !important;
        }

        .create-variation-chip.no-photo.has-option-price {
            grid-template-columns: minmax(0,1fr) 105px 20px !important;
        }

        .create-priced-option-row {
            width: 100%;
            min-height: 38px;
            padding: 5px 6px 5px 9px;
            display: grid;
            grid-template-columns: minmax(0,1fr) 105px 20px;
            gap: 7px;
            align-items: center;
            border: 1px solid #E9D8D3;
            border-radius: 9px;
            background: #FFF8F6;
        }

        .create-priced-option-row.no-option-price {
            grid-template-columns: minmax(0,1fr) 20px;
        }

        .create-option-value-name {
            overflow: hidden;
            color: #4E3732;
            font-size: 9px;
            font-weight: 500;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .create-option-remove {
            width: 20px;
            height: 20px;
            padding: 0;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: #A24C43;
            font-size: 13px;
            cursor: pointer;
        }

        .create-chip-list.variable-pricing-list {
            display: flex;
            flex-direction: column;
            flex-wrap: nowrap;
        }

        @media (max-width: 640px) {
            .create-variation-chip.has-option-price,
            .create-variation-chip.no-photo.has-option-price,
            .create-priced-option-row {
                grid-template-columns: minmax(0,1fr) 20px !important;
            }

            .create-variation-chip.has-option-price .create-option-price-wrap,
            .create-priced-option-row .create-option-price-wrap {
                grid-column: 1 / -1;
            }
        }



        /* =====================================================
           CREATE PRODUCT — GRAY FIELD GUIDANCE
        ====================================================== */

        #addProductModal input::placeholder,
        #addProductModal textarea::placeholder {
            color: #A7A3A0 !important;
            opacity: 1 !important;
        }

        #addProductModal input,
        #addProductModal textarea,
        #addProductModal select {
            caret-color: #76716E;
        }

        #addProductModal select {
            color: #77716E;
        }

        #addProductModal input:not(:placeholder-shown),
        #addProductModal textarea:not(:placeholder-shown) {
            color: #2F2926;
        }

        .created-product-cover {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        .inventory-new-product-row {
            animation: inventoryProductEnter .28s ease both;
        }

        @keyframes inventoryProductEnter {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }



        /* =====================================================
           CREATE PRODUCT — GRAY FIELD INDICATIONS
        ====================================================== */

        #addProductModal input::placeholder,
        #addProductModal textarea::placeholder,
        #addProductModal .create-option-price-input::placeholder,
        #addProductModal .create-variation-entry input::placeholder {
            color: #A9A5A2 !important;
            opacity: 1 !important;
        }

        #addProductModal input,
        #addProductModal textarea,
        #addProductModal select {
            caret-color: #77716E;
        }

        #addProductModal select:has(option:checked[value=""]) {
            color: #A9A5A2;
        }


        /* =====================================================
           CREATED PRODUCT — PENDING APPROVAL
        ====================================================== */

        .status-pending {
            background: #FFE8D6;
            color: #D96B10;
        }


        /* =====================================================
           PRODUCT DETAILS — CREATED PRODUCT DATA
        ====================================================== */

        .product-created-details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 9px 14px;
        }

        .product-created-detail-item {
            min-width: 0;
            padding: 9px 11px;
            border: 1px solid #E4DFDC;
            border-radius: 9px;
            background: #FAF9F8;
        }

        .product-created-detail-item.is-full {
            grid-column: span 2 / span 2;
        }

        .product-created-detail-label {
            display: block;
            margin-bottom: 4px;
            color: #96908D;
            font-size: 10px;
            font-weight: 400;
        }

        .product-created-detail-value {
            display: block;
            color: #302A27;
            font-size: 11px;
            line-height: 1.45;
            font-weight: 500;
            overflow-wrap: anywhere;
        }

        .product-created-option-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 8px;
        }

        .product-created-option-price {
            flex: 0 0 auto;
            color: #7B1B1B;
            font-size: 10px;
            font-weight: 600;
        }

        .product-created-option-thumb {
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            overflow: hidden;
            border: 1px solid #E2DAD7;
            border-radius: 6px;
            background: #FFFFFF;
        }

        .product-created-option-thumb img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        #productDetailsThumbnails .created-detail-thumb {
            width: 33px;
            height: 33px;
            overflow: hidden;
            border: 1px solid #DAD4D1;
            border-radius: 7px;
            background: #FFFFFF;
            cursor: pointer;
        }

        #productDetailsThumbnails .created-detail-thumb img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: cover;
        }

        #productDetailsThumbnails .created-detail-thumb.is-active {
            border-color: #7B1B1B;
            box-shadow: 0 0 0 1px rgba(123, 27, 27, .12);
        }

        @media (max-width: 700px) {
            .product-created-details-grid {
                grid-template-columns: 1fr;
            }

            .product-created-detail-item.is-full {
                grid-column: span 1 / span 1;
            }
        }



        /* =====================================================
           PRODUCT DETAILS — EDITABLE CREATED PRODUCT SPECS
        ====================================================== */

        #productDetailsSpecifications {
            align-items: start;
        }

        .product-created-spec-heading {
            grid-column: span 2 / span 2;
            padding: 5px 0 1px;
            color: #403936;
            font-size: 11px;
            line-height: 1.3;
            font-weight: 600;
        }

        .product-created-spec-heading small {
            display: block;
            margin-top: 2px;
            color: #9D9693;
            font-size: 9px;
            line-height: 1.4;
            font-weight: 400;
        }

        .product-created-spec-field {
            min-width: 0;
            padding: 9px 11px;
            border: 1px solid #E4DFDC;
            border-radius: 9px;
            background: #FAF9F8;
        }

        .product-created-spec-field.is-full {
            grid-column: span 2 / span 2;
        }

        .product-created-spec-field label {
            display: block;
            margin-bottom: 5px;
            color: #96908D;
            font-size: 10px;
            line-height: 1.2;
            font-weight: 400;
        }

        .product-created-spec-input,
        .product-created-spec-select,
        .product-created-spec-textarea {
            width: 100%;
            border: 1px solid #D8D2CF;
            border-radius: 7px;
            background: #FFFFFF;
            color: #302A27;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            line-height: 1.35;
            font-weight: 400;
            outline: none;
            transition:
                border-color .18s ease,
                box-shadow .18s ease,
                background .18s ease;
        }

        .product-created-spec-input,
        .product-created-spec-select {
            height: 34px;
            padding: 0 9px;
        }

        .product-created-spec-textarea {
            min-height: 68px;
            padding: 8px 9px;
            resize: vertical;
        }

        .product-created-spec-input::placeholder,
        .product-created-spec-textarea::placeholder {
            color: #AAA5A2;
            opacity: 1;
        }

        .product-created-spec-input:focus,
        .product-created-spec-select:focus,
        .product-created-spec-textarea:focus {
            border-color: #B8B0AC;
            box-shadow: 0 0 0 3px rgba(184,176,172,.10);
        }

        .product-created-spec-input[readonly] {
            background: #F2F0EF;
            color: #77716E;
            cursor: default;
        }

        .product-created-spec-help {
            display: block;
            margin-top: 4px;
            color: #A49E9B;
            font-size: 9px;
            line-height: 1.35;
        }

        @media (max-width: 700px) {
            .product-created-spec-heading,
            .product-created-spec-field.is-full {
                grid-column: span 1 / span 1;
            }
        }



        /* =====================================================
           ADD PRODUCT — GRAY TYPE / FIELD INDICATIONS
           Keep entered values dark, guidance / placeholders gray.
        ====================================================== */

        #addProductModal input::placeholder,
        #addProductModal textarea::placeholder,
        #addProductModal .create-option-price-input::placeholder,
        #addProductModal .create-variation-entry input::placeholder {
            color: #A6A19E !important;
            opacity: 1 !important;
            font-weight: 400 !important;
        }

        #addProductModal select {
            color: #2F2926 !important;
        }

        #addProductModal select:has(option:checked[value=""]) {
            color: #A6A19E !important;
        }

        #addProductModal .create-field small,
        #addProductModal .create-product-section-heading p,
        #addProductModal .create-category-help,
        #addProductModal .create-option-block-head span,
        #addProductModal .create-variable-pricing-copy span,
        #addProductModal #variablePricingRule {
            color: #A6A19E !important;
        }


        /* =====================================================
           PRODUCT DETAILS — PRODUCT SPECIFICATIONS ROW FORMAT
           Matches the Brand / Material / Sizes / Colors layout:
           label on the left, field on the right, no outer cards.
        ====================================================== */

        #productDetailsSpecificationsSection {
            padding-top: 16px !important;
        }

        #productDetailsSpecificationsSection > h3 {
            margin-bottom: 12px !important;
        }

        #productDetailsSpecifications.product-created-details-grid {
            display: grid !important;
            grid-template-columns: 1fr !important;
            gap: 0 !important;
            align-items: stretch !important;
        }

        #productDetailsSpecifications .product-created-spec-heading {
            grid-column: 1 / -1 !important;
            margin: 8px 0 12px !important;
            padding: 0 !important;
            color: #17120F !important;
            font-size: 13px !important;
            line-height: 1.3 !important;
            font-weight: 600 !important;
        }

        #productDetailsSpecifications .product-created-spec-heading:first-child {
            margin-top: 0 !important;
        }

        #productDetailsSpecifications .product-created-spec-heading small {
            display: block !important;
            margin-top: 3px !important;
            color: #A09A97 !important;
            font-size: 9px !important;
            line-height: 1.4 !important;
            font-weight: 400 !important;
        }

        #productDetailsSpecifications .product-created-spec-field,
        #productDetailsSpecifications .product-created-spec-field.is-full {
            grid-column: 1 / -1 !important;
            min-width: 0 !important;
            margin: 0 !important;
            padding: 0 0 12px !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            display: grid !important;
            grid-template-columns: 190px minmax(0, 1fr) !important;
            align-items: center !important;
            column-gap: 0 !important;
        }

        #productDetailsSpecifications .product-created-spec-field.is-full {
            align-items: start !important;
        }

        #productDetailsSpecifications .product-created-spec-field label {
            display: block !important;
            margin: 0 !important;
            padding-right: 16px !important;
            color: #8D8987 !important;
            font-size: 13px !important;
            line-height: 1.35 !important;
            font-weight: 400 !important;
        }

        #productDetailsSpecifications .product-created-spec-input,
        #productDetailsSpecifications .product-created-spec-select,
        #productDetailsSpecifications .product-created-spec-textarea {
            width: 215px !important;
            max-width: 100% !important;
            border: 1px solid #D6D2D0 !important;
            border-radius: 9px !important;
            background: #FFFFFF !important;
            color: #282422 !important;
            font-family: 'Poppins', sans-serif !important;
            font-size: 13px !important;
            line-height: 1.35 !important;
            font-weight: 400 !important;
            outline: none !important;
            box-shadow: none !important;
        }

        #productDetailsSpecifications .product-created-spec-input,
        #productDetailsSpecifications .product-created-spec-select {
            height: 38px !important;
            padding: 0 11px !important;
        }

        #productDetailsSpecifications .product-created-spec-textarea {
            min-height: 70px !important;
            padding: 9px 11px !important;
            resize: vertical !important;
        }

        #productDetailsSpecifications .product-created-spec-input::placeholder,
        #productDetailsSpecifications .product-created-spec-textarea::placeholder {
            color: #AAA5A2 !important;
            opacity: 1 !important;
        }

        #productDetailsSpecifications .product-created-spec-input:focus,
        #productDetailsSpecifications .product-created-spec-select:focus,
        #productDetailsSpecifications .product-created-spec-textarea:focus {
            border-color: #B8B0AC !important;
            box-shadow: 0 0 0 3px rgba(184,176,172,.10) !important;
        }

        #productDetailsSpecifications .product-created-spec-input[readonly],
        #productDetailsSpecifications .product-created-spec-readonly {
            background: #F7F5F4 !important;
            color: #77716E !important;
        }

        #productDetailsSpecifications .product-created-spec-help {
            grid-column: 2 / 3 !important;
            display: block !important;
            width: 215px !important;
            max-width: 100% !important;
            margin: 4px 0 0 !important;
            color: #A49E9B !important;
            font-size: 9px !important;
            line-height: 1.35 !important;
        }

        @media (max-width: 700px) {
            #productDetailsSpecifications .product-created-spec-field,
            #productDetailsSpecifications .product-created-spec-field.is-full {
                grid-template-columns: 1fr !important;
                row-gap: 6px !important;
                padding-bottom: 14px !important;
            }

            #productDetailsSpecifications .product-created-spec-field label {
                padding-right: 0 !important;
            }

            #productDetailsSpecifications .product-created-spec-input,
            #productDetailsSpecifications .product-created-spec-select,
            #productDetailsSpecifications .product-created-spec-textarea,
            #productDetailsSpecifications .product-created-spec-help {
                width: 100% !important;
                grid-column: 1 / -1 !important;
            }
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
                   - Expanded sidebar = 270px
                   - Collapsed sidebar = 100px
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
                            ? '100px'
                            : '270px';

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

                const addProductCategory =
                    document.getElementById(
                        'addProductCategory'
                    );

                const sellerRegisteredCategories =
                    @json(
                        array_column(
                            $sellerInventoryCategories,
                            'slug'
                        )
                    );


                const categoryChoicePills =
                    Array.from(
                        document.querySelectorAll(
                            '.create-category-pill[data-category-slug]'
                        )
                    );

                /*
                 * Category-specific specification library.
                 * All specification fields are optional and only the
                 * selected product category's fields are rendered.
                 */
                const commonProductSpecificationFields = [
                    {
                        label: 'Brand',
                        key: 'brand',
                        placeholder: 'Brand name'
                    },
                    {
                        label: 'Material',
                        key: 'material',
                        placeholder: 'Main material'
                    },
                    {
                        label: 'Quantity per Pack',
                        key: 'quantity_per_pack',
                        placeholder: 'e.g. 1 piece, 12 pcs'
                    },
                    {
                        label: 'Features',
                        key: 'features',
                        placeholder: 'Key features',
                        type: 'textarea'
                    },
                    {
                        label: 'Condition',
                        key: 'condition',
                        type: 'select',
                        options: [
                            'New',
                            'Like New',
                            'Used - Good',
                            'Used - Fair'
                        ]
                    },
                    {
                        label: 'Country of Origin',
                        key: 'country_of_origin',
                        placeholder: 'e.g. Philippines'
                    },
                    {
                        label: 'Ships From',
                        key: 'ships_from',
                        type: 'readonly',
                        value: @json($sellerShipFromAddress)
                    }
                ];


                const productSpecificationLibrary = {
                    "pet-supplies": {
                                        "label": "Pet Supplies",
                                        "generalFields": [
                                                            {
                                                                                "label": "Pet Type",
                                                                                "key": "pet_type",
                                                                                "placeholder": "e.g. Dog, Cat, Fish, Bird"
                                                            },
                                                            {
                                                                                "label": "Life Stage",
                                                                                "key": "life_stage",
                                                                                "placeholder": "e.g. Puppy, Adult, Senior"
                                                            },
                                                            {
                                                                                "label": "Pet Size",
                                                                                "key": "pet_size",
                                                                                "placeholder": "e.g. Small, Medium, Large"
                                                            },
                                                            {
                                                                                "label": "Net Weight / Volume",
                                                                                "key": "net_weight_volume",
                                                                                "placeholder": "e.g. 1 kg, 250 ml"
                                                            },
                                                            {
                                                                                "label": "Recommended Use",
                                                                                "key": "recommended_use",
                                                                                "placeholder": "General intended use"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Dog Food & Treats": [
                                                                                {
                                                                                                    "label": "Food Form",
                                                                                                    "key": "dog_food_form",
                                                                                                    "placeholder": "e.g. Dry, Wet, Treat"
                                                                                },
                                                                                {
                                                                                                    "label": "Flavor / Protein Source",
                                                                                                    "key": "dog_flavor",
                                                                                                    "placeholder": "e.g. Chicken, Beef"
                                                                                },
                                                                                {
                                                                                                    "label": "Breed Size",
                                                                                                    "key": "dog_breed_size",
                                                                                                    "placeholder": "e.g. Small Breed"
                                                                                },
                                                                                {
                                                                                                    "label": "Dietary Needs",
                                                                                                    "key": "dog_dietary_needs",
                                                                                                    "placeholder": "e.g. Grain-Free"
                                                                                },
                                                                                {
                                                                                                    "label": "Crude Protein",
                                                                                                    "key": "dog_crude_protein",
                                                                                                    "placeholder": "e.g. 24%"
                                                                                }
                                                            ],
                                                            "Cat Litter & Accessories": [
                                                                                {
                                                                                                    "label": "Litter Type",
                                                                                                    "key": "cat_litter_type",
                                                                                                    "placeholder": "e.g. Bentonite, Tofu"
                                                                                },
                                                                                {
                                                                                                    "label": "Clumping",
                                                                                                    "key": "cat_litter_clumping",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Clumping",
                                                                                                                        "Non-Clumping"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Scent",
                                                                                                    "key": "cat_litter_scent",
                                                                                                    "placeholder": "e.g. Unscented, Lavender"
                                                                                },
                                                                                {
                                                                                                    "label": "Dust Level",
                                                                                                    "key": "cat_litter_dust",
                                                                                                    "placeholder": "e.g. Low Dust"
                                                                                },
                                                                                {
                                                                                                    "label": "Absorption / Odor Control",
                                                                                                    "key": "cat_litter_absorption",
                                                                                                    "placeholder": "Performance details"
                                                                                }
                                                            ],
                                                            "Aquariums & Fish Supplies": [
                                                                                {
                                                                                                    "label": "Water Type",
                                                                                                    "key": "aquarium_water_type",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Freshwater",
                                                                                                                        "Saltwater",
                                                                                                                        "Both"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Tank Capacity",
                                                                                                    "key": "aquarium_capacity",
                                                                                                    "placeholder": "e.g. 20 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Fish / Species Type",
                                                                                                    "key": "aquarium_species",
                                                                                                    "placeholder": "e.g. Tropical Fish"
                                                                                },
                                                                                {
                                                                                                    "label": "Equipment Type",
                                                                                                    "key": "aquarium_equipment_type",
                                                                                                    "placeholder": "e.g. Filter, Pump, Light"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimensions",
                                                                                                    "key": "aquarium_dimensions",
                                                                                                    "placeholder": "L × W × H"
                                                                                }
                                                            ],
                                                            "Bird Feeders & Food": [
                                                                                {
                                                                                                    "label": "Bird Type",
                                                                                                    "key": "bird_type",
                                                                                                    "placeholder": "e.g. Parrot, Finch"
                                                                                },
                                                                                {
                                                                                                    "label": "Feed Type",
                                                                                                    "key": "bird_feed_type",
                                                                                                    "placeholder": "e.g. Seeds, Pellets"
                                                                                },
                                                                                {
                                                                                                    "label": "Feeder Capacity",
                                                                                                    "key": "bird_feeder_capacity",
                                                                                                    "placeholder": "e.g. 500 g"
                                                                                },
                                                                                {
                                                                                                    "label": "Mounting Type",
                                                                                                    "key": "bird_mounting_type",
                                                                                                    "placeholder": "e.g. Hanging"
                                                                                },
                                                                                {
                                                                                                    "label": "Weather Resistance",
                                                                                                    "key": "bird_weather_resistance",
                                                                                                    "placeholder": "e.g. Outdoor-safe"
                                                                                }
                                                            ],
                                                            "Pet Grooming Products": [
                                                                                {
                                                                                                    "label": "Grooming Type",
                                                                                                    "key": "pet_grooming_type",
                                                                                                    "placeholder": "e.g. Shampoo, Clipper, Brush"
                                                                                },
                                                                                {
                                                                                                    "label": "Coat / Hair Type",
                                                                                                    "key": "pet_coat_type",
                                                                                                    "placeholder": "e.g. Long Coat"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "pet_grooming_power",
                                                                                                    "placeholder": "e.g. Rechargeable"
                                                                                },
                                                                                {
                                                                                                    "label": "Blade / Brush Material",
                                                                                                    "key": "pet_grooming_material",
                                                                                                    "placeholder": "e.g. Stainless Steel"
                                                                                },
                                                                                {
                                                                                                    "label": "Waterproof",
                                                                                                    "key": "pet_grooming_waterproof",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Pet Health & Wellness": [
                                                                                {
                                                                                                    "label": "Wellness Type",
                                                                                                    "key": "pet_wellness_type",
                                                                                                    "placeholder": "e.g. Supplement, Dental"
                                                                                },
                                                                                {
                                                                                                    "label": "Health Concern",
                                                                                                    "key": "pet_health_concern",
                                                                                                    "placeholder": "e.g. Joint Support"
                                                                                },
                                                                                {
                                                                                                    "label": "Form",
                                                                                                    "key": "pet_health_form",
                                                                                                    "placeholder": "e.g. Tablet, Liquid"
                                                                                },
                                                                                {
                                                                                                    "label": "Active Ingredients",
                                                                                                    "key": "pet_active_ingredients",
                                                                                                    "placeholder": "Key active ingredients"
                                                                                },
                                                                                {
                                                                                                    "label": "Dosage / Usage",
                                                                                                    "key": "pet_dosage",
                                                                                                    "placeholder": "Suggested usage"
                                                                                }
                                                            ]
                                        }
                    },
                    "electronics-and-gadgets": {
                                        "label": "Electronics and Gadgets",
                                        "generalFields": [
                                                            {
                                                                                "label": "Model",
                                                                                "key": "electronics_model",
                                                                                "placeholder": "Product model"
                                                            },
                                                            {
                                                                                "label": "Compatibility",
                                                                                "key": "electronics_compatibility",
                                                                                "placeholder": "Compatible devices or systems"
                                                            },
                                                            {
                                                                                "label": "Connection Type",
                                                                                "key": "connection_type",
                                                                                "placeholder": "e.g. Bluetooth, USB-C, Wi-Fi"
                                                            },
                                                            {
                                                                                "label": "Power / Battery",
                                                                                "key": "power_battery",
                                                                                "placeholder": "Battery or power details"
                                                            },
                                                            {
                                                                                "label": "Warranty Duration",
                                                                                "key": "warranty_duration",
                                                                                "placeholder": "e.g. 12 months"
                                                            },
                                                            {
                                                                                "label": "Warranty Type",
                                                                                "key": "warranty_type",
                                                                                "placeholder": "e.g. Seller, Manufacturer"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Mobile Phones & Accessories": [
                                                                                {
                                                                                                    "label": "Device / Accessory Type",
                                                                                                    "key": "mobile_type",
                                                                                                    "placeholder": "e.g. Smartphone, Case, Charger"
                                                                                },
                                                                                {
                                                                                                    "label": "Operating System",
                                                                                                    "key": "mobile_os",
                                                                                                    "placeholder": "e.g. Android, iOS"
                                                                                },
                                                                                {
                                                                                                    "label": "Storage",
                                                                                                    "key": "mobile_storage",
                                                                                                    "placeholder": "e.g. 256 GB"
                                                                                },
                                                                                {
                                                                                                    "label": "RAM",
                                                                                                    "key": "mobile_ram",
                                                                                                    "placeholder": "e.g. 8 GB"
                                                                                },
                                                                                {
                                                                                                    "label": "Screen Size",
                                                                                                    "key": "mobile_screen_size",
                                                                                                    "placeholder": "e.g. 6.7 inches"
                                                                                },
                                                                                {
                                                                                                    "label": "Connector Type",
                                                                                                    "key": "mobile_connector",
                                                                                                    "placeholder": "e.g. USB-C"
                                                                                }
                                                            ],
                                                            "Laptops, Desktops & Monitors": [
                                                                                {
                                                                                                    "label": "Processor",
                                                                                                    "key": "computer_processor",
                                                                                                    "placeholder": "e.g. Intel Core i5"
                                                                                },
                                                                                {
                                                                                                    "label": "RAM",
                                                                                                    "key": "computer_ram",
                                                                                                    "placeholder": "e.g. 16 GB"
                                                                                },
                                                                                {
                                                                                                    "label": "Storage",
                                                                                                    "key": "computer_storage",
                                                                                                    "placeholder": "e.g. 512 GB SSD"
                                                                                },
                                                                                {
                                                                                                    "label": "Screen Size",
                                                                                                    "key": "computer_screen_size",
                                                                                                    "placeholder": "e.g. 15.6 inches"
                                                                                },
                                                                                {
                                                                                                    "label": "Resolution",
                                                                                                    "key": "computer_resolution",
                                                                                                    "placeholder": "e.g. 1920×1080"
                                                                                },
                                                                                {
                                                                                                    "label": "Graphics",
                                                                                                    "key": "computer_graphics",
                                                                                                    "placeholder": "GPU details"
                                                                                }
                                                            ],
                                                            "Audio & Video Equipment": [
                                                                                {
                                                                                                    "label": "Device / Headset Type",
                                                                                                    "key": "audio_device_type",
                                                                                                    "placeholder": "e.g. Earphone, Headphone, Speaker"
                                                                                },
                                                                                {
                                                                                                    "label": "Audio Compatibility",
                                                                                                    "key": "audio_compatibility",
                                                                                                    "placeholder": "e.g. Android, iOS, PC"
                                                                                },
                                                                                {
                                                                                                    "label": "Headphone Connection Type",
                                                                                                    "key": "headphone_connection_type",
                                                                                                    "placeholder": "e.g. Wired, Wireless"
                                                                                },
                                                                                {
                                                                                                    "label": "Gaming Focused",
                                                                                                    "key": "gaming_focused",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Microphone",
                                                                                                    "key": "audio_microphone",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Built-in",
                                                                                                                        "Detachable",
                                                                                                                        "None"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Frequency Response",
                                                                                                    "key": "frequency_response",
                                                                                                    "placeholder": "e.g. 20Hz–20kHz"
                                                                                }
                                                            ],
                                                            "Smart Home Devices": [
                                                                                {
                                                                                                    "label": "Device Type",
                                                                                                    "key": "smart_home_type",
                                                                                                    "placeholder": "e.g. Camera, Bulb, Plug"
                                                                                },
                                                                                {
                                                                                                    "label": "Smart Ecosystem",
                                                                                                    "key": "smart_ecosystem",
                                                                                                    "placeholder": "e.g. Google Home, Alexa"
                                                                                },
                                                                                {
                                                                                                    "label": "Wireless Protocol",
                                                                                                    "key": "smart_protocol",
                                                                                                    "placeholder": "e.g. Wi-Fi, Zigbee"
                                                                                },
                                                                                {
                                                                                                    "label": "Voice Assistant Support",
                                                                                                    "key": "voice_assistant",
                                                                                                    "placeholder": "e.g. Alexa"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "smart_power_source",
                                                                                                    "placeholder": "e.g. USB, AC"
                                                                                }
                                                            ],
                                                            "Cameras & Photography": [
                                                                                {
                                                                                                    "label": "Camera Type",
                                                                                                    "key": "camera_type",
                                                                                                    "placeholder": "e.g. Mirrorless, Action Camera"
                                                                                },
                                                                                {
                                                                                                    "label": "Megapixels",
                                                                                                    "key": "camera_megapixels",
                                                                                                    "placeholder": "e.g. 24 MP"
                                                                                },
                                                                                {
                                                                                                    "label": "Sensor Size",
                                                                                                    "key": "camera_sensor",
                                                                                                    "placeholder": "e.g. APS-C"
                                                                                },
                                                                                {
                                                                                                    "label": "Lens Mount",
                                                                                                    "key": "camera_lens_mount",
                                                                                                    "placeholder": "e.g. E-mount"
                                                                                },
                                                                                {
                                                                                                    "label": "Video Resolution",
                                                                                                    "key": "camera_video_resolution",
                                                                                                    "placeholder": "e.g. 4K"
                                                                                },
                                                                                {
                                                                                                    "label": "Optical Zoom",
                                                                                                    "key": "camera_optical_zoom",
                                                                                                    "placeholder": "e.g. 5×"
                                                                                }
                                                            ],
                                                            "Wearable Technology": [
                                                                                {
                                                                                                    "label": "Wearable Type",
                                                                                                    "key": "wearable_type",
                                                                                                    "placeholder": "e.g. Smartwatch, Fitness Band"
                                                                                },
                                                                                {
                                                                                                    "label": "Display Type",
                                                                                                    "key": "wearable_display",
                                                                                                    "placeholder": "e.g. AMOLED"
                                                                                },
                                                                                {
                                                                                                    "label": "Battery Life",
                                                                                                    "key": "wearable_battery_life",
                                                                                                    "placeholder": "e.g. 7 days"
                                                                                },
                                                                                {
                                                                                                    "label": "Sensors",
                                                                                                    "key": "wearable_sensors",
                                                                                                    "placeholder": "e.g. Heart Rate, SpO2"
                                                                                },
                                                                                {
                                                                                                    "label": "Water Resistance",
                                                                                                    "key": "wearable_water_resistance",
                                                                                                    "placeholder": "e.g. 5 ATM"
                                                                                },
                                                                                {
                                                                                                    "label": "Supported OS",
                                                                                                    "key": "wearable_os",
                                                                                                    "placeholder": "e.g. Android / iOS"
                                                                                }
                                                            ]
                                        }
                    },
                    "womens-apparel": {
                                        "label": "Women's Apparel",
                                        "generalFields": [
                                                            {
                                                                                "label": "Fabric Type",
                                                                                "key": "women_fabric",
                                                                                "placeholder": "e.g. Cotton, Linen"
                                                            },
                                                            {
                                                                                "label": "Fit",
                                                                                "key": "women_fit",
                                                                                "placeholder": "e.g. Regular, Slim, Oversized"
                                                            },
                                                            {
                                                                                "label": "Pattern",
                                                                                "key": "women_pattern",
                                                                                "placeholder": "e.g. Solid, Floral"
                                                            },
                                                            {
                                                                                "label": "Occasion",
                                                                                "key": "women_occasion",
                                                                                "placeholder": "e.g. Casual, Formal"
                                                            },
                                                            {
                                                                                "label": "Care Instructions",
                                                                                "key": "women_care",
                                                                                "placeholder": "e.g. Machine wash cold"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Dresses & Skirts": [
                                                                                {
                                                                                                    "label": "Dress / Skirt Type",
                                                                                                    "key": "women_dress_type",
                                                                                                    "placeholder": "e.g. Maxi Dress, A-Line Skirt"
                                                                                },
                                                                                {
                                                                                                    "label": "Length",
                                                                                                    "key": "women_dress_length",
                                                                                                    "placeholder": "e.g. Mini, Midi, Maxi"
                                                                                },
                                                                                {
                                                                                                    "label": "Waist Rise",
                                                                                                    "key": "women_waist_rise",
                                                                                                    "placeholder": "e.g. High Rise"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_dress_closure",
                                                                                                    "placeholder": "e.g. Zipper"
                                                                                },
                                                                                {
                                                                                                    "label": "Lining",
                                                                                                    "key": "women_dress_lining",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Lined",
                                                                                                                        "Unlined"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Tops & Blouses": [
                                                                                {
                                                                                                    "label": "Top Type",
                                                                                                    "key": "women_top_type",
                                                                                                    "placeholder": "e.g. Blouse, T-Shirt"
                                                                                },
                                                                                {
                                                                                                    "label": "Sleeve Length",
                                                                                                    "key": "women_top_sleeve",
                                                                                                    "placeholder": "e.g. Short Sleeve"
                                                                                },
                                                                                {
                                                                                                    "label": "Neckline",
                                                                                                    "key": "women_top_neckline",
                                                                                                    "placeholder": "e.g. V-Neck"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_top_closure",
                                                                                                    "placeholder": "e.g. Button"
                                                                                },
                                                                                {
                                                                                                    "label": "Top Length",
                                                                                                    "key": "women_top_length",
                                                                                                    "placeholder": "e.g. Cropped, Regular"
                                                                                }
                                                            ],
                                                            "Activewear & Yoga Pants": [
                                                                                {
                                                                                                    "label": "Activity",
                                                                                                    "key": "women_active_activity",
                                                                                                    "placeholder": "e.g. Yoga, Running"
                                                                                },
                                                                                {
                                                                                                    "label": "Support / Compression",
                                                                                                    "key": "women_active_support",
                                                                                                    "placeholder": "e.g. Medium Compression"
                                                                                },
                                                                                {
                                                                                                    "label": "Stretch Level",
                                                                                                    "key": "women_active_stretch",
                                                                                                    "placeholder": "e.g. 4-Way Stretch"
                                                                                },
                                                                                {
                                                                                                    "label": "Moisture Wicking",
                                                                                                    "key": "women_active_wicking",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Waist Type",
                                                                                                    "key": "women_active_waist",
                                                                                                    "placeholder": "e.g. High Waist"
                                                                                }
                                                            ],
                                                            "Lingerie & Sleepwear": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "women_lingerie_type",
                                                                                                    "placeholder": "e.g. Bra, Pajama Set"
                                                                                },
                                                                                {
                                                                                                    "label": "Support Level",
                                                                                                    "key": "women_lingerie_support",
                                                                                                    "placeholder": "e.g. Light, Medium"
                                                                                },
                                                                                {
                                                                                                    "label": "Padding",
                                                                                                    "key": "women_lingerie_padding",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Padded",
                                                                                                                        "Unpadded"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_lingerie_closure",
                                                                                                    "placeholder": "e.g. Hook & Eye"
                                                                                },
                                                                                {
                                                                                                    "label": "Cup / Coverage",
                                                                                                    "key": "women_lingerie_coverage",
                                                                                                    "placeholder": "e.g. Full Coverage"
                                                                                }
                                                            ],
                                                            "Jackets & Coats": [
                                                                                {
                                                                                                    "label": "Outerwear Type",
                                                                                                    "key": "women_jacket_type",
                                                                                                    "placeholder": "e.g. Trench Coat"
                                                                                },
                                                                                {
                                                                                                    "label": "Insulation",
                                                                                                    "key": "women_jacket_insulation",
                                                                                                    "placeholder": "e.g. Lightweight"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_jacket_closure",
                                                                                                    "placeholder": "e.g. Zipper"
                                                                                },
                                                                                {
                                                                                                    "label": "Hood",
                                                                                                    "key": "women_jacket_hood",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "With Hood",
                                                                                                                        "No Hood"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Weather Resistance",
                                                                                                    "key": "women_jacket_weather",
                                                                                                    "placeholder": "e.g. Water Resistant"
                                                                                }
                                                            ],
                                                            "Shoes & Accessories": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "women_shoe_accessory_type",
                                                                                                    "placeholder": "e.g. Sneakers, Bag, Belt"
                                                                                },
                                                                                {
                                                                                                    "label": "Shoe Size System",
                                                                                                    "key": "women_shoe_size_system",
                                                                                                    "placeholder": "e.g. US, EU"
                                                                                },
                                                                                {
                                                                                                    "label": "Heel Height",
                                                                                                    "key": "women_heel_height",
                                                                                                    "placeholder": "e.g. 5 cm"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "women_shoe_closure",
                                                                                                    "placeholder": "e.g. Lace-Up"
                                                                                },
                                                                                {
                                                                                                    "label": "Sole Material",
                                                                                                    "key": "women_sole_material",
                                                                                                    "placeholder": "e.g. Rubber"
                                                                                }
                                                            ]
                                        }
                    },
                    "mens-apparel": {
                                        "label": "Men's Apparel",
                                        "generalFields": [
                                                            {
                                                                                "label": "Fabric Type",
                                                                                "key": "men_fabric",
                                                                                "placeholder": "e.g. Cotton, Denim"
                                                            },
                                                            {
                                                                                "label": "Fit",
                                                                                "key": "men_fit",
                                                                                "placeholder": "e.g. Regular, Slim, Relaxed"
                                                            },
                                                            {
                                                                                "label": "Pattern",
                                                                                "key": "men_pattern",
                                                                                "placeholder": "e.g. Solid, Plaid"
                                                            },
                                                            {
                                                                                "label": "Occasion / Use",
                                                                                "key": "men_occasion",
                                                                                "placeholder": "e.g. Office, Casual"
                                                            },
                                                            {
                                                                                "label": "Care Instructions",
                                                                                "key": "men_care",
                                                                                "placeholder": "e.g. Machine wash cold"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Suits & Blazers": [
                                                                                {
                                                                                                    "label": "Suit / Blazer Type",
                                                                                                    "key": "men_suit_type",
                                                                                                    "placeholder": "e.g. Two-Piece Suit"
                                                                                },
                                                                                {
                                                                                                    "label": "Pieces Included",
                                                                                                    "key": "men_suit_pieces",
                                                                                                    "placeholder": "e.g. Jacket + Pants"
                                                                                },
                                                                                {
                                                                                                    "label": "Lapel Style",
                                                                                                    "key": "men_lapel",
                                                                                                    "placeholder": "e.g. Notch Lapel"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "men_suit_closure",
                                                                                                    "placeholder": "e.g. Two Button"
                                                                                },
                                                                                {
                                                                                                    "label": "Vents",
                                                                                                    "key": "men_suit_vents",
                                                                                                    "placeholder": "e.g. Double Vent"
                                                                                }
                                                            ],
                                                            "Casual Shirts & Pants": [
                                                                                {
                                                                                                    "label": "Garment Type",
                                                                                                    "key": "men_casual_type",
                                                                                                    "placeholder": "e.g. Polo, Chinos"
                                                                                },
                                                                                {
                                                                                                    "label": "Collar Type",
                                                                                                    "key": "men_collar",
                                                                                                    "placeholder": "e.g. Spread Collar"
                                                                                },
                                                                                {
                                                                                                    "label": "Waist Rise",
                                                                                                    "key": "men_pants_rise",
                                                                                                    "placeholder": "e.g. Mid Rise"
                                                                                },
                                                                                {
                                                                                                    "label": "Inseam",
                                                                                                    "key": "men_inseam",
                                                                                                    "placeholder": "e.g. 32 inches"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "men_casual_closure",
                                                                                                    "placeholder": "e.g. Button / Zipper"
                                                                                }
                                                            ],
                                                            "Outerwear & Jackets": [
                                                                                {
                                                                                                    "label": "Outerwear Type",
                                                                                                    "key": "men_outerwear_type",
                                                                                                    "placeholder": "e.g. Bomber Jacket"
                                                                                },
                                                                                {
                                                                                                    "label": "Insulation",
                                                                                                    "key": "men_outerwear_insulation",
                                                                                                    "placeholder": "e.g. Fleece Lined"
                                                                                },
                                                                                {
                                                                                                    "label": "Hood",
                                                                                                    "key": "men_outerwear_hood",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "With Hood",
                                                                                                                        "No Hood"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "men_outerwear_closure",
                                                                                                    "placeholder": "e.g. Zipper"
                                                                                },
                                                                                {
                                                                                                    "label": "Weather Resistance",
                                                                                                    "key": "men_outerwear_weather",
                                                                                                    "placeholder": "e.g. Windproof"
                                                                                }
                                                            ],
                                                            "Activewear & Fitness Gear": [
                                                                                {
                                                                                                    "label": "Activity",
                                                                                                    "key": "men_active_activity",
                                                                                                    "placeholder": "e.g. Gym, Running"
                                                                                },
                                                                                {
                                                                                                    "label": "Compression",
                                                                                                    "key": "men_active_compression",
                                                                                                    "placeholder": "e.g. Light Compression"
                                                                                },
                                                                                {
                                                                                                    "label": "Moisture Wicking",
                                                                                                    "key": "men_active_wicking",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Stretch Level",
                                                                                                    "key": "men_active_stretch",
                                                                                                    "placeholder": "e.g. 4-Way Stretch"
                                                                                },
                                                                                {
                                                                                                    "label": "Pocket Type",
                                                                                                    "key": "men_active_pockets",
                                                                                                    "placeholder": "e.g. Zippered Pockets"
                                                                                }
                                                            ],
                                                            "Shoes & Accessories": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "men_shoe_accessory_type",
                                                                                                    "placeholder": "e.g. Loafers, Belt, Wallet"
                                                                                },
                                                                                {
                                                                                                    "label": "Shoe Size System",
                                                                                                    "key": "men_shoe_size_system",
                                                                                                    "placeholder": "e.g. US, EU"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "men_shoe_closure",
                                                                                                    "placeholder": "e.g. Lace-Up"
                                                                                },
                                                                                {
                                                                                                    "label": "Sole Material",
                                                                                                    "key": "men_sole_material",
                                                                                                    "placeholder": "e.g. Rubber"
                                                                                },
                                                                                {
                                                                                                    "label": "Shaft / Heel Height",
                                                                                                    "key": "men_shoe_height",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Grooming Products": [
                                                                                {
                                                                                                    "label": "Grooming Type",
                                                                                                    "key": "men_grooming_type",
                                                                                                    "placeholder": "e.g. Trimmer, Shaving Cream"
                                                                                },
                                                                                {
                                                                                                    "label": "Skin / Hair Type",
                                                                                                    "key": "men_grooming_skin_hair",
                                                                                                    "placeholder": "e.g. Sensitive Skin"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "men_grooming_power",
                                                                                                    "placeholder": "e.g. Rechargeable"
                                                                                },
                                                                                {
                                                                                                    "label": "Blade / Head Material",
                                                                                                    "key": "men_grooming_blade",
                                                                                                    "placeholder": "e.g. Stainless Steel"
                                                                                },
                                                                                {
                                                                                                    "label": "Wet / Dry Use",
                                                                                                    "key": "men_grooming_wet_dry",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Wet & Dry",
                                                                                                                        "Dry Only"
                                                                                                    ]
                                                                                }
                                                            ]
                                        }
                    },
                    "kids-and-baby": {
                                        "label": "Kids and Baby",
                                        "generalFields": [
                                                            {
                                                                                "label": "Recommended Age",
                                                                                "key": "kids_age",
                                                                                "placeholder": "e.g. 0-6 months, 3-5 years"
                                                            },
                                                            {
                                                                                "label": "Age Group",
                                                                                "key": "kids_age_group",
                                                                                "placeholder": "e.g. Infant, Toddler"
                                                            },
                                                            {
                                                                                "label": "Gender",
                                                                                "key": "kids_gender",
                                                                                "placeholder": "e.g. Unisex"
                                                            },
                                                            {
                                                                                "label": "Safety Certification",
                                                                                "key": "kids_safety",
                                                                                "placeholder": "Safety standard if applicable"
                                                            },
                                                            {
                                                                                "label": "Care Instructions",
                                                                                "key": "kids_care",
                                                                                "placeholder": "Cleaning or care details"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Baby Clothes & Accessories": [
                                                                                {
                                                                                                    "label": "Clothing / Accessory Type",
                                                                                                    "key": "baby_clothing_type",
                                                                                                    "placeholder": "e.g. Onesie, Bib"
                                                                                },
                                                                                {
                                                                                                    "label": "Baby Size",
                                                                                                    "key": "baby_size",
                                                                                                    "placeholder": "e.g. 6-12 Months"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure",
                                                                                                    "key": "baby_closure",
                                                                                                    "placeholder": "e.g. Snap Button"
                                                                                },
                                                                                {
                                                                                                    "label": "Hypoallergenic",
                                                                                                    "key": "baby_hypoallergenic",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Season",
                                                                                                    "key": "baby_season",
                                                                                                    "placeholder": "e.g. All Season"
                                                                                }
                                                            ],
                                                            "Toys & Games": [
                                                                                {
                                                                                                    "label": "Toy Type",
                                                                                                    "key": "kids_toy_type",
                                                                                                    "placeholder": "e.g. Puzzle, Action Figure"
                                                                                },
                                                                                {
                                                                                                    "label": "Educational Benefit",
                                                                                                    "key": "kids_toy_benefit",
                                                                                                    "placeholder": "e.g. Motor Skills"
                                                                                },
                                                                                {
                                                                                                    "label": "Battery Required",
                                                                                                    "key": "kids_toy_battery",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Number of Pieces",
                                                                                                    "key": "kids_toy_pieces",
                                                                                                    "placeholder": "e.g. 24 pieces"
                                                                                },
                                                                                {
                                                                                                    "label": "Safety Standard",
                                                                                                    "key": "kids_toy_standard",
                                                                                                    "placeholder": "e.g. Non-Toxic"
                                                                                }
                                                            ],
                                                            "Educational Materials": [
                                                                                {
                                                                                                    "label": "Subject",
                                                                                                    "key": "kids_education_subject",
                                                                                                    "placeholder": "e.g. Math, Reading"
                                                                                },
                                                                                {
                                                                                                    "label": "Grade / Learning Level",
                                                                                                    "key": "kids_education_level",
                                                                                                    "placeholder": "e.g. Grade 2"
                                                                                },
                                                                                {
                                                                                                    "label": "Format",
                                                                                                    "key": "kids_education_format",
                                                                                                    "placeholder": "e.g. Workbook, Flash Cards"
                                                                                },
                                                                                {
                                                                                                    "label": "Language",
                                                                                                    "key": "kids_education_language",
                                                                                                    "placeholder": "e.g. English"
                                                                                },
                                                                                {
                                                                                                    "label": "Page / Piece Count",
                                                                                                    "key": "kids_education_count",
                                                                                                    "placeholder": "e.g. 80 pages"
                                                                                }
                                                            ],
                                                            "Strollers & Gear": [
                                                                                {
                                                                                                    "label": "Gear Type",
                                                                                                    "key": "baby_gear_type",
                                                                                                    "placeholder": "e.g. Stroller, Carrier"
                                                                                },
                                                                                {
                                                                                                    "label": "Weight Capacity",
                                                                                                    "key": "baby_gear_capacity",
                                                                                                    "placeholder": "e.g. 22 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Foldable",
                                                                                                    "key": "baby_gear_foldable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Wheel Type",
                                                                                                    "key": "baby_gear_wheels",
                                                                                                    "placeholder": "e.g. All-Terrain"
                                                                                },
                                                                                {
                                                                                                    "label": "Harness Type",
                                                                                                    "key": "baby_gear_harness",
                                                                                                    "placeholder": "e.g. 5-Point Harness"
                                                                                }
                                                            ],
                                                            "Nursery Furniture": [
                                                                                {
                                                                                                    "label": "Furniture Type",
                                                                                                    "key": "nursery_type",
                                                                                                    "placeholder": "e.g. Crib, Changing Table"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimensions",
                                                                                                    "key": "nursery_dimensions",
                                                                                                    "placeholder": "L × W × H"
                                                                                },
                                                                                {
                                                                                                    "label": "Weight Capacity",
                                                                                                    "key": "nursery_capacity",
                                                                                                    "placeholder": "e.g. 25 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Assembly Required",
                                                                                                    "key": "nursery_assembly",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Safety Standard",
                                                                                                    "key": "nursery_safety_standard",
                                                                                                    "placeholder": "Certification if applicable"
                                                                                }
                                                            ],
                                                            "Safety and Health": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "kids_health_type",
                                                                                                    "placeholder": "e.g. Thermometer, Safety Gate"
                                                                                },
                                                                                {
                                                                                                    "label": "Safety Feature",
                                                                                                    "key": "kids_health_feature",
                                                                                                    "placeholder": "Key safety feature"
                                                                                },
                                                                                {
                                                                                                    "label": "Certification",
                                                                                                    "key": "kids_health_certification",
                                                                                                    "placeholder": "Certification if applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Usage Location",
                                                                                                    "key": "kids_health_location",
                                                                                                    "placeholder": "e.g. Home, Car"
                                                                                },
                                                                                {
                                                                                                    "label": "Age Range",
                                                                                                    "key": "kids_health_age_range",
                                                                                                    "placeholder": "Recommended age range"
                                                                                }
                                                            ]
                                        }
                    },
                    "home-and-garden": {
                                        "label": "Home and Garden",
                                        "generalFields": [
                                                            {
                                                                                "label": "Room / Area",
                                                                                "key": "home_room_area",
                                                                                "placeholder": "e.g. Kitchen, Bedroom, Garden"
                                                            },
                                                            {
                                                                                "label": "Dimensions",
                                                                                "key": "home_dimensions",
                                                                                "placeholder": "L × W × H"
                                                            },
                                                            {
                                                                                "label": "Indoor / Outdoor",
                                                                                "key": "home_indoor_outdoor",
                                                                                "type": "select",
                                                                                "options": [
                                                                                                    "Indoor",
                                                                                                    "Outdoor",
                                                                                                    "Indoor & Outdoor"
                                                                                ]
                                                            },
                                                            {
                                                                                "label": "Assembly Required",
                                                                                "key": "home_assembly",
                                                                                "type": "select",
                                                                                "options": [
                                                                                                    "Yes",
                                                                                                    "No"
                                                                                ]
                                                            },
                                                            {
                                                                                "label": "Finish / Surface",
                                                                                "key": "home_finish",
                                                                                "placeholder": "e.g. Matte, Glossy, Wood Grain"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Kitchen Appliances": [
                                                                                {
                                                                                                    "label": "Appliance Type",
                                                                                                    "key": "kitchen_appliance_type",
                                                                                                    "placeholder": "e.g. Blender, Rice Cooker"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "kitchen_capacity",
                                                                                                    "placeholder": "e.g. 1.8 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Wattage",
                                                                                                    "key": "kitchen_wattage",
                                                                                                    "placeholder": "e.g. 700 W"
                                                                                },
                                                                                {
                                                                                                    "label": "Voltage",
                                                                                                    "key": "kitchen_voltage",
                                                                                                    "placeholder": "e.g. 220 V"
                                                                                },
                                                                                {
                                                                                                    "label": "Functions / Settings",
                                                                                                    "key": "kitchen_functions",
                                                                                                    "placeholder": "e.g. 3 Speed"
                                                                                }
                                                            ],
                                                            "Furniture & Decor": [
                                                                                {
                                                                                                    "label": "Furniture / Decor Type",
                                                                                                    "key": "home_furniture_type",
                                                                                                    "placeholder": "e.g. Chair, Vase"
                                                                                },
                                                                                {
                                                                                                    "label": "Style",
                                                                                                    "key": "home_decor_style",
                                                                                                    "placeholder": "e.g. Modern, Minimalist"
                                                                                },
                                                                                {
                                                                                                    "label": "Weight Capacity",
                                                                                                    "key": "home_furniture_capacity",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Number of Pieces",
                                                                                                    "key": "home_furniture_pieces",
                                                                                                    "placeholder": "e.g. 4-piece set"
                                                                                },
                                                                                {
                                                                                                    "label": "Mounting Type",
                                                                                                    "key": "home_decor_mounting",
                                                                                                    "placeholder": "e.g. Freestanding, Wall-Mounted"
                                                                                }
                                                            ],
                                                            "Gardening Tools": [
                                                                                {
                                                                                                    "label": "Tool Type",
                                                                                                    "key": "garden_tool_type",
                                                                                                    "placeholder": "e.g. Pruner, Shovel"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "garden_power_source",
                                                                                                    "placeholder": "e.g. Manual, Battery"
                                                                                },
                                                                                {
                                                                                                    "label": "Blade / Tool Material",
                                                                                                    "key": "garden_tool_material",
                                                                                                    "placeholder": "e.g. Carbon Steel"
                                                                                },
                                                                                {
                                                                                                    "label": "Handle Length",
                                                                                                    "key": "garden_handle_length",
                                                                                                    "placeholder": "e.g. 80 cm"
                                                                                },
                                                                                {
                                                                                                    "label": "Recommended Use",
                                                                                                    "key": "garden_tool_use",
                                                                                                    "placeholder": "e.g. Pruning, Digging"
                                                                                }
                                                            ],
                                                            "Outdoor Living": [
                                                                                {
                                                                                                    "label": "Outdoor Product Type",
                                                                                                    "key": "outdoor_living_type",
                                                                                                    "placeholder": "e.g. Patio Chair, Grill"
                                                                                },
                                                                                {
                                                                                                    "label": "Weather Resistance",
                                                                                                    "key": "outdoor_weather",
                                                                                                    "placeholder": "e.g. UV / Rain Resistant"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "outdoor_capacity",
                                                                                                    "placeholder": "e.g. Seats 4"
                                                                                },
                                                                                {
                                                                                                    "label": "Foldable / Portable",
                                                                                                    "key": "outdoor_portable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Cover Included",
                                                                                                    "key": "outdoor_cover",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Home Improvement Tools": [
                                                                                {
                                                                                                    "label": "Tool Type",
                                                                                                    "key": "home_tool_type",
                                                                                                    "placeholder": "e.g. Drill, Sander"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "home_tool_power",
                                                                                                    "placeholder": "e.g. Corded, Battery"
                                                                                },
                                                                                {
                                                                                                    "label": "Voltage",
                                                                                                    "key": "home_tool_voltage",
                                                                                                    "placeholder": "e.g. 18 V"
                                                                                },
                                                                                {
                                                                                                    "label": "Speed / Torque",
                                                                                                    "key": "home_tool_performance",
                                                                                                    "placeholder": "e.g. 0-1500 RPM"
                                                                                },
                                                                                {
                                                                                                    "label": "Included Accessories",
                                                                                                    "key": "home_tool_accessories",
                                                                                                    "placeholder": "e.g. 10 Drill Bits"
                                                                                }
                                                            ],
                                                            "Bedding & Bath": [
                                                                                {
                                                                                                    "label": "Item Type",
                                                                                                    "key": "bedding_bath_type",
                                                                                                    "placeholder": "e.g. Bedsheet, Towel"
                                                                                },
                                                                                {
                                                                                                    "label": "Size",
                                                                                                    "key": "bedding_bath_size",
                                                                                                    "placeholder": "e.g. Queen, Bath Towel"
                                                                                },
                                                                                {
                                                                                                    "label": "Thread Count / GSM",
                                                                                                    "key": "bedding_gsm",
                                                                                                    "placeholder": "e.g. 300 TC, 500 GSM"
                                                                                },
                                                                                {
                                                                                                    "label": "Set Includes",
                                                                                                    "key": "bedding_set_includes",
                                                                                                    "placeholder": "e.g. 1 Sheet + 2 Pillowcases"
                                                                                },
                                                                                {
                                                                                                    "label": "Absorbency / Softness",
                                                                                                    "key": "bedding_quality",
                                                                                                    "placeholder": "Product feel or absorbency"
                                                                                }
                                                            ]
                                        }
                    },
                    "sports-and-outdoors": {
                                        "label": "Sports and Outdoors",
                                        "generalFields": [
                                                            {
                                                                                "label": "Sport / Activity",
                                                                                "key": "sports_activity",
                                                                                "placeholder": "e.g. Basketball, Hiking"
                                                            },
                                                            {
                                                                                "label": "Skill Level",
                                                                                "key": "sports_skill_level",
                                                                                "placeholder": "e.g. Beginner, Pro"
                                                            },
                                                            {
                                                                                "label": "Dimensions / Size",
                                                                                "key": "sports_dimensions",
                                                                                "placeholder": "Product dimensions"
                                                            },
                                                            {
                                                                                "label": "Product Weight",
                                                                                "key": "sports_weight",
                                                                                "placeholder": "e.g. 2.5 kg"
                                                            },
                                                            {
                                                                                "label": "Weather / Water Resistance",
                                                                                "key": "sports_weather",
                                                                                "placeholder": "e.g. Waterproof"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Fitness Equipment": [
                                                                                {
                                                                                                    "label": "Equipment Type",
                                                                                                    "key": "fitness_equipment_type",
                                                                                                    "placeholder": "e.g. Dumbbell, Treadmill"
                                                                                },
                                                                                {
                                                                                                    "label": "Resistance / Weight",
                                                                                                    "key": "fitness_resistance",
                                                                                                    "placeholder": "e.g. 20 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Max User Weight",
                                                                                                    "key": "fitness_user_weight",
                                                                                                    "placeholder": "e.g. 120 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Foldable",
                                                                                                    "key": "fitness_foldable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Target Muscle / Use",
                                                                                                    "key": "fitness_target",
                                                                                                    "placeholder": "e.g. Cardio, Core"
                                                                                }
                                                            ],
                                                            "Camping & Hiking Gear": [
                                                                                {
                                                                                                    "label": "Gear Type",
                                                                                                    "key": "camping_gear_type",
                                                                                                    "placeholder": "e.g. Tent, Backpack"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "camping_capacity",
                                                                                                    "placeholder": "e.g. 4 persons, 40 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Packed Size / Weight",
                                                                                                    "key": "camping_packed_size",
                                                                                                    "placeholder": "Packed dimensions or weight"
                                                                                },
                                                                                {
                                                                                                    "label": "Waterproof Rating",
                                                                                                    "key": "camping_waterproof",
                                                                                                    "placeholder": "e.g. 3000 mm"
                                                                                },
                                                                                {
                                                                                                    "label": "Season Rating",
                                                                                                    "key": "camping_season",
                                                                                                    "placeholder": "e.g. 3-Season"
                                                                                }
                                                            ],
                                                            "Sports Apparel": [
                                                                                {
                                                                                                    "label": "Apparel Type",
                                                                                                    "key": "sports_apparel_type",
                                                                                                    "placeholder": "e.g. Jersey, Shorts"
                                                                                },
                                                                                {
                                                                                                    "label": "Fit / Compression",
                                                                                                    "key": "sports_apparel_fit",
                                                                                                    "placeholder": "e.g. Compression Fit"
                                                                                },
                                                                                {
                                                                                                    "label": "Moisture Wicking",
                                                                                                    "key": "sports_apparel_wicking",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "UPF Rating",
                                                                                                    "key": "sports_apparel_upf",
                                                                                                    "placeholder": "e.g. UPF 50+"
                                                                                },
                                                                                {
                                                                                                    "label": "Reflective Details",
                                                                                                    "key": "sports_apparel_reflective",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Cycling & Bikes": [
                                                                                {
                                                                                                    "label": "Bike / Accessory Type",
                                                                                                    "key": "cycling_type",
                                                                                                    "placeholder": "e.g. Mountain Bike, Helmet"
                                                                                },
                                                                                {
                                                                                                    "label": "Wheel Size",
                                                                                                    "key": "cycling_wheel_size",
                                                                                                    "placeholder": "e.g. 29 inches"
                                                                                },
                                                                                {
                                                                                                    "label": "Frame Size / Material",
                                                                                                    "key": "cycling_frame",
                                                                                                    "placeholder": "e.g. Medium / Aluminum"
                                                                                },
                                                                                {
                                                                                                    "label": "Gear Count",
                                                                                                    "key": "cycling_gears",
                                                                                                    "placeholder": "e.g. 21-Speed"
                                                                                },
                                                                                {
                                                                                                    "label": "Brake Type",
                                                                                                    "key": "cycling_brakes",
                                                                                                    "placeholder": "e.g. Hydraulic Disc"
                                                                                }
                                                            ],
                                                            "Water Sports": [
                                                                                {
                                                                                                    "label": "Equipment Type",
                                                                                                    "key": "water_sports_type",
                                                                                                    "placeholder": "e.g. Paddleboard, Life Vest"
                                                                                },
                                                                                {
                                                                                                    "label": "Buoyancy / Capacity",
                                                                                                    "key": "water_sports_capacity",
                                                                                                    "placeholder": "e.g. 100 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Board / Gear Size",
                                                                                                    "key": "water_sports_size",
                                                                                                    "placeholder": "Dimensions or size"
                                                                                },
                                                                                {
                                                                                                    "label": "Waterproof Rating",
                                                                                                    "key": "water_sports_waterproof",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Recommended Skill Level",
                                                                                                    "key": "water_sports_skill",
                                                                                                    "placeholder": "e.g. Beginner"
                                                                                }
                                                            ],
                                                            "Team Sports Equipment": [
                                                                                {
                                                                                                    "label": "Sport",
                                                                                                    "key": "team_sport",
                                                                                                    "placeholder": "e.g. Basketball, Volleyball"
                                                                                },
                                                                                {
                                                                                                    "label": "Equipment Type",
                                                                                                    "key": "team_equipment_type",
                                                                                                    "placeholder": "e.g. Ball, Net"
                                                                                },
                                                                                {
                                                                                                    "label": "Official Size",
                                                                                                    "key": "team_equipment_size",
                                                                                                    "placeholder": "e.g. Size 7"
                                                                                },
                                                                                {
                                                                                                    "label": "League / Standard",
                                                                                                    "key": "team_standard",
                                                                                                    "placeholder": "e.g. FIBA Standard"
                                                                                },
                                                                                {
                                                                                                    "label": "Surface / Playing Area",
                                                                                                    "key": "team_surface",
                                                                                                    "placeholder": "e.g. Indoor / Outdoor"
                                                                                }
                                                            ]
                                        }
                    },
                    "health-and-beauty": {
                                        "label": "Health and Beauty",
                                        "generalFields": [
                                                            {
                                                                                "label": "Product Type",
                                                                                "key": "beauty_type",
                                                                                "placeholder": "e.g. Serum, Shampoo, Trimmer"
                                                            },
                                                            {
                                                                                "label": "Skin / Hair Type",
                                                                                "key": "beauty_skin_hair_type",
                                                                                "placeholder": "e.g. Oily Skin"
                                                            },
                                                            {
                                                                                "label": "Net Weight / Volume",
                                                                                "key": "beauty_net_weight",
                                                                                "placeholder": "e.g. 50 ml"
                                                            },
                                                            {
                                                                                "label": "Key Ingredients",
                                                                                "key": "beauty_ingredients",
                                                                                "placeholder": "Key or active ingredients"
                                                            },
                                                            {
                                                                                "label": "Shelf Life / Expiry",
                                                                                "key": "beauty_shelf_life",
                                                                                "placeholder": "Shelf life or expiry"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Skincare Products": [
                                                                                {
                                                                                                    "label": "Skin Concern",
                                                                                                    "key": "skincare_concern",
                                                                                                    "placeholder": "e.g. Acne, Dryness"
                                                                                },
                                                                                {
                                                                                                    "label": "Active Ingredients",
                                                                                                    "key": "skincare_actives",
                                                                                                    "placeholder": "e.g. Niacinamide"
                                                                                },
                                                                                {
                                                                                                    "label": "SPF",
                                                                                                    "key": "skincare_spf",
                                                                                                    "placeholder": "e.g. SPF 50"
                                                                                },
                                                                                {
                                                                                                    "label": "Formulation",
                                                                                                    "key": "skincare_formulation",
                                                                                                    "placeholder": "e.g. Gel, Cream"
                                                                                },
                                                                                {
                                                                                                    "label": "Fragrance Free",
                                                                                                    "key": "skincare_fragrance_free",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Haircare Solutions": [
                                                                                {
                                                                                                    "label": "Hair Concern",
                                                                                                    "key": "haircare_concern",
                                                                                                    "placeholder": "e.g. Hair Fall, Dandruff"
                                                                                },
                                                                                {
                                                                                                    "label": "Hair Type",
                                                                                                    "key": "haircare_type",
                                                                                                    "placeholder": "e.g. Curly, Dry"
                                                                                },
                                                                                {
                                                                                                    "label": "Formulation",
                                                                                                    "key": "haircare_formulation",
                                                                                                    "placeholder": "e.g. Shampoo, Serum"
                                                                                },
                                                                                {
                                                                                                    "label": "Sulfate Free",
                                                                                                    "key": "haircare_sulfate_free",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Usage Frequency",
                                                                                                    "key": "haircare_frequency",
                                                                                                    "placeholder": "e.g. Daily, Weekly"
                                                                                }
                                                            ],
                                                            "Makeup & Cosmetics": [
                                                                                {
                                                                                                    "label": "Makeup Type",
                                                                                                    "key": "makeup_type",
                                                                                                    "placeholder": "e.g. Foundation, Lipstick"
                                                                                },
                                                                                {
                                                                                                    "label": "Shade",
                                                                                                    "key": "makeup_shade",
                                                                                                    "placeholder": "e.g. Warm Beige"
                                                                                },
                                                                                {
                                                                                                    "label": "Finish",
                                                                                                    "key": "makeup_finish",
                                                                                                    "placeholder": "e.g. Matte, Dewy"
                                                                                },
                                                                                {
                                                                                                    "label": "Coverage",
                                                                                                    "key": "makeup_coverage",
                                                                                                    "placeholder": "e.g. Light, Full"
                                                                                },
                                                                                {
                                                                                                    "label": "Waterproof",
                                                                                                    "key": "makeup_waterproof",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Personal Care Appliances": [
                                                                                {
                                                                                                    "label": "Appliance Type",
                                                                                                    "key": "care_appliance_type",
                                                                                                    "placeholder": "e.g. Hair Dryer, Shaver"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "care_appliance_power",
                                                                                                    "placeholder": "e.g. Corded, Rechargeable"
                                                                                },
                                                                                {
                                                                                                    "label": "Heat / Speed Settings",
                                                                                                    "key": "care_appliance_settings",
                                                                                                    "placeholder": "e.g. 3 Speeds"
                                                                                },
                                                                                {
                                                                                                    "label": "Voltage",
                                                                                                    "key": "care_appliance_voltage",
                                                                                                    "placeholder": "e.g. 220 V"
                                                                                },
                                                                                {
                                                                                                    "label": "Attachments Included",
                                                                                                    "key": "care_appliance_attachments",
                                                                                                    "placeholder": "Included accessories"
                                                                                }
                                                            ],
                                                            "Men's Grooming": [
                                                                                {
                                                                                                    "label": "Grooming Type",
                                                                                                    "key": "mens_grooming_type",
                                                                                                    "placeholder": "e.g. Beard Trimmer"
                                                                                },
                                                                                {
                                                                                                    "label": "Suitable Skin / Hair Type",
                                                                                                    "key": "mens_grooming_suitable",
                                                                                                    "placeholder": "e.g. Sensitive Skin"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "mens_grooming_power",
                                                                                                    "placeholder": "e.g. Rechargeable"
                                                                                },
                                                                                {
                                                                                                    "label": "Blade Material",
                                                                                                    "key": "mens_grooming_blade",
                                                                                                    "placeholder": "e.g. Stainless Steel"
                                                                                },
                                                                                {
                                                                                                    "label": "Wet / Dry Use",
                                                                                                    "key": "mens_grooming_wet_dry",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Wet & Dry",
                                                                                                                        "Dry Only"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Health Supplements": [
                                                                                {
                                                                                                    "label": "Supplement Type",
                                                                                                    "key": "supplement_type",
                                                                                                    "placeholder": "e.g. Vitamin, Protein"
                                                                                },
                                                                                {
                                                                                                    "label": "Form",
                                                                                                    "key": "supplement_form",
                                                                                                    "placeholder": "e.g. Capsule, Powder"
                                                                                },
                                                                                {
                                                                                                    "label": "Serving Size",
                                                                                                    "key": "supplement_serving_size",
                                                                                                    "placeholder": "e.g. 2 Capsules"
                                                                                },
                                                                                {
                                                                                                    "label": "Servings per Container",
                                                                                                    "key": "supplement_servings",
                                                                                                    "placeholder": "e.g. 30"
                                                                                },
                                                                                {
                                                                                                    "label": "Active Nutrients",
                                                                                                    "key": "supplement_nutrients",
                                                                                                    "placeholder": "e.g. Vitamin C 500 mg"
                                                                                }
                                                            ]
                                        }
                    },
                    "books-and-media": {
                                        "label": "Books and Media",
                                        "generalFields": [
                                                            {
                                                                                "label": "Media Type",
                                                                                "key": "media_type",
                                                                                "placeholder": "e.g. Book, Vinyl, DVD, Game"
                                                            },
                                                            {
                                                                                "label": "Creator / Publisher",
                                                                                "key": "media_creator",
                                                                                "placeholder": "Author, artist, publisher"
                                                            },
                                                            {
                                                                                "label": "Language",
                                                                                "key": "media_language",
                                                                                "placeholder": "e.g. English"
                                                            },
                                                            {
                                                                                "label": "Release / Publication Year",
                                                                                "key": "media_release_year",
                                                                                "placeholder": "e.g. 2026"
                                                            },
                                                            {
                                                                                "label": "Edition / Format",
                                                                                "key": "media_format",
                                                                                "placeholder": "e.g. Paperback, Deluxe"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Fiction & Non-Fiction Books": [
                                                                                {
                                                                                                    "label": "Author",
                                                                                                    "key": "book_author",
                                                                                                    "placeholder": "Author name"
                                                                                },
                                                                                {
                                                                                                    "label": "Genre",
                                                                                                    "key": "book_genre",
                                                                                                    "placeholder": "e.g. Fiction, Biography"
                                                                                },
                                                                                {
                                                                                                    "label": "ISBN",
                                                                                                    "key": "book_isbn",
                                                                                                    "placeholder": "ISBN number"
                                                                                },
                                                                                {
                                                                                                    "label": "Binding",
                                                                                                    "key": "book_binding",
                                                                                                    "placeholder": "e.g. Hardcover"
                                                                                },
                                                                                {
                                                                                                    "label": "Page Count",
                                                                                                    "key": "book_pages",
                                                                                                    "placeholder": "e.g. 320 pages"
                                                                                }
                                                            ],
                                                            "Magazines & Periodicals": [
                                                                                {
                                                                                                    "label": "Publication",
                                                                                                    "key": "magazine_publication",
                                                                                                    "placeholder": "Magazine title"
                                                                                },
                                                                                {
                                                                                                    "label": "Issue / Volume",
                                                                                                    "key": "magazine_issue",
                                                                                                    "placeholder": "e.g. Vol. 12 No. 3"
                                                                                },
                                                                                {
                                                                                                    "label": "Frequency",
                                                                                                    "key": "magazine_frequency",
                                                                                                    "placeholder": "e.g. Monthly"
                                                                                },
                                                                                {
                                                                                                    "label": "Issue Date",
                                                                                                    "key": "magazine_issue_date",
                                                                                                    "placeholder": "Publication date"
                                                                                },
                                                                                {
                                                                                                    "label": "Page Count",
                                                                                                    "key": "magazine_pages",
                                                                                                    "placeholder": "e.g. 80 pages"
                                                                                }
                                                            ],
                                                            "Music CDs & Vinyl Records": [
                                                                                {
                                                                                                    "label": "Artist",
                                                                                                    "key": "music_artist",
                                                                                                    "placeholder": "Artist / band"
                                                                                },
                                                                                {
                                                                                                    "label": "Music Format",
                                                                                                    "key": "music_format",
                                                                                                    "placeholder": "e.g. CD, Vinyl"
                                                                                },
                                                                                {
                                                                                                    "label": "Genre",
                                                                                                    "key": "music_genre",
                                                                                                    "placeholder": "e.g. Pop, Rock"
                                                                                },
                                                                                {
                                                                                                    "label": "Track Count",
                                                                                                    "key": "music_tracks",
                                                                                                    "placeholder": "e.g. 12 tracks"
                                                                                },
                                                                                {
                                                                                                    "label": "Edition",
                                                                                                    "key": "music_edition",
                                                                                                    "placeholder": "e.g. Limited Edition"
                                                                                }
                                                            ],
                                                            "Movie DVDs & Blu-ray": [
                                                                                {
                                                                                                    "label": "Video Format",
                                                                                                    "key": "movie_format",
                                                                                                    "placeholder": "e.g. DVD, Blu-ray"
                                                                                },
                                                                                {
                                                                                                    "label": "Genre",
                                                                                                    "key": "movie_genre",
                                                                                                    "placeholder": "e.g. Action, Drama"
                                                                                },
                                                                                {
                                                                                                    "label": "Region Code",
                                                                                                    "key": "movie_region",
                                                                                                    "placeholder": "e.g. Region A"
                                                                                },
                                                                                {
                                                                                                    "label": "Runtime",
                                                                                                    "key": "movie_runtime",
                                                                                                    "placeholder": "e.g. 120 minutes"
                                                                                },
                                                                                {
                                                                                                    "label": "Audio / Subtitle",
                                                                                                    "key": "movie_audio_subtitle",
                                                                                                    "placeholder": "Languages available"
                                                                                }
                                                            ],
                                                            "Video Games & Consoles": [
                                                                                {
                                                                                                    "label": "Platform",
                                                                                                    "key": "game_platform",
                                                                                                    "placeholder": "e.g. PS5, Switch"
                                                                                },
                                                                                {
                                                                                                    "label": "Genre",
                                                                                                    "key": "game_genre",
                                                                                                    "placeholder": "e.g. RPG, Sports"
                                                                                },
                                                                                {
                                                                                                    "label": "Age Rating",
                                                                                                    "key": "game_rating",
                                                                                                    "placeholder": "e.g. ESRB T"
                                                                                },
                                                                                {
                                                                                                    "label": "Region",
                                                                                                    "key": "game_region",
                                                                                                    "placeholder": "e.g. Asia"
                                                                                },
                                                                                {
                                                                                                    "label": "Multiplayer",
                                                                                                    "key": "game_multiplayer",
                                                                                                    "placeholder": "e.g. Online / Local"
                                                                                }
                                                            ],
                                                            "Educational DVDs": [
                                                                                {
                                                                                                    "label": "Subject",
                                                                                                    "key": "edu_dvd_subject",
                                                                                                    "placeholder": "e.g. Science, Language"
                                                                                },
                                                                                {
                                                                                                    "label": "Target Audience",
                                                                                                    "key": "edu_dvd_audience",
                                                                                                    "placeholder": "e.g. Grade School"
                                                                                },
                                                                                {
                                                                                                    "label": "Runtime",
                                                                                                    "key": "edu_dvd_runtime",
                                                                                                    "placeholder": "e.g. 90 minutes"
                                                                                },
                                                                                {
                                                                                                    "label": "Language",
                                                                                                    "key": "edu_dvd_language",
                                                                                                    "placeholder": "e.g. English"
                                                                                },
                                                                                {
                                                                                                    "label": "Disc / Lesson Count",
                                                                                                    "key": "edu_dvd_count",
                                                                                                    "placeholder": "e.g. 3 Discs"
                                                                                }
                                                            ]
                                        }
                    },
                    "food-and-gourmet": {
                                        "label": "Food and Gourmet",
                                        "generalFields": [
                                                            {
                                                                                "label": "Food / Beverage Type",
                                                                                "key": "food_type",
                                                                                "placeholder": "e.g. Snack, Beverage"
                                                            },
                                                            {
                                                                                "label": "Net Weight / Volume",
                                                                                "key": "food_net_weight",
                                                                                "placeholder": "e.g. 500 g, 1 L"
                                                            },
                                                            {
                                                                                "label": "Ingredients",
                                                                                "key": "food_ingredients",
                                                                                "placeholder": "Main ingredients"
                                                            },
                                                            {
                                                                                "label": "Allergen Information",
                                                                                "key": "food_allergens",
                                                                                "placeholder": "e.g. Contains nuts"
                                                            },
                                                            {
                                                                                "label": "Expiration / Best Before",
                                                                                "key": "food_expiration",
                                                                                "placeholder": "Date or shelf life"
                                                            },
                                                            {
                                                                                "label": "Storage Instructions",
                                                                                "key": "food_storage",
                                                                                "placeholder": "e.g. Keep refrigerated"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Baking Supplies & Ingredients": [
                                                                                {
                                                                                                    "label": "Ingredient Type",
                                                                                                    "key": "baking_type",
                                                                                                    "placeholder": "e.g. Flour, Cocoa"
                                                                                },
                                                                                {
                                                                                                    "label": "Baking Use",
                                                                                                    "key": "baking_use",
                                                                                                    "placeholder": "e.g. Cakes, Bread"
                                                                                },
                                                                                {
                                                                                                    "label": "Dietary Information",
                                                                                                    "key": "baking_dietary",
                                                                                                    "placeholder": "e.g. Gluten-Free"
                                                                                },
                                                                                {
                                                                                                    "label": "Packaging Type",
                                                                                                    "key": "baking_packaging",
                                                                                                    "placeholder": "e.g. Resealable Pouch"
                                                                                },
                                                                                {
                                                                                                    "label": "Preparation Requirement",
                                                                                                    "key": "baking_preparation",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Coffee, Tea & Beverages": [
                                                                                {
                                                                                                    "label": "Beverage Type",
                                                                                                    "key": "beverage_type",
                                                                                                    "placeholder": "e.g. Coffee, Tea"
                                                                                },
                                                                                {
                                                                                                    "label": "Roast / Flavor",
                                                                                                    "key": "beverage_roast_flavor",
                                                                                                    "placeholder": "e.g. Medium Roast"
                                                                                },
                                                                                {
                                                                                                    "label": "Caffeine Level",
                                                                                                    "key": "beverage_caffeine",
                                                                                                    "placeholder": "e.g. Caffeinated"
                                                                                },
                                                                                {
                                                                                                    "label": "Serving Size",
                                                                                                    "key": "beverage_serving",
                                                                                                    "placeholder": "e.g. 1 Sachet"
                                                                                },
                                                                                {
                                                                                                    "label": "Brewing / Preparation",
                                                                                                    "key": "beverage_preparation",
                                                                                                    "placeholder": "How to prepare"
                                                                                }
                                                            ],
                                                            "Snacks & Candy": [
                                                                                {
                                                                                                    "label": "Snack / Candy Type",
                                                                                                    "key": "snack_type",
                                                                                                    "placeholder": "e.g. Chips, Chocolate"
                                                                                },
                                                                                {
                                                                                                    "label": "Flavor",
                                                                                                    "key": "snack_flavor",
                                                                                                    "placeholder": "e.g. Cheese"
                                                                                },
                                                                                {
                                                                                                    "label": "Pieces / Servings",
                                                                                                    "key": "snack_servings",
                                                                                                    "placeholder": "e.g. 10 pieces"
                                                                                },
                                                                                {
                                                                                                    "label": "Dietary Information",
                                                                                                    "key": "snack_dietary",
                                                                                                    "placeholder": "e.g. Sugar-Free"
                                                                                },
                                                                                {
                                                                                                    "label": "Texture",
                                                                                                    "key": "snack_texture",
                                                                                                    "placeholder": "e.g. Crunchy"
                                                                                }
                                                            ],
                                                            "Specialty Foods & International Cuisine": [
                                                                                {
                                                                                                    "label": "Cuisine / Origin",
                                                                                                    "key": "specialty_cuisine",
                                                                                                    "placeholder": "e.g. Korean, Japanese"
                                                                                },
                                                                                {
                                                                                                    "label": "Food Type",
                                                                                                    "key": "specialty_food_type",
                                                                                                    "placeholder": "e.g. Sauce, Noodles"
                                                                                },
                                                                                {
                                                                                                    "label": "Spice Level",
                                                                                                    "key": "specialty_spice_level",
                                                                                                    "placeholder": "e.g. Mild, Spicy"
                                                                                },
                                                                                {
                                                                                                    "label": "Serving Suggestion",
                                                                                                    "key": "specialty_serving",
                                                                                                    "placeholder": "Serving suggestion"
                                                                                },
                                                                                {
                                                                                                    "label": "Preparation Method",
                                                                                                    "key": "specialty_preparation",
                                                                                                    "placeholder": "Cooking instructions"
                                                                                }
                                                            ],
                                                            "Organic and Health Foods": [
                                                                                {
                                                                                                    "label": "Certification",
                                                                                                    "key": "organic_certification",
                                                                                                    "placeholder": "e.g. Organic Certified"
                                                                                },
                                                                                {
                                                                                                    "label": "Dietary Type",
                                                                                                    "key": "organic_dietary",
                                                                                                    "placeholder": "e.g. Vegan, Keto"
                                                                                },
                                                                                {
                                                                                                    "label": "Nutrition Focus",
                                                                                                    "key": "organic_nutrition_focus",
                                                                                                    "placeholder": "e.g. High Protein"
                                                                                },
                                                                                {
                                                                                                    "label": "Organic Content",
                                                                                                    "key": "organic_content",
                                                                                                    "placeholder": "e.g. 100% Organic"
                                                                                },
                                                                                {
                                                                                                    "label": "Added Sugar",
                                                                                                    "key": "organic_added_sugar",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Meal Kits & Prepped Foods": [
                                                                                {
                                                                                                    "label": "Cuisine",
                                                                                                    "key": "meal_kit_cuisine",
                                                                                                    "placeholder": "e.g. Filipino, Italian"
                                                                                },
                                                                                {
                                                                                                    "label": "Servings",
                                                                                                    "key": "meal_kit_servings",
                                                                                                    "placeholder": "e.g. Serves 2"
                                                                                },
                                                                                {
                                                                                                    "label": "Preparation Time",
                                                                                                    "key": "meal_kit_prep_time",
                                                                                                    "placeholder": "e.g. 15 minutes"
                                                                                },
                                                                                {
                                                                                                    "label": "Storage Requirement",
                                                                                                    "key": "meal_kit_storage",
                                                                                                    "placeholder": "e.g. Refrigerated"
                                                                                },
                                                                                {
                                                                                                    "label": "Included Components",
                                                                                                    "key": "meal_kit_components",
                                                                                                    "placeholder": "What's included"
                                                                                }
                                                            ]
                                        }
                    },
                    "automotive-motorcycle": {
                                        "label": "Automotive & Motorcycle",
                                        "generalFields": [
                                                            {
                                                                                "label": "Part / Accessory Type",
                                                                                "key": "auto_type",
                                                                                "placeholder": "e.g. Helmet, Mirror, Battery"
                                                            },
                                                            {
                                                                                "label": "Vehicle Compatibility",
                                                                                "key": "auto_compatibility",
                                                                                "placeholder": "Vehicle make and model"
                                                            },
                                                            {
                                                                                "label": "Model / Year",
                                                                                "key": "auto_model_year",
                                                                                "placeholder": "e.g. 2020-2026"
                                                            },
                                                            {
                                                                                "label": "Part Number",
                                                                                "key": "auto_part_number",
                                                                                "placeholder": "Manufacturer part number"
                                                            },
                                                            {
                                                                                "label": "Installation Type",
                                                                                "key": "auto_installation",
                                                                                "placeholder": "e.g. Bolt-On"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Protective Gear": [
                                                                                {
                                                                                                    "label": "Gear Type",
                                                                                                    "key": "auto_gear_type",
                                                                                                    "placeholder": "e.g. Helmet, Gloves"
                                                                                },
                                                                                {
                                                                                                    "label": "Size",
                                                                                                    "key": "auto_gear_size",
                                                                                                    "placeholder": "e.g. Medium"
                                                                                },
                                                                                {
                                                                                                    "label": "Safety Certification",
                                                                                                    "key": "auto_gear_certification",
                                                                                                    "placeholder": "e.g. DOT, ECE"
                                                                                },
                                                                                {
                                                                                                    "label": "Protection Level",
                                                                                                    "key": "auto_gear_protection",
                                                                                                    "placeholder": "Protection details"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure Type",
                                                                                                    "key": "auto_gear_closure",
                                                                                                    "placeholder": "e.g. Double D-Ring"
                                                                                }
                                                            ],
                                                            "Maintenance & Repair Tools": [
                                                                                {
                                                                                                    "label": "Tool Type",
                                                                                                    "key": "auto_tool_type",
                                                                                                    "placeholder": "e.g. Wrench, Scanner"
                                                                                },
                                                                                {
                                                                                                    "label": "Drive / Tool Size",
                                                                                                    "key": "auto_tool_size",
                                                                                                    "placeholder": "e.g. 1/2 inch"
                                                                                },
                                                                                {
                                                                                                    "label": "Tool Material",
                                                                                                    "key": "auto_tool_material",
                                                                                                    "placeholder": "e.g. Chrome Vanadium"
                                                                                },
                                                                                {
                                                                                                    "label": "Power Source",
                                                                                                    "key": "auto_tool_power",
                                                                                                    "placeholder": "e.g. Manual, Battery"
                                                                                },
                                                                                {
                                                                                                    "label": "Set / Piece Count",
                                                                                                    "key": "auto_tool_count",
                                                                                                    "placeholder": "e.g. 24 pieces"
                                                                                }
                                                            ],
                                                            "Parts & Accessories": [
                                                                                {
                                                                                                    "label": "Part Type",
                                                                                                    "key": "auto_part_type_specific",
                                                                                                    "placeholder": "e.g. Mirror, Brake Pad"
                                                                                },
                                                                                {
                                                                                                    "label": "Placement",
                                                                                                    "key": "auto_part_placement",
                                                                                                    "placeholder": "e.g. Front Left"
                                                                                },
                                                                                {
                                                                                                    "label": "OEM / Aftermarket",
                                                                                                    "key": "auto_part_origin",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "OEM",
                                                                                                                        "Aftermarket"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Fitment",
                                                                                                    "key": "auto_part_fitment",
                                                                                                    "placeholder": "Exact fitment details"
                                                                                },
                                                                                {
                                                                                                    "label": "Installation Difficulty",
                                                                                                    "key": "auto_part_installation_difficulty",
                                                                                                    "placeholder": "e.g. Easy, Professional"
                                                                                }
                                                            ],
                                                            "Electrical Components": [
                                                                                {
                                                                                                    "label": "Component Type",
                                                                                                    "key": "auto_electrical_type",
                                                                                                    "placeholder": "e.g. Relay, LED"
                                                                                },
                                                                                {
                                                                                                    "label": "Voltage",
                                                                                                    "key": "auto_electrical_voltage",
                                                                                                    "placeholder": "e.g. 12 V"
                                                                                },
                                                                                {
                                                                                                    "label": "Current / Wattage",
                                                                                                    "key": "auto_electrical_current",
                                                                                                    "placeholder": "e.g. 5 A"
                                                                                },
                                                                                {
                                                                                                    "label": "Connector Type",
                                                                                                    "key": "auto_electrical_connector",
                                                                                                    "placeholder": "e.g. Plug Type"
                                                                                },
                                                                                {
                                                                                                    "label": "Polarity",
                                                                                                    "key": "auto_electrical_polarity",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Tires, Wheels, and Fluids": [
                                                                                {
                                                                                                    "label": "Product Type",
                                                                                                    "key": "auto_tire_fluid_type",
                                                                                                    "placeholder": "e.g. Tire, Wheel, Oil"
                                                                                },
                                                                                {
                                                                                                    "label": "Tire / Wheel Size",
                                                                                                    "key": "auto_tire_wheel_size",
                                                                                                    "placeholder": "e.g. 195/65 R15"
                                                                                },
                                                                                {
                                                                                                    "label": "Viscosity / Fluid Grade",
                                                                                                    "key": "auto_fluid_grade",
                                                                                                    "placeholder": "e.g. 5W-30"
                                                                                },
                                                                                {
                                                                                                    "label": "Volume",
                                                                                                    "key": "auto_fluid_volume",
                                                                                                    "placeholder": "e.g. 4 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Load / Speed / Performance Rating",
                                                                                                    "key": "auto_rating",
                                                                                                    "placeholder": "Applicable rating"
                                                                                }
                                                            ]
                                        }
                    },
                    "furniture-and-office-equipment": {
                                        "label": "Furniture and Office Equipment",
                                        "generalFields": [
                                                            {
                                                                                "label": "Furniture / Equipment Type",
                                                                                "key": "furniture_type",
                                                                                "placeholder": "e.g. Chair, Desk"
                                                            },
                                                            {
                                                                                "label": "Dimensions",
                                                                                "key": "furniture_dimensions",
                                                                                "placeholder": "L × W × H"
                                                            },
                                                            {
                                                                                "label": "Assembly Required",
                                                                                "key": "furniture_assembly",
                                                                                "type": "select",
                                                                                "options": [
                                                                                                    "Yes",
                                                                                                    "No"
                                                                                ]
                                                            },
                                                            {
                                                                                "label": "Weight Capacity",
                                                                                "key": "furniture_capacity",
                                                                                "placeholder": "If applicable"
                                                            },
                                                            {
                                                                                "label": "Finish",
                                                                                "key": "furniture_finish",
                                                                                "placeholder": "e.g. Matte, Wood Grain"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Office Desks & Chairs": [
                                                                                {
                                                                                                    "label": "Item Type",
                                                                                                    "key": "office_desk_chair_type",
                                                                                                    "placeholder": "e.g. Desk, Ergonomic Chair"
                                                                                },
                                                                                {
                                                                                                    "label": "Adjustable Height",
                                                                                                    "key": "office_adjustable_height",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Seat / Desk Width",
                                                                                                    "key": "office_desk_chair_width",
                                                                                                    "placeholder": "Relevant measurement"
                                                                                },
                                                                                {
                                                                                                    "label": "Ergonomic Support",
                                                                                                    "key": "office_ergonomic_support",
                                                                                                    "placeholder": "e.g. Lumbar Support"
                                                                                },
                                                                                {
                                                                                                    "label": "Caster / Base Type",
                                                                                                    "key": "office_base_type",
                                                                                                    "placeholder": "e.g. 5-Star Base"
                                                                                }
                                                            ],
                                                            "Storage Cabinets & Shelving": [
                                                                                {
                                                                                                    "label": "Storage Type",
                                                                                                    "key": "office_storage_type",
                                                                                                    "placeholder": "e.g. Cabinet, Shelf"
                                                                                },
                                                                                {
                                                                                                    "label": "Number of Shelves / Drawers",
                                                                                                    "key": "office_storage_count",
                                                                                                    "placeholder": "e.g. 4 shelves"
                                                                                },
                                                                                {
                                                                                                    "label": "Lockable",
                                                                                                    "key": "office_storage_lockable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Shelf Weight Capacity",
                                                                                                    "key": "office_storage_capacity",
                                                                                                    "placeholder": "e.g. 30 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Door Type",
                                                                                                    "key": "office_storage_door",
                                                                                                    "placeholder": "e.g. Sliding, Hinged"
                                                                                }
                                                            ],
                                                            "Conference & Meeting Furniture": [
                                                                                {
                                                                                                    "label": "Furniture Type",
                                                                                                    "key": "conference_furniture_type",
                                                                                                    "placeholder": "e.g. Conference Table"
                                                                                },
                                                                                {
                                                                                                    "label": "Seating Capacity",
                                                                                                    "key": "conference_capacity",
                                                                                                    "placeholder": "e.g. 8 persons"
                                                                                },
                                                                                {
                                                                                                    "label": "Table Shape",
                                                                                                    "key": "conference_shape",
                                                                                                    "placeholder": "e.g. Rectangular"
                                                                                },
                                                                                {
                                                                                                    "label": "Cable Management",
                                                                                                    "key": "conference_cable_management",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Modular",
                                                                                                    "key": "conference_modular",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Computer Tables & Workstations": [
                                                                                {
                                                                                                    "label": "Workstation Type",
                                                                                                    "key": "workstation_type",
                                                                                                    "placeholder": "e.g. L-Shaped Desk"
                                                                                },
                                                                                {
                                                                                                    "label": "Monitor Capacity",
                                                                                                    "key": "workstation_monitor_capacity",
                                                                                                    "placeholder": "e.g. 2 monitors"
                                                                                },
                                                                                {
                                                                                                    "label": "Cable Management",
                                                                                                    "key": "workstation_cable_management",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Keyboard Tray",
                                                                                                    "key": "workstation_keyboard_tray",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Height Adjustable",
                                                                                                    "key": "workstation_adjustable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Ergonomic Accessories": [
                                                                                {
                                                                                                    "label": "Accessory Type",
                                                                                                    "key": "ergonomic_accessory_type",
                                                                                                    "placeholder": "e.g. Footrest, Monitor Arm"
                                                                                },
                                                                                {
                                                                                                    "label": "Adjustment Type",
                                                                                                    "key": "ergonomic_adjustment",
                                                                                                    "placeholder": "e.g. Height / Tilt"
                                                                                },
                                                                                {
                                                                                                    "label": "Compatible Equipment",
                                                                                                    "key": "ergonomic_compatibility",
                                                                                                    "placeholder": "Compatible devices"
                                                                                },
                                                                                {
                                                                                                    "label": "Maximum Load",
                                                                                                    "key": "ergonomic_max_load",
                                                                                                    "placeholder": "e.g. 9 kg"
                                                                                },
                                                                                {
                                                                                                    "label": "Ergonomic Benefit",
                                                                                                    "key": "ergonomic_benefit",
                                                                                                    "placeholder": "e.g. Wrist Support"
                                                                                }
                                                            ],
                                                            "Office Lighting & Fixtures": [
                                                                                {
                                                                                                    "label": "Fixture Type",
                                                                                                    "key": "office_light_type",
                                                                                                    "placeholder": "e.g. Desk Lamp"
                                                                                },
                                                                                {
                                                                                                    "label": "Bulb / Light Source",
                                                                                                    "key": "office_light_source",
                                                                                                    "placeholder": "e.g. LED"
                                                                                },
                                                                                {
                                                                                                    "label": "Wattage",
                                                                                                    "key": "office_light_wattage",
                                                                                                    "placeholder": "e.g. 12 W"
                                                                                },
                                                                                {
                                                                                                    "label": "Color Temperature",
                                                                                                    "key": "office_light_temperature",
                                                                                                    "placeholder": "e.g. 4000K"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimmable",
                                                                                                    "key": "office_light_dimmable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ]
                                        }
                    },
                    "jewelry-and-watches": {
                                        "label": "Jewelry and Watches",
                                        "generalFields": [
                                                            {
                                                                                "label": "Jewelry / Watch Type",
                                                                                "key": "jewelry_type",
                                                                                "placeholder": "e.g. Ring, Necklace, Watch"
                                                            },
                                                            {
                                                                                "label": "Metal / Finish",
                                                                                "key": "jewelry_metal_finish",
                                                                                "placeholder": "e.g. Stainless Steel"
                                                            },
                                                            {
                                                                                "label": "Size / Length",
                                                                                "key": "jewelry_size",
                                                                                "placeholder": "Ring size, length, case size"
                                                            },
                                                            {
                                                                                "label": "Plating",
                                                                                "key": "jewelry_plating",
                                                                                "placeholder": "e.g. 18K Gold Plated"
                                                            },
                                                            {
                                                                                "label": "Care Instructions",
                                                                                "key": "jewelry_care",
                                                                                "placeholder": "Care details"
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Necklaces & Pendants": [
                                                                                {
                                                                                                    "label": "Chain Length",
                                                                                                    "key": "necklace_chain_length",
                                                                                                    "placeholder": "e.g. 45 cm"
                                                                                },
                                                                                {
                                                                                                    "label": "Pendant Size",
                                                                                                    "key": "necklace_pendant_size",
                                                                                                    "placeholder": "Pendant dimensions"
                                                                                },
                                                                                {
                                                                                                    "label": "Clasp Type",
                                                                                                    "key": "necklace_clasp",
                                                                                                    "placeholder": "e.g. Lobster Clasp"
                                                                                },
                                                                                {
                                                                                                    "label": "Gemstone / Stone",
                                                                                                    "key": "necklace_gemstone",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Adjustable Length",
                                                                                                    "key": "necklace_adjustable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Rings & Earrings": [
                                                                                {
                                                                                                    "label": "Ring / Earring Type",
                                                                                                    "key": "ring_earring_type",
                                                                                                    "placeholder": "e.g. Stud, Band"
                                                                                },
                                                                                {
                                                                                                    "label": "Ring Size",
                                                                                                    "key": "ring_size",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Stone / Setting",
                                                                                                    "key": "ring_earring_stone",
                                                                                                    "placeholder": "e.g. Cubic Zirconia"
                                                                                },
                                                                                {
                                                                                                    "label": "Earring Closure",
                                                                                                    "key": "earring_closure",
                                                                                                    "placeholder": "e.g. Push Back"
                                                                                },
                                                                                {
                                                                                                    "label": "Hypoallergenic",
                                                                                                    "key": "ring_earring_hypoallergenic",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Bracelets & Bangles": [
                                                                                {
                                                                                                    "label": "Bracelet Type",
                                                                                                    "key": "bracelet_type",
                                                                                                    "placeholder": "e.g. Chain, Bangle"
                                                                                },
                                                                                {
                                                                                                    "label": "Length / Diameter",
                                                                                                    "key": "bracelet_length",
                                                                                                    "placeholder": "e.g. 18 cm"
                                                                                },
                                                                                {
                                                                                                    "label": "Closure Type",
                                                                                                    "key": "bracelet_closure",
                                                                                                    "placeholder": "e.g. Lobster Clasp"
                                                                                },
                                                                                {
                                                                                                    "label": "Adjustable",
                                                                                                    "key": "bracelet_adjustable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Charm / Stone Details",
                                                                                                    "key": "bracelet_charm_stone",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Watches for Men & Women": [
                                                                                {
                                                                                                    "label": "Movement",
                                                                                                    "key": "watch_movement",
                                                                                                    "placeholder": "e.g. Quartz, Automatic"
                                                                                },
                                                                                {
                                                                                                    "label": "Case Size",
                                                                                                    "key": "watch_case_size",
                                                                                                    "placeholder": "e.g. 42 mm"
                                                                                },
                                                                                {
                                                                                                    "label": "Band Material",
                                                                                                    "key": "watch_band_material",
                                                                                                    "placeholder": "e.g. Leather"
                                                                                },
                                                                                {
                                                                                                    "label": "Water Resistance",
                                                                                                    "key": "watch_water_resistance",
                                                                                                    "placeholder": "e.g. 5 ATM"
                                                                                },
                                                                                {
                                                                                                    "label": "Display Type",
                                                                                                    "key": "watch_display",
                                                                                                    "placeholder": "e.g. Analog, Digital"
                                                                                }
                                                            ],
                                                            "Fashion Jewelry": [
                                                                                {
                                                                                                    "label": "Fashion Jewelry Type",
                                                                                                    "key": "fashion_jewelry_type",
                                                                                                    "placeholder": "e.g. Necklace, Ring"
                                                                                },
                                                                                {
                                                                                                    "label": "Style / Theme",
                                                                                                    "key": "fashion_jewelry_style",
                                                                                                    "placeholder": "e.g. Minimalist"
                                                                                },
                                                                                {
                                                                                                    "label": "Hypoallergenic",
                                                                                                    "key": "fashion_jewelry_hypoallergenic",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Occasion",
                                                                                                    "key": "fashion_jewelry_occasion",
                                                                                                    "placeholder": "e.g. Daily, Party"
                                                                                },
                                                                                {
                                                                                                    "label": "Stone / Decorative Material",
                                                                                                    "key": "fashion_jewelry_stone",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Jewelry Storage & Care": [
                                                                                {
                                                                                                    "label": "Storage / Care Type",
                                                                                                    "key": "jewelry_storage_type",
                                                                                                    "placeholder": "e.g. Box, Cleaner"
                                                                                },
                                                                                {
                                                                                                    "label": "Compartments",
                                                                                                    "key": "jewelry_storage_compartments",
                                                                                                    "placeholder": "e.g. 12 compartments"
                                                                                },
                                                                                {
                                                                                                    "label": "Lining Material",
                                                                                                    "key": "jewelry_storage_lining",
                                                                                                    "placeholder": "e.g. Velvet"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "jewelry_storage_capacity",
                                                                                                    "placeholder": "Number of items"
                                                                                },
                                                                                {
                                                                                                    "label": "Care Use",
                                                                                                    "key": "jewelry_storage_use",
                                                                                                    "placeholder": "e.g. Polishing, Storage"
                                                                                }
                                                            ]
                                        }
                    },
                    "office-and-school-supplies": {
                                        "label": "Office and School Supplies",
                                        "generalFields": [
                                                            {
                                                                                "label": "Product Type",
                                                                                "key": "office_school_type",
                                                                                "placeholder": "e.g. Notebook, Pen, Printer Ink"
                                                            },
                                                            {
                                                                                "label": "Item Size",
                                                                                "key": "office_school_size",
                                                                                "placeholder": "e.g. A4"
                                                            },
                                                            {
                                                                                "label": "Pack Count",
                                                                                "key": "office_school_pack_count",
                                                                                "placeholder": "e.g. 12 pieces"
                                                            },
                                                            {
                                                                                "label": "Recommended Use",
                                                                                "key": "office_school_use",
                                                                                "placeholder": "e.g. School, Office"
                                                            },
                                                            {
                                                                                "label": "Reusable / Refillable",
                                                                                "key": "office_school_reusable",
                                                                                "type": "select",
                                                                                "options": [
                                                                                                    "Yes",
                                                                                                    "No",
                                                                                                    "Not Applicable"
                                                                                ]
                                                            }
                                        ],
                                        "subcategories": {
                                                            "Notebooks & Paper Products": [
                                                                                {
                                                                                                    "label": "Paper Size",
                                                                                                    "key": "paper_size",
                                                                                                    "placeholder": "e.g. A4, Letter"
                                                                                },
                                                                                {
                                                                                                    "label": "Page Count",
                                                                                                    "key": "paper_page_count",
                                                                                                    "placeholder": "e.g. 80 pages"
                                                                                },
                                                                                {
                                                                                                    "label": "Ruling",
                                                                                                    "key": "paper_ruling",
                                                                                                    "placeholder": "e.g. Ruled, Grid, Plain"
                                                                                },
                                                                                {
                                                                                                    "label": "Paper Weight",
                                                                                                    "key": "paper_gsm",
                                                                                                    "placeholder": "e.g. 80 GSM"
                                                                                },
                                                                                {
                                                                                                    "label": "Binding",
                                                                                                    "key": "paper_binding",
                                                                                                    "placeholder": "e.g. Spiral"
                                                                                }
                                                            ],
                                                            "Writing Instruments": [
                                                                                {
                                                                                                    "label": "Instrument Type",
                                                                                                    "key": "writing_type",
                                                                                                    "placeholder": "e.g. Ballpen, Pencil"
                                                                                },
                                                                                {
                                                                                                    "label": "Ink / Lead Color",
                                                                                                    "key": "writing_color",
                                                                                                    "placeholder": "e.g. Black"
                                                                                },
                                                                                {
                                                                                                    "label": "Point / Tip Size",
                                                                                                    "key": "writing_tip_size",
                                                                                                    "placeholder": "e.g. 0.5 mm"
                                                                                },
                                                                                {
                                                                                                    "label": "Refillable",
                                                                                                    "key": "writing_refillable",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Grip Type",
                                                                                                    "key": "writing_grip",
                                                                                                    "placeholder": "e.g. Rubber Grip"
                                                                                }
                                                            ],
                                                            "Office Furniture": [
                                                                                {
                                                                                                    "label": "Furniture Type",
                                                                                                    "key": "school_office_furniture_type",
                                                                                                    "placeholder": "e.g. Desk, Chair"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimensions",
                                                                                                    "key": "school_office_furniture_dimensions",
                                                                                                    "placeholder": "L × W × H"
                                                                                },
                                                                                {
                                                                                                    "label": "Assembly Required",
                                                                                                    "key": "school_office_furniture_assembly",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                },
                                                                                {
                                                                                                    "label": "Weight Capacity",
                                                                                                    "key": "school_office_furniture_capacity",
                                                                                                    "placeholder": "If applicable"
                                                                                },
                                                                                {
                                                                                                    "label": "Ergonomic Feature",
                                                                                                    "key": "school_office_furniture_ergonomic",
                                                                                                    "placeholder": "If applicable"
                                                                                }
                                                            ],
                                                            "Printers & Printing Supplies": [
                                                                                {
                                                                                                    "label": "Supply / Printer Type",
                                                                                                    "key": "printer_supply_type",
                                                                                                    "placeholder": "e.g. Ink, Toner, Printer"
                                                                                },
                                                                                {
                                                                                                    "label": "Printer Compatibility",
                                                                                                    "key": "printer_compatibility",
                                                                                                    "placeholder": "Compatible models"
                                                                                },
                                                                                {
                                                                                                    "label": "Page Yield",
                                                                                                    "key": "printer_page_yield",
                                                                                                    "placeholder": "e.g. 1,500 pages"
                                                                                },
                                                                                {
                                                                                                    "label": "Color",
                                                                                                    "key": "printer_color",
                                                                                                    "placeholder": "e.g. Black, CMYK"
                                                                                },
                                                                                {
                                                                                                    "label": "Cartridge / Model Code",
                                                                                                    "key": "printer_model_code",
                                                                                                    "placeholder": "e.g. 680 Black"
                                                                                }
                                                            ],
                                                            "School Bags & Backpacks": [
                                                                                {
                                                                                                    "label": "Bag Type",
                                                                                                    "key": "school_bag_type",
                                                                                                    "placeholder": "e.g. Backpack, Sling Bag"
                                                                                },
                                                                                {
                                                                                                    "label": "Capacity",
                                                                                                    "key": "school_bag_capacity",
                                                                                                    "placeholder": "e.g. 25 L"
                                                                                },
                                                                                {
                                                                                                    "label": "Dimensions",
                                                                                                    "key": "school_bag_dimensions",
                                                                                                    "placeholder": "L × W × H"
                                                                                },
                                                                                {
                                                                                                    "label": "Compartments",
                                                                                                    "key": "school_bag_compartments",
                                                                                                    "placeholder": "e.g. 3 compartments"
                                                                                },
                                                                                {
                                                                                                    "label": "Water Resistant",
                                                                                                    "key": "school_bag_water_resistant",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ],
                                                            "Arts & Craft Materials": [
                                                                                {
                                                                                                    "label": "Material Type",
                                                                                                    "key": "art_material_type",
                                                                                                    "placeholder": "e.g. Paint, Marker, Paper"
                                                                                },
                                                                                {
                                                                                                    "label": "Color Count",
                                                                                                    "key": "art_color_count",
                                                                                                    "placeholder": "e.g. 24 colors"
                                                                                },
                                                                                {
                                                                                                    "label": "Medium",
                                                                                                    "key": "art_medium",
                                                                                                    "placeholder": "e.g. Acrylic, Watercolor"
                                                                                },
                                                                                {
                                                                                                    "label": "Quantity / Set Size",
                                                                                                    "key": "art_quantity",
                                                                                                    "placeholder": "e.g. 12-piece set"
                                                                                },
                                                                                {
                                                                                                    "label": "Non-Toxic",
                                                                                                    "key": "art_non_toxic",
                                                                                                    "type": "select",
                                                                                                    "options": [
                                                                                                                        "Yes",
                                                                                                                        "No"
                                                                                                    ]
                                                                                }
                                                            ]
                                        }
                    }
};


                const showingCount =
                    document.getElementById(
                        'showingCount'
                    );

                const totalEntriesCount =
                    document.getElementById(
                        'totalEntriesCount'
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

                const productDetailsName =
                    document.getElementById(
                        'productDetailsName'
                    );

                const productDetailsPrice =
                    document.getElementById(
                        'productDetailsPrice'
                    );

                const productDetailsStock =
                    document.getElementById(
                        'productDetailsStock'
                    );

                const productDetailsStatus =
                    document.getElementById(
                        'productDetailsStatus'
                    );

                const productDetailsUploaded =
                    document.getElementById(
                        'productDetailsUploaded'
                    );

                const productDetailsMainImage =
                    document.getElementById(
                        'productDetailsMainImage'
                    );

                const productDetailsImageFallback =
                    document.getElementById(
                        'productDetailsImageFallback'
                    );

                const productDetailsThumbnails =
                    document.getElementById(
                        'productDetailsThumbnails'
                    );

                const productDetailBrand =
                    document.getElementById(
                        'productDetailBrand'
                    );

                const productDetailMaterial =
                    document.getElementById(
                        'productDetailMaterial'
                    );

                const productDetailSizes =
                    document.getElementById(
                        'productDetailSizes'
                    );

                const productDetailColors =
                    document.getElementById(
                        'productDetailColors'
                    );

                const productDetailQuantity =
                    document.getElementById(
                        'productDetailQuantity'
                    );

                const productDetailCountry =
                    document.getElementById(
                        'productDetailCountry'
                    );

                const productDetailsBuyerOptionsSection =
                    document.getElementById(
                        'productDetailsBuyerOptionsSection'
                    );

                const productDetailsBuyerOptions =
                    document.getElementById(
                        'productDetailsBuyerOptions'
                    );

                const productDetailsSpecificationsSection =
                    document.getElementById(
                        'productDetailsSpecificationsSection'
                    );

                const productDetailsSpecifications =
                    document.getElementById(
                        'productDetailsSpecifications'
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

                /*
                 * Editable Product Specifications draft for a created product.
                 * The draft is saved back to createdInventoryProducts only when
                 * the seller presses Save Changes.
                 */
                let currentCreatedSpecificationDraft = {};
                let currentCreatedSpecificationLabels = {};


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

                function getCreatedProductFromRow(
                    row
                ) {

                    const productId =
                        row?.dataset?.createdProductId ||
                        '';

                    if (!productId) {
                        return null;
                    }

                    return createdInventoryProducts.find(
                        function (product) {
                            return product.id ===
                                productId;
                        }
                    ) || null;

                }


                function formatCreatedProductDate(
                    value
                ) {

                    if (!value) {
                        return '—';
                    }

                    const date =
                        new Date(
                            value
                        );

                    if (
                        Number.isNaN(
                            date.getTime()
                        )
                    ) {
                        return value;
                    }

                    return new Intl.DateTimeFormat(
                        'en-PH',
                        {
                            month:
                                'long',
                            day:
                                'numeric',
                            year:
                                'numeric',
                            hour:
                                'numeric',
                            minute:
                                '2-digit'
                        }
                    ).format(
                        date
                    );

                }


                function createdProductLabelFromKey(
                    key
                ) {

                    return String(
                        key ||
                        ''
                    )
                    .replace(
                        /^category_specifications\[/,
                        ''
                    )
                    .replace(
                        /\]$/,
                        ''
                    )
                    .replaceAll(
                        '_',
                        ' '
                    )
                    .replace(
                        /\b\w/g,
                        function (letter) {
                            return letter.toUpperCase();
                        }
                    );

                }


                function createdProductSpecificationValue(
                    product,
                    target
                ) {

                    const entries =
                        product?.specificationDisplay ||
                        [];

                    const found =
                        entries.find(
                            function (item) {
                                return item.key ===
                                    target;
                            }
                        );

                    if (found?.value) {
                        return found.value;
                    }

                    const legacy =
                        product?.specifications?.[
                            `category_specifications[${target}]`
                        ];

                    return legacy ||
                        '';

                }


                function makeCreatedDetailItem(
                    label,
                    value,
                    fullWidth = false
                ) {

                    const item =
                        document.createElement(
                            'div'
                        );

                    item.className =
                        fullWidth
                            ? 'product-created-detail-item is-full'
                            : 'product-created-detail-item';

                    const labelElement =
                        document.createElement(
                            'span'
                        );

                    labelElement.className =
                        'product-created-detail-label';

                    labelElement.textContent =
                        label;

                    const valueElement =
                        document.createElement(
                            'span'
                        );

                    valueElement.className =
                        'product-created-detail-value';

                    valueElement.textContent =
                        value ||
                        '—';

                    item.appendChild(
                        labelElement
                    );

                    item.appendChild(
                        valueElement
                    );

                    return item;

                }


                function renderCreatedProductPhotos(
                    product
                ) {

                    const photos =
                        (
                            Array.isArray(
                                product.photos
                            ) &&
                            product.photos.length
                        )
                            ? product.photos
                            : (
                                product.coverPhoto
                                    ? [
                                        product.coverPhoto
                                    ]
                                    : []
                            );

                    if (productDetailsMainImage) {

                        if (photos.length) {

                            productDetailsMainImage.src =
                                photos[0];

                            productDetailsMainImage.alt =
                                product.title ||
                                'Product image';

                            productDetailsMainImage.style.display =
                                'block';

                            if (productDetailsImageFallback) {
                                productDetailsImageFallback.style.display =
                                    'none';
                            }

                        } else {

                            productDetailsMainImage.style.display =
                                'none';

                            if (productDetailsImageFallback) {
                                productDetailsImageFallback.style.display =
                                    'flex';
                            }

                        }

                    }

                    if (!productDetailsThumbnails) {
                        return;
                    }

                    productDetailsThumbnails.innerHTML =
                        '';

                    photos.forEach(
                        function (photo, index) {

                            const thumb =
                                document.createElement(
                                    'button'
                                );

                            thumb.type =
                                'button';

                            thumb.className =
                                index === 0
                                    ? 'created-detail-thumb is-active'
                                    : 'created-detail-thumb';

                            const image =
                                document.createElement(
                                    'img'
                                );

                            image.src =
                                photo;

                            image.alt =
                                `${product.title || 'Product'} photo ${index + 1}`;

                            thumb.appendChild(
                                image
                            );

                            thumb.addEventListener(
                                'click',
                                function () {

                                    if (productDetailsMainImage) {
                                        productDetailsMainImage.src =
                                            photo;
                                    }

                                    productDetailsThumbnails
                                        .querySelectorAll(
                                            '.created-detail-thumb'
                                        )
                                        .forEach(
                                            function (item) {
                                                item.classList.remove(
                                                    'is-active'
                                                );
                                            }
                                        );

                                    thumb.classList.add(
                                        'is-active'
                                    );

                                }
                            );

                            productDetailsThumbnails.appendChild(
                                thumb
                            );

                        }
                    );

                }


                function renderCreatedBuyerOptions(
                    product
                ) {

                    if (
                        !productDetailsBuyerOptionsSection ||
                        !productDetailsBuyerOptions
                    ) {
                        return;
                    }

                    productDetailsBuyerOptions.innerHTML =
                        '';

                    const groups = [
                        {
                            label:
                                'Variations',
                            key:
                                'variations'
                        },
                        {
                            label:
                                'Colors',
                            key:
                                'colors'
                        },
                        {
                            label:
                                'Sizes',
                            key:
                                'sizes'
                        }
                    ];

                    let hasAny =
                        false;

                    groups.forEach(
                        function (group) {

                            const values =
                                Array.isArray(
                                    product[group.key]
                                )
                                    ? product[group.key]
                                    : [];

                            if (!values.length) {
                                return;
                            }

                            hasAny =
                                true;

                            const rows =
                                values.map(
                                    function (entry) {

                                        const numericPrice =
                                            Number(
                                                entry.price ||
                                                0
                                            );

                                        const priceText =
                                            product.pricingMode ===
                                                'varies'
                                                ? (
                                                    entry.priceType ===
                                                        'base'
                                                        ? formatMoney(
                                                            numericPrice
                                                        )
                                                        : `+${formatMoney(
                                                            numericPrice
                                                        )}`
                                                )
                                                : '';

                                        const photoNote =
                                            entry.photoData
                                                ? ' 📷'
                                                : '';

                                        return priceText
                                            ? `${entry.name}${photoNote} — ${priceText}`
                                            : `${entry.name}${photoNote}`;

                                    }
                                );

                            productDetailsBuyerOptions.appendChild(
                                makeCreatedDetailItem(
                                    group.label,
                                    rows.join(
                                        ', '
                                    ),
                                    true
                                )
                            );

                        }
                    );

                    productDetailsBuyerOptionsSection.classList.toggle(
                        'hidden',
                        !hasAny
                    );

                }


                function buildCreatedSpecificationDraft(
                    product
                ) {

                    const draft =
                        {};

                    const labels =
                        {};

                    const source =
                        Array.isArray(
                            product?.specificationDisplay
                        )
                            ? product.specificationDisplay
                            : [];

                    source.forEach(
                        function (item) {

                            if (!item?.key) {
                                return;
                            }

                            draft[item.key] =
                                item.value ?? '';

                            labels[item.key] =
                                item.label ||
                                createdProductLabelFromKey(
                                    item.key
                                );

                        }
                    );

                    Object.entries(
                        product?.specifications ||
                        {}
                    ).forEach(
                        function ([
                            rawKey,
                            value
                        ]) {

                            const key =
                                String(
                                    rawKey
                                )
                                .replace(
                                    /^category_specifications\[/,
                                    ''
                                )
                                .replace(
                                    /\]$/,
                                    ''
                                );

                            if (
                                draft[key] ===
                                undefined
                            ) {
                                draft[key] =
                                    value ?? '';
                            }

                            if (!labels[key]) {
                                labels[key] =
                                    createdProductLabelFromKey(
                                        key
                                    );
                            }

                        }
                    );

                    return {
                        draft,
                        labels
                    };

                }


                function makeCreatedSpecificationHeading(
                    title,
                    subtitle = ''
                ) {

                    const heading =
                        document.createElement(
                            'div'
                        );

                    heading.className =
                        'product-created-spec-heading';

                    heading.textContent =
                        title;

                    if (subtitle) {

                        const small =
                            document.createElement(
                                'small'
                            );

                        small.textContent =
                            subtitle;

                        heading.appendChild(
                            small
                        );

                    }

                    return heading;

                }


                function makeCreatedSpecificationField(
                    field,
                    value = ''
                ) {

                    const type =
                        field.type ||
                        'text';

                    const fullWidth =
                        type === 'textarea';

                    const wrapper =
                        document.createElement(
                            'div'
                        );

                    wrapper.className =
                        fullWidth
                            ? 'product-created-spec-field is-full'
                            : 'product-created-spec-field';

                    const label =
                        document.createElement(
                            'label'
                        );

                    label.htmlFor =
                        `detailsSpec_${field.key}`;

                    label.textContent =
                        field.label ||
                        createdProductLabelFromKey(
                            field.key
                        );

                    let control;

                    if (type === 'select') {

                        control =
                            document.createElement(
                                'select'
                            );

                        control.className =
                            'product-created-spec-select';

                        const empty =
                            document.createElement(
                                'option'
                            );

                        empty.value =
                            '';

                        empty.textContent =
                            `Select ${String(field.label || '').toLowerCase()}`;

                        control.appendChild(
                            empty
                        );

                        (
                            field.options ||
                            []
                        ).forEach(
                            function (optionLabel) {

                                const option =
                                    document.createElement(
                                        'option'
                                    );

                                option.value =
                                    optionLabel;

                                option.textContent =
                                    optionLabel;

                                control.appendChild(
                                    option
                                );

                            }
                        );

                    } else if (type === 'textarea') {

                        control =
                            document.createElement(
                                'textarea'
                            );

                        control.className =
                            'product-created-spec-textarea';

                        control.rows =
                            2;

                    } else {

                        control =
                            document.createElement(
                                'input'
                            );

                        control.type =
                            'text';

                        control.className =
                            'product-created-spec-input';

                    }

                    control.id =
                        `detailsSpec_${field.key}`;

                    control.dataset.createdSpecKey =
                        field.key;

                    control.dataset.createdSpecLabel =
                        field.label ||
                        createdProductLabelFromKey(
                            field.key
                        );

                    if (field.placeholder) {
                        control.placeholder =
                            field.placeholder;
                    }

                    if (type === 'readonly') {

                        control.readOnly =
                            true;

                        control.classList.add(
                            'product-created-spec-readonly'
                        );

                    }

                    control.value =
                        value ?? '';

                    const updateDraft =
                        function () {

                            currentCreatedSpecificationDraft[
                                field.key
                            ] =
                                control.value;

                            currentCreatedSpecificationLabels[
                                field.key
                            ] =
                                field.label ||
                                createdProductLabelFromKey(
                                    field.key
                                );

                        };

                    control.addEventListener(
                        'input',
                        updateDraft
                    );

                    control.addEventListener(
                        'change',
                        updateDraft
                    );

                    wrapper.appendChild(
                        label
                    );

                    wrapper.appendChild(
                        control
                    );

                    if (field.help) {

                        const help =
                            document.createElement(
                                'small'
                            );

                        help.className =
                            'product-created-spec-help';

                        help.textContent =
                            field.help;

                        wrapper.appendChild(
                            help
                        );

                    }

                    return {
                        wrapper,
                        control
                    };

                }


                function makeCreatedSubcategoryField(
                    product,
                    config
                ) {

                    const wrapper =
                        document.createElement(
                            'div'
                        );

                    wrapper.className =
                        'product-created-spec-field';

                    const label =
                        document.createElement(
                            'label'
                        );

                    label.htmlFor =
                        'detailsSpec_subcategory';

                    label.textContent =
                        'Subcategory';

                    const select =
                        document.createElement(
                            'select'
                        );

                    select.id =
                        'detailsSpec_subcategory';

                    select.className =
                        'product-created-spec-select';

                    select.dataset.createdSpecKey =
                        'subcategory';

                    select.dataset.createdSpecLabel =
                        'Subcategory';

                    const empty =
                        document.createElement(
                            'option'
                        );

                    empty.value =
                        '';

                    empty.textContent =
                        'Select subcategory';

                    select.appendChild(
                        empty
                    );

                    Object.keys(
                        config?.subcategories ||
                        {}
                    ).forEach(
                        function (subcategory) {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                subcategory;

                            option.textContent =
                                subcategory;

                            select.appendChild(
                                option
                            );

                        }
                    );

                    select.value =
                        currentCreatedSpecificationDraft
                            .subcategory ||
                        '';

                    select.addEventListener(
                        'change',
                        function () {

                            /*
                             * First collect any unsaved values from the current
                             * dynamic specification fields, then re-render so
                             * the newly selected subcategory gets its own fields.
                             */
                            collectCreatedSpecificationInputs();

                            currentCreatedSpecificationDraft
                                .subcategory =
                                select.value;

                            currentCreatedSpecificationLabels
                                .subcategory =
                                'Subcategory';

                            renderCreatedSpecifications(
                                product,
                                false
                            );

                        }
                    );

                    wrapper.appendChild(
                        label
                    );

                    wrapper.appendChild(
                        select
                    );

                    return wrapper;

                }


                function collectCreatedSpecificationInputs() {

                    if (!productDetailsSpecifications) {
                        return;
                    }

                    productDetailsSpecifications
                        .querySelectorAll(
                            '[data-created-spec-key]'
                        )
                        .forEach(
                            function (control) {

                                const key =
                                    control.dataset
                                        .createdSpecKey;

                                if (!key) {
                                    return;
                                }

                                currentCreatedSpecificationDraft[
                                    key
                                ] =
                                    control.value ?? '';

                                currentCreatedSpecificationLabels[
                                    key
                                ] =
                                    control.dataset
                                        .createdSpecLabel ||
                                    createdProductLabelFromKey(
                                        key
                                    );

                            }
                        );

                }


                function renderCreatedSpecifications(
                    product,
                    initializeDraft = true
                ) {

                    if (
                        !productDetailsSpecificationsSection ||
                        !productDetailsSpecifications
                    ) {
                        return;
                    }

                    const config =
                        productSpecificationLibrary[
                            product?.category
                        ] ||
                        null;

                    if (initializeDraft) {

                        const prepared =
                            buildCreatedSpecificationDraft(
                                product
                            );

                        currentCreatedSpecificationDraft =
                            prepared.draft;

                        currentCreatedSpecificationLabels =
                            prepared.labels;

                    }

                    productDetailsSpecifications.innerHTML =
                        '';

                    if (!config) {

                        productDetailsSpecificationsSection
                            .classList.add(
                                'hidden'
                            );

                        return;
                    }

                    productDetailsSpecificationsSection
                        .classList.remove(
                            'hidden'
                        );

                    productDetailsSpecifications.appendChild(
                        makeCreatedSpecificationHeading(
                            `General ${config.label} Specifications`,
                            'You can still edit these values before or after product approval.'
                        )
                    );

                    /*
                     * Subcategory remains editable. Changing it updates the
                     * category-specific fields shown underneath.
                     */
                    productDetailsSpecifications.appendChild(
                        makeCreatedSubcategoryField(
                            product,
                            config
                        )
                    );

                    /*
                     * Brand / Material / Quantity per Pack / Country of Origin
                     * already have editable fields in Product Details above,
                     * so they are not duplicated here.
                     */
                    const duplicateCommonKeys =
                        new Set([
                            'brand',
                            'material',
                            'quantity_per_pack',
                            'country_of_origin'
                        ]);

                    commonProductSpecificationFields.forEach(
                        function (field) {

                            if (
                                duplicateCommonKeys.has(
                                    field.key
                                )
                            ) {
                                return;
                            }

                            const result =
                                makeCreatedSpecificationField(
                                    field,
                                    currentCreatedSpecificationDraft[
                                        field.key
                                    ] ??
                                    field.value ??
                                    ''
                                );

                            productDetailsSpecifications.appendChild(
                                result.wrapper
                            );

                        }
                    );

                    (
                        config.generalFields ||
                        []
                    ).forEach(
                        function (field) {

                            const result =
                                makeCreatedSpecificationField(
                                    field,
                                    currentCreatedSpecificationDraft[
                                        field.key
                                    ] ??
                                    ''
                                );

                            productDetailsSpecifications.appendChild(
                                result.wrapper
                            );

                        }
                    );

                    const selectedSubcategory =
                        currentCreatedSpecificationDraft
                            .subcategory ||
                        '';

                    const subcategoryFields =
                        config.subcategories?.[
                            selectedSubcategory
                        ] ||
                        [];

                    if (
                        selectedSubcategory &&
                        subcategoryFields.length
                    ) {

                        productDetailsSpecifications.appendChild(
                            makeCreatedSpecificationHeading(
                                `${selectedSubcategory} Specifications`,
                                'These fields are specific to the selected subcategory.'
                            )
                        );

                        subcategoryFields.forEach(
                            function (field) {

                                const result =
                                    makeCreatedSpecificationField(
                                        field,
                                        currentCreatedSpecificationDraft[
                                            field.key
                                        ] ??
                                        ''
                                    );

                                productDetailsSpecifications.appendChild(
                                    result.wrapper
                                );

                            }
                        );

                    }

                }


                function saveCreatedProductSpecificationChanges(
                    product
                ) {

                    if (!product) {
                        return;
                    }

                    collectCreatedSpecificationInputs();

                    /*
                     * Sync the editable common Product Details fields into the
                     * same specification model used by Create Product.
                     */
                    currentCreatedSpecificationDraft.brand =
                        productDetailBrand?.value?.trim() ||
                        '';

                    currentCreatedSpecificationLabels.brand =
                        'Brand';

                    currentCreatedSpecificationDraft.material =
                        productDetailMaterial?.value?.trim() ||
                        '';

                    currentCreatedSpecificationLabels.material =
                        'Material';

                    currentCreatedSpecificationDraft
                        .quantity_per_pack =
                        productDetailQuantity?.value?.trim() ||
                        '';

                    currentCreatedSpecificationLabels
                        .quantity_per_pack =
                        'Quantity per Pack';

                    currentCreatedSpecificationDraft
                        .country_of_origin =
                        productDetailCountry?.value?.trim() ||
                        '';

                    currentCreatedSpecificationLabels
                        .country_of_origin =
                        'Country of Origin';

                    /*
                     * Keep category and stock as system values rather than
                     * seller-editable specification fields.
                     */
                    currentCreatedSpecificationDraft
                        .selected_category =
                        product.categoryLabel ||
                        productSpecificationLibrary[
                            product.category
                        ]?.label ||
                        product.category ||
                        '';

                    currentCreatedSpecificationLabels
                        .selected_category =
                        'Category';

                    currentCreatedSpecificationDraft
                        .stock_display =
                        String(
                            product.stock ??
                            0
                        );

                    currentCreatedSpecificationLabels
                        .stock_display =
                        'Stock';

                    const specificationDisplay =
                        Object.entries(
                            currentCreatedSpecificationDraft
                        )
                        .filter(
                            function ([
                                key,
                                value
                            ]) {

                                return (
                                    key &&
                                    value !== '' &&
                                    value !== null &&
                                    value !== undefined
                                );

                            }
                        )
                        .map(
                            function ([
                                key,
                                value
                            ]) {

                                return {
                                    key,
                                    label:
                                        currentCreatedSpecificationLabels[
                                            key
                                        ] ||
                                        createdProductLabelFromKey(
                                            key
                                        ),
                                    value:
                                        String(
                                            value
                                        )
                                };

                            }
                        );

                    const specifications =
                        {};

                    specificationDisplay.forEach(
                        function (item) {

                            specifications[
                                `category_specifications[${item.key}]`
                            ] =
                                item.value;

                        }
                    );

                    product.specificationDisplay =
                        specificationDisplay;

                    product.specifications =
                        specifications;

                    product.description =
                        productDescription?.value?.trim() ||
                        '';

                    /*
                     * Product Details already exposes Colors and Sizes as
                     * editable comma-separated fields. Keep those edits too
                     * while retaining their previous price values where names
                     * still match.
                     */
                    const syncNamedOptions =
                        function (
                            rawValue,
                            existing,
                            group
                        ) {

                            const names =
                                String(
                                    rawValue ||
                                    ''
                                )
                                .split(',')
                                .map(
                                    function (value) {
                                        return value.trim();
                                    }
                                )
                                .filter(Boolean);

                            return names.map(
                                function (name) {

                                    const previous =
                                        (
                                            existing ||
                                            []
                                        )
                                        .find(
                                            function (item) {
                                                return (
                                                    String(
                                                        item.name ||
                                                        ''
                                                    ).toLowerCase() ===
                                                    name.toLowerCase()
                                                );
                                            }
                                        );

                                    return {
                                        name,
                                        price:
                                            previous?.price ||
                                            0,
                                        priceType:
                                            product.pricingMode ===
                                                'varies' &&
                                            product.pricingSource ===
                                                group
                                                ? 'base'
                                                : 'addon'
                                    };

                                }
                            );

                        };

                    product.colors =
                        syncNamedOptions(
                            productDetailColors?.value,
                            product.colors,
                            'colors'
                        );

                    product.sizes =
                        syncNamedOptions(
                            productDetailSizes?.value,
                            product.sizes,
                            'sizes'
                        );

                    saveCreatedProducts();

                }


                function updateProductDetailsModal(row) {

                    if (!row) {
                        return;
                    }

                    const createdProduct =
                        getCreatedProductFromRow(
                            row
                        );

                    const isPolicy =
                        row.dataset.policy === 'true' ||
                        row.classList.contains(
                            'policy-product-clickable'
                        );


                    /*
                     * CREATED PRODUCT
                     * Use the exact values entered in Create Product.
                     */
                    if (createdProduct) {

                        const category =
                            createdProduct.categoryLabel ||
                            productSpecificationLibrary[
                                createdProduct.category
                            ]?.label ||
                            createdProduct.category ||
                            '—';

                        if (productDetailsName) {
                            productDetailsName.textContent =
                                createdProduct.title ||
                                'Product';
                        }

                        if (productDetailsPrice) {
                            productDetailsPrice.textContent =
                                calculateCreatedProductPrice(
                                    createdProduct
                                );
                        }

                        if (productDetailsStock) {
                            productDetailsStock.textContent =
                                `${createdProduct.stock ?? 0} pieces`;
                        }

                        if (productDetailsStatus) {
                            productDetailsStatus.textContent =
                                'Pending';

                            productDetailsStatus.className =
                                'status-badge status-pending mt-[6px]';
                        }

                        if (productDetailsUploaded) {
                            productDetailsUploaded.textContent =
                                formatCreatedProductDate(
                                    createdProduct.createdAt
                                );
                        }

                        if (productDetailsCategory) {

                            productDetailsCategory.textContent =
                                category;

                            productDetailsCategory.className =
                                `inline-flex items-center mt-[7px] rounded-full px-[13px] py-[4px] text-[11px] font-medium category-badge ${categoryBadgeClass(category)}`;

                        }

                        if (productDescription) {
                            productDescription.value =
                                createdProduct.description ||
                                '';
                        }

                        if (productDetailBrand) {
                            productDetailBrand.value =
                                createdProductSpecificationValue(
                                    createdProduct,
                                    'brand'
                                );
                        }

                        if (productDetailMaterial) {
                            productDetailMaterial.value =
                                createdProductSpecificationValue(
                                    createdProduct,
                                    'material'
                                );
                        }

                        if (productDetailSizes) {
                            productDetailSizes.value =
                                (
                                    createdProduct.sizes ||
                                    []
                                )
                                .map(
                                    function (item) {
                                        return item.name;
                                    }
                                )
                                .join(
                                    ', '
                                );
                        }

                        if (productDetailColors) {
                            productDetailColors.value =
                                (
                                    createdProduct.colors ||
                                    []
                                )
                                .map(
                                    function (item) {
                                        return item.name;
                                    }
                                )
                                .join(
                                    ', '
                                );
                        }

                        if (productDetailQuantity) {
                            productDetailQuantity.value =
                                createdProductSpecificationValue(
                                    createdProduct,
                                    'quantity_per_pack'
                                );
                        }

                        if (productDetailCountry) {
                            productDetailCountry.value =
                                createdProductSpecificationValue(
                                    createdProduct,
                                    'country_of_origin'
                                );
                        }

                        renderCreatedProductPhotos(
                            createdProduct
                        );

                        renderCreatedBuyerOptions(
                            createdProduct
                        );

                        renderCreatedSpecifications(
                            createdProduct
                        );

                        productPolicyWarning?.classList.add(
                            'hidden'
                        );

                        updateDescriptionCount();

                        return;

                    }


                    /*
                     * EXISTING DEMO PRODUCT
                     * Keep the existing behavior, but update its summary
                     * from the clicked table row where possible.
                     */
                    const rowCategoryBadge =
                        row.querySelector(
                            '.category-badge'
                        );

                    const category =
                        rowCategoryBadge?.textContent?.trim() ||
                        '—';

                    const rowNumbers =
                        row.querySelectorAll(
                            '.product-number'
                        );

                    if (productDetailsName) {
                        productDetailsName.textContent =
                            row.dataset.name ||
                            'Product';
                    }

                    if (productDetailsPrice) {
                        productDetailsPrice.textContent =
                            rowNumbers[0]?.textContent?.trim() ||
                            '—';
                    }

                    if (productDetailsStock) {
                        productDetailsStock.textContent =
                            `${rowNumbers[1]?.textContent?.trim() || '0'} pieces`;
                    }

                    if (productDetailsStatus) {

                        const statusBadge =
                            row.querySelector(
                                '.status-badge'
                            );

                        const statusText =
                            statusBadge?.textContent?.trim() ||
                            '—';

                        productDetailsStatus.textContent =
                            statusText;

                        productDetailsStatus.className =
                            `status-badge mt-[6px] ${
                                row.dataset.status === 'low-stock'
                                    ? 'status-low-stock'
                                    : row.dataset.status === 'out-of-stock'
                                        ? 'status-out-stock'
                                        : row.dataset.status === 'pending'
                                            ? 'status-pending'
                                            : 'status-in-stock'
                            }`;

                    }

                    if (productDetailsCategory) {

                        productDetailsCategory.textContent =
                            category;

                        productDetailsCategory.className =
                            `inline-flex items-center mt-[7px] rounded-full px-[13px] py-[4px] text-[11px] font-medium category-badge ${categoryBadgeClass(category)}`;

                    }

                    productDetailsBuyerOptionsSection?.classList.add(
                        'hidden'
                    );

                    productDetailsSpecificationsSection?.classList.add(
                        'hidden'
                    );

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

                    updateDescriptionCount();

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

                function bindProductRow(
                    row
                ) {

                    if (
                        !row ||
                        row.dataset.productRowBound ===
                            'true'
                    ) {
                        return;
                    }

                    row.dataset.productRowBound =
                        'true';

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

                            openProductDetails(
                                row
                            );

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

                                openProductDetails(
                                    row
                                );

                            }

                        }
                    );

                }


                productRows.forEach(
                    bindProductRow
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

                        const createdProduct =
                            getCreatedProductFromRow(
                                currentProductRow
                            );

                        saveProductChanges.disabled =
                            true;

                        saveProductChanges.textContent =
                            'Saving...';

                        /*
                         * Created products are truly editable in the current
                         * Inventory UI. Save the Product Specifications,
                         * Product Description, Colors, Sizes, and common
                         * Product Details back to local persistence.
                         */
                        if (createdProduct) {

                            saveCreatedProductSpecificationChanges(
                                createdProduct
                            );

                            /*
                             * Re-render the dynamic Product Specifications
                             * immediately from the values that were saved.
                             */
                            renderCreatedSpecifications(
                                createdProduct,
                                true
                            );

                        }

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

                                                if (
                                                    typeof showInventoryFlash ===
                                                    'function'
                                                ) {

                                                    showInventoryFlash(
                                                        createdProduct
                                                            ? 'Product details and specifications updated successfully.'
                                                            : 'Product changes saved successfully.'
                                                    );

                                                } else {

                                                    window.alert(
                                                        createdProduct
                                                            ? 'Product details and specifications updated successfully.'
                                                            : 'Product changes saved successfully.'
                                                    );

                                                }

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

                    if (
                        row.dataset.createdProductId
                    ) {

                        removeCreatedProductFromStorage(
                            row.dataset.createdProductId
                        );

                    }

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
                   CREATED PRODUCT LIST
                   Front-end persistence for the current Inventory UI.
                ================================================== */

                const createdProductsStorageKey =
                    'shopease_seller_inventory_created_products_v1';


                function loadCreatedProducts() {

                    try {

                        const stored =
                            JSON.parse(
                                localStorage.getItem(
                                    createdProductsStorageKey
                                ) ||
                                '[]'
                            );

                        return Array.isArray(
                            stored
                        )
                            ? stored
                            : [];

                    } catch (error) {

                        console.warn(
                            'Unable to read created products:',
                            error
                        );

                        return [];

                    }

                }


                let createdInventoryProducts =
                    loadCreatedProducts();


                function saveCreatedProducts() {

                    try {

                        localStorage.setItem(
                            createdProductsStorageKey,
                            JSON.stringify(
                                createdInventoryProducts
                            )
                        );

                    } catch (error) {

                        console.warn(
                            'Unable to save created products locally:',
                            error
                        );

                    }

                }


                function updateInventoryTotalCount() {

                    if (!totalEntriesCount) {
                        return;
                    }

                    const baseTotal =
                        Number(
                            totalEntriesCount.dataset.baseTotal ||
                            378
                        );

                    totalEntriesCount.textContent =
                        baseTotal +
                        createdInventoryProducts.length;

                }


                function removeCreatedProductFromStorage(
                    productId
                ) {

                    if (!productId) {
                        return;
                    }

                    createdInventoryProducts =
                        createdInventoryProducts.filter(
                            function (product) {
                                return product.id !==
                                    productId;
                            }
                        );

                    saveCreatedProducts();
                    updateInventoryTotalCount();

                }


                function formatMoney(
                    value
                ) {

                    return new Intl.NumberFormat(
                        'en-PH',
                        {
                            style:
                                'currency',

                            currency:
                                'PHP',

                            minimumFractionDigits:
                                2,

                            maximumFractionDigits:
                                2
                        }
                    ).format(
                        Number(
                            value ||
                            0
                        )
                    );

                }


                function calculateCreatedProductPrice(
                    product
                ) {

                    if (
                        product.pricingMode !==
                        'varies'
                    ) {
                        return formatMoney(
                            product.basePrice
                        );
                    }

                    const groups = {
                        variations:
                            product.variations ||
                            [],

                        colors:
                            product.colors ||
                            [],

                        sizes:
                            product.sizes ||
                            []
                    };

                    const primary =
                        groups[
                            product.pricingSource
                        ] ||
                        [];

                    if (!primary.length) {
                        return 'Price varies';
                    }

                    const basePrices =
                        primary.map(
                            function (item) {
                                return Number(
                                    item.price ||
                                    0
                                );
                            }
                        );

                    let minimum =
                        Math.min(
                            ...basePrices
                        );

                    let maximum =
                        Math.max(
                            ...basePrices
                        );

                    Object.entries(
                        groups
                    ).forEach(
                        function ([
                            group,
                            items
                        ]) {

                            if (
                                group ===
                                    product.pricingSource ||
                                !items.length
                            ) {
                                return;
                            }

                            const additions =
                                items.map(
                                    function (item) {
                                        return Number(
                                            item.price ||
                                            0
                                        );
                                    }
                                );

                            minimum +=
                                Math.min(
                                    ...additions
                                );

                            maximum +=
                                Math.max(
                                    ...additions
                                );

                        }
                    );

                    if (
                        Math.abs(
                            maximum -
                            minimum
                        ) <
                        0.005
                    ) {
                        return formatMoney(
                            minimum
                        );
                    }

                    return `${formatMoney(minimum)} – ${formatMoney(maximum)}`;

                }


                function getCreatedProductStatus(
                    stock
                ) {

                    /*
                     * Every newly created product starts as Pending.
                     * Stock is retained, but the listing does not become
                     * In Stock / Low Stock / Out of Stock until approval.
                     */
                    return {
                        slug:
                            'pending',

                        label:
                            'Pending',

                        className:
                            'status-pending'
                    };

                }


                function buildCreatedProductRow(
                    product,
                    animate = false
                ) {

                    if (!allTable) {
                        return null;
                    }

                    const status =
                        getCreatedProductStatus(
                            product.stock
                        );

                    const categoryLabel =
                        product.categoryLabel ||
                        productSpecificationLibrary[
                            product.category
                        ]?.label ||
                        product.category ||
                        '—';

                    const row =
                        document.createElement(
                            'article'
                        );

                    row.className =
                        [
                            'inventory-row',
                            'product-clickable',
                            'grid',
                            'grid-cols-[2.3fr_1.65fr_1fr_0.9fr_1fr_0.18fr]',
                            'items-center',
                            'min-h-[90px]',
                            'px-[20px]',
                            'border-b',
                            'border-[#DDD9D7]',
                            'transition-all',
                            'duration-200',
                            'hover:bg-[#FFFBF9]',
                            animate
                                ? 'inventory-new-product-row'
                                : ''
                        ]
                        .filter(
                            Boolean
                        )
                        .join(
                            ' '
                        );

                    row.dataset.name =
                        product.title ||
                        'New Product';

                    row.dataset.category =
                        product.category ||
                        '';

                    row.dataset.status =
                        status.slug;

                    row.dataset.createdProductId =
                        product.id ||
                        '';

                    row.tabIndex =
                        0;

                    row.setAttribute(
                        'role',
                        'button'
                    );

                    const coverMarkup =
                        product.coverPhoto
                            ? `
                                <img
                                    src="${escapeHtml(product.coverPhoto)}"
                                    alt="${escapeHtml(product.title || 'Product')}"
                                    class="created-product-cover"
                                >
                            `
                            : `
                                <div class="product-bag"></div>
                            `;

                    row.innerHTML = `
                        <div class="product-main">

                            <div class="product-thumb">
                                ${coverMarkup}
                            </div>

                            <div class="min-w-0">

                                <h3 class="product-name">
                                    ${escapeHtml(product.title || 'New Product')}
                                </h3>

                                <p class="product-sold">
                                    0 sold
                                </p>

                            </div>

                        </div>

                        <div>
                            <span class="category-badge ${categoryBadgeClass(categoryLabel)}">
                                ${escapeHtml(categoryLabel)}
                            </span>
                        </div>

                        <div class="product-number">
                            ${escapeHtml(calculateCreatedProductPrice(product))}
                        </div>

                        <div class="product-number">
                            ${escapeHtml(product.stock ?? 0)}
                        </div>

                        <div>
                            <span class="status-badge ${status.className}">
                                ${status.label}
                            </span>
                        </div>

                        <div></div>
                    `;

                    bindProductRow(
                        row
                    );

                    return row;

                }


                function insertCreatedProductRow(
                    product,
                    animate = true
                ) {

                    const row =
                        buildCreatedProductRow(
                            product,
                            animate
                        );

                    if (!row) {
                        return;
                    }

                    allTable.prepend(
                        row
                    );

                    activeTab =
                        'all';

                    tabs.forEach(
                        function (tab) {

                            tab.classList.toggle(
                                'active',
                                tab.dataset.tab ===
                                    'all'
                            );

                        }
                    );

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

                    updateTabView();
                    filterProducts();

                }


                function restoreCreatedProductRows() {

                    createdInventoryProducts
                        .slice()
                        .reverse()
                        .forEach(
                            function (product) {

                                const row =
                                    buildCreatedProductRow(
                                        product,
                                        false
                                    );

                                if (row) {
                                    allTable.prepend(
                                        row
                                    );
                                }

                            }
                        );

                    updateInventoryTotalCount();
                    filterProducts();

                }


                function createCompactCoverDataUrl(
                    file
                ) {

                    return new Promise(
                        function (resolve) {

                            if (!file) {
                                resolve('');
                                return;
                            }

                            const reader =
                                new FileReader();

                            reader.onload =
                                function () {

                                    const image =
                                        new Image();

                                    image.onload =
                                        function () {

                                            const maxSize =
                                                320;

                                            const ratio =
                                                Math.min(
                                                    1,
                                                    maxSize /
                                                    Math.max(
                                                        image.width,
                                                        image.height
                                                    )
                                                );

                                            const canvas =
                                                document.createElement(
                                                    'canvas'
                                                );

                                            canvas.width =
                                                Math.max(
                                                    1,
                                                    Math.round(
                                                        image.width *
                                                        ratio
                                                    )
                                                );

                                            canvas.height =
                                                Math.max(
                                                    1,
                                                    Math.round(
                                                        image.height *
                                                        ratio
                                                    )
                                                );

                                            const context =
                                                canvas.getContext(
                                                    '2d'
                                                );

                                            if (!context) {
                                                resolve('');
                                                return;
                                            }

                                            context.drawImage(
                                                image,
                                                0,
                                                0,
                                                canvas.width,
                                                canvas.height
                                            );

                                            resolve(
                                                canvas.toDataURL(
                                                    'image/jpeg',
                                                    0.72
                                                )
                                            );

                                        };

                                    image.onerror =
                                        function () {
                                            resolve('');
                                        };

                                    image.src =
                                        reader.result;

                                };

                            reader.onerror =
                                function () {
                                    resolve('');
                                };

                            reader.readAsDataURL(
                                file
                            );

                        }
                    );

                }


                restoreCreatedProductRows();


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
                   CREATE PRODUCT MODAL
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

                const productPhotosInput =
                    document.getElementById(
                        'productPhotosInput'
                    );

                const productPhotosDropZone =
                    document.getElementById(
                        'productPhotosDropZone'
                    );

                const productPhotoPreviewGrid =
                    document.getElementById(
                        'productPhotoPreviewGrid'
                    );

                const productPhotoCount =
                    document.getElementById(
                        'productPhotoCount'
                    );

                const productVariationEntry =
                    document.getElementById(
                        'productVariationEntry'
                    );

                const productVariationPhotoInput =
                    document.getElementById(
                        'productVariationPhotoInput'
                    );

                const variationPhotoPickerLabel =
                    document.getElementById(
                        'variationPhotoPickerLabel'
                    );

                const variationPhotoPickerText =
                    document.getElementById(
                        'variationPhotoPickerText'
                    );

                const addProductVariationButton =
                    document.getElementById(
                        'addProductVariationButton'
                    );

                const productVariationsContainer =
                    document.getElementById(
                        'productVariationsContainer'
                    );

                const productColorEntry =
                    document.getElementById(
                        'productColorEntry'
                    );

                const addProductColorButton =
                    document.getElementById(
                        'addProductColorButton'
                    );

                const productColorsContainer =
                    document.getElementById(
                        'productColorsContainer'
                    );

                const productSizeEntry =
                    document.getElementById(
                        'productSizeEntry'
                    );

                const addProductSizeButton =
                    document.getElementById(
                        'addProductSizeButton'
                    );

                const productSizesContainer =
                    document.getElementById(
                        'productSizesContainer'
                    );

                const productSpecificationsSection =
                    document.getElementById(
                        'productSpecificationsSection'
                    );

                const categorySpecificationsTitle =
                    document.getElementById(
                        'categorySpecificationsTitle'
                    );

                const categorySpecificationsFields =
                    document.getElementById(
                        'categorySpecificationsFields'
                    );

                const createProductStock =
                    document.getElementById(
                        'createProductStock'
                    );

                const createProductPrice =
                    document.getElementById(
                        'createProductPrice'
                    );

                const productPricingMode =
                    document.getElementById(
                        'productPricingMode'
                    );

                const fixedPricePanel =
                    document.getElementById(
                        'fixedPricePanel'
                    );

                const variablePricePanel =
                    document.getElementById(
                        'variablePricePanel'
                    );

                const variablePricingSetup =
                    document.getElementById(
                        'variablePricingSetup'
                    );

                const productPricingSource =
                    document.getElementById(
                        'productPricingSource'
                    );

                const pricingModeButtons =
                    Array.from(
                        document.querySelectorAll(
                            '.create-pricing-mode-button[data-pricing-mode]'
                        )
                    );

                const pricingSourceButtons =
                    Array.from(
                        document.querySelectorAll(
                            '.create-pricing-source-button[data-pricing-source]'
                        )
                    );

                const variablePricingRule =
                    document.getElementById(
                        'variablePricingRule'
                    );

                const createProductDescription =
                    document.getElementById(
                        'createProductDescription'
                    );

                const createProductDescriptionCount =
                    document.getElementById(
                        'createProductDescriptionCount'
                    );


                let selectedProductPhotos = [];
                let selectedVariationPhoto = null;
                let selectedVariationPhotoUrl = null;
                let productVariations = [];
                let productColors = [];
                let productSizes = [];
                let pricingMode = 'fixed';
                let pricingSource = '';


                /* =================================================
                   PRODUCT PHOTOS
                ================================================== */

                function syncProductPhotoInput() {

                    if (
                        !productPhotosInput ||
                        typeof DataTransfer === 'undefined'
                    ) {
                        return;
                    }

                    const transfer =
                        new DataTransfer();

                    selectedProductPhotos.forEach(
                        function (entry) {
                            transfer.items.add(
                                entry.file
                            );
                        }
                    );

                    productPhotosInput.files =
                        transfer.files;

                }


                function revokeProductPhotoUrls() {

                    selectedProductPhotos.forEach(
                        function (entry) {
                            if (entry.url) {
                                URL.revokeObjectURL(
                                    entry.url
                                );
                            }
                        }
                    );

                }


                function renderProductPhotos() {

                    if (
                        !productPhotoPreviewGrid ||
                        !productPhotoCount
                    ) {
                        return;
                    }

                    productPhotoCount.textContent =
                        `${selectedProductPhotos.length} ${selectedProductPhotos.length === 1 ? 'photo' : 'photos'}`;

                    productPhotoPreviewGrid.innerHTML =
                        '';

                    productPhotoPreviewGrid.classList.toggle(
                        'hidden',
                        selectedProductPhotos.length === 0
                    );

                    selectedProductPhotos.forEach(
                        function (entry, index) {

                            const item =
                                document.createElement(
                                    'div'
                                );

                            item.className =
                                'create-product-photo';

                            const image =
                                document.createElement(
                                    'img'
                                );

                            image.src =
                                entry.url;

                            image.alt =
                                `Product photo ${index + 1}`;

                            const remove =
                                document.createElement(
                                    'button'
                                );

                            remove.type =
                                'button';

                            remove.className =
                                'create-product-photo-remove';

                            remove.setAttribute(
                                'aria-label',
                                `Remove product photo ${index + 1}`
                            );

                            remove.textContent =
                                '×';

                            remove.addEventListener(
                                'click',
                                function () {

                                    URL.revokeObjectURL(
                                        entry.url
                                    );

                                    selectedProductPhotos.splice(
                                        index,
                                        1
                                    );

                                    syncProductPhotoInput();
                                    renderProductPhotos();

                                }
                            );

                            item.appendChild(
                                image
                            );

                            item.appendChild(
                                remove
                            );

                            if (index === 0) {

                                const cover =
                                    document.createElement(
                                        'span'
                                    );

                                cover.className =
                                    'create-product-photo-cover';

                                cover.textContent =
                                    'Cover';

                                item.appendChild(
                                    cover
                                );

                            }

                            productPhotoPreviewGrid.appendChild(
                                item
                            );

                        }
                    );

                }


                function addProductPhotoFiles(fileList) {

                    const incoming =
                        Array.from(
                            fileList || []
                        );

                    const valid =
                        incoming.filter(
                            function (file) {
                                return [
                                    'image/jpeg',
                                    'image/png',
                                    'image/webp'
                                ].includes(
                                    file.type
                                );
                            }
                        );

                    valid.forEach(
                        function (file) {

                            selectedProductPhotos.push({
                                file,
                                url:
                                    URL.createObjectURL(
                                        file
                                    )
                            });

                        }
                    );

                    syncProductPhotoInput();
                    renderProductPhotos();

                }


                productPhotosInput?.addEventListener(
                    'change',
                    function () {
                        addProductPhotoFiles(
                            productPhotosInput.files
                        );
                    }
                );


                productPhotosDropZone?.addEventListener(
                    'dragover',
                    function (event) {

                        event.preventDefault();

                        productPhotosDropZone.classList.add(
                            'is-dragging'
                        );

                    }
                );


                productPhotosDropZone?.addEventListener(
                    'dragleave',
                    function () {
                        productPhotosDropZone.classList.remove(
                            'is-dragging'
                        );
                    }
                );


                productPhotosDropZone?.addEventListener(
                    'drop',
                    function (event) {

                        event.preventDefault();

                        productPhotosDropZone.classList.remove(
                            'is-dragging'
                        );

                        addProductPhotoFiles(
                            event.dataTransfer?.files
                        );

                    }
                );


                /* =================================================
                   PRODUCT PRICING
                ================================================== */

                function getPricingGroupItems(group) {
                    if (group === 'variations') return productVariations;
                    if (group === 'colors') return productColors;
                    if (group === 'sizes') return productSizes;
                    return [];
                }

                function getPricingGroupLabel(group) {
                    if (group === 'variations') return 'Variations';
                    if (group === 'colors') return 'Colors';
                    if (group === 'sizes') return 'Sizes';
                    return '';
                }

                function findFirstAvailablePricingSource() {
                    return ['variations', 'colors', 'sizes'].find(
                        function (group) {
                            return getPricingGroupItems(group).length > 0;
                        }
                    ) || '';
                }

                function setPricingSource(source, rerender = true) {
                    pricingSource = source || '';

                    if (productPricingSource) {
                        productPricingSource.value = pricingSource;
                    }

                    pricingSourceButtons.forEach(
                        function (button) {
                            button.classList.toggle(
                                'is-selected',
                                button.dataset.pricingSource === pricingSource
                            );
                        }
                    );

                    if (variablePricingRule) {
                        variablePricingRule.textContent =
                            pricingSource
                                ? `${getPricingGroupLabel(pricingSource)} use actual item prices. Other buyer options add extra charges (+₱).`
                                : 'Select which buyer option carries the actual item price. Other groups will use additional price (+₱).';
                    }

                    if (rerender) {
                        renderAllBuyerOptions();
                    }
                }

                function ensurePricingSource(preferredGroup = '') {
                    if (pricingMode !== 'varies') return;

                    if (
                        pricingSource &&
                        getPricingGroupItems(pricingSource).length > 0
                    ) {
                        return;
                    }

                    if (
                        preferredGroup &&
                        getPricingGroupItems(preferredGroup).length > 0
                    ) {
                        setPricingSource(preferredGroup, false);
                        return;
                    }

                    setPricingSource(
                        findFirstAvailablePricingSource(),
                        false
                    );
                }

                function getOptionPriceMeta(group) {
                    if (pricingMode !== 'varies') {
                        return null;
                    }

                    const isPrimary =
                        pricingSource === group;

                    return {
                        isPrimary,
                        label:
                            isPrimary
                                ? 'Item Price'
                                : 'Additional',
                        prefix:
                            isPrimary
                                ? '₱'
                                : '+₱',
                        placeholder:
                            isPrimary
                                ? 'Price'
                                : '0.00'
                    };
                }

                function createOptionPriceControl(group, item) {
                    const meta =
                        getOptionPriceMeta(group);

                    if (!meta) {
                        return null;
                    }

                    const wrap =
                        document.createElement('div');

                    wrap.className =
                        'create-option-price-wrap';

                    const label =
                        document.createElement('small');

                    label.className =
                        'create-option-price-label';

                    label.textContent =
                        meta.label;

                    const box =
                        document.createElement('div');

                    box.className =
                        'create-option-price-box';

                    const prefix =
                        document.createElement('span');

                    prefix.textContent =
                        meta.prefix;

                    const input =
                        document.createElement('input');

                    input.type =
                        'number';

                    input.min =
                        '0';

                    input.step =
                        '0.01';

                    input.placeholder =
                        meta.placeholder;

                    input.className =
                        'create-option-price-input';

                    if (meta.isPrimary) {
                        input.classList.add(
                            'is-primary-price'
                        );
                        input.required = true;
                    }

                    input.value =
                        item.price ?? '';

                    input.addEventListener(
                        'input',
                        function () {
                            item.price =
                                input.value;
                        }
                    );

                    box.appendChild(prefix);
                    box.appendChild(input);

                    wrap.appendChild(label);
                    wrap.appendChild(box);

                    return wrap;
                }

                function renderAllBuyerOptions() {
                    renderVariations();

                    renderOptionChips(
                        productColorsContainer,
                        productColors,
                        'colors'
                    );

                    renderOptionChips(
                        productSizesContainer,
                        productSizes,
                        'sizes'
                    );
                }

                function applyPricingMode(mode) {
                    pricingMode =
                        mode === 'varies'
                            ? 'varies'
                            : 'fixed';

                    if (productPricingMode) {
                        productPricingMode.value =
                            pricingMode;
                    }

                    pricingModeButtons.forEach(
                        function (button) {
                            button.classList.toggle(
                                'is-selected',
                                button.dataset.pricingMode === pricingMode
                            );
                        }
                    );

                    const isVariable =
                        pricingMode === 'varies';

                    fixedPricePanel?.classList.toggle(
                        'hidden',
                        isVariable
                    );

                    variablePricePanel?.classList.toggle(
                        'hidden',
                        !isVariable
                    );

                    variablePricingSetup?.classList.toggle(
                        'hidden',
                        !isVariable
                    );

                    if (createProductPrice) {
                        createProductPrice.disabled =
                            isVariable;

                        createProductPrice.required =
                            !isVariable;

                        if (isVariable) {
                            createProductPrice.value =
                                '';
                        }
                    }

                    if (isVariable) {
                        ensurePricingSource();
                    } else {
                        setPricingSource('', false);
                    }

                    renderAllBuyerOptions();
                }

                pricingModeButtons.forEach(
                    function (button) {
                        button.addEventListener(
                            'click',
                            function () {
                                applyPricingMode(
                                    button.dataset.pricingMode
                                );
                            }
                        );
                    }
                );

                pricingSourceButtons.forEach(
                    function (button) {
                        button.addEventListener(
                            'click',
                            function () {
                                if (pricingMode !== 'varies') {
                                    return;
                                }

                                setPricingSource(
                                    button.dataset.pricingSource
                                );
                            }
                        );
                    }
                );


                /* =================================================
                   VARIATIONS — CHIP STYLE + OPTIONAL PHOTO
                ================================================== */

                function clearPendingVariationPhoto() {

                    if (selectedVariationPhotoUrl) {
                        URL.revokeObjectURL(
                            selectedVariationPhotoUrl
                        );
                    }

                    selectedVariationPhoto =
                        null;

                    selectedVariationPhotoUrl =
                        null;

                    if (productVariationPhotoInput) {
                        productVariationPhotoInput.value =
                            '';
                    }

                    if (variationPhotoPickerText) {
                        variationPhotoPickerText.textContent =
                            'Photo';
                    }

                    variationPhotoPickerLabel?.classList.remove(
                        'has-photo'
                    );

                }


                productVariationPhotoInput?.addEventListener(
                    'change',
                    function () {

                        clearPendingVariationPhoto();

                        const file =
                            productVariationPhotoInput.files?.[0];

                        if (!file) {
                            return;
                        }

                        if (
                            ![
                                'image/jpeg',
                                'image/png',
                                'image/webp'
                            ].includes(
                                file.type
                            )
                        ) {
                            productVariationPhotoInput.value =
                                '';

                            return;
                        }

                        selectedVariationPhoto =
                            file;

                        selectedVariationPhotoUrl =
                            URL.createObjectURL(
                                file
                            );

                        if (variationPhotoPickerText) {
                            variationPhotoPickerText.textContent =
                                'Added';
                        }

                        variationPhotoPickerLabel?.classList.add(
                            'has-photo'
                        );

                    }
                );


                function revokeVariationUrls() {

                    productVariations.forEach(
                        function (variation) {
                            if (variation.photoUrl) {
                                URL.revokeObjectURL(
                                    variation.photoUrl
                                );
                            }
                        }
                    );

                    if (selectedVariationPhotoUrl) {
                        URL.revokeObjectURL(
                            selectedVariationPhotoUrl
                        );
                    }

                }


                function renderVariations() {
                    if (!productVariationsContainer) {
                        return;
                    }

                    productVariationsContainer.innerHTML = '';

                    productVariations.forEach(
                        function (variation, index) {
                            const item =
                                document.createElement('div');

                            const hasPhoto =
                                Boolean(variation.photoUrl);

                            const hasOptionPrice =
                                pricingMode === 'varies';

                            item.className =
                                [
                                    'create-variation-chip',
                                    hasPhoto ? '' : 'no-photo',
                                    hasOptionPrice ? 'has-option-price' : ''
                                ]
                                .filter(Boolean)
                                .join(' ');

                            if (variation.photoUrl) {
                                const thumb =
                                    document.createElement('div');

                                thumb.className =
                                    'create-variation-thumb';

                                const image =
                                    document.createElement('img');

                                image.src =
                                    variation.photoUrl;

                                image.alt =
                                    variation.name;

                                thumb.appendChild(image);
                                item.appendChild(thumb);
                            }

                            const label =
                                document.createElement('span');

                            label.className =
                                'create-variation-chip-name';

                            label.textContent =
                                variation.name;

                            const hidden =
                                document.createElement('input');

                            hidden.type =
                                'hidden';

                            hidden.name =
                                'variations[]';

                            hidden.value =
                                variation.name;

                            item.appendChild(label);
                            item.appendChild(hidden);

                            const priceControl =
                                createOptionPriceControl(
                                    'variations',
                                    variation
                                );

                            if (priceControl) {
                                item.appendChild(
                                    priceControl
                                );
                            }

                            const remove =
                                document.createElement('button');

                            remove.type =
                                'button';

                            remove.className =
                                'create-variation-chip-remove';

                            remove.textContent =
                                '×';

                            remove.setAttribute(
                                'aria-label',
                                `Remove ${variation.name}`
                            );

                            remove.addEventListener(
                                'click',
                                function () {
                                    if (
                                        productVariations[index]
                                            ?.photoUrl
                                    ) {
                                        URL.revokeObjectURL(
                                            productVariations[index]
                                                .photoUrl
                                        );
                                    }

                                    productVariations.splice(
                                        index,
                                        1
                                    );

                                    ensurePricingSource();
                                    renderAllBuyerOptions();
                                }
                            );

                            item.appendChild(remove);

                            productVariationsContainer.appendChild(
                                item
                            );
                        }
                    );
                }


                function addVariationValue() {

                    if (!productVariationEntry) {
                        return;
                    }

                    const value =
                        productVariationEntry.value.trim();

                    if (!value) {
                        productVariationEntry.focus();
                        return;
                    }

                    const alreadyExists =
                        productVariations.some(
                            function (variation) {
                                return variation.name
                                    .toLowerCase() ===
                                    value.toLowerCase();
                            }
                        );

                    if (alreadyExists) {
                        productVariationEntry.focus();
                        return;
                    }

                    /*
                     * Keep the photo URL with the variation.
                     * The File object is also retained so a future
                     * store endpoint can append it to FormData.
                     */
                    productVariations.push({
                        name:
                            value,

                        photo:
                            selectedVariationPhoto,

                        photoUrl:
                            selectedVariationPhotoUrl,

                        price:
                            ''
                    });

                    selectedVariationPhoto =
                        null;

                    selectedVariationPhotoUrl =
                        null;

                    productVariationEntry.value =
                        '';

                    if (productVariationPhotoInput) {
                        productVariationPhotoInput.value =
                            '';
                    }

                    if (variationPhotoPickerText) {
                        variationPhotoPickerText.textContent =
                            'Photo';
                    }

                    variationPhotoPickerLabel?.classList.remove(
                        'has-photo'
                    );

                    ensurePricingSource(
                        'variations'
                    );

                    renderAllBuyerOptions();

                    productVariationEntry.focus();

                }


                addProductVariationButton?.addEventListener(
                    'click',
                    addVariationValue
                );


                productVariationEntry?.addEventListener(
                    'keydown',
                    function (event) {

                        if (event.key === 'Enter') {

                            event.preventDefault();

                            addVariationValue();

                        }

                    }
                );


                /* =================================================
                   COLOR / SIZE CHIPS
                ================================================== */

                function renderOptionChips(
                    container,
                    values,
                    group
                ) {
                    if (!container) {
                        return;
                    }

                    container.innerHTML = '';

                    container.classList.toggle(
                        'variable-pricing-list',
                        pricingMode === 'varies'
                    );

                    values.forEach(
                        function (item, index) {
                            const row =
                                document.createElement('div');

                            row.className =
                                pricingMode === 'varies'
                                    ? 'create-priced-option-row'
                                    : 'create-priced-option-row no-option-price';

                            const label =
                                document.createElement('span');

                            label.className =
                                'create-option-value-name';

                            label.textContent =
                                item.name;

                            const hidden =
                                document.createElement('input');

                            hidden.type =
                                'hidden';

                            hidden.name =
                                `${group}[]`;

                            hidden.value =
                                item.name;

                            row.appendChild(label);
                            row.appendChild(hidden);

                            const priceControl =
                                createOptionPriceControl(
                                    group,
                                    item
                                );

                            if (priceControl) {
                                row.appendChild(
                                    priceControl
                                );
                            }

                            const remove =
                                document.createElement('button');

                            remove.type =
                                'button';

                            remove.className =
                                'create-option-remove';

                            remove.textContent =
                                '×';

                            remove.setAttribute(
                                'aria-label',
                                `Remove ${item.name}`
                            );

                            remove.addEventListener(
                                'click',
                                function () {
                                    values.splice(
                                        index,
                                        1
                                    );

                                    ensurePricingSource();
                                    renderAllBuyerOptions();
                                }
                            );

                            row.appendChild(remove);
                            container.appendChild(row);
                        }
                    );
                }


                function addChipValue(
                    entry,
                    values,
                    container,
                    group
                ) {
                    if (!entry) {
                        return;
                    }

                    const value =
                        entry.value.trim();

                    if (!value) {
                        return;
                    }

                    const alreadyExists =
                        values.some(
                            function (existing) {
                                return existing.name
                                    .toLowerCase() ===
                                    value.toLowerCase();
                            }
                        );

                    if (!alreadyExists) {
                        values.push({
                            name:
                                value,

                            price:
                                ''
                        });
                    }

                    entry.value = '';

                    ensurePricingSource(group);
                    renderAllBuyerOptions();
                    entry.focus();
                }


                addProductColorButton?.addEventListener(
                    'click',
                    function () {

                        addChipValue(
                            productColorEntry,
                            productColors,
                            productColorsContainer,
                            'colors'
                        );

                    }
                );


                addProductSizeButton?.addEventListener(
                    'click',
                    function () {

                        addChipValue(
                            productSizeEntry,
                            productSizes,
                            productSizesContainer,
                            'sizes'
                        );

                    }
                );


                productColorEntry?.addEventListener(
                    'keydown',
                    function (event) {

                        if (event.key === 'Enter') {

                            event.preventDefault();

                            addProductColorButton?.click();

                        }

                    }
                );


                productSizeEntry?.addEventListener(
                    'keydown',
                    function (event) {

                        if (event.key === 'Enter') {

                            event.preventDefault();

                            addProductSizeButton?.click();

                        }

                    }
                );


                /* =================================================
                   CATEGORY-AWARE PRODUCT SPECIFICATIONS
                ================================================== */

                function createSpecificationWrapper(
                    labelText,
                    key,
                    fullWidth = false
                ) {

                    const wrapper =
                        document.createElement(
                            'div'
                        );

                    wrapper.className =
                        fullWidth
                            ? 'create-field category-spec-full'
                            : 'create-field';

                    const label =
                        document.createElement(
                            'label'
                        );

                    label.htmlFor =
                        `categorySpec_${key}`;

                    label.textContent =
                        labelText;

                    wrapper.appendChild(
                        label
                    );

                    return {
                        wrapper,
                        label
                    };

                }


                function syncCreateCategoryPills() {

                    const selectedCategory =
                        addProductCategory?.value ||
                        '';

                    categoryChoicePills.forEach(
                        function (pill) {

                            pill.classList.toggle(
                                'is-selected',
                                pill.dataset.categorySlug ===
                                    selectedCategory
                            );

                            pill.setAttribute(
                                'aria-pressed',
                                pill.dataset.categorySlug ===
                                    selectedCategory
                                    ? 'true'
                                    : 'false'
                            );

                        }
                    );

                }


                categoryChoicePills.forEach(
                    function (pill) {

                        pill.setAttribute(
                            'aria-pressed',
                            'false'
                        );

                        pill.addEventListener(
                            'click',
                            function () {

                                if (!addProductCategory) {
                                    return;
                                }

                                addProductCategory.value =
                                    pill.dataset.categorySlug ||
                                    '';

                                syncCreateCategoryPills();

                                addProductCategory.dispatchEvent(
                                    new Event(
                                        'change',
                                        {
                                            bubbles: true
                                        }
                                    )
                                );

                            }
                        );

                    }
                );


                function createSpecificationWrapper(
                    labelText,
                    key,
                    fullWidth = false
                ) {

                    const wrapper =
                        document.createElement(
                            'div'
                        );

                    wrapper.className =
                        fullWidth
                            ? 'create-field category-spec-full'
                            : 'create-field';

                    const label =
                        document.createElement(
                            'label'
                        );

                    label.htmlFor =
                        `categorySpec_${key}`;

                    label.textContent =
                        labelText;

                    wrapper.appendChild(
                        label
                    );

                    return {
                        wrapper,
                        label
                    };

                }


                function buildSpecificationField(
                    field
                ) {

                    const type =
                        field.type ||
                        'text';

                    const fullWidth =
                        type === 'textarea';

                    const {
                        wrapper
                    } =
                        createSpecificationWrapper(
                            field.label,
                            field.key,
                            fullWidth
                        );

                    let control;

                    if (type === 'select') {

                        control =
                            document.createElement(
                                'select'
                            );

                        const empty =
                            document.createElement(
                                'option'
                            );

                        empty.value =
                            '';

                        empty.textContent =
                            `Select ${field.label.toLowerCase()}`;

                        control.appendChild(
                            empty
                        );

                        (
                            field.options ||
                            []
                        ).forEach(
                            function (optionLabel) {

                                const option =
                                    document.createElement(
                                        'option'
                                    );

                                option.value =
                                    optionLabel;

                                option.textContent =
                                    optionLabel;

                                control.appendChild(
                                    option
                                );

                            }
                        );

                    } else if (type === 'textarea') {

                        control =
                            document.createElement(
                                'textarea'
                            );

                        control.rows =
                            2;

                    } else {

                        control =
                            document.createElement(
                                'input'
                            );

                        control.type =
                            'text';

                    }

                    control.id =
                        `categorySpec_${field.key}`;

                    control.name =
                        `category_specifications[${field.key}]`;

                    if (field.placeholder) {
                        control.placeholder =
                            field.placeholder;
                    }

                    if (type === 'readonly') {

                        control.value =
                            field.value ||
                            '';

                        control.readOnly =
                            true;

                        control.classList.add(
                            'category-spec-readonly'
                        );

                    }

                    wrapper.appendChild(
                        control
                    );

                    return wrapper;

                }


                function buildReadonlySpecification(
                    label,
                    key,
                    value
                ) {

                    return buildSpecificationField({
                        label,
                        key,
                        type:
                            'readonly',
                        value
                    });

                }


                function buildSpecificationGroupTitle(
                    title,
                    subtitle = ''
                ) {

                    const heading =
                        document.createElement(
                            'div'
                        );

                    heading.className =
                        'category-spec-group-title';

                    heading.textContent =
                        title;

                    if (subtitle) {

                        const small =
                            document.createElement(
                                'small'
                            );

                        small.textContent =
                            subtitle;

                        heading.appendChild(
                            small
                        );

                    }

                    return heading;

                }


                function buildSubcategorySelect(
                    config
                ) {

                    const {
                        wrapper
                    } =
                        createSpecificationWrapper(
                            'Subcategory',
                            'subcategory'
                        );

                    const select =
                        document.createElement(
                            'select'
                        );

                    select.id =
                        'categorySpec_subcategory';

                    select.name =
                        'category_specifications[subcategory]';

                    const empty =
                        document.createElement(
                            'option'
                        );

                    empty.value =
                        '';

                    empty.textContent =
                        'Select subcategory';

                    select.appendChild(
                        empty
                    );

                    Object.keys(
                        config.subcategories ||
                        {}
                    ).forEach(
                        function (subcategory) {

                            const option =
                                document.createElement(
                                    'option'
                                );

                            option.value =
                                subcategory;

                            option.textContent =
                                subcategory;

                            select.appendChild(
                                option
                            );

                        }
                    );

                    wrapper.appendChild(
                        select
                    );

                    return {
                        wrapper,
                        select
                    };

                }


                function renderSubcategorySpecifications(
                    config,
                    subcategory
                ) {

                    const container =
                        document.getElementById(
                            'subcategorySpecificFields'
                        );

                    const title =
                        document.getElementById(
                            'subcategorySpecificTitle'
                        );

                    if (
                        !container ||
                        !title
                    ) {
                        return;
                    }

                    container.innerHTML =
                        '';

                    const fields =
                        config.subcategories?.[
                            subcategory
                        ] ||
                        [];

                    const hasFields =
                        Boolean(
                            subcategory &&
                            fields.length
                        );

                    title.classList.toggle(
                        'hidden',
                        !hasFields
                    );

                    container.classList.toggle(
                        'hidden',
                        !hasFields
                    );

                    if (!hasFields) {
                        return;
                    }

                    title.firstChild.textContent =
                        `${subcategory} Specifications`;

                    fields.forEach(
                        function (field) {

                            container.appendChild(
                                buildSpecificationField(
                                    field
                                )
                            );

                        }
                    );

                }


                function renderCategorySpecifications() {

                    if (
                        !addProductCategory ||
                        !productSpecificationsSection ||
                        !categorySpecificationsFields
                    ) {
                        return;
                    }

                    syncCreateCategoryPills();

                    const category =
                        addProductCategory.value;

                    const config =
                        productSpecificationLibrary[
                            category
                        ];

                    categorySpecificationsFields.innerHTML =
                        '';

                    const hasConfig =
                        Boolean(
                            category &&
                            config
                        );

                    productSpecificationsSection.classList.toggle(
                        'hidden',
                        !hasConfig
                    );

                    if (!hasConfig) {

                        if (categorySpecificationsTitle) {
                            categorySpecificationsTitle.textContent =
                                'Product Specifications';
                        }

                        return;

                    }

                    if (categorySpecificationsTitle) {

                        categorySpecificationsTitle.textContent =
                            `${config.label} Product Specifications`;

                    }

                    categorySpecificationsFields.appendChild(
                        buildReadonlySpecification(
                            'Category',
                            'selected_category',
                            config.label
                        )
                    );

                    categorySpecificationsFields.appendChild(
                        buildReadonlySpecification(
                            'Stock',
                            'stock_display',
                            createProductStock?.value ||
                            ''
                        )
                    );

                    const subcategorySelectData =
                        buildSubcategorySelect(
                            config
                        );

                    categorySpecificationsFields.appendChild(
                        subcategorySelectData.wrapper
                    );

                    categorySpecificationsFields.appendChild(
                        buildSpecificationGroupTitle(
                            `General ${config.label} Specifications`,
                            'These fields apply generally to products under this category.'
                        )
                    );

                    commonProductSpecificationFields.forEach(
                        function (field) {

                            categorySpecificationsFields.appendChild(
                                buildSpecificationField(
                                    field
                                )
                            );

                        }
                    );

                    (
                        config.generalFields ||
                        []
                    ).forEach(
                        function (field) {

                            categorySpecificationsFields.appendChild(
                                buildSpecificationField(
                                    field
                                )
                            );

                        }
                    );

                    const subcategoryTitle =
                        buildSpecificationGroupTitle(
                            'Subcategory Specifications',
                            'Choose a subcategory to show more specific product fields.'
                        );

                    subcategoryTitle.id =
                        'subcategorySpecificTitle';

                    subcategoryTitle.classList.add(
                        'hidden'
                    );

                    const subcategoryContainer =
                        document.createElement(
                            'div'
                        );

                    subcategoryContainer.id =
                        'subcategorySpecificFields';

                    subcategoryContainer.className =
                        'subcategory-specifications-wrap hidden';

                    categorySpecificationsFields.appendChild(
                        subcategoryTitle
                    );

                    categorySpecificationsFields.appendChild(
                        subcategoryContainer
                    );

                    subcategorySelectData.select.addEventListener(
                        'change',
                        function () {

                            renderSubcategorySpecifications(
                                config,
                                subcategorySelectData.select.value
                            );

                        }
                    );

                }


                addProductCategory?.addEventListener(
                    'change',
                    renderCategorySpecifications
                );


                createProductStock?.addEventListener(
                    'input',
                    function () {

                        const stockDisplay =
                            document.getElementById(
                                'categorySpec_stock_display'
                            );

                        if (stockDisplay) {
                            stockDisplay.value =
                                createProductStock.value;
                        }

                    }
                );


                /* =================================================
                   DESCRIPTION COUNTER
                ================================================== */

                function updateCreateDescriptionCount() {

                    if (
                        !createProductDescription ||
                        !createProductDescriptionCount
                    ) {
                        return;
                    }

                    createProductDescriptionCount.textContent =
                        `${createProductDescription.value.length}/2000`;

                }


                createProductDescription?.addEventListener(
                    'input',
                    updateCreateDescriptionCount
                );


                /* =================================================
                   RESET CREATE PRODUCT MODAL
                ================================================== */

                function resetCreateProductModal() {

                    revokeProductPhotoUrls();
                    revokeVariationUrls();

                    selectedProductPhotos =
                        [];

                    selectedVariationPhoto =
                        null;

                    selectedVariationPhotoUrl =
                        null;

                    productVariations =
                        [];

                    productColors =
                        [];

                    productSizes =
                        [];

                    pricingMode =
                        'fixed';

                    pricingSource =
                        '';

                    addProductForm?.reset();

                    if (productPricingMode) {
                        productPricingMode.value =
                            'fixed';
                    }

                    if (productPricingSource) {
                        productPricingSource.value =
                            '';
                    }

                    if (productVariationsContainer) {
                        productVariationsContainer.innerHTML =
                            '';
                    }

                    if (productColorsContainer) {
                        productColorsContainer.innerHTML =
                            '';
                    }

                    if (productSizesContainer) {
                        productSizesContainer.innerHTML =
                            '';
                    }

                    if (productPhotoPreviewGrid) {
                        productPhotoPreviewGrid.innerHTML =
                            '';
                    }

                    if (variationPhotoPickerText) {
                        variationPhotoPickerText.textContent =
                            'Photo';
                    }

                    variationPhotoPickerLabel?.classList.remove(
                        'has-photo'
                    );

                    syncProductPhotoInput();
                    renderProductPhotos();

                    applyPricingMode(
                        'fixed'
                    );

                    renderCategorySpecifications();
                    updateCreateDescriptionCount();

                }


                /* =================================================
                   OPEN / CLOSE CREATE PRODUCT
                ================================================== */

                function openAddModal() {

                    if (!addProductModal) {
                        return;
                    }

                    resetCreateProductModal();

                    addProductModal.classList.remove(
                        'hidden'
                    );

                    addProductModal.classList.add(
                        'modal-open'
                    );

                    addProductModal.setAttribute(
                        'aria-hidden',
                        'false'
                    );

                    document.body.classList.add(
                        'overflow-hidden'
                    );

                    window.setTimeout(
                        function () {

                            document.getElementById(
                                'createProductTitle'
                            )?.focus();

                        },
                        80
                    );

                }


                function closeAddModal() {

                    if (
                        !addProductModal ||
                        !addProductModalPanel
                    ) {
                        return;
                    }

                    fadeCloseModal(
                        addProductModal,
                        addProductModalPanel,
                        function () {

                            document.body.classList.remove(
                                'overflow-hidden'
                            );

                            resetCreateProductModal();

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


                document.addEventListener(
                    'keydown',
                    function (event) {

                        if (
                            event.key === 'Escape' &&
                            addProductModal?.classList.contains(
                                'modal-open'
                            )
                        ) {

                            closeAddModal();

                        }

                    }
                );


                /* =================================================
                   CREATE PRODUCT SUBMIT
                   Still front-end/demo until Product persistence is wired.
                ================================================== */

                addProductForm?.addEventListener(
                    'submit',
                    async function (event) {

                        event.preventDefault();

                        if (
                            !sellerRegisteredCategories.length
                        ) {

                            window.alert(
                                'No registered product categories are available for this seller.'
                            );

                            return;

                        }

                        if (
                            !addProductCategory ||
                            !sellerRegisteredCategories.includes(
                                addProductCategory.value
                            )
                        ) {

                            window.alert(
                                'Please choose one of your registered product categories.'
                            );

                            document
                                .getElementById(
                                    'createProductCategoryPills'
                                )
                                ?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                            return;

                        }

                        if (
                            selectedProductPhotos.length === 0
                        ) {

                            window.alert(
                                'Please upload at least one product photo.'
                            );

                            productPhotosDropZone?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                            return;

                        }

                        if (
                            pricingMode ===
                            'varies'
                        ) {

                            if (!pricingSource) {

                                window.alert(
                                    'Choose which buyer option sets the actual product price.'
                                );

                                variablePricingSetup?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                return;
                            }

                            const primaryItems =
                                getPricingGroupItems(
                                    pricingSource
                                );

                            if (
                                primaryItems.length === 0
                            ) {

                                window.alert(
                                    `Add at least one ${getPricingGroupLabel(pricingSource).toLowerCase()} option for variable pricing.`
                                );

                                variablePricingSetup?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                return;
                            }

                            const missingPrimaryPrice =
                                primaryItems.some(
                                    function (item) {
                                        return (
                                            item.price === '' ||
                                            item.price === null ||
                                            item.price === undefined ||
                                            Number.isNaN(
                                                Number(item.price)
                                            )
                                        );
                                    }
                                );

                            if (missingPrimaryPrice) {

                                window.alert(
                                    `Enter an item price for every ${getPricingGroupLabel(pricingSource).toLowerCase()} option.`
                                );

                                variablePricingSetup?.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                return;
                            }
                        }

                        if (
                            !addProductForm.checkValidity()
                        ) {

                            addProductForm.reportValidity();
                            return;

                        }

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

                        const formData =
                            new FormData(
                                addProductForm
                            );

                        productVariations.forEach(
                            function (variation, index) {

                                formData.append(
                                    `variation_items[${index}][name]`,
                                    variation.name
                                );

                                formData.append(
                                    `variation_items[${index}][price]`,
                                    variation.price || '0'
                                );

                                formData.append(
                                    `variation_items[${index}][price_type]`,
                                    pricingMode === 'varies' &&
                                    pricingSource === 'variations'
                                        ? 'base'
                                        : 'addon'
                                );

                                if (variation.photo) {

                                    formData.append(
                                        `variation_items[${index}][photo]`,
                                        variation.photo
                                    );

                                }

                            }
                        );

                        productColors.forEach(
                            function (color, index) {

                                formData.append(
                                    `color_items[${index}][name]`,
                                    color.name
                                );

                                formData.append(
                                    `color_items[${index}][price]`,
                                    color.price || '0'
                                );

                                formData.append(
                                    `color_items[${index}][price_type]`,
                                    pricingMode === 'varies' &&
                                    pricingSource === 'colors'
                                        ? 'base'
                                        : 'addon'
                                );

                            }
                        );

                        productSizes.forEach(
                            function (size, index) {

                                formData.append(
                                    `size_items[${index}][name]`,
                                    size.name
                                );

                                formData.append(
                                    `size_items[${index}][price]`,
                                    size.price || '0'
                                );

                                formData.append(
                                    `size_items[${index}][price_type]`,
                                    pricingMode === 'varies' &&
                                    pricingSource === 'sizes'
                                        ? 'base'
                                        : 'addon'
                                );

                            }
                        );

                        const previewPayload = {
                            title:
                                formData.get(
                                    'title'
                                ),

                            category:
                                formData.get(
                                    'category'
                                ),

                            pricingMode:
                                pricingMode,

                            pricingSource:
                                pricingSource,

                            basePrice:
                                pricingMode === 'fixed'
                                    ? formData.get(
                                        'price'
                                    )
                                    : null,

                            stock:
                                formData.get(
                                    'stock'
                                ),

                            variations:
                                productVariations.map(
                                    function (variation) {
                                        return {
                                            name:
                                                variation.name,

                                            hasPhoto:
                                                Boolean(
                                                    variation.photo
                                                ),

                                            price:
                                                Number(
                                                    variation.price ||
                                                    0
                                                ),

                                            priceType:
                                                pricingMode === 'varies' &&
                                                pricingSource === 'variations'
                                                    ? 'base'
                                                    : 'addon'
                                        };
                                    }
                                ),

                            colors:
                                productColors.map(
                                    function (color) {
                                        return {
                                            name:
                                                color.name,

                                            price:
                                                Number(
                                                    color.price ||
                                                    0
                                                ),

                                            priceType:
                                                pricingMode === 'varies' &&
                                                pricingSource === 'colors'
                                                    ? 'base'
                                                    : 'addon'
                                        };
                                    }
                                ),

                            sizes:
                                productSizes.map(
                                    function (size) {
                                        return {
                                            name:
                                                size.name,

                                            price:
                                                Number(
                                                    size.price ||
                                                    0
                                                ),

                                            priceType:
                                                pricingMode === 'varies' &&
                                                pricingSource === 'sizes'
                                                    ? 'base'
                                                    : 'addon'
                                        };
                                    }
                                ),

                            pricingFormula:
                                pricingMode === 'fixed'
                                    ? 'fixed_price'
                                    : 'selected_base_option_price + selected_addon_prices',

                            specifications:
                                Object.fromEntries(
                                    Array.from(
                                        formData.entries()
                                    )
                                    .filter(
                                        function ([key]) {
                                            return key.startsWith(
                                                'category_specifications['
                                            );
                                        }
                                    )
                                )
                        };


                        const compactProductPhotos =
                            (
                                await Promise.all(
                                    selectedProductPhotos
                                        .slice(
                                            0,
                                            8
                                        )
                                        .map(
                                            function (entry) {
                                                return createCompactCoverDataUrl(
                                                    entry.file
                                                );
                                            }
                                        )
                                )
                            )
                            .filter(
                                Boolean
                            );


                        const compactVariationPhotos =
                            await Promise.all(
                                productVariations.map(
                                    function (variation) {

                                        return createCompactCoverDataUrl(
                                            variation.photo
                                        );

                                    }
                                )
                            );


                        const specificationDisplay =
                            Array.from(
                                categorySpecificationsFields
                                    ?.querySelectorAll(
                                        '.create-field'
                                    ) ||
                                []
                            )
                            .map(
                                function (wrapper) {

                                    const control =
                                        wrapper.querySelector(
                                            'input, select, textarea'
                                        );

                                    const label =
                                        wrapper.querySelector(
                                            'label'
                                        );

                                    if (!control) {
                                        return null;
                                    }

                                    const rawName =
                                        control.name ||
                                        '';

                                    const key =
                                        rawName
                                            .replace(
                                                /^category_specifications\[/,
                                                ''
                                            )
                                            .replace(
                                                /\]$/,
                                                ''
                                            );

                                    return {
                                        key:
                                            key,

                                        label:
                                            label?.textContent?.trim() ||
                                            createdProductLabelFromKey(
                                                key
                                            ),

                                        value:
                                            control.value?.trim() ||
                                            ''
                                    };

                                }
                            )
                            .filter(
                                function (item) {
                                    return (
                                        item &&
                                        item.value !== ''
                                    );
                                }
                            );


                        const createdProduct = {
                            id:
                                `seller-product-${Date.now()}-${Math.random()
                                    .toString(36)
                                    .slice(2, 8)}`,

                            title:
                                previewPayload.title,

                            category:
                                previewPayload.category,

                            categoryLabel:
                                productSpecificationLibrary[
                                    previewPayload.category
                                ]?.label ||
                                previewPayload.category,

                            approvalStatus:
                                'pending',

                            pricingMode:
                                previewPayload.pricingMode,

                            pricingSource:
                                previewPayload.pricingSource,

                            basePrice:
                                previewPayload.basePrice,

                            stock:
                                Number(
                                    previewPayload.stock ||
                                    0
                                ),

                            variations:
                                previewPayload.variations.map(
                                    function (variation, index) {
                                        return {
                                            ...variation,

                                            photoData:
                                                compactVariationPhotos[
                                                    index
                                                ] ||
                                                ''
                                        };
                                    }
                                ),

                            colors:
                                previewPayload.colors,

                            sizes:
                                previewPayload.sizes,

                            specifications:
                                previewPayload.specifications,

                            specificationDisplay:
                                specificationDisplay,

                            description:
                                formData.get(
                                    'description'
                                ) ||
                                '',

                            sku:
                                formData.get(
                                    'sku'
                                ) ||
                                '',

                            photos:
                                compactProductPhotos,

                            coverPhoto:
                                compactProductPhotos[
                                    0
                                ] ||
                                '',

                            createdAt:
                                new Date()
                                    .toISOString()
                        };
                        createdInventoryProducts.unshift(
                            createdProduct
                        );

                        saveCreatedProducts();
                        updateInventoryTotalCount();

                        insertCreatedProductRow(
                            createdProduct,
                            true
                        );


                        console.log(
                            'Create Product payload:',
                            previewPayload
                        );

                        window.setTimeout(
                            function () {

                                if (submitButton) {

                                    submitButton.disabled =
                                        false;

                                    submitButton.textContent =
                                        'Add Product';

                                }

                                closeAddModal();

                                window.setTimeout(
                                    function () {

                                        if (
                                            typeof showInventoryFlash ===
                                            'function'
                                        ) {

                                            showInventoryFlash(
                                                'Product created and added to your inventory with Pending status.'
                                            );

                                        } else {

                                            window.alert(
                                                'Product created and added to your inventory with Pending status.'
                                            );

                                        }

                                    },
                                    220
                                );

                            },
                            550
                        );

                    }
                );


                applyPricingMode(
                    'fixed'
                );

                renderCategorySpecifications();
                updateCreateDescriptionCount();



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