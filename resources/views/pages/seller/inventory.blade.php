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

    <meta name="csrf-token" content="{{ csrf_token() }}">

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
        'resources/css/app.css',
        'resources/css/seller/inventory.css',
        'resources/js/seller/inventory.js',
    ])

    {{-- Runtime data/config for the extracted inventory JavaScript --}}
    @php
        $sellerInventoryConfig = [
            'sellerRegisteredCategories' => array_column($sellerInventoryCategories, 'slug'),
            'sellerShipFromAddress' => $sellerShipFromAddress,
            'inventoryProducts' => $inventoryProducts ?? [],
            'archivedProducts' => $archivedProducts ?? [],
            'inventoryProductsUrl' => url('/seller/inventory/products'),
            'storageUrl' => asset('storage'),
            'storeProductUrl' => url('/seller/inventory/products'),
        ];
    @endphp
    <script type="application/json" id="sellerInventoryConfig">{!! json_encode($sellerInventoryConfig, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

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
                        class="text-[20px] leading-none font-light"
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

                            <option value="warning">
                                Issue Warning
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
                ></div>



                {{-- =================================================
                     POLICY ISSUES
                ================================================== --}}

                <div
                    id="policyIssuesTable"

                    class="hidden"
                ></div>



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
                            0
                        </span>

                        out of
                        <span
                            id="totalEntriesCount"
                            data-base-total="0"
                        >
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

                                    <label
                                        for="productColorPhotoInput"
                                        class="create-variation-photo-picker"
                                        title="Add optional color photo"
                                    >
                                        <input
                                            id="productColorPhotoInput"
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="hidden"
                                        >
                                        <span id="colorPhotoPickerText">Photo</span>
                                    </label>

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

                                    <label
                                        for="productSizePhotoInput"
                                        class="create-variation-photo-picker"
                                        title="Add optional size photo"
                                    >
                                        <input
                                            id="productSizePhotoInput"
                                            type="file"
                                            accept="image/jpeg,image/png,image/webp"
                                            class="hidden"
                                        >
                                        <span id="sizePhotoPickerText">Photo</span>
                                    </label>

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
                                        src=""

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
                                —
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

                        </p>

                        <p
                            id="productPolicyIssueDate"
                            class="
                                mt-[5px]
                                text-[11px]
                                text-[#A78E6D]
                            "
                        >

                        </p>

                        <p
                            id="productPolicyIssueReason"
                            class="
                                mt-[8px]
                                text-[13px]
                                leading-[1.5]
                                text-[#5F534C]
                            "
                        >

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
                        id="removeProductModalTitle"
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
                        id="removeProductModalSubtitle"
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

                <div id="removeReasonBlock" class="mt-[26px]">

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

                <div id="removeDetailsBlock" class="mt-[27px]">

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
{{-- =========================================================
         INVENTORY JAVASCRIPT
    ========================================================== --}}
</body>

</html>
