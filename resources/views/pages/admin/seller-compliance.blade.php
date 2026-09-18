{{-- =========================================================
     ADMIN SELLER COMPLIANCE PAGE
     resources/views/pages/admin/seller-compliance.blade.php

     Figma-matched Seller Compliance UI
     ---------------------------------------------------------
     - Existing Admin Sidebar
     - Existing Admin Navbar
     - Seller Compliance heading
     - Compliance summary cards
     - Search
     - User type filter
     - Compliance filter
     - Status filter
     - Date range field
     - Refresh
     - Seller compliance table
     - Category-colored pills
     - Compliance score bars
     - Status indicators
     - Pagination UX
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
        ShopEase - Seller Compliance
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
    'resources/js/app.js'
])
</head>


<body
    class="
        m-0
        p-0

        bg-[#FFF4EF]

        font-[Poppins,sans-serif]

        text-[#17120F]
    "
>


    {{-- =====================================================
         EXISTING ADMIN SIDEBAR
    ====================================================== --}}

    @include(
        'components.admin.sidebar'
    )



    {{-- =====================================================
         EXISTING ADMIN NAVBAR
    ====================================================== --}}

    @section(
        'page-title',
        'Seller Compliance'
    )

    @include(
        'components.admin.navbar'
    )



    {{-- =====================================================
         PAGE
    ====================================================== --}}

    <main
        id="admin-content"

        class="seller-compliance-page 
            min-h-screen

            bg-[#FFF4EF]

            pt-[125px]

            ml-65

            transition-all

            duration-300
        "
    >

        <div
            class="
                px-[25px]

                pt-[10px]

                pb-[24px]
            "
        >





            {{-- =================================================
                 SUMMARY CARDS
            ================================================== --}}

            <div
                class="
                    grid

                    grid-cols-5

                    gap-[12px]

                    mb-[34px]
                "
            >


                {{-- =================================================
                     COMPLIANT
                ================================================== --}}

                <div
                    class="
                        compliance-stat-card
                    "
                >

                    <div
                        class="
                            compliance-stat-main
                        "
                    >

                        <img
                            src="{{ asset('icons/admin/seller-compliance/compliant.png') }}"
                            alt="Compliant"
                            class="compliance-stat-icon"
                        >


                        <div
                            class="
                                compliance-stat-content
                            "
                        >

                            <div
                                class="
                                    compliance-stat-number
                                "
                            >
                                328
                            </div>


                            <div
                                class="
                                    compliance-stat-label
                                "
                            >
                                Compliant
                            </div>

                        </div>

                    </div>


                    <div
                        class="
                            compliance-stat-growth
                        "
                    >

                        <span
                            class="
                                growth-arrow
                            "
                        >
                            ↑
                        </span>

                        <strong>
                            12%
                        </strong>

                        of total sellers

                    </div>

                </div>



                {{-- =================================================
                     WARNINGS
                ================================================== --}}

                <div
                    class="
                        compliance-stat-card
                    "
                >

                    <div
                        class="
                            compliance-stat-main
                        "
                    >

                        <img
                            src="{{ asset('icons/admin/seller-compliance/warnings.png') }}"
                            alt="Warnings Issued"
                            class="compliance-stat-icon"
                        >


                        <div
                            class="
                                compliance-stat-content
                            "
                        >

                            <div
                                class="
                                    compliance-stat-number
                                "
                            >
                                105
                            </div>


                            <div
                                class="
                                    compliance-stat-label
                                "
                            >
                                Warnings Issued
                            </div>

                        </div>

                    </div>


                    <div
                        class="
                            compliance-stat-growth
                        "
                    >

                        <span
                            class="
                                growth-arrow
                            "
                        >
                            ↑
                        </span>

                        <strong>
                            12%
                        </strong>

                        of total sellers

                    </div>

                </div>



                {{-- =================================================
                     UNDER REVIEW
                ================================================== --}}

                <div
                    class="
                        compliance-stat-card
                    "
                >

                    <div
                        class="
                            compliance-stat-main
                        "
                    >

                        <img
                            src="{{ asset('icons/admin/seller-compliance/under-review.png') }}"
                            alt="Under Review"
                            class="compliance-stat-icon"
                        >


                        <div
                            class="
                                compliance-stat-content
                            "
                        >

                            <div
                                class="
                                    compliance-stat-number
                                "
                            >
                                105
                            </div>


                            <div
                                class="
                                    compliance-stat-label
                                "
                            >
                                Under Review
                            </div>

                        </div>

                    </div>


                    <div
                        class="
                            compliance-stat-growth
                        "
                    >

                        <span
                            class="
                                growth-arrow
                            "
                        >
                            ↑
                        </span>

                        <strong>
                            12%
                        </strong>

                        of total sellers

                    </div>

                </div>



                {{-- =================================================
                     SUSPENDED
                ================================================== --}}

                <div
                    class="
                        compliance-stat-card
                    "
                >

                    <div
                        class="
                            compliance-stat-main
                        "
                    >

                        <img
                            src="{{ asset('icons/admin/seller-compliance/suspended.png') }}"
                            alt="Suspended"
                            class="compliance-stat-icon"
                        >


                        <div
                            class="
                                compliance-stat-content
                            "
                        >

                            <div
                                class="
                                    compliance-stat-number
                                "
                            >
                                153
                            </div>


                            <div
                                class="
                                    compliance-stat-label
                                "
                            >
                                Suspended
                            </div>

                        </div>

                    </div>

                </div>



                {{-- =================================================
                     TOTAL SELLERS
                ================================================== --}}

                <div
                    class="
                        compliance-stat-card
                    "
                >

                    <div
                        class="
                            compliance-stat-main
                        "
                    >

                        <img
                            src="{{ asset('icons/admin/seller-compliance/sellers.png') }}"
                            alt="Total Sellers"
                            class="compliance-stat-icon"
                        >


                        <div
                            class="
                                compliance-stat-content
                            "
                        >

                            <div
                                class="
                                    compliance-stat-number
                                "
                            >
                                412
                            </div>


                            <div
                                class="
                                    compliance-stat-label
                                "
                            >
                                Total Sellers
                            </div>

                        </div>

                    </div>


                    <div
                        class="
                            compliance-stat-growth
                        "
                    >

                        <span
                            class="
                                growth-arrow
                            "
                        >
                            ↑
                        </span>

                        <strong>
                            12%
                        </strong>

                        from last month

                    </div>

                </div>

            </div>



            {{-- =================================================
                 FILTER BAR
            ================================================== --}}

            <section
                class="
                    compliance-filter-card
                "
            >


                {{-- SEARCH --}}

                <div
                    class="
                        compliance-search-wrap
                    "
                >

                    <input
                        type="text"

                        id="sellerSearch"

                        placeholder="Search name, email, and phone"

                        autocomplete="off"

                        class="
                            compliance-search
                        "
                    >


                    <svg
                        class="
                            compliance-search-icon
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



                {{-- CATEGORY FILTER --}}

                <select
                    id="sellerTypeFilter"

                    class="
                        compliance-filter
                        filter-user-type
                    "
                    aria-label="Filter by category"
                >

                    <option value="all">
                        All Categories
                    </option>

                    <option value="pet-supplies">
                        Pet Supplies
                    </option>

                    <option value="electronics-and-gadgets">
                        Electronics &amp; Gadgets
                    </option>

                    <option value="womens-apparel">
                        Women’s Apparel
                    </option>

                    <option value="mens-apparel">
                        Men’s Apparel
                    </option>

                    <option value="kids-and-baby">
                        Kids &amp; Baby
                    </option>

                    <option value="home-and-garden">
                        Home &amp; Garden
                    </option>

                    <option value="sports-and-outdoors">
                        Sports &amp; Outdoors
                    </option>

                    <option value="health-and-beauty">
                        Health &amp; Beauty
                    </option>

                    <option value="books-and-media">
                        Books &amp; Media
                    </option>

                    <option value="food-and-gourmet">
                        Food &amp; Gourmet
                    </option>

                    <option value="automotive-motorcycle">
                        Automotive &amp; Motorcycle
                    </option>

                    <option value="furniture-and-office-equipment">
                        Furniture &amp; Office Equipment
                    </option>

                    <option value="jewelry-and-watches">
                        Jewelry &amp; Watches
                    </option>

                    <option value="office-and-school-supplies">
                        Office &amp; School Supplies
                    </option>

                </select>



                {{-- COMPLIANCE --}}

                <select
                    id="complianceFilter"

                    class="
                        compliance-filter
                        filter-compliance
                    "
                >

                    <option value="all">
                        All Compliance
                    </option>

                    <option value="compliant">
                        Compliant
                    </option>

                    <option value="warning">
                        Warning
                    </option>

                    <option value="under-review">
                        Under Review
                    </option>

                    <option value="suspended">
                        Suspended
                    </option>

                </select>




                {{-- REFRESH --}}

                <button
                    type="button"

                    id="refreshCompliance"

                    class="
                        compliance-refresh
                    "

                    aria-label="Refresh seller compliance"
                >

                    <svg
                        id="complianceRefreshIcon"

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

            </section>



            {{-- =================================================
                 SELLER TABLE CARD
            ================================================== --}}

            <section
                class="
                    compliance-table-card
                "
            >


                {{-- =================================================
                     TABLE HEADER
                ================================================== --}}

                <div
                    class="
                        compliance-table-header
                    "
                >

                    <div>
                        Sellers
                    </div>

                    <div>
                        Category
                    </div>

                    <div>
                        Products
                    </div>

                    <div>
                        Compliance score
                    </div>

                    <div>
                        Status
                    </div>

                </div>



                {{-- =================================================
                     TABLE BODY
                ================================================== --}}

                <div
                    id="sellerComplianceTable"
                    class="compliance-table-body"
                >


                    {{-- =================================================
                         SELLER 1
                    ================================================== --}}

                    <article
                        class="
                            seller-compliance-row
                        "

                        data-name="DelaCruzShop"

                        data-user-type="seller"

                        data-category="womens-apparel jewelry-and-watches health-and-beauty"

                        data-compliance="compliant"

                        data-status="active"

                        data-search="DelaCruzShop Juan Dela Cruz dela@example.com 0917-123-4567"
                    >

                        {{-- SELLER --}}

                        <div
                            class="
                                seller-cell
                            "
                        >

                            <div
                                class="
                                    seller-avatar
                                "
                            ></div>


                            <div
                                class="
                                    seller-name-group
                                "
                            >

                                <div
                                    class="
                                        seller-store-name
                                    "
                                >
                                    DelaCruzShop
                                </div>


                                <div
                                    class="
                                        seller-owner-name
                                    "
                                >
                                    Juan Dela Cruz
                                </div>

                            </div>

                        </div>



                        {{-- CATEGORY --}}

                        <div class="category-pill-group">
                            <span class="category-pill category-womens-apparel">Women’s Apparel</span>
                            <span class="category-pill category-jewelry-and-watches">Jewelry and Watches</span>
                            <span class="category-pill category-health-and-beauty">Health and Beauty</span>
                        </div>


                        {{-- PRODUCTS --}}
                        <div class="seller-products">
                            <span class="seller-products-total">105 Products</span>
                            <span class="seller-products-review" title="Products currently under admin review">3 Under Review</span>
                        </div>



                        {{-- SCORE --}}

                        <div
                            class="
                                score-cell
                            "
                        >

                            <div
                                class="
                                    score-number
                                "
                            >
                                93%
                            </div>


                            <div
                                class="
                                    score-bar
                                "
                            >

                                <span
                                    style="
                                        width: 93%;
                                    "
                                    class="
                                        score-fill
                                        score-green
                                    "
                                ></span>

                            </div>

                        </div>



                        {{-- STATUS --}}

                        <div>

                            <span
                                class="
                                    status-pill
                                    status-compliant
                                "
                            >
                                Compliant
                            </span>

                        </div>

                    </article>



                    {{-- =================================================
                         SELLER 2
                    ================================================== --}}

                    <article
                        class="
                            seller-compliance-row
                        "

                        data-name="WellnessHub"

                        data-user-type="seller"

                        data-category="health-and-beauty food-and-gourmet"

                        data-compliance="under-review"

                        data-status="active"

                        data-search="WellnessHub Angela Reyes angela@example.com 0918-222-1001"
                    >

                        <div class="seller-cell">

                            <div
                                class="
                                    seller-avatar
                                "
                            ></div>


                            <div
                                class="
                                    seller-name-group
                                "
                            >

                                <div
                                    class="
                                        seller-store-name
                                    "
                                >
                                    WellnessHub
                                </div>

                                <div
                                    class="
                                        seller-owner-name
                                    "
                                >
                                    Angela Reyes
                                </div>

                            </div>

                        </div>


                        <div class="category-pill-group">
                            <span class="category-pill category-health-and-beauty">Health and Beauty</span>
                            <span class="category-pill category-food-and-gourmet">Food and Gourmet</span>
                        </div>


                        <div class="seller-products">
                            <span class="seller-products-total">86 Products</span>
                            <span class="seller-products-review" title="Products currently under admin review">8 Under Review</span>
                        </div>


                        <div class="score-cell">

                            <div class="score-number">
                                78%
                            </div>

                            <div class="score-bar">

                                <span
                                    style="width: 78%;"
                                    class="
                                        score-fill
                                        score-orange
                                    "
                                ></span>

                            </div>

                        </div>


                        <div>

                            <span
                                class="
                                    status-pill
                                    status-under-review
                                "
                            >
                                Warning
                            </span>

                        </div>

                    </article>



                    {{-- =================================================
                         SELLER 3
                    ================================================== --}}

                    <article
                        class="
                            seller-compliance-row
                        "

                        data-name="TechCore PH"

                        data-user-type="seller"

                        data-category="electronics-and-gadgets automotive-motorcycle"

                        data-compliance="suspended"

                        data-status="suspended"

                        data-search="TechCore PH Marco Santos marco@example.com 0919-333-2002"
                    >

                        <div class="seller-cell">

                            <div
                                class="
                                    seller-avatar
                                "
                            ></div>


                            <div
                                class="
                                    seller-name-group
                                "
                            >

                                <div
                                    class="
                                        seller-store-name
                                    "
                                >
                                    TechCore PH
                                </div>

                                <div
                                    class="
                                        seller-owner-name
                                    "
                                >
                                    Marco Santos
                                </div>

                            </div>

                        </div>


                        <div class="category-pill-group">
                            <span class="category-pill category-electronics-and-gadgets">Electronics and Gadgets</span>
                            <span class="category-pill category-automotive-motorcycle">Automotive &amp; Motorcycle</span>
                        </div>


                        <div class="seller-products">
                            <span class="seller-products-total">142 Products</span>
                            <span class="seller-products-review" title="Products currently under admin review">12 Under Review</span>
                        </div>


                        <div class="score-cell">

                            <div class="score-number">
                                35%
                            </div>

                            <div class="score-bar">

                                <span
                                    style="width: 35%;"
                                    class="
                                        score-fill
                                        score-red
                                    "
                                ></span>

                            </div>

                        </div>


                        <div>

                            <span
                                class="
                                    status-pill
                                    status-suspended
                                "
                            >
                                Suspended
                            </span>

                        </div>

                    </article>



                    {{-- =================================================
                         SELLER 4
                    ================================================== --}}

                    <article
                        class="
                            seller-compliance-row
                        "

                        data-name="ActiveLife Hub"

                        data-user-type="seller"

                        data-category="sports-and-outdoors pet-supplies"

                        data-compliance="compliant"

                        data-status="active"

                        data-search="ActiveLife Hub Carlo Reyes carlo@example.com 0920-444-3003"
                    >

                        <div class="seller-cell">

                            <div
                                class="
                                    seller-avatar
                                "
                            ></div>


                            <div
                                class="
                                    seller-name-group
                                "
                            >

                                <div
                                    class="
                                        seller-store-name
                                    "
                                >
                                    ActiveLife Hub
                                </div>

                                <div
                                    class="
                                        seller-owner-name
                                    "
                                >
                                    Carlo Reyes
                                </div>

                            </div>

                        </div>


                        <div class="category-pill-group">
                            <span class="category-pill category-sports-and-outdoors">Sports and Outdoors</span>
                            <span class="category-pill category-pet-supplies">Pet Supplies</span>
                        </div>


                        <div class="seller-products">
                            <span class="seller-products-total">74 Products</span>
                            <span class="seller-products-review" title="Products currently under admin review">2 Under Review</span>
                        </div>


                        <div class="score-cell">

                            <div class="score-number">
                                93%
                            </div>

                            <div class="score-bar">

                                <span
                                    style="width: 93%;"
                                    class="
                                        score-fill
                                        score-green
                                    "
                                ></span>

                            </div>

                        </div>


                        <div>

                            <span
                                class="
                                    status-pill
                                    status-compliant
                                "
                            >
                                Compliant
                            </span>

                        </div>

                    </article>



                    {{-- =================================================
                         SELLER 5
                    ================================================== --}}

                    <article
                        class="
                            seller-compliance-row
                        "

                        data-name="Home Haven PH"

                        data-user-type="seller"

                        data-category="home-and-garden furniture-and-office-equipment"

                        data-compliance="compliant"

                        data-status="active"

                        data-search="Home Haven PH Maria Santos maria@example.com 0921-555-4004"
                    >

                        <div class="seller-cell">

                            <div
                                class="
                                    seller-avatar
                                "
                            ></div>


                            <div
                                class="
                                    seller-name-group
                                "
                            >

                                <div
                                    class="
                                        seller-store-name
                                    "
                                >
                                    Home Haven PH
                                </div>

                                <div
                                    class="
                                        seller-owner-name
                                    "
                                >
                                    Maria Santos
                                </div>

                            </div>

                        </div>


                        <div class="category-pill-group">
                            <span class="category-pill category-home-and-garden">Home and Garden</span>
                            <span class="category-pill category-furniture-and-office-equipment">Furniture and Office Equipment</span>
                        </div>


                        <div class="seller-products">
                            <span class="seller-products-total">118 Products</span>
                            <span class="seller-products-review" title="Products currently under admin review">5 Under Review</span>
                        </div>


                        <div class="score-cell">

                            <div class="score-number">
                                98%
                            </div>

                            <div class="score-bar">

                                <span
                                    style="width: 98%;"
                                    class="
                                        score-fill
                                        score-green
                                    "
                                ></span>

                            </div>

                        </div>


                        <div>

                            <span
                                class="
                                    status-pill
                                    status-compliant
                                "
                            >
                                Compliant
                            </span>

                        </div>

                    </article>



                    {{-- =================================================
                         SELLER 6
                    ================================================== --}}

                    <article
                        class="
                            seller-compliance-row
                        "

                        data-name="Little Sprouts"

                        data-user-type="seller"

                        data-category="kids-and-baby office-and-school-supplies books-and-media"

                        data-compliance="under-review"

                        data-status="active"

                        data-search="Little Sprouts Ana Garcia ana@example.com 0922-666-5005"
                    >

                        <div class="seller-cell">

                            <div
                                class="
                                    seller-avatar
                                "
                            ></div>


                            <div
                                class="
                                    seller-name-group
                                "
                            >

                                <div
                                    class="
                                        seller-store-name
                                    "
                                >
                                    Little Sprouts
                                </div>

                                <div
                                    class="
                                        seller-owner-name
                                    "
                                >
                                    Ana Garcia
                                </div>

                            </div>

                        </div>


                        <div class="category-pill-group">
                            <span class="category-pill category-kids-and-baby">Kids and Baby</span>
                            <span class="category-pill category-office-and-school-supplies">Office and School Supplies</span>
                            <span class="category-pill category-books-and-media">Books and Media</span>
                        </div>


                        <div class="seller-products">
                            <span class="seller-products-total">63 Products</span>
                            <span class="seller-products-review" title="Products currently under admin review">4 Under Review</span>
                        </div>


                        <div class="score-cell">

                            <div class="score-number">
                                93%
                            </div>

                            <div class="score-bar">

                                <span
                                    style="width: 93%;"
                                    class="
                                        score-fill
                                        score-green
                                    "
                                ></span>

                            </div>

                        </div>


                        <div>

                            <span
                                class="
                                    status-pill
                                    status-compliant
                                "
                            >
                                Compliant
                            </span>

                        </div>

                    </article>



                    {{-- =================================================
                         SELLER 7
                    ================================================== --}}

                    <article
                        class="
                            seller-compliance-row
                        "

                        data-name="Glow & Care"

                        data-user-type="seller"

                        data-category="health-and-beauty womens-apparel"

                        data-compliance="compliant"

                        data-status="active"

                        data-search="Glow & Care Sofia Cruz sofia@example.com 0923-777-6006"
                    >

                        <div class="seller-cell">

                            <div
                                class="
                                    seller-avatar
                                "
                            ></div>


                            <div
                                class="
                                    seller-name-group
                                "
                            >

                                <div
                                    class="
                                        seller-store-name
                                    "
                                >
                                    Glow & Care
                                </div>

                                <div
                                    class="
                                        seller-owner-name
                                    "
                                >
                                    Sofia Cruz
                                </div>

                            </div>

                        </div>


                        <div class="category-pill-group">
                            <span class="category-pill category-health-and-beauty">Health and Beauty</span>
                            <span class="category-pill category-womens-apparel">Women’s Apparel</span>
                        </div>


                        <div class="seller-products">
                            <span class="seller-products-total">97 Products</span>
                            <span class="seller-products-review" title="Products currently under admin review">7 Under Review</span>
                        </div>


                        <div class="score-cell">

                            <div class="score-number">
                                93%
                            </div>

                            <div class="score-bar">

                                <span
                                    style="width: 93%;"
                                    class="
                                        score-fill
                                        score-green
                                    "
                                ></span>

                            </div>

                        </div>


                        <div>

                            <span
                                class="
                                    status-pill
                                    status-compliant
                                "
                            >
                                Compliant
                            </span>

                        </div>

                    </article>

                </div>



                {{-- =================================================
                     NO RESULTS
                ================================================== --}}

                <div
                    id="sellerComplianceNoResults"

                    class="
                        hidden

                        px-[20px]

                        py-[48px]

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
                        No sellers found.
                    </p>

                    <p
                        class="
                            mt-[5px]

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
                        compliance-pagination
                    "
                >

                    <p
                        class="
                            text-[12px]

                            text-[#8E8885]
                        "
                    >

                        Showing

                        <span
                            id="sellerShowingCount"
                        >
                            1-7
                        </span>

                        out of 7 entries

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

                            id="sellerPreviousPage"

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

                            id="sellerNextPage"

                            class="
                                pagination-button
                            "

                            aria-label="Next page"
                        >
                            ›
                        </button>


                        <select
                            id="sellerItemsPerPage"

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


    {{-- =========================================================
         SELLER DETAILS MODAL
    ========================================================== --}}

    <div
        id="sellerDetailsModal"
        class="seller-details-overlay hidden"
        aria-hidden="true"
    >
        <div
            class="seller-details-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sellerDetailsTitle"
            tabindex="-1"
        >

            <div class="seller-details-title-row">
                <h2 id="sellerDetailsTitle">Seller Details</h2>
                <button
                    type="button"
                    id="sellerDetailsClose"
                    class="seller-details-close"
                    aria-label="Close seller details"
                >
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M6 6l12 12" />
                        <path d="M18 6L6 18" />
                    </svg>
                </button>
            </div>

            <div class="seller-details-profile-card">
                <div class="seller-details-avatar">
                    <svg viewBox="0 0 100 100" aria-hidden="true">
                        <circle cx="50" cy="31" r="13" fill="currentColor" />
                        <path d="M23 70c0-11.5 10.5-21 27-21s27 9.5 27 21v3c0 2.8-2.2 5-5 5H28c-2.8 0-5-2.2-5-5z" fill="currentColor" />
                    </svg>
                </div>

                <div class="seller-details-main">
                    <div class="seller-details-store-wrap">
                        <h3 id="sellerModalStoreName">DelaCruzShop</h3>
                        <p>Seller since <span id="sellerModalSince">August 2024</span></p>
                    </div>

                    <div class="seller-details-contact-list">
                        <div>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <circle cx="8" cy="8" r="3" />
                                <circle cx="17" cy="9" r="2.5" />
                                <path d="M2.5 20c.5-4 2.4-6 5.5-6s5 2 5.5 6" />
                                <path d="M13.5 15c2.9-.6 5.1.7 5.9 4" />
                            </svg>
                            <span id="sellerModalOwner">Juan Dela Cruz</span>
                        </div>

                        <div>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <rect x="3" y="5" width="18" height="14" rx="2" />
                                <path d="M4 7l8 6 8-6" />
                            </svg>
                            <span id="sellerModalEmail">juandelacruz@gmail.com</span>
                        </div>

                        <div>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M7 3h3l2 5-2.2 1.7a14.3 14.3 0 0 0 4.5 4.5L16 12l5 2v3c0 1.1-.9 2-2 2C10.7 19 5 13.3 5 5c0-1.1.9-2 2-2z" />
                            </svg>
                            <span id="sellerModalPhone">0917 123 4567</span>
                        </div>

                        <div>
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M12 21s7-6.3 7-12a7 7 0 1 0-14 0c0 5.7 7 12 7 12z" />
                                <circle cx="12" cy="9" r="2.5" />
                            </svg>
                            <span id="sellerModalLocation">Calamba, Laguna</span>
                        </div>
                    </div>
                </div>

                <div class="seller-details-summary">
                    <div class="seller-details-summary-block">
                        <span>Category</span>
                        <div id="sellerModalCategories" class="seller-modal-category-list"></div>
                    </div>

                    <div class="seller-details-summary-block seller-modal-products-block">
                        <span>Total Products</span>
                        <strong id="sellerModalProducts">145 Products</strong>
                    </div>
                </div>
            </div>

            <div class="seller-details-tabs" role="tablist" aria-label="Seller details sections">
                <button type="button" class="seller-details-tab active" data-tab="overview" role="tab" aria-selected="true">
                    Compliance Overview
                </button>
                <button type="button" class="seller-details-tab" data-tab="products" role="tab" aria-selected="false">
                    Products
                </button>
            </div>

            <div id="sellerOverviewPanel" class="seller-overview-panel">
                <div class="seller-overview-grid">
                    <div class="seller-compliance-summary">
                        <p class="seller-overview-heading">Compliant Score</p>
                        <div id="sellerModalScore" class="seller-modal-score">93%</div>
                        <div class="seller-modal-score-bar">
                            <span id="sellerModalScoreFill" style="width:93%"></span>
                        </div>
                        <span id="sellerModalCompliancePill" class="seller-modal-compliance-pill compliant">Compliant</span>
                        <p id="sellerModalPolicyText" class="seller-policy-text">
                            This seller is following the platform policies.
                        </p>
                    </div>

                    <div class="seller-documents-card">
                        <h4>Documents</h4>
                        <button type="button" class="seller-document-item" data-document="business_permit.png">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M7 3h7l4 4v14H7z" />
                                <path d="M14 3v5h5" />
                                <path d="M9 13h6M9 17h5" />
                            </svg>
                            <span>business_permit.png</span>
                        </button>
                        <button type="button" class="seller-document-item" data-document="valid_ID.png">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M7 3h7l4 4v14H7z" />
                                <path d="M14 3v5h5" />
                                <path d="M9 13h6M9 17h5" />
                            </svg>
                            <span>valid_ID.png</span>
                        </button>
                    </div>
                </div>

                <div class="seller-issues-card">
                    <div class="seller-issues-heading">
                        <h4>Recent Issues/Warnings</h4>
                    </div>
                    <div id="sellerModalIssues" class="seller-issues-list"></div>
                </div>
            </div>

            <div id="sellerProductsPanel" class="seller-products-panel hidden">
                <div class="seller-products-grid" id="sellerProductsGrid"></div>
            </div>

            <div class="seller-details-actions">
                <button type="button" id="sellerModalSuspend" class="seller-suspend-button">Suspend</button>
                <button type="button" id="sellerModalCancel" class="seller-cancel-button">Cancel</button>
            </div>
        </div>
    </div>



    {{-- =========================================================
         PRODUCT DETAILS MODAL
    ========================================================== --}}

    <div
        id="sellerProductDetailsModal"
        class="seller-product-details-overlay hidden"
        aria-hidden="true"
    >
        <div
            class="seller-product-details-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sellerProductModalTitle"
            tabindex="-1"
        >
            <button type="button" id="sellerProductBack" class="seller-product-back" aria-label="Back to products">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 12H5" />
                    <path d="M11 6l-6 6 6 6" />
                </svg>
            </button>

            <div class="seller-product-main-card">
                <div class="seller-product-gallery">
                    <div class="seller-product-main-image" id="sellerProductMainImage"></div>
                    <div class="seller-product-thumbnails" id="sellerProductThumbnails"></div>
                    <button type="button" class="seller-product-thumb-next" id="sellerProductThumbNext" aria-label="Next product image">›</button>
                </div>

                <div class="seller-product-main-info">
                    <h2 id="sellerProductModalTitle">Men’s Graphic T-shirt</h2>

                    <div class="seller-product-price-row">
                        <strong id="sellerProductModalPrice">₱59</strong>
                        <span id="sellerProductModalSold">0 sold</span>
                    </div>

                    <div class="seller-product-field">
                        <span>Category</span>
                        <div id="sellerProductModalCategory" class="seller-product-category-pill">Women’s Apparel</div>
                    </div>

                    <div class="seller-product-field seller-product-stock-field">
                        <span>Stock</span>
                        <strong id="sellerProductModalStock">154 pieces</strong>
                    </div>

                    <div id="sellerProductComplianceNotice" class="seller-product-compliance-notice hidden"></div>
                </div>

                <div class="seller-product-uploaded">
                    Uploaded: <strong id="sellerProductModalUploaded">September 9, 2026&nbsp;&nbsp;2:13 PM</strong>
                </div>
            </div>

            <div class="seller-product-description-card">
                <h3>Product Description</h3>
                <p id="sellerProductModalDescription">High-quality cotton graphic T-shirt for everyday wear. Comfortable, breathable, and perfect for casual outfits. Available in multiple sizes.</p>

                <div class="seller-product-divider"></div>

                <h3>Product Details</h3>
                <div class="seller-product-detail-grid">
                    <span>Brand</span><strong id="sellerProductDetailBrand">HangLoose</strong>
                    <span>Material</span><strong id="sellerProductDetailMaterial">100% Cotton</strong>
                    <span>Sizes</span><strong id="sellerProductDetailSizes">S, M, L, XL, XXL</strong>
                    <span>Colors</span><strong id="sellerProductDetailColors">Black, White, Gray</strong>
                    <span>Weight</span><strong id="sellerProductDetailWeight">150g</strong>
                    <span>Country of Origin</span><strong id="sellerProductDetailOrigin">Philippines</strong>
                </div>
            </div>

            <div class="seller-product-actions" id="sellerProductActions">
                <button type="button" class="seller-product-remove">Remove</button>
                <button type="button" class="seller-product-warning">Issue Warning</button>
                <button type="button" class="seller-product-approve">Approve</button>
                <button type="button" id="sellerProductClose" class="seller-product-close-action" hidden>Close</button>
            </div>
        </div>
    </div>


    {{-- =========================================================
         REMOVE PRODUCT MODAL
    ========================================================== --}}

    <div
        id="sellerRemoveProductModal"
        class="seller-remove-product-overlay hidden"
        aria-hidden="true"
    >
        <div
            class="seller-remove-product-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sellerRemoveProductTitle"
            tabindex="-1"
        >
            <div class="seller-remove-product-header">
                <h2 id="sellerRemoveProductTitle">Remove Product</h2>
            </div>

            <p class="seller-remove-product-intro">
                You are about to remove this product from the seller's store.
            </p>

            <fieldset class="seller-remove-reasons">
                <legend>Reason<span>*</span></legend>

                <label class="seller-remove-option">
                    <input type="radio" name="remove_reason" value="Prohibited product">
                    <span class="seller-remove-radio"></span>
                    <span>Prohibited product</span>
                </label>

                <label class="seller-remove-option">
                    <input type="radio" name="remove_reason" value="Product does not match the registered category">
                    <span class="seller-remove-radio"></span>
                    <span>Product does not match the registered category</span>
                </label>

                <label class="seller-remove-option">
                    <input type="radio" name="remove_reason" value="Inappropriate product content">
                    <span class="seller-remove-radio"></span>
                    <span>Inappropriate product content</span>
                </label>

                <label class="seller-remove-option">
                    <input type="radio" name="remove_reason" value="Misleading product information">
                    <span class="seller-remove-radio"></span>
                    <span>Misleading product information</span>
                </label>

                <label class="seller-remove-option">
                    <input type="radio" name="remove_reason" value="Violation of platform policies">
                    <span class="seller-remove-radio"></span>
                    <span>Violation of platform policies</span>
                </label>

                <label class="seller-remove-option">
                    <input type="radio" name="remove_reason" value="Other">
                    <span class="seller-remove-radio"></span>
                    <span>Other (please specify)</span>
                </label>
            </fieldset>

            <div class="seller-remove-details-wrap">
                <label for="sellerRemoveProductDetails">Additional Details (Optional)</label>
                <div class="seller-remove-textarea-wrap">
                    <textarea
                        id="sellerRemoveProductDetails"
                        maxlength="300"
                        rows="3"
                        placeholder="Write additional details here..."
                    ></textarea>
                    <span id="sellerRemoveProductDetailsCount">0/300</span>
                </div>
            </div>

            <div class="seller-remove-actions">
                <button
                    type="button"
                    id="sellerRemoveProductCancel"
                    class="seller-remove-cancel"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    id="sellerRemoveProductSubmit"
                    class="seller-remove-submit"
                >
                    Remove
                </button>
            </div>
        </div>
    </div>


    {{-- =========================================================
         ISSUE WARNING MODAL
    ========================================================== --}}

    <div
        id="sellerIssueWarningModal"
        class="seller-issue-warning-overlay hidden"
        aria-hidden="true"
    >
        <div
            class="seller-issue-warning-dialog"
            role="dialog"
            aria-modal="true"
            aria-labelledby="sellerIssueWarningTitle"
            tabindex="-1"
        >
            <div class="seller-issue-warning-header">
                <h2 id="sellerIssueWarningTitle">Issue Warning</h2>
            </div>

            <p class="seller-issue-warning-intro">
                You are about to issue a warning for this product due to a
                violation of ShopEase's platform policies.
            </p>

            <fieldset class="seller-warning-reasons">
                <legend>Reason<span>*</span></legend>

                <label class="seller-warning-option">
                    <input type="radio" name="warning_reason" value="Product does not match the registered category">
                    <span class="seller-warning-radio"></span>
                    <span>Product does not match the registered category</span>
                </label>

                <label class="seller-warning-option">
                    <input type="radio" name="warning_reason" value="Prohibited product">
                    <span class="seller-warning-radio"></span>
                    <span>Prohibited product</span>
                </label>

                <label class="seller-warning-option">
                    <input type="radio" name="warning_reason" value="Inappropriate or misleading product content">
                    <span class="seller-warning-radio"></span>
                    <span>Inappropriate or misleading product content</span>
                </label>

                <label class="seller-warning-option">
                    <input type="radio" name="warning_reason" value="Misleading product information">
                    <span class="seller-warning-radio"></span>
                    <span>Misleading product information</span>
                </label>

                <label class="seller-warning-option">
                    <input type="radio" name="warning_reason" value="Unauthorized or restricted item">
                    <span class="seller-warning-radio"></span>
                    <span>Unauthorized or restricted item</span>
                </label>

                <label class="seller-warning-option">
                    <input type="radio" name="warning_reason" value="Other">
                    <span class="seller-warning-radio"></span>
                    <span>Other (please specify)</span>
                </label>
            </fieldset>

            <div class="seller-warning-details-wrap">
                <label for="sellerWarningDetails">Additional Details (Optional)</label>
                <div class="seller-warning-textarea-wrap">
                    <textarea
                        id="sellerWarningDetails"
                        maxlength="300"
                        rows="3"
                        placeholder="Write additional details here..."
                    ></textarea>
                    <span id="sellerWarningDetailsCount">0/300</span>
                </div>
            </div>

            <div class="seller-warning-actions">
                <button
                    type="button"
                    id="sellerWarningCancel"
                    class="seller-warning-cancel"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    id="sellerWarningSubmit"
                    class="seller-warning-submit"
                >
                    Issue Warning
                </button>
            </div>
        </div>
    </div>

    

    {{-- =========================================================
         PRODUCT MODERATION FLASH MESSAGE
    ========================================================== --}}
    <div
        id="productDecisionFlash"
        class="product-decision-flash"
        role="status"
        aria-live="polite"
        aria-atomic="true"
    >
        <div id="productDecisionFlashIcon" class="product-decision-flash-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12l4 4L19 6" />
            </svg>
        </div>

        <div class="product-decision-flash-copy">
            <strong id="productDecisionFlashTitle">Product updated</strong>
            <span id="productDecisionFlashMessage">The product moderation status has been updated.</span>
        </div>

        <button
            type="button"
            id="productDecisionFlashClose"
            class="product-decision-flash-close"
            aria-label="Close notification"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <path d="M6 6l12 12" />
                <path d="M18 6L6 18" />
            </svg>
        </button>
    </div>

    </main>



    {{-- =========================================================
         CSS
    ========================================================== --}}

    <style>


        /* =====================================================
           SUMMARY CARDS
        ====================================================== */

        .compliance-stat-card {

            min-height:
                108px;

            background:
                #FFFFFF;

            border:
                1px solid
                #F0E9E6;

            border-radius:
                16px;

            box-shadow:
                0
                2px
                12px
                rgba(
                    42,
                    20,
                    15,
                    0.05
                );

            padding:
                16px;

            box-sizing:
                border-box;

        }


        .compliance-stat-main {

            display:
                flex;

            align-items:
                center;

            gap:
                12px;

        }


        /*
         * IMPORTANT:
         * The PNG already contains its own pink square.
         * We DO NOT create another background wrapper.
         */

        .compliance-stat-icon {

            width:
                40px;

            height:
                40px;

            flex:
                0 0 40px;

            object-fit:
                contain;

            display:
                block;

        }


        .compliance-stat-content {

            min-width:
                0;

        }


        .compliance-stat-number {

            font-size:
                23px;

            line-height:
                1;

            font-weight:
                700;

            color:
                #17120F;

        }


        .compliance-stat-label {

            margin-top:
                5px;

            font-size:
                13px;

            line-height:
                1.15;

            color:
                #8C8784;

        }


        .compliance-stat-growth {

            margin-top:
                15px;

            font-size:
                12px;

            line-height:
                1.2;

            color:
                #8C8784;

            white-space:
                nowrap;

        }


        .compliance-stat-growth strong {

            color:
                #11951B;

            font-weight:
                500;

            margin-right:
                2px;

        }


        .growth-arrow {

            color:
                #11951B;

            font-size:
                16px;

            vertical-align:
                -1px;

            margin-right:
                2px;

        }



        /* =====================================================
           FILTER BAR
        ====================================================== */

        .compliance-filter-card {

            min-height:
                64px;

            display:
                flex;

            align-items:
                center;

            gap:
                10px;

            padding:
                10px
                16px;

            background:
                #FFFFFF;

            border:
                1px solid
                #F0E9E6;

            border-radius:
                11px;

            box-shadow:
                0
                2px
                12px
                rgba(
                    42,
                    20,
                    15,
                    0.04
                );

            margin-bottom:
                14px;

            box-sizing:
                border-box;

        }


        .compliance-search-wrap {

            position:
                relative;

            width:
                330px;

            flex:
                0 0 330px;

        }


        .compliance-search {

            width:
                100%;

            height:
                36px;

            border:
                1px solid
                #D9D6D4;

            border-radius:
                8px;

            background:
                #FFFFFF;

            padding:
                0 38px 0 12px;

            box-sizing:
                border-box;

            outline:
                none;

            font-family:
                inherit;

            font-size:
                13px;

            color:
                #76716E;

        }


        .compliance-search::placeholder {

            color:
                #76716E;

            opacity:
                1;

        }


        .compliance-search:focus,
        .compliance-filter:focus {

            border-color:
                #7B1B1B;

            box-shadow:
                0
                0
                0
                2px
                rgba(
                    123,
                    27,
                    27,
                    0.10
                );

        }


        .compliance-search-icon {

            position:
                absolute;

            right:
                10px;

            top:
                50%;

            width:
                17px;

            height:
                17px;

            transform:
                translateY(-50%);

            color:
                #76716E;

            pointer-events:
                none;

        }


        .compliance-filter {

            height:
                36px;

            border:
                1px solid
                #D9D6D4;

            border-radius:
                8px;

            background:
                #FFFFFF;

            padding:
                0 31px 0 11px;

            outline:
                none;

            cursor:
                pointer;

            font-family:
                inherit;

            font-size:
                13px;

            color:
                #76716E;

            box-sizing:
                border-box;

        }


        .filter-user-type {

            width:
                188px;

        }


        .filter-compliance {

            width:
                168px;

        }


        .filter-status {

            width:
                120px;

        }


        .compliance-date-wrap {

            width:
                174px;

            height:
                36px;

            position:
                relative;

            flex:
                0 0 174px;

        }


        .compliance-date-icon {

            position:
                absolute;

            left:
                10px;

            top:
                50%;

            width:
                15px;

            height:
                15px;

            transform:
                translateY(-50%);

            color:
                #17120F;

            pointer-events:
                none;

        }


        .compliance-date-input {

            width:
                100%;

            height:
                100%;

            border:
                1px solid
                #D9D6D4;

            border-radius:
                8px;

            background:
                #FFFFFF;

            padding:
                0
                10px
                0
                33px;

            outline:
                none;

            font-family:
                inherit;

            font-size:
                11px;

            color:
                #AAA6A4;

            box-sizing:
                border-box;

            cursor:
                pointer;

        }


        .compliance-refresh {

            width:
                28px;

            height:
                36px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                center;

            border:
                none;

            border-radius:
                50%;

            background:
                transparent;

            color:
                #17120F;

            cursor:
                pointer;

            flex:
                0 0 28px;

            transition:
                all
                .18s
                ease;

        }


        .compliance-refresh:hover {

            background:
                #FFF2EE;

            transform:
                scale(1.05);

        }


        .compliance-refresh svg {

            width:
                21px;

            height:
                21px;

        }



        /* =====================================================
           TABLE CARD
        ====================================================== */

        .compliance-table-card {

            overflow:
                hidden;

            background:
                #FFFFFF;

            border:
                1px solid
                #F0E9E6;

            border-radius:
                12px;

            box-shadow:
                0
                2px
                12px
                rgba(
                    42,
                    20,
                    15,
                    0.04
                );

        }


        .compliance-table-header,
        .seller-compliance-row {

            display:
                grid;

            grid-template-columns:
                .90fr
                1.60fr
                1.10fr
                1.00fr
                .62fr;

            column-gap:
                20px;

            align-items:
                center;

        }


        .compliance-table-header {

            min-height:
                48px;

            margin:
                12px
                0
                0;

            padding:
                0
                18px;

            font-size:
                13px;

            line-height:
                1.2;

            font-weight:
                500;

            color:
                #8C8784;

        }


        .seller-compliance-row {

            min-height:
                58px;

            padding:
                0
                18px;

            border-top:
                1px solid
                #DDD9D7;

            transition:
                background-color
                .18s
                ease;

        }


        .seller-compliance-row:hover {

            background:
                #FFFBF9;

        }

        /* User Management table typography */
        .compliance-table-card,
        .compliance-table-card * {
            font-family: 'Poppins', sans-serif;
        }


        /* Ensure JavaScript filtering can actually hide rows/results.
           This style block is loaded after Tailwind, so the selector must
           explicitly override the row display declaration above. */

        .seller-compliance-row.hidden {

            display:
                none !important;

        }


        #sellerComplianceNoResults.hidden,
        .pagination-button.hidden {

            display:
                none !important;

        }


        .seller-cell {

            display:
                flex;

            align-items:
                center;

            gap:
                9px;

            min-width:
                0;

        }


        .seller-avatar {

            width:
                30px;

            height:
                30px;

            flex:
                0 0 30px;

            border-radius:
                50%;

            background:
                #DADADA;

        }


        .seller-name-group {

            min-width:
                0;

        }


        .seller-store-name {

            font-size:
                12px;

            line-height:
                1.25;

            font-weight:
                500;

            color:
                #17120F;

            white-space:
                nowrap;

            overflow:
                hidden;

            text-overflow:
                ellipsis;

        }


        .seller-owner-name {

            margin-top:
                2px;

            font-size:
                12px;

            line-height:
                1.2;

            font-weight:
                400;

            color:
                #8C8784;

            white-space:
                nowrap;

            overflow:
                hidden;

            text-overflow:
                ellipsis;

        }


        .seller-products {

            font-size:
                13px;

            line-height:
                1.25;

            color:
                #17120F;

            white-space:
                nowrap;

        }



        /* =====================================================
           CATEGORY PILLS
           Sellers may have multiple registered categories.
        ====================================================== */

        .category-pill-group {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 5px;
            max-width: 100%;
            transform: translateX(-10px);
        }

        .compliance-table-header > div:nth-child(2) {
            transform: translateX(-10px);
        }

        .category-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 22px;
            padding: 0 10px;
            border-radius: 999px;
            font-size: 12px;
            line-height: 1;
            font-weight: 500;
            white-space: nowrap;
        }

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
        .category-default { background: #F1EFEE; color: #6B6663; }

        .seller-products {
            display: flex;
            flex-direction: column;
            gap: 3px;
            font-size: 10px;
            color: #17120F;
            white-space: nowrap;
        }

        .seller-products-total {
            font-weight: 600;
            color: #17120F;
        }

        .seller-products-review {
            font-size: 12px;
            line-height: 1.2;
            color: #C56A19;
            font-weight: 500;
        }


        /* =====================================================
           SCORE
        ====================================================== */

        .score-cell {

            min-width:
                125px;

        }


        .score-number {

            font-size:
                13px;

            line-height:
                1.2;

            font-weight:
                500;

            color:
                #17120F;

            margin-bottom:
                3px;

        }


        .score-bar {

            width:
                122px;

            height:
                5px;

            border-radius:
                999px;

            background:
                #DADADA;

            overflow:
                hidden;

        }


        .score-fill {

            display:
                block;

            height:
                100%;

            border-radius:
                999px;

        }


        .score-green {

            background:
                #13951A;

        }


        .score-orange {

            background:
                #FF9D22;

        }


        .score-red {

            background:
                #CF1616;

        }



        /* =====================================================
           STATUS
        ====================================================== */

        .status-pill {

            display:
                inline-flex;

            align-items:
                center;

            justify-content:
                center;

            min-height:
                21px;

            padding:
                0
                11px;

            border-radius:
                999px;

            font-size:
                12px;

            line-height:
                1;

            font-weight:
                500;

            white-space:
                nowrap;

        }


        .status-compliant {

            background:
                #D8ECD2;

            color:
                #37802E;

        }


        .status-warning {

            background:
                #FFE4D3;

            color:
                #C56A19;

        }


        .status-under-review {

            background:
                #FFF2C9;

            color:
                #9A7100;

        }


        .status-suspended {

            background:
                #FFD8DB;

            color:
                #C3313C;

        }



        /* =====================================================
           PAGINATION
        ====================================================== */

        .compliance-pagination {

            min-height:
                58px;

            display:
                flex;

            align-items:
                center;

            justify-content:
                space-between;

            padding:
                0
                20px;

            border-top:
                1px solid
                #E4DFDD;

        }


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

            border:
                none;

            border-radius:
                6px;

            background:
                transparent;

            font-family:
                inherit;

            font-size:
                11px;

            color:
                #615A57;

            cursor:
                pointer;

            transition:
                all
                .18s
                ease;

        }


        .pagination-button:hover:not(.disabled):not(.current) {

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
           RESPONSIVE
        ====================================================== */

        @media (max-width: 1250px) {

            .compliance-stat-card {

                min-width:
                    0;

            }


            .compliance-stat-number {

                font-size:
                    20px;

            }


            .compliance-stat-label {

                font-size:
                    11px;

            }


            .compliance-stat-growth {

                font-size:
                    9px;

            }


            .compliance-filter-card {

                flex-wrap:
                    wrap;

            }

        }



        @media (max-width: 1050px) {

            .compliance-table-card {

                overflow-x:
                    auto;

                scrollbar-width:
                    none;

                -ms-overflow-style:
                    none;

            }


            .compliance-table-card::-webkit-scrollbar {

                display:
                    none;

            }


            .compliance-table-header,
            .seller-compliance-row {

                min-width:
                    930px;

            }

        }



        @media (max-width: 900px) {

            .seller-compliance-page {

                margin-left:
                    80px;

            }


            .seller-compliance-page > div {

                padding-left:
                    24px;

                padding-right:
                    24px;

            }


            .compliance-stat-card {

                min-height:
                    105px;

            }

        }



        @media (max-width: 760px) {

            .seller-compliance-page {

                margin-left:
                    0;

            }


            .seller-compliance-page > div {

                padding-left:
                    16px;

                padding-right:
                    16px;

            }


            .compliance-filter-card {

                align-items:
                    stretch;

            }


            .compliance-search-wrap {

                width:
                    100%;

                flex-basis:
                    100%;

            }


            .compliance-filter {

                flex:
                    1;

                width:
                    auto;

            }


            .compliance-date-wrap {

                flex:
                    1;

                width:
                    auto;

            }


            .compliance-refresh {

                align-self:
                    center;

            }


            .compliance-pagination {

                padding:
                    0
                    12px;

            }

        }



        @media (max-width: 650px) {

            .compliance-stat-card {

                min-height:
                    100px;

            }

        }



        @media (max-width: 520px) {

            .compliance-stat-card {

                padding:
                    13px 10px;

            }


            .compliance-stat-icon {

                width:
                    36px;

                height:
                    36px;

                flex-basis:
                    36px;

            }


            .compliance-stat-number {

                font-size:
                    18px;

            }


            .compliance-stat-growth {

                display:
                    none;

            }

        }



        /* =====================================================
           REDUCED MOTION
        ====================================================== */

        @media (prefers-reduced-motion: reduce) {

            .seller-compliance-page,
            .compliance-refresh,
            .seller-compliance-row,
            .pagination-button {

                transition:
                    none !important;

            }

        }



        /* =====================================================
           SELLER DETAILS MODAL + PAGE UX
        ====================================================== */

        .seller-compliance-row {
            cursor: pointer;
            outline: none;
        }

        .seller-compliance-row:focus-visible {
            box-shadow: inset 0 0 0 2px #E6A596;
        }

        .seller-compliance-row:hover .seller-store-name {
            color: #7B1B1B;
        }

        .seller-details-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(23, 18, 15, 0.40);
            backdrop-filter: blur(3px);
            opacity: 1;
            transition: opacity .18s ease;
        }

        .seller-details-overlay.hidden {
            display: none !important;
        }

        .seller-details-dialog {
            width: min(900px, calc(100vw - 32px));
            max-height: calc(100vh - 32px);
            overflow-y: auto;
            background: #FFFFFF;
            border-radius: 24px;
            padding: 27px 30px 26px;
            box-shadow: 0 24px 70px rgba(35, 24, 20, .22);
            box-sizing: border-box;
            transform: translateY(0) scale(1);
            transition: transform .18s ease;
            scrollbar-width: thin;
            scrollbar-color: #DDD7D4 transparent;
        }

        .seller-details-title-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 18px;
        }

        .seller-details-title-row h2 {
            margin: 0;
            font-size: 16px;
            line-height: 1.1;
            font-weight: 600;
            color: #090807;
        }

        .seller-details-close {
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #F7F3F1;
            color: #5F5956;
            cursor: pointer;
            transition: .18s ease;
        }

        .seller-details-close:hover {
            background: #EFE8E5;
            color: #17120F;
        }

        .seller-details-close svg {
            width: 16px;
            height: 16px;
        }

        .seller-details-profile-card {
            min-height: 235px;
            border: 1px solid #D8D4D2;
            border-radius: 20px;
            padding: 21px 24px;
            display: grid;
            grid-template-columns: 120px minmax(0, 1fr) 190px;
            gap: 22px;
            align-items: start;
            box-sizing: border-box;
        }

        .seller-details-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #D9D9D9;
            color: #050505;
            margin: 0 auto;
        }

        .seller-details-avatar svg {
            width: 62px;
            height: 62px;
        }

        .seller-details-store-wrap h3 {
            margin: 7px 0 1px;
            font-size: 19px;
            line-height: 1.05;
            font-weight: 700;
            color: #080706;
        }

        .seller-details-store-wrap p {
            margin: 0 0 21px;
            font-size: 12px;
            color: #1A1817;
        }

        .seller-details-contact-list {
            display: grid;
            gap: 12px;
        }

        .seller-details-contact-list > div {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
            color: #131110;
            font-size: 12px;
        }

        .seller-details-contact-list svg {
            width: 15px;
            height: 15px;
            flex: 0 0 19px;
            color: #151313;
        }

        .seller-details-summary {
            min-width: 0;
            padding-top: 17px;
        }

        .seller-details-summary-block > span {
            display: block;
            margin-bottom: 9px;
            font-size: 12px;
            color: #8B8582;
        }

        .seller-modal-category-list {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-bottom: 35px;
        }

        .seller-modal-category {
            display: inline-flex;
            align-items: center;
            min-height: 18px;
            padding: 2px 10px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 500;
            white-space: nowrap;
        }

        .seller-modal-products-block strong {
            font-size: 12px;
            font-weight: 500;
            color: #090807;
        }

        .seller-details-tabs {
            display: flex;
            align-items: end;
            gap: 0;
            margin-top: 20px;
        }

        .seller-details-tab {
            border: 0;
            background: transparent;
            padding: 9px 18px 8px;
            border-radius: 18px 18px 0 0;
            font: inherit;
            font-size: 16px;
            font-weight: 600;
            color: #0B0A09;
            cursor: pointer;
            transition: .18s ease;
        }

        .seller-details-tab.active {
            background: #FBF5F2;
        }

        .seller-details-tab:not(.active):hover {
            background: #F8F4F2;
        }

        .seller-overview-panel {
            background: #FBF5F2;
            border-radius: 0 18px 18px 18px;
            padding: 21px 23px 23px;
        }

        .seller-overview-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 240px;
            gap: 28px;
            align-items: start;
        }

        .seller-overview-heading {
            margin: 12px 0 16px;
            font-size: 17px;
            font-weight: 500;
            color: #11100F;
        }

        .seller-modal-score {
            font-size: 19px;
            line-height: 1;
            font-weight: 600;
            color: #149822;
            margin-left: 14px;
        }

        .seller-modal-score-bar {
            width: min(100%, 390px);
            height: 8px;
            margin: 7px 0 9px 14px;
            overflow: hidden;
            border-radius: 999px;
            background: #D8D8D8;
        }

        .seller-modal-score-bar span {
            display: block;
            height: 100%;
            background: #149822;
            border-radius: inherit;
            transition: width .24s ease, background-color .18s ease;
        }

        .seller-modal-score.modal-score-green {
            background: transparent !important;
            color: #149822;
        }

        .seller-modal-score.modal-score-orange {
            background: transparent !important;
            color: #FF9D22;
        }

        .seller-modal-score.modal-score-red {
            background: transparent !important;
            color: #CF1616;
        }

        .seller-modal-score-bar span.score-orange {
            background: #FF9D22;
        }

        .seller-modal-score-bar span.score-red {
            background: #CF1616;
        }

        .seller-modal-compliance-pill {
            display: inline-flex;
            align-items: center;
            min-height: 18px;
            padding: 2px 10px;
            margin-left: 14px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 500;
        }

        .seller-modal-compliance-pill.compliant {
            background: #D9EDCF;
            color: #3A812F;
        }

        .seller-modal-compliance-pill.warning {
            background: #FFE4D2;
            color: #C56A19;
        }

        .seller-modal-compliance-pill.under-review {
            background: #FFF0C3;
            color: #977000;
        }

        .seller-modal-compliance-pill.suspended {
            background: #FFD8DC;
            color: #C3313C;
        }

        .seller-policy-text {
            margin: 15px 0 0 14px;
            font-size: 13px;
            color: #827B78;
        }

        .seller-documents-card {
            min-height: 172px;
            border-radius: 13px;
            background: #FFFFFF;
            padding: 15px;
            box-shadow: 0 2px 12px rgba(40, 25, 20, .08);
            box-sizing: border-box;
        }

        .seller-documents-card h4,
        .seller-issues-heading h4 {
            margin: 0 0 10px;
            font-size: 16px;
            line-height: 1.1;
            font-weight: 500;
            color: #11100F;
        }

        .seller-document-item {
            width: 100%;
            min-height: 28px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 7px;
            padding: 0 10px;
            border: 1px solid #D9D5D3;
            border-radius: 8px;
            background: #FFFFFF;
            color: #1C1918;
            font: inherit;
            font-size: 10px;
            text-align: left;
            cursor: pointer;
            transition: .16s ease;
        }

        .seller-document-item:hover {
            background: #FBF8F7;
            border-color: #C7C1BE;
        }

        .seller-document-item svg {
            width: 15px;
            height: 15px;
            color: #A92F28;
            flex: 0 0 17px;
        }

        .seller-issues-card {
            margin-top: 20px;
            background: #FFFFFF;
            border-radius: 13px;
            padding: 19px 21px 16px;
            box-shadow: 0 2px 12px rgba(40, 25, 20, .07);
        }

        .seller-issues-list {
            display: grid;
            gap: 6px;
        }

        .seller-issue-item {
            display: grid;
            grid-template-columns: 54px minmax(0, 1fr) auto;
            gap: 9px;
            align-items: center;
            min-height: 54px;
        }

        .seller-issue-thumb {
            width: 42px;
            height: 42px;
            border: 1px solid #BBB2AD;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F0ECEA;
            overflow: hidden;
        }

        .seller-issue-thumb svg {
            width: 30px;
            height: 37px;
        }

        .seller-issue-info {
            min-width: 0;
        }

        .seller-issue-info strong {
            display: block;
            font-size: 12px;
            line-height: 1.15;
            font-weight: 600;
            color: #151312;
        }

        .seller-issue-info p {
            margin: 3px 0 0;
            font-size: 10px;
            color: #928B88;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .seller-issue-meta {
            min-width: 118px;
            display: grid;
            justify-items: end;
            gap: 3px;
        }

        .seller-issue-status {
            display: inline-flex;
            align-items: center;
            min-height: 18px;
            padding: 2px 13px;
            border-radius: 999px;
            font-size: 9px;
            font-weight: 500;
        }

        .seller-issue-status.warning {
            color: #C56A19;
            background: #FFE4D2;
        }

        .seller-issue-status.removed {
            color: #C3313C;
            background: #FFD8DC;
        }

        .seller-issue-date {
            font-size: 10px;
            color: #151312;
        }

        .seller-products-panel {
            background: #FBF5F2;
            border-radius: 0 18px 18px 18px;
            padding: 22px 18px 30px;
            max-height: 505px;
            min-height: 505px;
            overflow-y: auto;
            box-sizing: border-box;
            scrollbar-width: thin;
            scrollbar-color: #B9B2AE transparent;
        }

        .seller-products-panel::-webkit-scrollbar {
            width: 6px;
        }

        .seller-products-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        .seller-products-panel::-webkit-scrollbar-thumb {
            background: #B9B2AE;
            border-radius: 999px;
        }

        .seller-products-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
            align-items: start;
        }

        .seller-product-card {
            min-width: 0;
            background: #FFFFFF;
            border: 1px solid #D7D1CE;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 5px rgba(38, 28, 24, .05);
        }

        .seller-product-card.status-green {
            border-color: #25A32D;
        }

        .seller-product-card.status-orange {
            border-color: #FF9D22;
        }

        .seller-product-card.status-red {
            border-color: #D71919;
        }

        .seller-product-image {
            height: 151px;
            background: #F8F1EE;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .seller-product-image svg,
        .seller-product-image img {
            width: 100%;
            height: 100%;
            display: block;
        }

        .seller-product-info {
            padding: 7px 6px 8px;
        }

        .seller-product-name {
            min-height: 30px;
            font-size: 9px;
            line-height: 1.2;
            color: #11100F;
            font-weight: 500;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .seller-product-bottom {
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 5px;
        }

        .seller-product-price {
            font-size: 10px;
            font-weight: 700;
            color: #17120F;
        }

        .seller-product-sold {
            font-size: 8px;
            color: #17120F;
            white-space: nowrap;
        }

        .seller-products-empty {
            min-height: 220px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .seller-products-empty-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: #FFF0EB;
            color: #8D211D;
            font-size: 19px;
        }

        .seller-products-empty h4 {
            margin: 12px 0 4px;
            font-size: 16px;
            font-weight: 600;
        }

        .seller-products-empty p {
            max-width: 350px;
            margin: 0;
            font-size: 12px;
            line-height: 1.5;
            color: #89827E;
        }

        .seller-details-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .seller-suspend-button,
        .seller-cancel-button {
            min-width: 108px;
            height: 38px;
            padding: 0 18px;
            border-radius: 8px;
            font: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: .18s ease;
        }

        .seller-suspend-button {
            border: 1px solid #D51818;
            background: #FFDCDC;
            color: #B40000;
        }

        .seller-suspend-button:hover {
            background: #FFCACA;
        }

        .seller-cancel-button {
            border: 1px solid #17120F;
            background: #FFFFFF;
            color: #17120F;
        }

        .seller-cancel-button:hover {
            background: #F7F4F2;
        }

        body.seller-modal-open {
            overflow: hidden;
        }

        @media (max-width: 820px) {
            .seller-products-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .seller-products-panel {
                min-height: 430px;
                max-height: 430px;
            }

            .seller-details-profile-card {
                grid-template-columns: 106px minmax(0, 1fr);
            }
            .seller-details-summary {
                grid-column: 2;
                padding-top: 0;
            }
            .seller-overview-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .seller-products-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .seller-products-panel {
                min-height: 390px;
                max-height: 390px;
                padding: 16px 14px 24px;
            }

            .seller-details-overlay {
                padding: 10px;
            }
            .seller-details-dialog {
                width: 100%;
                max-height: calc(100vh - 20px);
                padding: 24px 18px 20px;
                border-radius: 22px;
            }
            .seller-details-profile-card {
                grid-template-columns: 1fr;
                gap: 18px;
            }
            .seller-details-avatar {
                margin: 0;
            }
            .seller-details-summary {
                grid-column: auto;
            }
            .seller-issue-item {
                grid-template-columns: 46px minmax(0, 1fr);
            }
            .seller-issue-meta {
                grid-column: 2;
                justify-items: start;
                min-width: 0;
            }
            .seller-details-actions {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }
            .seller-suspend-button,
            .seller-cancel-button {
                width: 100%;
                min-width: 0;
            }
        }


        /* =====================================================
           PRODUCT DETAILS MODAL
        ====================================================== */

        .seller-product-details-overlay {
            position: fixed;
            inset: 0;
            z-index: 120;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            background: rgba(23, 18, 15, .42);
            backdrop-filter: blur(3px);
        }

        .seller-product-details-overlay.hidden {
            display: none !important;
        }

        .seller-product-details-dialog {
            position: relative;
            width: min(916px, calc(100vw - 28px));
            max-height: calc(100vh - 10px);
            overflow-y: auto;
            background: #FFFFFF;
            border-radius: 26px;
            padding: 20px 34px 34px;
            box-sizing: border-box;
            box-shadow: 0 24px 70px rgba(35, 24, 20, .24);
            scrollbar-width: thin;
            scrollbar-color: #CEC6C2 transparent;
        }

        .seller-product-back {
            width: 34px;
            height: 34px;
            border: 0;
            background: transparent;
            color: #9A2A25;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .seller-product-back svg {
            width: 30px;
            height: 30px;
        }

        .seller-product-main-card {
            position: relative;
            min-height: 286px;
            border: 1px solid #D8D4D2;
            border-radius: 20px;
            padding: 18px 22px 16px;
            display: grid;
            grid-template-columns: 210px minmax(0, 1fr);
            gap: 25px;
            box-sizing: border-box;
        }

        .seller-product-gallery {
            position: relative;
            min-width: 0;
        }

        .seller-product-main-image {
            width: 188px;
            height: 188px;
            border: 1px solid #BEB8B4;
            border-radius: 10px;
            overflow: hidden;
            background: #F7F2EF;
        }

        .seller-product-main-image svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .seller-product-thumbnails {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .seller-product-thumbnail {
            width: 34px;
            height: 34px;
            border: 1px solid #C6BFBB;
            border-radius: 6px;
            overflow: hidden;
            background: #F7F2EF;
            cursor: pointer;
            padding: 0;
        }

        .seller-product-thumbnail.active {
            border-color: #8D211D;
        }

        .seller-product-thumbnail svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .seller-product-thumb-next {
            position: absolute;
            right: 6px;
            bottom: 8px;
            width: 24px;
            height: 34px;
            border: 0;
            background: transparent;
            font-size: 27px;
            color: #24201E;
            cursor: pointer;
        }

        .seller-product-main-info {
            padding-top: 20px;
            min-width: 0;
        }

        .seller-product-main-info h2 {
            margin: 0 0 2px;
            font-size: 24px;
            line-height: 1.12;
            font-weight: 700;
            color: #080706;
        }

        .seller-product-price-row {
            display: flex;
            align-items: center;
            gap: 180px;
            margin-top: 9px;
        }

        .seller-product-price-row strong {
            font-size: 18px;
            color: #7F1B1A;
        }

        .seller-product-price-row span {
            font-size: 10px;
            color: #17120F;
        }

        .seller-product-field {
            margin-top: 22px;
        }

        .seller-product-field > span {
            display: block;
            font-size: 12px;
            color: #8C8582;
            margin-bottom: 7px;
        }

        .seller-product-category-pill {
            display: inline-flex;
            padding: 3px 11px;
            min-height: 18px;
            align-items: center;
            border-radius: 999px;
            background: #F9DFEA;
            color: #A12763;
            font-size: 9px;
        }

        .seller-product-stock-field {
            margin-top: 18px;
        }

        .seller-product-stock-field strong {
            display: block;
            font-size: 12px;
            color: #17120F;
            font-weight: 500;
        }

        .seller-product-uploaded {
            position: absolute;
            right: 22px;
            bottom: 12px;
            font-size: 10px;
            color: #7D7773;
        }

        .seller-product-uploaded strong {
            color: #17120F;
            font-weight: 500;
        }

        .seller-product-description-card {
            margin-top: 14px;
            border: 1px solid #D8D4D2;
            border-radius: 18px;
            padding: 19px 22px 21px;
            box-sizing: border-box;
        }

        .seller-product-description-card h3 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #12100F;
        }

        .seller-product-description-card p {
            margin: 12px 0 0;
            font-size: 12px;
            line-height: 1.6;
            color: #2B2927;
        }

        .seller-product-divider {
            height: 1px;
            background: #D8D4D2;
            margin: 20px 0 16px;
        }

        .seller-product-detail-grid {
            display: grid;
            grid-template-columns: 180px 1fr;
            row-gap: 10px;
            margin-top: 17px;
            font-size: 12px;
        }

        .seller-product-detail-grid span {
            color: #8C8582;
        }

        .seller-product-detail-grid strong {
            font-weight: 500;
            color: #17120F;
        }

        .seller-product-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 18px;
        }

        .seller-product-actions button {
            height: 48px;
            padding: 0 28px;
            border-radius: 8px;
            font: inherit;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: .18s ease;
        }

        .seller-product-remove {
            background: #FFDDE0;
            color: #A20E16;
            border: 1px solid #D51B23;
        }

        .seller-product-warning {
            background: #FFE6D5;
            color: #B55D00;
            border: 1px solid #E79A46;
        }

        .seller-product-approve {
            background: #D8EED0;
            color: #19731A;
            border: 1px solid #4E9B46;
        }

        .seller-product-actions button:hover {
            filter: brightness(.98);
            transform: translateY(-1px);
        }

        @media (max-width: 760px) {
            .seller-product-details-dialog {
                padding: 15px 18px 26px;
            }

            .seller-product-main-card {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .seller-product-main-image {
                margin: 0 auto;
            }

            .seller-product-main-info {
                padding-top: 4px;
            }

            .seller-product-price-row {
                gap: 90px;
            }

            .seller-product-uploaded {
                position: static;
                margin-top: 14px;
            }

            .seller-product-actions {
                margin-top: 80px;
                flex-wrap: wrap;
            }

            .seller-product-actions button {
                flex: 1 1 auto;
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

                const sidebar =
                    document.getElementById('admin-sidebar') ||
                    document.getElementById('adminSidebar') ||
                    document.getElementById('sellerSidebar') ||
                    document.querySelector('[data-admin-sidebar]') ||
                    document.querySelector('.admin-sidebar');

                const page =
                    document.getElementById('admin-content');

                /* =================================================
                   SIDEBAR MOVEMENT ONLY
                   -------------------------------------------------
                   Keep ALL existing Seller Compliance spacing as-is.
                   This only synchronizes the page movement with the
                   shared sidebar's w-20 collapsed state.
                ================================================== */
                if (sidebar && page) {

                    const originalPageMarginLeft =
                        parseFloat(
                            window.getComputedStyle(page).marginLeft
                        ) || 0;

                    const originalSidebarWidth =
                        sidebar.getBoundingClientRect().width || 256;

                    const originalSidebarGap =
                        originalPageMarginLeft - originalSidebarWidth;

                    const collapsedSidebarWidth = 80;

                    page.style.transitionProperty =
                        'margin-left';

                    page.style.transitionDuration =
                        '300ms';

                    page.style.transitionTimingFunction =
                        'ease';

                    function syncSellerComplianceWithSidebar() {

                        /*
                         * Leave the page's existing responsive spacing
                         * completely untouched on smaller screens.
                         */
                        if (window.innerWidth <= 900) {
                            page.style.removeProperty('margin-left');
                            return;
                        }

                        const isCollapsed =
                            sidebar.classList.contains('w-20');

                        if (isCollapsed) {
                            page.style.marginLeft =
                                `${collapsedSidebarWidth + originalSidebarGap}px`;
                        } else {
                            /*
                             * Restore the exact margin the page already had.
                             * No desktop spacing is changed.
                             */
                            page.style.marginLeft =
                                `${originalPageMarginLeft}px`;
                        }
                    }

                    const sidebarMovementObserver =
                        new MutationObserver(function (mutations) {

                            const classChanged =
                                mutations.some(function (mutation) {
                                    return (
                                        mutation.type === 'attributes' &&
                                        mutation.attributeName === 'class'
                                    );
                                });

                            if (!classChanged) {
                                return;
                            }

                            /*
                             * Run after the sidebar toggle has updated its
                             * width class, so both animations begin together.
                             */
                            requestAnimationFrame(
                                syncSellerComplianceWithSidebar
                            );
                        });

                    sidebarMovementObserver.observe(
                        sidebar,
                        {
                            attributes: true,
                            attributeFilter: ['class']
                        }
                    );

                    window.addEventListener(
                        'resize',
                        syncSellerComplianceWithSidebar
                    );
                }

                const rows = Array.from(
                    document.querySelectorAll('.seller-compliance-row')
                );

                const searchInput =
                    document.getElementById('sellerSearch');

                const categoryFilter =
                    document.getElementById('sellerTypeFilter');

                const complianceFilter =
                    document.getElementById('complianceFilter');

                const refreshButton =
                    document.getElementById('refreshCompliance');

                const refreshIcon =
                    document.getElementById('complianceRefreshIcon');

                const showingCount =
                    document.getElementById('sellerShowingCount');

                const showingText =
                    showingCount?.parentElement;

                const noResults =
                    document.getElementById('sellerComplianceNoResults');

                const previousPage =
                    document.getElementById('sellerPreviousPage');

                const nextPage =
                    document.getElementById('sellerNextPage');

                const pageButtons =
                    document.querySelectorAll(
                        '.seller-compliance-page .pagination-button[data-page]'
                    );

                const itemsPerPage =
                    document.getElementById('sellerItemsPerPage');

                let currentPage = 1;
                let itemsLimit = Number(itemsPerPage?.value) || 7;



/* =================================================
                   FILTER HELPERS
                ================================================== */

                function normalize(value) {

                    return String(value ?? '')
                        .toLowerCase()
                        .replace(/[’‘]/g, "'")
                        .replace(/&/g, 'and')
                        .replace(/[^a-z0-9]+/g, ' ')
                        .trim()
                        .replace(/\s+/g, ' ');

                }


                function categoryMatches(row, selectedCategory) {

                    if (selectedCategory === 'all') {
                        return true;
                    }

                    const categories = String(
                        row.dataset.category || ''
                    )
                        .toLowerCase()
                        .split(/\s+/)
                        .filter(Boolean);

                    return categories.includes(
                        String(selectedCategory).toLowerCase()
                    );

                }


                function getFilteredRows() {

                    const query = normalize(
                        searchInput?.value || ''
                    );

                    const selectedCategory =
                        categoryFilter?.value || 'all';

                    const selectedCompliance =
                        complianceFilter?.value || 'all';

                    return rows.filter(function (row) {

                        /* Search across seller name, owner, email, phone,
                           shop/category text, and the visible row content. */
                        const searchableText = normalize(
                            [
                                row.dataset.name || '',
                                row.dataset.search || '',
                                row.dataset.category || '',
                                row.textContent || ''
                            ].join(' ')
                        );

                        const matchesSearch =
                            query === '' ||
                            searchableText.includes(query);

                        const matchesCategory =
                            categoryMatches(
                                row,
                                selectedCategory
                            );

                        const rowCompliance = String(
                            row.dataset.compliance || ''
                        )
                            .toLowerCase()
                            .trim();

                        const matchesCompliance =
                            selectedCompliance === 'all' ||
                            rowCompliance === selectedCompliance;

                        return (
                            matchesSearch &&
                            matchesCategory &&
                            matchesCompliance
                        );

                    });

                }


                /* =================================================
                   RENDER FILTERED ROWS + PAGINATION
                ================================================== */

                function renderRows() {

                    const filteredRows = getFilteredRows();
                    const total = filteredRows.length;

                    const totalPages = Math.max(
                        1,
                        Math.ceil(total / itemsLimit)
                    );

                    if (currentPage > totalPages) {
                        currentPage = totalPages;
                    }

                    const startIndex =
                        (currentPage - 1) * itemsLimit;

                    const endIndex =
                        startIndex + itemsLimit;

                    rows.forEach(function (row) {
                        row.classList.add('hidden');
                    });

                    filteredRows
                        .slice(startIndex, endIndex)
                        .forEach(function (row) {
                            row.classList.remove('hidden');
                        });

                    const visibleCount = Math.min(
                        itemsLimit,
                        Math.max(0, total - startIndex)
                    );

                    if (showingCount) {
                        const countStart = total === 0 ? 0 : startIndex + 1;
                        const countEnd = total === 0 ? 0 : startIndex + visibleCount;
                        showingCount.textContent = `${countStart}-${countEnd}`;
                    }

                    if (showingText) {
                        const suffix = total === 1 ? 'entry' : 'entries';
                        showingText.lastChild.textContent = ` out of ${total} ${suffix}`;
                    }

                    if (noResults) {
                        noResults.classList.toggle(
                            'hidden',
                            total !== 0
                        );
                    }

                    pageButtons.forEach(function (button) {

                        const buttonPage = Number(
                            button.dataset.page
                        );

                        button.classList.toggle(
                            'current',
                            buttonPage === currentPage
                        );

                        button.classList.toggle(
                            'hidden',
                            buttonPage > totalPages
                        );

                    });

                    previousPage?.classList.toggle(
                        'disabled',
                        currentPage === 1
                    );

                    nextPage?.classList.toggle(
                        'disabled',
                        currentPage >= totalPages
                    );

                }


                /* =================================================
                   SEARCH + FILTER EVENTS
                ================================================== */

                searchInput?.addEventListener(
                    'input',
                    function () {
                        currentPage = 1;
                        renderRows();
                    }
                );

                categoryFilter?.addEventListener(
                    'change',
                    function () {
                        currentPage = 1;
                        renderRows();
                    }
                );

                complianceFilter?.addEventListener(
                    'change',
                    function () {
                        currentPage = 1;
                        renderRows();
                    }
                );


                /* =================================================
                   PAGINATION
                ================================================== */

                pageButtons.forEach(function (button) {

                    button.addEventListener(
                        'click',
                        function () {

                            currentPage = Number(
                                button.dataset.page
                            );

                            renderRows();

                        }
                    );

                });

                previousPage?.addEventListener(
                    'click',
                    function () {

                        if (currentPage <= 1) {
                            return;
                        }

                        currentPage--;
                        renderRows();

                    }
                );

                nextPage?.addEventListener(
                    'click',
                    function () {

                        const totalPages = Math.max(
                            1,
                            Math.ceil(
                                getFilteredRows().length /
                                itemsLimit
                            )
                        );

                        if (currentPage >= totalPages) {
                            return;
                        }

                        currentPage++;
                        renderRows();

                    }
                );

                itemsPerPage?.addEventListener(
                    'change',
                    function () {

                        itemsLimit =
                            Number(itemsPerPage.value) || 7;

                        currentPage = 1;
                        renderRows();

                    }
                );


                /* =================================================
                   REFRESH
                ================================================== */

                refreshButton?.addEventListener(
                    'click',
                    function () {

                        refreshIcon?.classList.add(
                            'compliance-spin'
                        );

                        if (searchInput) {
                            searchInput.value = '';
                        }

                        if (categoryFilter) {
                            categoryFilter.value = 'all';
                        }

                        if (complianceFilter) {
                            complianceFilter.value = 'all';
                        }

                        currentPage = 1;

                        setTimeout(function () {

                            refreshIcon?.classList.remove(
                                'compliance-spin'
                            );

                            renderRows();

                        }, 350);

                    }
                );



                /* =================================================
                   SELLER DETAILS MODAL UX
                ================================================== */

                const sellerDetailsModal =
                    document.getElementById('sellerDetailsModal');

                const sellerDetailsDialog =
                    sellerDetailsModal?.querySelector('.seller-details-dialog');

                const sellerDetailsClose =
                    document.getElementById('sellerDetailsClose');

                const sellerDetailsCancel =
                    document.getElementById('sellerModalCancel');

                const sellerDetailsSuspend =
                    document.getElementById('sellerModalSuspend');

                const sellerModalStoreName =
                    document.getElementById('sellerModalStoreName');

                const sellerModalSince =
                    document.getElementById('sellerModalSince');

                const sellerModalOwner =
                    document.getElementById('sellerModalOwner');

                const sellerModalEmail =
                    document.getElementById('sellerModalEmail');

                const sellerModalPhone =
                    document.getElementById('sellerModalPhone');

                const sellerModalLocation =
                    document.getElementById('sellerModalLocation');

                const sellerModalCategories =
                    document.getElementById('sellerModalCategories');

                const sellerModalProducts =
                    document.getElementById('sellerModalProducts');

                const sellerModalScore =
                    document.getElementById('sellerModalScore');

                const sellerModalScoreFill =
                    document.getElementById('sellerModalScoreFill');

                const sellerModalCompliancePill =
                    document.getElementById('sellerModalCompliancePill');

                const sellerModalPolicyText =
                    document.getElementById('sellerModalPolicyText');

                const sellerModalIssues =
                    document.getElementById('sellerModalIssues');

                const sellerOverviewPanel =
                    document.getElementById('sellerOverviewPanel');

                const sellerProductsPanel =
                    document.getElementById('sellerProductsPanel');

                const sellerProductsGrid =
                    document.getElementById('sellerProductsGrid');

                const sellerProductDetailsModal =
                    document.getElementById('sellerProductDetailsModal');

                const sellerProductDetailsDialog =
                    sellerProductDetailsModal?.querySelector('.seller-product-details-dialog');

                const sellerProductBack =
                    document.getElementById('sellerProductBack');

                const sellerProductActions =
                    document.getElementById('sellerProductActions');

                const sellerProductClose =
                    document.getElementById('sellerProductClose');

                const sellerProductMainImage =
                    document.getElementById('sellerProductMainImage');

                const sellerProductThumbnails =
                    document.getElementById('sellerProductThumbnails');

                const sellerProductComplianceNotice =
                    document.getElementById('sellerProductComplianceNotice');

                const sellerProductThumbNext =
                    document.getElementById('sellerProductThumbNext');

                const sellerProductModalTitle =
                    document.getElementById('sellerProductModalTitle');

                const sellerProductModalPrice =
                    document.getElementById('sellerProductModalPrice');

                const sellerProductModalSold =
                    document.getElementById('sellerProductModalSold');

                const sellerProductModalCategory =
                    document.getElementById('sellerProductModalCategory');

                const sellerProductModalStock =
                    document.getElementById('sellerProductModalStock');

                const sellerProductModalUploaded =
                    document.getElementById('sellerProductModalUploaded');

                const sellerProductModalDescription =
                    document.getElementById('sellerProductModalDescription');

                const sellerProductDetailBrand =
                    document.getElementById('sellerProductDetailBrand');

                const sellerProductDetailMaterial =
                    document.getElementById('sellerProductDetailMaterial');

                const sellerProductDetailSizes =
                    document.getElementById('sellerProductDetailSizes');

                const sellerProductDetailColors =
                    document.getElementById('sellerProductDetailColors');

                const sellerProductDetailWeight =
                    document.getElementById('sellerProductDetailWeight');

                const sellerProductDetailOrigin =
                    document.getElementById('sellerProductDetailOrigin');

                let activeSellerRow = null;
                let activeProduct = null;
                let lastProductFocusedElement = null;
                let lastFocusedElement = null;

                const sellerProfiles = {
                    'DelaCruzShop': {
                        since: 'August 2024',
                        email: 'juandelacruz@gmail.com',
                        phone: '0917 123 4567',
                        location: 'Calamba, Laguna',
                        products: '145 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Issue: Pangit ng design nyo hahahaha.', date: 'September 24, 2026' },
                            { status: 'Removed', type: 'removed', issue: 'Reason: Pangit ng design nyo hahahaha.', date: 'September 24, 2026' },
                            { status: 'Warning', type: 'warning', issue: 'Issue: Pangit ng design nyo hahahaha.', date: 'September 24, 2026' }
                        ]
                    },
                    'WellnessHub': {
                        since: 'July 2024', email: 'angela.reyes@gmail.com', phone: '0918 222 1001', location: 'Quezon City', products: '86 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Missing compliance documentation.', date: 'September 20, 2026' },
                            { status: 'Warning', type: 'warning', issue: 'Product listing requires review.', date: 'September 18, 2026' }
                        ]
                    },
                    'TechCore PH': {
                        since: 'June 2024', email: 'marco.santos@gmail.com', phone: '0919 333 2002', location: 'Makati City', products: '142 Products',
                        issues: [
                            { status: 'Removed', type: 'removed', issue: 'Repeated policy violation.', date: 'September 22, 2026' },
                            { status: 'Warning', type: 'warning', issue: 'Restricted product documentation incomplete.', date: 'September 18, 2026' }
                        ]
                    },
                    'ActiveLife Hub': {
                        since: 'May 2024', email: 'carlo.reyes@gmail.com', phone: '0920 444 3003', location: 'Taguig City', products: '74 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'One product listing is under review.', date: 'September 19, 2026' }
                        ]
                    },
                    'Home Haven PH': {
                        since: 'April 2024', email: 'maria.santos@gmail.com', phone: '0921 555 4004', location: 'Pasig City', products: '118 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Five product listings require review.', date: 'September 21, 2026' }
                        ]
                    },
                    'Little Sprouts': {
                        since: 'March 2024', email: 'ana.garcia@gmail.com', phone: '0922 666 5005', location: 'Antipolo City', products: '63 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Four product listings are under review.', date: 'September 17, 2026' }
                        ]
                    },
                    'Glow & Care': {
                        since: 'February 2024', email: 'sofia.cruz@gmail.com', phone: '0923 777 6006', location: 'Muntinlupa City', products: '97 Products',
                        issues: [
                            { status: 'Warning', type: 'warning', issue: 'Seven product listings require review.', date: 'September 16, 2026' }
                        ]
                    }
                };

                const categoryLabels = {
                    'pet-supplies': 'Pet Supplies',
                    'electronics-and-gadgets': 'Electronics & Gadgets',
                    'womens-apparel': 'Women’s Apparel',
                    'mens-apparel': 'Men’s Apparel',
                    'kids-and-baby': 'Kids & Baby',
                    'home-and-garden': 'Home & Garden',
                    'sports-and-outdoors': 'Sports & Outdoors',
                    'health-and-beauty': 'Health & Beauty',
                    'books-and-media': 'Books & Media',
                    'food-and-gourmet': 'Food & Gourmet',
                    'automotive-motorcycle': 'Automotive & Motorcycle',
                    'furniture-and-office-equipment': 'Furniture & Office Equipment',
                    'jewelry-and-watches': 'Jewelry & Watches',
                    'office-and-school-supplies': 'Office & School Supplies'
                };

                const categoryClasses = {
                    'pet-supplies': 'category-pet-supplies',
                    'electronics-and-gadgets': 'category-electronics-and-gadgets',
                    'womens-apparel': 'category-womens-apparel',
                    'mens-apparel': 'category-mens-apparel',
                    'kids-and-baby': 'category-kids-and-baby',
                    'home-and-garden': 'category-home-and-garden',
                    'sports-and-outdoors': 'category-sports-and-outdoors',
                    'health-and-beauty': 'category-health-and-beauty',
                    'books-and-media': 'category-books-and-media',
                    'food-and-gourmet': 'category-food-and-gourmet',
                    'automotive-motorcycle': 'category-automotive-motorcycle',
                    'furniture-and-office-equipment': 'category-furniture-and-office-equipment',
                    'jewelry-and-watches': 'category-jewelry-and-watches',
                    'office-and-school-supplies': 'category-office-and-school-supplies'
                };

                function escapeModalHtml(value) {
                    return String(value ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#039;');
                }

                function renderModalCategories(rawCategory) {
                    const values = String(rawCategory || '')
                        .split(/\s+/)
                        .filter(Boolean);

                    if (!values.length) {
                        return '<span class="seller-modal-category category-default">—</span>';
                    }

                    return values.map(function (value) {
                        const key = value.toLowerCase();
                        const label = categoryLabels[key] || value;
                        const cls = categoryClasses[key] || 'category-default';
                        return `<span class="seller-modal-category ${cls}">${escapeModalHtml(label)}</span>`;
                    }).join('');
                }

                function shirtThumbSvg() {
                    return `
                        <svg viewBox="0 0 60 70" aria-hidden="true">
                            <path d="M18 8l12 5 12-5 8 6 5 14-9 4-4-8v31H18V24l-4 8-9-4 5-14z" fill="#1A1817" stroke="#4A4542" stroke-width="1.2"/>
                            <path d="M25 13c0 4 2 6 5 6s5-2 5-6" fill="none" stroke="#EEEAE8" stroke-width="2"/>
                        </svg>
                    `;
                }

                function blackShirtProductSvg() {
                    return `
                        <svg viewBox="0 0 240 240" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                            <rect width="240" height="240" fill="#F8F5F3"/>
                            <ellipse cx="120" cy="225" rx="76" ry="9" fill="#DED7D3" opacity=".6"/>
                            <path d="M77 48 L102 36 C111 48 129 48 138 36 L163 48 198 80 176 114 157 102 151 214 89 214 83 102 64 114 42 80z" fill="#111111" stroke="#2E2B29" stroke-width="2"/>
                            <path d="M102 36 C103 53 137 53 138 36" fill="#EEE7E3"/>
                            <path d="M96 75 C98 105 98 158 96 205" stroke="#222" stroke-width="2" opacity=".35"/>
                            <path d="M144 75 C142 105 142 158 144 205" stroke="#222" stroke-width="2" opacity=".35"/>
                        </svg>
                    `;
                }

                function snailProductSvg() {
                    return `
                        <svg viewBox="0 0 240 240" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
                            <defs>
                                <linearGradient id="snailBg" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0" stop-color="#6D4D3D"/>
                                    <stop offset=".55" stop-color="#A97B51"/>
                                    <stop offset="1" stop-color="#D7AE73"/>
                                </linearGradient>
                            </defs>
                            <rect width="240" height="240" fill="url(#snailBg)"/>
                            <text x="12" y="20" fill="#FFF8F0" font-size="7" font-family="Arial" font-weight="700">NATURE REPUBLIC</text>
                            <text x="12" y="30" fill="#FFF8F0" font-size="5" font-family="Arial">OFFICIAL STORE</text>
                            <rect x="17" y="138" width="205" height="73" rx="8" fill="#E8D7C5" opacity=".42"/>
                            <rect x="25" y="94" width="48" height="91" rx="9" fill="#EDE2D5" stroke="#D5C3B2"/>
                            <rect x="80" y="112" width="42" height="76" rx="8" fill="#F2E8DD" stroke="#D5C3B2"/>
                            <rect x="132" y="104" width="57" height="82" rx="9" fill="#EFE1D3" stroke="#D5C3B2"/>
                            <circle cx="48" cy="111" r="11" fill="#B98A63" opacity=".8"/>
                            <circle cx="101" cy="130" r="8" fill="#B98A63" opacity=".8"/>
                            <circle cx="160" cy="123" r="10" fill="#B98A63" opacity=".8"/>
                            <text x="98" y="67" fill="#FFF8F0" font-size="14" font-family="Arial" font-style="italic">Snail Solution</text>
                            <text x="99" y="82" fill="#FFF8F0" font-size="10" font-family="Arial">Skin Care Set</text>
                            <text x="18" y="221" fill="#FFF8F0" font-size="7" font-family="Arial">MOISTURIZING • REPAIR • GLOW</text>
                        </svg>
                    `;
                }

                const sellerProductSets = {
                    'DelaCruzShop': [
                        { name: 'Men’s Graphic T-shirt for everyone', price: '₱59', sold: '0 sold', status: 'green', image: 'shirt', category: 'Women’s Apparel', stock: '154 pieces', uploaded: 'September 9, 2026  2:13 PM', brand: 'HangLoose', material: '100% Cotton', sizes: 'S, M, L, XL, XXL', colors: 'Black, White, Gray', weight: '150g', origin: 'Philippines' },
                        { name: 'Snail Solution Care Set', price: '₱690', sold: '15 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '85 pieces', uploaded: 'September 9, 2026  2:13 PM', brand: 'Nature Republic', material: 'Skincare formula', sizes: 'One Size', colors: 'Natural', weight: '320g', origin: 'Philippines' },
                        { name: 'Classic Cotton Tee', price: '₱79', sold: '8 sold', status: 'orange', image: 'shirt', category: 'Women’s Apparel', stock: '62 pieces', uploaded: 'September 8, 2026  4:20 PM', brand: 'HangLoose', material: '100% Cotton', sizes: 'S, M, L, XL', colors: 'Black, White', weight: '145g', origin: 'Philippines' },
                        { name: 'Everyday Graphic Tee', price: '₱89', sold: '4 sold', status: 'orange', image: 'shirt', category: 'Women’s Apparel', stock: '47 pieces', uploaded: 'September 8, 2026  1:05 PM', brand: 'HangLoose', material: 'Cotton Blend', sizes: 'S, M, L, XL', colors: 'Black, Gray', weight: '150g', origin: 'Philippines' },
                        { name: 'Daily Repair Skincare Set', price: '₱720', sold: '21 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '73 pieces', uploaded: 'September 7, 2026  11:42 AM', brand: 'Nature Republic', material: 'Skincare formula', sizes: 'One Size', colors: 'Natural', weight: '330g', origin: 'Philippines' },
                        { name: 'Snail Solution Care Set', price: '₱690', sold: '15 sold', status: 'red', image: 'snail', category: 'Health & Beauty', stock: '28 pieces', uploaded: 'September 6, 2026  9:30 AM', brand: 'Nature Republic', material: 'Skincare formula', sizes: 'One Size', colors: 'Natural', weight: '320g', origin: 'Philippines' }
                    ],
                    'WellnessHub': [
                        { name: 'Daily Wellness Kit', price: '₱549', sold: '32 sold', status: 'orange', image: 'snail', category: 'Health & Beauty', stock: '44 pieces', uploaded: 'September 9, 2026  10:15 AM', brand: 'WellnessHub', material: 'Personal care formula', sizes: 'One Size', colors: 'Natural', weight: '280g', origin: 'Philippines' },
                        { name: 'Hydrating Care Set', price: '₱629', sold: '18 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '69 pieces', uploaded: 'September 8, 2026  3:25 PM', brand: 'WellnessHub', material: 'Hydrating formula', sizes: 'One Size', colors: 'Natural', weight: '300g', origin: 'Philippines' },
                        { name: 'Gourmet Tea Collection', price: '₱399', sold: '25 sold', status: 'green', image: 'snail', category: 'Food & Gourmet', stock: '88 packs', uploaded: 'September 8, 2026  9:40 AM', brand: 'WellnessHub', material: 'Premium tea blend', sizes: 'One Size', colors: 'Assorted', weight: '250g', origin: 'Philippines' },
                        { name: 'Herbal Glow Set', price: '₱459', sold: '11 sold', status: 'orange', image: 'snail', category: 'Health & Beauty', stock: '31 pieces', uploaded: 'September 7, 2026  2:10 PM', brand: 'WellnessHub', material: 'Herbal formula', sizes: 'One Size', colors: 'Natural', weight: '265g', origin: 'Philippines' },
                        { name: 'Organic Snack Box', price: '₱299', sold: '41 sold', status: 'green', image: 'snail', category: 'Food & Gourmet', stock: '57 boxes', uploaded: 'September 6, 2026  5:35 PM', brand: 'WellnessHub', material: 'Organic ingredients', sizes: 'Medium', colors: 'Assorted', weight: '500g', origin: 'Philippines' },
                        { name: 'Complete Self Care Set', price: '₱799', sold: '9 sold', status: 'red', image: 'snail', category: 'Health & Beauty', stock: '16 pieces', uploaded: 'September 5, 2026  1:55 PM', brand: 'WellnessHub', material: 'Personal care formula', sizes: 'One Size', colors: 'Natural', weight: '420g', origin: 'Philippines' }
                    ],
                    'TechCore PH': [
                        { name: 'Wireless Earbuds Pro', price: '₱1,499.00', sold: '1.2k sold', status: 'red', image: 'snail', category: 'Electronics & Gadgets', stock: '24 pieces', uploaded: 'September 10, 2026  9:20 AM', brand: 'TechCore', material: 'ABS + Silicone', sizes: 'One Size', colors: 'Black', weight: '85g', origin: 'Philippines' },
                        { name: 'Smart Watch Series 5', price: '₱2,899.00', sold: '420 sold', status: 'orange', image: 'snail', category: 'Electronics & Gadgets', stock: '36 pieces', uploaded: 'September 9, 2026  2:45 PM', brand: 'TechCore', material: 'Aluminum + Glass', sizes: '42mm', colors: 'Black', weight: '48g', origin: 'Philippines' },
                        { name: 'Car Phone Mount', price: '₱349.00', sold: '215 sold', status: 'green', image: 'shirt', category: 'Automotive & Motorcycle', stock: '92 pieces', uploaded: 'September 8, 2026  11:15 AM', brand: 'TechCore', material: 'ABS Plastic', sizes: 'Universal', colors: 'Black', weight: '180g', origin: 'Philippines' },
                        { name: 'USB-C Fast Charger', price: '₱599.00', sold: '680 sold', status: 'orange', image: 'snail', category: 'Electronics & Gadgets', stock: '51 pieces', uploaded: 'September 7, 2026  4:05 PM', brand: 'TechCore', material: 'Fireproof PC', sizes: '30W', colors: 'White', weight: '95g', origin: 'Philippines' },
                        { name: 'Wireless Car Charger', price: '₱899.00', sold: '144 sold', status: 'green', image: 'snail', category: 'Automotive & Motorcycle', stock: '63 pieces', uploaded: 'September 6, 2026  8:50 AM', brand: 'TechCore', material: 'ABS + Silicone', sizes: 'Universal', colors: 'Black', weight: '220g', origin: 'Philippines' },
                        { name: 'Bluetooth Speaker Mini', price: '₱1,099.00', sold: '305 sold', status: 'red', image: 'snail', category: 'Electronics & Gadgets', stock: '12 pieces', uploaded: 'September 5, 2026  10:30 AM', brand: 'TechCore', material: 'ABS + Metal Mesh', sizes: 'Mini', colors: 'Black', weight: '310g', origin: 'Philippines' }
                    ],
                    'ActiveLife Hub': [
                        { name: 'Performance Running Shoes', price: '₱1,899', sold: '86 sold', status: 'green', image: 'shirt', category: 'Sports & Outdoors', stock: '34 pairs', uploaded: 'September 10, 2026  7:40 AM', brand: 'ActiveLife', material: 'Mesh + Rubber', sizes: '6-12', colors: 'Black', weight: '650g', origin: 'Philippines' },
                        { name: 'Training Resistance Bands', price: '₱499', sold: '124 sold', status: 'green', image: 'shirt', category: 'Sports & Outdoors', stock: '75 sets', uploaded: 'September 9, 2026  1:10 PM', brand: 'ActiveLife', material: 'Natural Latex', sizes: 'Set of 5', colors: 'Assorted', weight: '420g', origin: 'Philippines' },
                        { name: 'Pet Comfort Bed', price: '₱899', sold: '56 sold', status: 'orange', image: 'shirt', category: 'Pet Supplies', stock: '22 pieces', uploaded: 'September 8, 2026  3:00 PM', brand: 'ActiveLife', material: 'Plush Fabric', sizes: 'Medium', colors: 'Gray', weight: '900g', origin: 'Philippines' },
                        { name: 'Adjustable Dumbbell', price: '₱2,499', sold: '31 sold', status: 'green', image: 'shirt', category: 'Sports & Outdoors', stock: '18 pieces', uploaded: 'September 7, 2026  12:20 PM', brand: 'ActiveLife', material: 'Steel + Rubber', sizes: '20kg', colors: 'Black', weight: '20kg', origin: 'Philippines' },
                        { name: 'Interactive Pet Toy', price: '₱329', sold: '73 sold', status: 'green', image: 'shirt', category: 'Pet Supplies', stock: '48 pieces', uploaded: 'September 6, 2026  9:15 AM', brand: 'ActiveLife', material: 'ABS + Rubber', sizes: 'Medium', colors: 'Blue', weight: '190g', origin: 'Philippines' },
                        { name: 'Outdoor Camping Mat', price: '₱799', sold: '38 sold', status: 'orange', image: 'shirt', category: 'Sports & Outdoors', stock: '27 pieces', uploaded: 'September 5, 2026  5:10 PM', brand: 'ActiveLife', material: 'EVA Foam', sizes: 'Standard', colors: 'Green', weight: '620g', origin: 'Philippines' }
                    ],
                    'Home Haven PH': [
                        { name: 'Modern Storage Cabinet', price: '₱3,499', sold: '15 sold', status: 'green', image: 'shirt', category: 'Home & Garden', stock: '12 pieces', uploaded: 'September 10, 2026  10:05 AM', brand: 'Home Haven', material: 'Engineered Wood', sizes: 'Large', colors: 'Oak', weight: '18kg', origin: 'Philippines' },
                        { name: 'Minimalist Office Desk', price: '₱4,299', sold: '9 sold', status: 'green', image: 'shirt', category: 'Furniture & Office Equipment', stock: '8 pieces', uploaded: 'September 9, 2026  2:15 PM', brand: 'Home Haven', material: 'Wood + Steel', sizes: '120cm', colors: 'Oak', weight: '16kg', origin: 'Philippines' },
                        { name: 'Indoor Plant Stand', price: '₱999', sold: '27 sold', status: 'orange', image: 'shirt', category: 'Home & Garden', stock: '19 pieces', uploaded: 'September 8, 2026  8:45 AM', brand: 'Home Haven', material: 'Solid Wood', sizes: 'Medium', colors: 'Walnut', weight: '2.5kg', origin: 'Philippines' },
                        { name: 'Ergonomic Office Chair', price: '₱5,999', sold: '13 sold', status: 'green', image: 'shirt', category: 'Furniture & Office Equipment', stock: '6 pieces', uploaded: 'September 7, 2026  11:30 AM', brand: 'Home Haven', material: 'Mesh + Steel', sizes: 'Standard', colors: 'Black', weight: '14kg', origin: 'Philippines' },
                        { name: 'Garden Tool Set', price: '₱1,299', sold: '22 sold', status: 'green', image: 'shirt', category: 'Home & Garden', stock: '25 sets', uploaded: 'September 6, 2026  4:30 PM', brand: 'Home Haven', material: 'Steel + Wood', sizes: '5-Piece', colors: 'Natural', weight: '1.8kg', origin: 'Philippines' },
                        { name: 'Compact Bookshelf', price: '₱2,199', sold: '17 sold', status: 'orange', image: 'shirt', category: 'Furniture & Office Equipment', stock: '10 pieces', uploaded: 'September 5, 2026  1:25 PM', brand: 'Home Haven', material: 'Engineered Wood', sizes: 'Small', colors: 'Oak', weight: '11kg', origin: 'Philippines' }
                    ],
                    'Little Sprouts': [
                        { name: 'Soft Baby Romper', price: '₱349', sold: '74 sold', status: 'green', image: 'shirt', category: 'Kids & Baby', stock: '54 pieces', uploaded: 'September 10, 2026  9:05 AM', brand: 'Little Sprouts', material: '100% Cotton', sizes: '0-24M', colors: 'Pastel', weight: '110g', origin: 'Philippines' },
                        { name: 'Kids School Supply Set', price: '₱499', sold: '42 sold', status: 'green', image: 'shirt', category: 'Office & School Supplies', stock: '37 sets', uploaded: 'September 9, 2026  12:35 PM', brand: 'Little Sprouts', material: 'Plastic + Paper', sizes: '20-Piece', colors: 'Assorted', weight: '520g', origin: 'Philippines' },
                        { name: 'Storybook Collection', price: '₱699', sold: '29 sold', status: 'orange', image: 'shirt', category: 'Books & Media', stock: '31 sets', uploaded: 'September 8, 2026  3:45 PM', brand: 'Little Sprouts', material: 'Paper', sizes: 'Set of 5', colors: 'Assorted', weight: '750g', origin: 'Philippines' },
                        { name: 'Toddler Backpack', price: '₱599', sold: '61 sold', status: 'green', image: 'shirt', category: 'Kids & Baby', stock: '45 pieces', uploaded: 'September 7, 2026  10:50 AM', brand: 'Little Sprouts', material: 'Polyester', sizes: 'Small', colors: 'Blue', weight: '280g', origin: 'Philippines' },
                        { name: 'Learning Activity Book', price: '₱259', sold: '88 sold', status: 'green', image: 'shirt', category: 'Books & Media', stock: '63 pieces', uploaded: 'September 6, 2026  1:15 PM', brand: 'Little Sprouts', material: 'Paper', sizes: 'A4', colors: 'Assorted', weight: '180g', origin: 'Philippines' },
                        { name: 'Kids Art Supply Kit', price: '₱429', sold: '35 sold', status: 'red', image: 'shirt', category: 'Office & School Supplies', stock: '14 sets', uploaded: 'September 5, 2026  9:55 AM', brand: 'Little Sprouts', material: 'Plastic + Paper', sizes: '15-Piece', colors: 'Assorted', weight: '410g', origin: 'Philippines' }
                    ],
                    'Glow & Care': [
                        { name: 'Brightening Skin Care Set', price: '₱899', sold: '96 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '39 sets', uploaded: 'September 10, 2026  8:30 AM', brand: 'Glow & Care', material: 'Skincare formula', sizes: 'One Size', colors: 'Natural', weight: '300g', origin: 'Philippines' },
                        { name: 'Everyday Blouse', price: '₱499', sold: '47 sold', status: 'green', image: 'shirt', category: 'Women’s Apparel', stock: '28 pieces', uploaded: 'September 9, 2026  2:20 PM', brand: 'Glow & Care', material: 'Cotton Blend', sizes: 'S, M, L, XL', colors: 'White', weight: '180g', origin: 'Philippines' },
                        { name: 'Gentle Cleansing Set', price: '₱649', sold: '73 sold', status: 'orange', image: 'snail', category: 'Health & Beauty', stock: '25 sets', uploaded: 'September 8, 2026  11:40 AM', brand: 'Glow & Care', material: 'Gentle skincare formula', sizes: 'One Size', colors: 'Natural', weight: '280g', origin: 'Philippines' },
                        { name: 'Classic Women’s Top', price: '₱549', sold: '33 sold', status: 'green', image: 'shirt', category: 'Women’s Apparel', stock: '41 pieces', uploaded: 'September 7, 2026  4:10 PM', brand: 'Glow & Care', material: 'Rayon Blend', sizes: 'S, M, L', colors: 'Pink', weight: '160g', origin: 'Philippines' },
                        { name: 'Repair Moisture Care Set', price: '₱749', sold: '58 sold', status: 'green', image: 'snail', category: 'Health & Beauty', stock: '47 sets', uploaded: 'September 6, 2026  10:05 AM', brand: 'Glow & Care', material: 'Moisturizing formula', sizes: 'One Size', colors: 'Natural', weight: '315g', origin: 'Philippines' },
                        { name: 'Soft Knit Cardigan', price: '₱799', sold: '21 sold', status: 'orange', image: 'shirt', category: 'Women’s Apparel', stock: '18 pieces', uploaded: 'September 5, 2026  12:50 PM', brand: 'Glow & Care', material: 'Knit Fabric', sizes: 'S, M, L', colors: 'Beige', weight: '320g', origin: 'Philippines' }
                    ]
                };

                function normalizeProductModerationStatus(product) {
                    if (!product) return 'under-review';
                    if (product.moderationStatus) return product.moderationStatus;
                    if (product.status === 'green') return 'approved';
                    if (product.status === 'red') return 'removed';
                    return 'under-review';
                }

                function getProductStatusClass(product) {
                    const status = normalizeProductModerationStatus(product);
                    if (status === 'approved') return 'approved';
                    if (status === 'warning') return 'warning';
                    if (status === 'removed') return 'removed';
                    return 'under-review';
                }

                function getProductStatusLabel(status) {
                    return { approved: 'Approved', warning: 'Warning Issued', removed: 'Removed', 'under-review': 'Under Review' }[status] || 'Under Review';
                }


                const productDecisionFlash = document.getElementById('productDecisionFlash');
                const productDecisionFlashTitle = document.getElementById('productDecisionFlashTitle');
                const productDecisionFlashMessage = document.getElementById('productDecisionFlashMessage');
                const productDecisionFlashClose = document.getElementById('productDecisionFlashClose');
                let productDecisionFlashTimer = null;

                function hideProductDecisionFlash() {
                    if (productDecisionFlashTimer) {
                        clearTimeout(productDecisionFlashTimer);
                        productDecisionFlashTimer = null;
                    }
                    productDecisionFlash?.classList.remove('show');
                }

                function showProductDecisionFlash(type, productName) {
                    if (!productDecisionFlash) return;

                    const productLabel = productName || 'Product';
                    const config = {
                        approved: {
                            title: 'Product Approved',
                            message: `${productLabel} has been approved successfully.`
                        },
                        warning: {
                            title: 'Warning Issued',
                            message: `A warning has been issued for ${productLabel}.`
                        },
                        removed: {
                            title: 'Product Removed',
                            message: `${productLabel} has been removed from the seller's store.`
                        }
                    }[type] || {
                        title: 'Product Updated',
                        message: `${productLabel} has been updated.`
                    };

                    if (productDecisionFlashTitle) productDecisionFlashTitle.textContent = config.title;
                    if (productDecisionFlashMessage) productDecisionFlashMessage.textContent = config.message;

                    productDecisionFlash.classList.remove('approved', 'warning', 'removed');
                    productDecisionFlash.classList.add(type);

                    if (productDecisionFlashTimer) clearTimeout(productDecisionFlashTimer);

                    requestAnimationFrame(function () {
                        productDecisionFlash.classList.add('show');
                    });

                    productDecisionFlashTimer = setTimeout(hideProductDecisionFlash, 3800);
                }

                productDecisionFlashClose?.addEventListener('click', hideProductDecisionFlash);

                function currentProductStoreName() {
                    return activeSellerRow?.dataset?.name || '';
                }

                function refreshCurrentSellerProducts() {
                    const storeName = currentProductStoreName();
                    if (storeName) renderSellerProducts(storeName);
                }

                function addProductIssue(storeName, product, type, reason, details) {
                    const profile = sellerProfiles[storeName] || (sellerProfiles[storeName] = {});
                    profile.issues = Array.isArray(profile.issues) ? profile.issues : [];
                    profile.issues = profile.issues.filter(function(issue) { return issue.productKey !== product.productKey; });
                    profile.issues.unshift({
                        productKey: product.productKey,
                        productName: product.name,
                        status: type === 'warning' ? 'Warning' : 'Removed',
                        type: type === 'warning' ? 'warning' : 'removed',
                        issue: reason + (details ? ' — ' + details : ''),
                        date: new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })
                    });
                }

                function removeProductIssue(storeName, product) {
                    const profile = sellerProfiles[storeName];
                    if (!profile || !Array.isArray(profile.issues)) return;
                    profile.issues = profile.issues.filter(function(issue) { return issue.productKey !== product.productKey; });
                }

                function renderProductComplianceNotice(product) {
                    if (!sellerProductComplianceNotice) return;
                    const status = normalizeProductModerationStatus(product);
                    if (status === 'approved') {
                        sellerProductComplianceNotice.className = 'seller-product-compliance-notice approved';
                        sellerProductComplianceNotice.innerHTML = '<strong>Approved</strong><span>This product is approved and follows the platform policies.</span>';
                    } else if (status === 'warning') {
                        const reason = product.warningReason || 'A compliance issue was identified for this product.';
                        const details = product.warningDetails ? ' — ' + product.warningDetails : '';
                        sellerProductComplianceNotice.className = 'seller-product-compliance-notice warning';
                        sellerProductComplianceNotice.innerHTML = '<strong>Issue Warning</strong><span>' + escapeModalHtml(reason + details) + '</span>';
                    } else if (status === 'removed') {
                        const reason = product.removeReason || "This product has been removed from the seller's store.";
                        const details = product.removeDetails ? ' — ' + product.removeDetails : '';
                        sellerProductComplianceNotice.className = 'seller-product-compliance-notice removed';
                        sellerProductComplianceNotice.innerHTML = '<strong>Removed</strong><span>' + escapeModalHtml(reason + details) + '</span>';
                    } else {
                        sellerProductComplianceNotice.className = 'seller-product-compliance-notice under-review';
                        sellerProductComplianceNotice.innerHTML = '<strong>Under Review</strong><span>This product is awaiting a compliance decision.</span>';
                    }
                }

                function renderSellerProducts(storeName) {
                    if (!sellerProductsGrid) return;
                    const products = sellerProductSets[storeName] || [];
                    sellerProductsGrid.innerHTML = products.map(function(product, index) {
                        const status = normalizeProductModerationStatus(product);
                        const statusClass = getProductStatusClass(product);
                        const image = product.image === 'snail' ? snailProductSvg() : blackShirtProductSvg();
                        return `
                            <article class="seller-product-card status-${statusClass}" tabindex="0" role="button" data-product-index="${index}" aria-label="View ${escapeModalHtml(product.name)} (${getProductStatusLabel(status)})">
                                <div class="seller-product-image">${image}</div>
                                <div class="seller-product-info">
                                    <div class="seller-product-name">${escapeModalHtml(product.name)}</div>
                                    <div class="seller-product-bottom">
                                        <span class="seller-product-price">${escapeModalHtml(product.price)}</span>
                                        <span class="seller-product-sold">${escapeModalHtml(product.sold)}</span>
                                    </div>
                                    <span class="seller-product-status-label">${escapeModalHtml(getProductStatusLabel(status))}</span>
                                </div>
                            </article>
                        `;
                    }).join('');
                    sellerProductsGrid.querySelectorAll('.seller-product-card').forEach(function(card) {
                        const open = function() {
                            const productsForStore = sellerProductSets[storeName] || [];
                            const product = productsForStore[Number(card.dataset.productIndex)];
                            if (product) openProductDetails(product);
                        };
                        card.addEventListener('click', open);
                        card.addEventListener('keydown', function(event) {
                            if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); open(); }
                        });
                    });
                }

                function productDetailsData(product) {
                    const isSnail = product.image === 'snail';

                    return {
                        image: isSnail ? snailProductSvg() : blackShirtProductSvg(),
                        title: product.name || 'Men’s Graphic T-shirt',
                        price: product.price || '₱59',
                        sold: product.sold || '0 sold',
                        category: product.category || (isSnail ? 'Health & Beauty' : 'Women’s Apparel'),
                        stock: product.stock || (isSnail ? '85 pieces' : '154 pieces'),
                        uploaded: product.uploaded || 'September 9, 2026  2:13 PM',
                        description: product.description || (
                            isSnail
                                ? 'Gentle skincare set for daily use. Helps cleanse, hydrate, and support smoother-looking skin.'
                                : 'High-quality cotton graphic T-shirt for everyday wear. Comfortable, breathable, and perfect for casual outfits. Available in multiple sizes.'
                        ),
                        brand: product.brand || (isSnail ? 'Nature Republic' : 'HangLoose'),
                        material: product.material || (isSnail ? 'Skincare formula' : '100% Cotton'),
                        sizes: product.sizes || (isSnail ? 'One Size' : 'S, M, L, XL, XXL'),
                        colors: product.colors || (isSnail ? 'Natural' : 'Black, White, Gray'),
                        weight: product.weight || (isSnail ? '320g' : '150g'),
                        origin: product.origin || 'Philippines',
                        moderationStatus: normalizeProductModerationStatus(product),
                        warningReason: product.warningReason || '',
                        warningDetails: product.warningDetails || '',
                        removeReason: product.removeReason || '',
                        removeDetails: product.removeDetails || ''
                    };
                }

                function updateProductActionButtons(product) {
                    if (!sellerProductActions) return;

                    const status = normalizeProductModerationStatus(product);
                    const isUnderReview = status === 'under-review';

                    sellerProductActions
                        .querySelectorAll('.seller-product-remove, .seller-product-warning, .seller-product-approve')
                        .forEach(function (button) {
                            button.hidden = !isUnderReview;
                        });

                    if (sellerProductClose) {
                        sellerProductClose.hidden = isUnderReview;
                    }
                }

                function openProductDetails(product) {
                    if (!sellerProductDetailsModal || !product) return;

                    activeProduct = product;
                    lastProductFocusedElement = document.activeElement;

                    const details = productDetailsData(product);

                    if (sellerProductModalTitle) sellerProductModalTitle.textContent = details.title;
                    if (sellerProductModalPrice) sellerProductModalPrice.textContent = details.price;
                    if (sellerProductModalSold) sellerProductModalSold.textContent = details.sold;
                    if (sellerProductModalCategory) sellerProductModalCategory.textContent = details.category;
                    if (sellerProductModalStock) sellerProductModalStock.textContent = details.stock;
                    if (sellerProductModalUploaded) sellerProductModalUploaded.textContent = details.uploaded;
                    if (sellerProductModalDescription) sellerProductModalDescription.textContent = details.description;
                    if (sellerProductDetailBrand) sellerProductDetailBrand.textContent = details.brand;
                    if (sellerProductDetailMaterial) sellerProductDetailMaterial.textContent = details.material;
                    if (sellerProductDetailSizes) sellerProductDetailSizes.textContent = details.sizes;
                    if (sellerProductDetailColors) sellerProductDetailColors.textContent = details.colors;
                    if (sellerProductDetailWeight) sellerProductDetailWeight.textContent = details.weight;
                    if (sellerProductDetailOrigin) sellerProductDetailOrigin.textContent = details.origin;
                    renderProductComplianceNotice(product);
                    updateProductActionButtons(product);

                    if (sellerProductMainImage) {
                        sellerProductMainImage.innerHTML = details.image;
                    }

                    if (sellerProductThumbnails) {
                        sellerProductThumbnails.innerHTML = Array.from({ length: 5 }).map(function (_, index) {
                            return `
                                <button
                                    type="button"
                                    class="seller-product-thumbnail ${index === 0 ? 'active' : ''}"
                                    aria-label="Product image ${index + 1}"
                                >
                                    ${details.image}
                                </button>
                            `;
                        }).join('');
                    }

                    sellerProductDetailsModal.classList.remove('hidden');
                    sellerProductDetailsModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('seller-modal-open');
                    sellerProductDetailsDialog?.focus();
                }

                function closeProductDetails() {
                    if (!sellerProductDetailsModal) return;

                    sellerProductDetailsModal.classList.add('hidden');
                    sellerProductDetailsModal.setAttribute('aria-hidden', 'true');
                    activeProduct = null;

                    if (lastProductFocusedElement && typeof lastProductFocusedElement.focus === 'function') {
                        lastProductFocusedElement.focus();
                    }

                    lastProductFocusedElement = null;
                }

                function openSellerDetails(row) {
                    if (!sellerDetailsModal) return;

                    activeSellerRow = row;
                    lastFocusedElement = document.activeElement;

                    const storeName = row.dataset.name || 'Seller';
                    const owner = row.querySelector('.seller-owner-name')?.textContent?.trim() || '—';
                    const score = Number((row.querySelector('.score-number')?.textContent || '0').replace('%', '')) || 0;
                    const compliance = (row.dataset.compliance || 'compliant').toLowerCase();
                    const profile = sellerProfiles[storeName] || {};

                    sellerModalStoreName.textContent = storeName;
                    sellerModalOwner.textContent = owner;
                    sellerModalSince.textContent = profile.since || '2024';
                    sellerModalEmail.textContent = profile.email || 'Not available';
                    sellerModalPhone.textContent = profile.phone || 'Not available';
                    sellerModalLocation.textContent = profile.location || 'Not available';
                    sellerModalProducts.textContent = profile.products || ((row.querySelector('.seller-products-total')?.textContent || '').trim() || '0 Products');
                    sellerModalCategories.innerHTML = renderModalCategories(row.dataset.category);
                    sellerModalScore.textContent = `${score}%`;
                    sellerModalScoreFill.style.width = `${Math.max(0, Math.min(100, score))}%`;

                    const scoreTone = compliance === 'suspended'
                        ? 'score-red'
                        : compliance === 'warning' || compliance === 'under-review'
                            ? 'score-orange'
                            : 'score-green';

                    sellerModalScore.className = `seller-modal-score modal-${scoreTone}`;
                    sellerModalScoreFill.className = scoreTone;
                    renderSellerProducts(storeName);

                    sellerModalCompliancePill.className = 'seller-modal-compliance-pill ' + (
                        compliance === 'under-review' ? 'under-review' : compliance
                    );
                    const complianceLabel = compliance === 'under-review' ? 'Under Review' : compliance.charAt(0).toUpperCase() + compliance.slice(1);
                    sellerModalCompliancePill.textContent = complianceLabel;

                    sellerModalPolicyText.textContent =
                        compliance === 'compliant'
                            ? 'This seller is following the platform policies.'
                            : compliance === 'warning' || compliance === 'under-review'
                                ? 'This seller has compliance items that require attention.'
                                : 'This seller currently has serious compliance concerns.';

                    const issues = profile.issues || [
                        { status: 'Warning', type: 'warning', issue: 'No recent issues recorded.', date: 'September 2026' }
                    ];

                    sellerModalIssues.innerHTML = issues.map(function (issue) {
                        return `
                            <div class="seller-issue-item">
                                <div class="seller-issue-thumb">${shirtThumbSvg()}</div>
                                <div class="seller-issue-info">
                                    <strong>Men’s Graphic T-shirt</strong>
                                    <p>${escapeModalHtml(issue.issue)}</p>
                                </div>
                                <div class="seller-issue-meta">
                                    <span class="seller-issue-status ${escapeModalHtml(issue.type)}">${escapeModalHtml(issue.status)}</span>
                                    <span class="seller-issue-date">${escapeModalHtml(issue.date)}</span>
                                </div>
                            </div>
                        `;
                    }).join('');

                    sellerOverviewPanel?.classList.remove('hidden');
                    sellerProductsPanel?.classList.add('hidden');
                    document.querySelectorAll('.seller-details-tab').forEach(function (tab) {
                        const active = tab.dataset.tab === 'overview';
                        tab.classList.toggle('active', active);
                        tab.setAttribute('aria-selected', active ? 'true' : 'false');
                    });

                    sellerDetailsModal.classList.remove('hidden');
                    sellerDetailsModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('seller-modal-open');
                    sellerDetailsDialog?.focus();
                }

                function closeSellerDetails() {
                    if (!sellerDetailsModal) return;
                    sellerDetailsModal.classList.add('hidden');
                    sellerDetailsModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('seller-modal-open');
                    if (lastFocusedElement && typeof lastFocusedElement.focus === 'function') {
                        lastFocusedElement.focus();
                    }
                    activeSellerRow = null;
                }

                rows.forEach(function (row) {
                    row.setAttribute('tabindex', '0');
                    row.setAttribute('role', 'button');
                    row.setAttribute('aria-label', `View details for ${row.dataset.name || 'seller'}`);

                    row.addEventListener('click', function () {
                        openSellerDetails(row);
                    });

                    row.addEventListener('keydown', function (event) {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            openSellerDetails(row);
                        }
                    });
                });

                sellerDetailsClose?.addEventListener('click', closeSellerDetails);
                sellerDetailsCancel?.addEventListener('click', closeSellerDetails);

                sellerDetailsModal?.addEventListener('click', function (event) {
                    if (event.target === sellerDetailsModal) {
                        closeSellerDetails();
                    }
                });

                sellerProductBack?.addEventListener('click', closeProductDetails);

                sellerProductClose?.addEventListener('click', closeProductDetails);

                sellerProductDetailsModal?.addEventListener('click', function (event) {
                    if (event.target === sellerProductDetailsModal) {
                        closeProductDetails();
                    }
                });

                sellerProductThumbnails?.addEventListener('click', function (event) {
                    const thumbnail = event.target.closest('.seller-product-thumbnail');
                    if (!thumbnail) return;

                    sellerProductThumbnails
                        .querySelectorAll('.seller-product-thumbnail')
                        .forEach(function (item) {
                            item.classList.toggle('active', item === thumbnail);
                        });

                    const image = activeProduct?.image === 'snail'
                        ? snailProductSvg()
                        : blackShirtProductSvg();

                    if (sellerProductMainImage) {
                        sellerProductMainImage.innerHTML = image;
                    }
                });

                sellerProductThumbNext?.addEventListener('click', function () {
                    if (!sellerProductThumbnails || !sellerProductMainImage) return;

                    const thumbnails = Array.from(
                        sellerProductThumbnails.querySelectorAll('.seller-product-thumbnail')
                    );

                    if (!thumbnails.length) return;

                    const currentIndex = thumbnails.findIndex(function (item) {
                        return item.classList.contains('active');
                    });

                    const nextIndex = (currentIndex + 1) % thumbnails.length;
                    thumbnails.forEach(function (item, index) {
                        item.classList.toggle('active', index === nextIndex);
                    });

                    const image = activeProduct?.image === 'snail'
                        ? snailProductSvg()
                        : blackShirtProductSvg();

                    sellerProductMainImage.innerHTML = image;
                });

                const sellerRemoveProductModal =
                    document.getElementById('sellerRemoveProductModal');

                const sellerRemoveProductDialog =
                    sellerRemoveProductModal?.querySelector('.seller-remove-product-dialog');

                const sellerRemoveProductCancel =
                    document.getElementById('sellerRemoveProductCancel');

                const sellerRemoveProductSubmit =
                    document.getElementById('sellerRemoveProductSubmit');

                const sellerRemoveProductDetails =
                    document.getElementById('sellerRemoveProductDetails');

                const sellerRemoveProductDetailsCount =
                    document.getElementById('sellerRemoveProductDetailsCount');

                let removeReturnFocus = null;

                function openRemoveProductModal() {
                    if (!sellerRemoveProductModal) return;

                    removeReturnFocus = document.activeElement;

                    sellerProductDetailsModal?.classList.add('hidden');
                    sellerProductDetailsModal?.setAttribute('aria-hidden', 'true');

                    sellerRemoveProductModal.classList.remove('hidden');
                    sellerRemoveProductModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('seller-remove-modal-open');

                    sellerRemoveProductModal
                        .querySelectorAll('input[name=\"remove_reason\"]')
                        .forEach(function (input) {
                            input.checked = false;
                        });

                    sellerRemoveProductModal
                        .querySelector('.seller-remove-reasons')
                        ?.classList.remove('has-error');

                    if (sellerRemoveProductDetails) {
                        sellerRemoveProductDetails.value = '';
                    }

                    if (sellerRemoveProductDetailsCount) {
                        sellerRemoveProductDetailsCount.textContent = '0/300';
                    }

                    requestAnimationFrame(function () {
                        sellerRemoveProductCancel?.focus();
                    });
                }

                function closeRemoveProductModal(returnToProduct = true) {
                    if (!sellerRemoveProductModal) return;

                    sellerRemoveProductModal.classList.add('hidden');
                    sellerRemoveProductModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('seller-remove-modal-open');

                    if (returnToProduct && sellerProductDetailsModal) {
                        sellerProductDetailsModal.classList.remove('hidden');
                        sellerProductDetailsModal.setAttribute('aria-hidden', 'false');
                        requestAnimationFrame(function () {
                            sellerProductDetailsDialog?.focus();
                        });
                    } else if (removeReturnFocus && typeof removeReturnFocus.focus === 'function') {
                        removeReturnFocus.focus();
                    }

                    removeReturnFocus = null;
                }

                sellerRemoveProductCancel?.addEventListener('click', function () {
                    closeRemoveProductModal(true);
                });

                sellerRemoveProductDetails?.addEventListener('input', function () {
                    if (sellerRemoveProductDetailsCount) {
                        sellerRemoveProductDetailsCount.textContent = `${sellerRemoveProductDetails.value.length}/300`;
                    }
                });

                sellerRemoveProductSubmit?.addEventListener('click', function () {
                    const selectedReason = sellerRemoveProductModal?.querySelector('input[name=\"remove_reason\"]:checked');
                    if (!selectedReason) {
                        sellerRemoveProductModal?.querySelector('.seller-remove-reasons')?.classList.add('has-error');
                        return;
                    }
                    sellerRemoveProductModal?.querySelector('.seller-remove-reasons')?.classList.remove('has-error');
                    if (!activeProduct) return;

                    const storeName = currentProductStoreName();
                    activeProduct.moderationStatus = 'removed';
                    activeProduct.status = 'gray';
                    activeProduct.removeReason = selectedReason.value;
                    activeProduct.removeDetails = sellerRemoveProductDetails?.value?.trim() || '';
                    activeProduct.warningReason = '';
                    activeProduct.warningDetails = '';
                    addProductIssue(storeName, activeProduct, 'removed', activeProduct.removeReason, activeProduct.removeDetails);
                    refreshCurrentSellerProducts();
                    closeRemoveProductModal(true);
                    openProductDetails(activeProduct);
                    showProductDecisionFlash('removed', activeProduct.name);
                });

                sellerRemoveProductModal?.addEventListener('click', function (event) {
                    if (event.target === sellerRemoveProductModal) {
                        closeRemoveProductModal(true);
                    }
                });

                const sellerIssueWarningModal =
                    document.getElementById('sellerIssueWarningModal');

                const sellerIssueWarningDialog =
                    sellerIssueWarningModal?.querySelector('.seller-issue-warning-dialog');

                const sellerWarningCancel =
                    document.getElementById('sellerWarningCancel');

                const sellerWarningSubmit =
                    document.getElementById('sellerWarningSubmit');

                const sellerWarningDetails =
                    document.getElementById('sellerWarningDetails');

                const sellerWarningDetailsCount =
                    document.getElementById('sellerWarningDetailsCount');

                let warningReturnFocus = null;

                function openIssueWarningModal() {
                    if (!sellerIssueWarningModal) return;

                    warningReturnFocus = document.activeElement;

                    sellerProductDetailsModal?.classList.add('hidden');
                    sellerProductDetailsModal?.setAttribute('aria-hidden', 'true');

                    sellerIssueWarningModal.classList.remove('hidden');
                    sellerIssueWarningModal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('seller-warning-modal-open');

                    sellerIssueWarningModal
                        .querySelectorAll('input[name="warning_reason"]')
                        .forEach(function (input) {
                            input.checked = false;
                        });

                    if (sellerWarningDetails) {
                        sellerWarningDetails.value = '';
                    }

                    if (sellerWarningDetailsCount) {
                        sellerWarningDetailsCount.textContent = '0/300';
                    }

                    requestAnimationFrame(function () {
                        sellerWarningCancel?.focus();
                    });
                }

                function closeIssueWarningModal(returnToProduct = true) {
                    if (!sellerIssueWarningModal) return;

                    sellerIssueWarningModal.classList.add('hidden');
                    sellerIssueWarningModal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('seller-warning-modal-open');

                    if (returnToProduct && sellerProductDetailsModal) {
                        sellerProductDetailsModal.classList.remove('hidden');
                        sellerProductDetailsModal.setAttribute('aria-hidden', 'false');
                        requestAnimationFrame(function () {
                            sellerProductDetailsDialog?.focus();
                        });
                    } else if (warningReturnFocus && typeof warningReturnFocus.focus === 'function') {
                        warningReturnFocus.focus();
                    }

                    warningReturnFocus = null;
                }

                document.querySelector('.seller-product-warning')?.addEventListener('click', openIssueWarningModal);

                sellerWarningCancel?.addEventListener('click', function () {
                    closeIssueWarningModal(true);
                });

                sellerWarningDetails?.addEventListener('input', function () {
                    if (sellerWarningDetailsCount) {
                        sellerWarningDetailsCount.textContent = `${sellerWarningDetails.value.length}/300`;
                    }
                });

                sellerWarningSubmit?.addEventListener('click', function () {
                    const selectedReason = sellerIssueWarningModal?.querySelector('input[name=\"warning_reason\"]:checked');
                    if (!selectedReason) {
                        sellerIssueWarningModal?.querySelector('.seller-warning-reasons')?.classList.add('has-error');
                        return;
                    }
                    sellerIssueWarningModal?.querySelector('.seller-warning-reasons')?.classList.remove('has-error');
                    if (!activeProduct) return;

                    const storeName = currentProductStoreName();
                    activeProduct.moderationStatus = 'warning';
                    activeProduct.status = 'yellow';
                    activeProduct.warningReason = selectedReason.value;
                    activeProduct.warningDetails = sellerWarningDetails?.value?.trim() || '';
                    activeProduct.removeReason = '';
                    activeProduct.removeDetails = '';
                    addProductIssue(storeName, activeProduct, 'warning', activeProduct.warningReason, activeProduct.warningDetails);
                    refreshCurrentSellerProducts();
                    closeIssueWarningModal(false);
                    openProductDetails(activeProduct);
                    showProductDecisionFlash('warning', activeProduct.name);
                });

                sellerIssueWarningModal?.addEventListener('click', function (event) {
                    if (event.target === sellerIssueWarningModal) {
                        closeIssueWarningModal(true);
                    }
                });


                sellerProductDetailsModal?.addEventListener('click', function(event) {
                    const removeButton = event.target.closest('.seller-product-remove');
                    const warningButton = event.target.closest('.seller-product-warning');
                    const approveButton = event.target.closest('.seller-product-approve');

                    if (removeButton) {
                        openRemoveProductModal();
                        return;
                    }

                    if (warningButton) {
                        openIssueWarningModal();
                        return;
                    }

                    if (approveButton) {
                        if (!activeProduct) return;
                        const storeName = currentProductStoreName();
                        activeProduct.moderationStatus = 'approved';
                        activeProduct.status = 'green';
                        activeProduct.warningReason = '';
                        activeProduct.warningDetails = '';
                        activeProduct.removeReason = '';
                        activeProduct.removeDetails = '';
                        removeProductIssue(storeName, activeProduct);
                        refreshCurrentSellerProducts();
                        openProductDetails(activeProduct);
                        showProductDecisionFlash('approved', activeProduct.name);
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && sellerRemoveProductModal && !sellerRemoveProductModal.classList.contains('hidden')) {
                        closeRemoveProductModal(true);
                        return;
                    }

                    if (event.key === 'Escape' && sellerIssueWarningModal && !sellerIssueWarningModal.classList.contains('hidden')) {
                        closeIssueWarningModal(true);
                        return;
                    }

                    if (event.key === 'Escape' && sellerProductDetailsModal && !sellerProductDetailsModal.classList.contains('hidden')) {
                        closeProductDetails();
                        return;
                    }

                    if (event.key === 'Escape' && sellerDetailsModal && !sellerDetailsModal.classList.contains('hidden')) {
                        closeSellerDetails();
                    }
                });

                document.querySelectorAll('.seller-details-tab').forEach(function (tab) {
                    tab.addEventListener('click', function () {
                        const isOverview = tab.dataset.tab === 'overview';
                        document.querySelectorAll('.seller-details-tab').forEach(function (item) {
                            const active = item === tab;
                            item.classList.toggle('active', active);
                            item.setAttribute('aria-selected', active ? 'true' : 'false');
                        });
                        sellerOverviewPanel?.classList.toggle('hidden', !isOverview);
                        sellerProductsPanel?.classList.toggle('hidden', isOverview);
                    });
                });

                document.querySelectorAll('.seller-document-item').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const name = button.dataset.document || 'document';
                        button.blur();
                        window.alert(`${name} is available in your seller document storage.`);
                    });
                });

                sellerDetailsSuspend?.addEventListener('click', function () {
                    if (!activeSellerRow) return;

                    const storeName = activeSellerRow.dataset.name || 'this seller';
                    const confirmed = window.confirm(`Suspend ${storeName}? This demo updates the interface only and does not save to the database.`);

                    if (!confirmed) return;

                    activeSellerRow.dataset.status = 'suspended';
                    const statusPill = activeSellerRow.querySelector('.status-pill');
                    if (statusPill) {
                        statusPill.className = 'status-pill status-suspended';
                        statusPill.textContent = 'Suspended';
                    }

                    closeSellerDetails();
                    renderRows();
                });

                /* =================================================
                   INITIAL
                ================================================== */

                renderRows();

            }
        );

    </script>



    <style>

        @keyframes complianceRefreshSpin {

            from {

                transform:
                    rotate(0deg);

            }

            to {

                transform:
                    rotate(360deg);

            }

        }


        .compliance-spin {

            animation:
                complianceRefreshSpin
                .45s
                linear;

        }

    </style>


    <style>
        /* Compact Product Details Modal */
        .seller-product-details-dialog {
            width: min(790px, calc(100vw - 36px));
            max-height: calc(100vh - 36px);
            padding: 16px 26px 24px;
            border-radius: 22px;
        }

        .seller-product-back {
            width: 30px;
            height: 30px;
            margin-bottom: 8px;
        }

        .seller-product-back svg {
            width: 27px;
            height: 27px;
        }

        .seller-product-main-card {
            min-height: 238px;
            padding: 14px 18px 13px;
            grid-template-columns: 176px minmax(0, 1fr);
            gap: 20px;
            border-radius: 17px;
        }

        .seller-product-main-image {
            width: 156px;
            height: 156px;
            border-radius: 9px;
        }

        .seller-product-thumbnails {
            gap: 6px;
            margin-top: 8px;
        }

        .seller-product-thumbnail {
            width: 30px;
            height: 30px;
            border-radius: 5px;
        }

        .seller-product-thumb-next {
            right: 2px;
            bottom: 5px;
            width: 20px;
            height: 30px;
            font-size: 23px;
        }

        .seller-product-main-info {
            padding-top: 14px;
        }

        .seller-product-main-info h2 {
            font-size: 19px;
            line-height: 1.12;
        }

        .seller-product-price-row {
            gap: 125px;
            margin-top: 8px;
        }

        .seller-product-price-row strong {
            font-size: 16px;
        }

        .seller-product-price-row span {
            font-size: 9px;
        }

        .seller-product-field {
            margin-top: 17px;
        }

        .seller-product-field > span {
            font-size: 11px;
            margin-bottom: 6px;
        }

        .seller-product-category-pill {
            padding: 2px 10px;
            min-height: 17px;
            font-size: 8px;
        }

        .seller-product-stock-field {
            margin-top: 14px;
        }

        .seller-product-stock-field strong {
            font-size: 12px;
        }

        .seller-product-uploaded {
            right: 18px;
            bottom: 10px;
            font-size: 9px;
        }

        .seller-product-description-card {
            margin-top: 12px;
            border-radius: 16px;
            padding: 15px 18px 17px;
        }

        .seller-product-description-card h3 {
            font-size: 12px;
        }

        .seller-product-description-card p {
            margin-top: 9px;
            font-size: 11px;
            line-height: 1.5;
        }

        .seller-product-divider {
            margin: 16px 0 13px;
        }

        .seller-product-detail-grid {
            grid-template-columns: 155px 1fr;
            row-gap: 8px;
            margin-top: 13px;
            font-size: 11px;
        }

        .seller-product-actions {
            gap: 9px;
            margin-top: 18px;
        }

        .seller-product-actions button {
            height: 38px;
            padding: 0 20px;
            border-radius: 8px;
            font-size: 11px;
        }

        @media (max-width: 760px) {
            .seller-product-details-dialog {
                width: min(94vw, 560px);
                padding: 14px 16px 22px;
                max-height: calc(100vh - 20px);
            }

            .seller-product-main-card {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .seller-product-main-image {
                width: 150px;
                height: 150px;
                margin: 0 auto;
            }

            .seller-product-main-info {
                padding-top: 2px;
            }

            .seller-product-price-row {
                gap: 80px;
            }

            .seller-product-uploaded {
                position: static;
                margin-top: 10px;
            }

            .seller-product-actions {
                margin-top: 55px;
                flex-wrap: wrap;
            }
        }
    </style>


    <style>
        /* =========================================================
           REMOVE PRODUCT MODAL
        ========================================================== */

        .seller-remove-product-overlay {
            position: fixed;
            inset: 0;
            z-index: 155;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(23, 18, 15, 0.40);
            backdrop-filter: blur(2px);
        }

        .seller-remove-product-overlay.hidden {
            display: none !important;
        }

        .seller-remove-product-dialog {
            width: min(540px, calc(100vw - 28px));
            background: #FFFFFF;
            border-radius: 20px;
            padding: 24px 24px 22px;
            box-sizing: border-box;
            box-shadow: 0 24px 70px rgba(35, 24, 20, 0.22);
            color: #151312;
            outline: none;
        }

        .seller-remove-product-header h2 {
            margin: 0;
            font-size: 19px;
            line-height: 1.15;
            font-weight: 600;
            color: #11100F;
        }

        .seller-remove-product-intro {
            margin: 9px 0 19px;
            max-width: 470px;
            font-size: 12px;
            line-height: 1.45;
            color: #999492;
        }

        .seller-remove-reasons {
            border: 0;
            padding: 0;
            margin: 0;
        }

        .seller-remove-reasons legend {
            padding: 0;
            margin: 0 0 7px;
            font-size: 12px;
            line-height: 1.25;
            font-weight: 500;
            color: #17120F;
        }

        .seller-remove-reasons legend span {
            color: #E21B23;
            margin-left: 2px;
        }

        .seller-remove-option {
            position: relative;
            width: 100%;
            min-height: 36px;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 0 10px;
            margin-top: 6px;
            border: 1px solid #E7E3E1;
            border-radius: 4px;
            background: #FCFCFC;
            box-sizing: border-box;
            cursor: pointer;
            font-size: 12px;
            color: #272321;
            transition: border-color .16s ease, background-color .16s ease;
        }

        .seller-remove-option:hover {
            border-color: #D1CAC6;
            background: #FAF8F7;
        }

        .seller-remove-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .seller-remove-radio {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            border: 1.5px solid #D5D5D5;
            border-radius: 50%;
            background: #FFFFFF;
            box-sizing: border-box;
        }

        .seller-remove-option input:checked + .seller-remove-radio {
            border-color: #A52F2B;
            box-shadow: inset 0 0 0 4px #FFFFFF;
            background: #A52F2B;
        }

        .seller-remove-option:has(input:focus-visible) {
            outline: 2px solid rgba(165, 47, 43, 0.18);
            outline-offset: 1px;
        }

        .seller-remove-reasons.has-error .seller-remove-option {
            border-color: #F0D4D2;
        }

        .seller-remove-details-wrap {
            margin-top: 20px;
        }

        .seller-remove-details-wrap > label {
            display: block;
            margin: 0 0 7px;
            font-size: 12px;
            line-height: 1.25;
            font-weight: 500;
            color: #17120F;
        }

        .seller-remove-textarea-wrap {
            position: relative;
        }

        .seller-remove-textarea-wrap textarea {
            width: 100%;
            min-height: 54px;
            resize: none;
            border: 1px solid #E0DEDC;
            border-radius: 5px;
            background: #FFFFFF;
            padding: 8px 10px 20px;
            box-sizing: border-box;
            outline: none;
            font: inherit;
            font-size: 12px;
            line-height: 1.4;
            color: #373230;
        }

        .seller-remove-textarea-wrap textarea::placeholder {
            color: #AAA6A4;
        }

        .seller-remove-textarea-wrap textarea:focus {
            border-color: #BBB5B1;
            box-shadow: 0 0 0 3px rgba(165, 47, 43, 0.05);
        }

        #sellerRemoveProductDetailsCount {
            position: absolute;
            right: 9px;
            bottom: 5px;
            font-size: 9px;
            color: #7B7673;
        }

        .seller-remove-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 16px;
        }

        .seller-remove-actions button {
            height: 38px;
            padding: 0 15px;
            border-radius: 8px;
            font: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: transform .16s ease, filter .16s ease, background-color .16s ease;
        }

        .seller-remove-cancel {
            min-width: 96px;
            border: 1px solid #D6D2D0;
            background: #FFFFFF;
            color: #8D211D;
        }

        .seller-remove-submit {
            min-width: 128px;
            border: 1px solid #D51B23;
            background: #FFDDE0;
            color: #A20E16;
        }

        .seller-remove-actions button:hover {
            transform: translateY(-1px);
            filter: brightness(.985);
        }

        body.seller-remove-modal-open {
            overflow: hidden;
        }

        @media (max-width: 640px) {
            .seller-remove-product-dialog {
                width: min(92vw, 540px);
                padding: 20px 18px 18px;
                border-radius: 18px;
            }

            .seller-remove-option {
                min-height: 39px;
                font-size: 11px;
            }

            .seller-remove-actions {
                flex-wrap: wrap;
            }

            .seller-remove-actions button {
                flex: 1 1 0;
            }
        }

        /* =========================================================
           ISSUE WARNING MODAL
        ========================================================== */

        .seller-issue-warning-overlay {
            position: fixed;
            inset: 0;
            z-index: 150;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            background: rgba(23, 18, 15, 0.40);
            backdrop-filter: blur(2px);
        }

        .seller-issue-warning-overlay.hidden {
            display: none !important;
        }

        .seller-issue-warning-dialog {
            width: min(540px, calc(100vw - 28px));
            background: #FFFFFF;
            border-radius: 20px;
            padding: 24px 24px 22px;
            box-sizing: border-box;
            box-shadow: 0 24px 70px rgba(35, 24, 20, 0.22);
            color: #151312;
            outline: none;
        }

        .seller-issue-warning-header h2 {
            margin: 0;
            font-size: 19px;
            line-height: 1.15;
            font-weight: 600;
            color: #11100F;
        }

        .seller-issue-warning-intro {
            margin: 9px 0 19px;
            max-width: 470px;
            font-size: 12px;
            line-height: 1.45;
            color: #999492;
        }

        .seller-warning-reasons {
            border: 0;
            padding: 0;
            margin: 0;
        }

        .seller-warning-reasons legend {
            padding: 0;
            margin: 0 0 7px;
            font-size: 12px;
            line-height: 1.25;
            font-weight: 500;
            color: #17120F;
        }

        .seller-warning-reasons legend span {
            color: #E21B23;
            margin-left: 2px;
        }

        .seller-warning-option {
            position: relative;
            width: 100%;
            min-height: 36px;
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 0 10px;
            margin-top: 6px;
            border: 1px solid #E7E3E1;
            border-radius: 4px;
            background: #FCFCFC;
            box-sizing: border-box;
            cursor: pointer;
            font-size: 12px;
            color: #272321;
            transition: border-color .16s ease, background-color .16s ease;
        }

        .seller-warning-option:hover {
            border-color: #D1CAC6;
            background: #FAF8F7;
        }

        .seller-warning-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .seller-warning-radio {
            width: 16px;
            height: 16px;
            flex: 0 0 16px;
            border: 1.5px solid #D5D5D5;
            border-radius: 50%;
            background: #FFFFFF;
            box-sizing: border-box;
        }

        .seller-warning-option input:checked + .seller-warning-radio {
            border-color: #A52F2B;
            box-shadow: inset 0 0 0 4px #FFFFFF;
            background: #A52F2B;
        }

        .seller-warning-option:has(input:focus-visible) {
            outline: 2px solid rgba(165, 47, 43, 0.18);
            outline-offset: 1px;
        }

        .seller-warning-reasons.has-error .seller-warning-option {
            border-color: #F0D4D2;
        }

        .seller-warning-details-wrap {
            margin-top: 20px;
        }

        .seller-warning-details-wrap > label {
            display: block;
            margin: 0 0 7px;
            font-size: 12px;
            line-height: 1.25;
            font-weight: 500;
            color: #17120F;
        }

        .seller-warning-textarea-wrap {
            position: relative;
        }

        .seller-warning-textarea-wrap textarea {
            width: 100%;
            min-height: 54px;
            resize: none;
            border: 1px solid #E0DEDC;
            border-radius: 5px;
            background: #FFFFFF;
            padding: 8px 10px 20px;
            box-sizing: border-box;
            outline: none;
            font: inherit;
            font-size: 12px;
            line-height: 1.4;
            color: #373230;
        }

        .seller-warning-textarea-wrap textarea::placeholder {
            color: #AAA6A4;
        }

        .seller-warning-textarea-wrap textarea:focus {
            border-color: #BBB5B1;
            box-shadow: 0 0 0 3px rgba(165, 47, 43, 0.05);
        }

        #sellerWarningDetailsCount {
            position: absolute;
            right: 9px;
            bottom: 5px;
            font-size: 9px;
            color: #7B7673;
        }

        .seller-warning-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-top: 16px;
        }

        .seller-warning-actions button {
            height: 38px;
            padding: 0 15px;
            border-radius: 8px;
            font: inherit;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: transform .16s ease, filter .16s ease, background-color .16s ease;
        }

        .seller-warning-cancel {
            min-width: 96px;
            border: 1px solid #D6D2D0;
            background: #FFFFFF;
            color: #8D211D;
        }

        .seller-warning-submit {
            min-width: 140px;
            border: 1px solid #E28E3B;
            background: #FFE5D2;
            color: #B55D00;
        }

        .seller-warning-actions button:hover {
            transform: translateY(-1px);
            filter: brightness(.985);
        }

        body.seller-warning-modal-open {
            overflow: hidden;
        }

        @media (max-width: 640px) {
            .seller-issue-warning-dialog {
                width: min(92vw, 540px);
                padding: 20px 18px 18px;
                border-radius: 18px;
            }

            .seller-issue-warning-intro,
            .seller-warning-reasons legend,
            .seller-warning-details-wrap > label {
                font-size: 12px;
            }

            .seller-warning-option {
                min-height: 39px;
                font-size: 11px;
            }

            .seller-warning-actions {
                flex-wrap: wrap;
            }

            .seller-warning-actions button {
                flex: 1 1 0;
            }
        }
    
        /* Product moderation states */
        .seller-product-card.status-approved { border-color:#25A32D !important; }
        .seller-product-card.status-warning { border-color:#E7A31A !important; }
        .seller-product-card.status-under-review { border-color:#D71919 !important; }
        .seller-product-card.status-removed { border-color:#A9A9A9 !important; background:#F1F1F1; }

        /* Lower-right product moderation feedback */
        .product-decision-flash {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 220;
            width: min(370px, calc(100vw - 32px));
            min-height: 72px;
            display: flex;
            align-items: flex-start;
            gap: 11px;
            padding: 13px 14px;
            box-sizing: border-box;
            background: #FFFFFF;
            border: 1px solid #E7E2DF;
            border-radius: 14px;
            box-shadow: 0 14px 34px rgba(42, 20, 15, .16);
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transform: translateY(14px);
            transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
        }

        .product-decision-flash.show {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
            transform: translateY(0);
        }

        .product-decision-flash.approved { border-color: #BFDDB8; }
        .product-decision-flash.warning { border-color: #F0C776; }
        .product-decision-flash.removed { border-color: #E7A8AB; }

        .product-decision-flash-icon {
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #E7F4E3;
            color: #28721B;
        }

        .product-decision-flash.warning .product-decision-flash-icon {
            background: #FFF1D6;
            color: #A56500;
        }

        .product-decision-flash.removed .product-decision-flash-icon {
            background: #FFE3E5;
            color: #B3262E;
        }

        .product-decision-flash-icon svg {
            width: 19px;
            height: 19px;
        }

        .product-decision-flash-copy {
            min-width: 0;
            flex: 1 1 auto;
            display: grid;
            gap: 3px;
            padding-top: 1px;
        }

        .product-decision-flash-copy strong {
            font-size: 13px;
            line-height: 1.25;
            font-weight: 600;
            color: #17120F;
        }

        .product-decision-flash-copy span {
            font-size: 11px;
            line-height: 1.45;
            font-weight: 400;
            color: #77716E;
        }

        .product-decision-flash-close {
            width: 28px;
            height: 28px;
            flex: 0 0 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: -2px;
            border: 0;
            border-radius: 50%;
            background: transparent;
            color: #AAA4A1;
            cursor: pointer;
            transition: background .15s ease, color .15s ease;
        }

        .product-decision-flash-close:hover {
            background: #F6F2F0;
            color: #5D5754;
        }

        .product-decision-flash-close svg {
            width: 15px;
            height: 15px;
        }

        @media (max-width: 640px) {
            .product-decision-flash {
                right: 16px;
                bottom: 16px;
            }
        }

        .seller-product-card.status-removed .seller-product-image,
        .seller-product-card.status-removed .seller-product-info { opacity:.58; }
        .seller-product-status-label { display:inline-flex; margin-top:4px; font-size:7px; line-height:1; font-weight:600; }
        .seller-product-card.status-approved .seller-product-status-label { color:#2E8A32; }
        .seller-product-card.status-warning .seller-product-status-label { color:#C78B0A; }
        .seller-product-card.status-under-review .seller-product-status-label { color:red; }
        .seller-product-card.status-removed .seller-product-status-label { color:#777777; }
        .seller-product-compliance-notice { margin-top:12px; padding:8px 10px; border-radius:8px; display:grid; gap:3px; width:min(100%,285px); box-sizing:border-box; }
        .seller-product-compliance-notice.hidden { display:none !important; }
        .seller-product-compliance-notice strong { font-size:9px; font-weight:700; }
        .seller-product-compliance-notice span { font-size:8px; line-height:1.35; }
        .seller-product-compliance-notice.approved { background:#E9F5E5; color:#2D7A2D; }
        .seller-product-compliance-notice.warning { background:#FFF3D5; color:#9A6B00; }
        .seller-product-compliance-notice.removed { background:#ECECEC; color:#666666; }
        .seller-product-compliance-notice.under-review { background:#FFF0DF; color:#A8660D; }
        .seller-product-actions button[hidden] {
            display: none !important;
        }

        .seller-product-close-action {
            background: #FFFFFF;
            color: #17120F;
            border: 1px solid #17120F;
        }

        .seller-product-close-action:hover {
            background: #F7F4F2;
        }
    
    /* =====================================================
       DASHBOARD TYPOGRAPHY MATCH
    ====================================================== */

    .seller-compliance-page,
    .seller-compliance-page button,
    .seller-compliance-page input,
    .seller-compliance-page select,
    .seller-compliance-page textarea {
        font-family: Poppins, sans-serif;
    }

    .seller-compliance-page > div > h2 {
        font-size: 21px;
        font-weight: 700;
        line-height: 1.25;
    }


        /* =========================================================
           BALANCED ADMIN TYPOGRAPHY
           Matches the Dashboard scale while avoiding overly heavy text.
        ========================================================== */

        .seller-compliance-page,
        .seller-compliance-page button,
        .seller-compliance-page input,
        .seller-compliance-page select,
        .seller-compliance-page textarea {
            font-family: Poppins, sans-serif;
        }

        .seller-compliance-page > div > h2,
        .seller-compliance-page h2 {
            font-size: 21px;
            line-height: 1.2;
            font-weight: 600;
        }

        .compliance-stat-number {
            /* Same as Registrations stat card number */
            font-size: 23px;
            line-height: 1;
            font-weight: 600;
        }

        .compliance-stat-label {
            /* Same as Registrations stat card label */
            font-size: 13px;
            line-height: 1.15;
            font-weight: 400;
        }

        .compliance-stat-growth {
            /* Same compact secondary-text scale used by Registrations */
            font-size: 12px;
            line-height: 1.2;
            font-weight: 400;
        }

        .compliance-stat-growth strong {
            font-weight: 500;
        }

        /* Final card typography lock — matches Registrations */
        .seller-compliance-page .compliance-stat-number {
            font-size: 23px;
            line-height: 1;
            font-weight: 600;
        }

        .seller-compliance-page .compliance-stat-label {
            font-size: 13px;
            line-height: 1.15;
            font-weight: 400;
        }

        .seller-compliance-page .compliance-stat-growth {
            font-size: 12px;
            line-height: 1.2;
            font-weight: 400;
        }

        .compliance-table-header {
            font-size: 13px;
            line-height: 1.2;
            font-weight: 400;
        }

        .seller-store-name {
            font-size: 14px;
            line-height: 1.1;
            font-weight: 500;
        }

        .seller-owner-name {
            font-size: 12px;
            line-height: 1.1;
            font-weight: 400;
        }

        .category-pill {
            font-size: 12px;
            line-height: 1;
            font-weight: 500;
        }

        .seller-products-total {
            font-size: 14px;
            line-height: 1.2;
            font-weight: 500;
        }

        .seller-products-review {
            font-size: 12px;
            line-height: 1.2;
            font-weight: 400;
        }

        .score-number {
            font-size: 14px;
            line-height: 1.2;
            font-weight: 400;
        }

        .status-pill {
            font-size: 12px;
            line-height: 1.2;
            font-weight: 500;
        }

        .compliance-search,
        .compliance-filter {
            font-size: 12px;
            font-weight: 400;
        }

        .compliance-pagination {
            font-size: 12px;
            font-weight: 400;
        }

        .seller-details-title-row h2 {
            font-size: 17px;
            line-height: 1.2;
            font-weight: 600;
        }

        .seller-details-store-wrap h3 {
            font-size: 19px;
            line-height: 1.05;
            font-weight: 600;
        }

        .seller-product-main-info h2 {
            font-size: 19px;
            line-height: 1.12;
            font-weight: 600;
        }

        .seller-product-price {
            font-size: 14px;
            font-weight: 600;
        }

        .seller-product-sold {
            font-size: 12px;
            font-weight: 400;
        }

        .seller-product-compliance-notice strong {
            font-size: 12px;
            font-weight: 600;
        }

        .seller-product-compliance-notice span {
            font-size: 12px;
            font-weight: 400;
        }

</style>


    <style>
        /* FINAL TABLE CONTENT FONT SIZE
           Keep every visible Seller Compliance table content at 12px.
           Header styling remains separate from row content. */
        .seller-compliance-page .compliance-table-body,
        .seller-compliance-page .compliance-table-body .seller-compliance-row,
        .seller-compliance-page .compliance-table-body .seller-compliance-row .seller-store-name,
        .seller-compliance-page .compliance-table-body .seller-compliance-row .seller-owner-name,
        .seller-compliance-page .compliance-table-body .seller-compliance-row .category-pill,
        .seller-compliance-page .compliance-table-body .seller-compliance-row .seller-products,
        .seller-compliance-page .compliance-table-body .seller-compliance-row .seller-products-total,
        .seller-compliance-page .compliance-table-body .seller-compliance-row .seller-products-review,
        .seller-compliance-page .compliance-table-body .seller-compliance-row .score-cell,
        .seller-compliance-page .compliance-table-body .seller-compliance-row .score-number,
        .seller-compliance-page .compliance-table-body .seller-compliance-row .status-pill {
            font-size: 12px !important;
        }
    </style>

    <style>
        /* =========================================================
           SELLER COMPLIANCE NAVBAR TITLE
           Keep the title lighter while allowing the shared admin
           sidebar script to control page movement at the same speed
           as the other admin pages.
        ========================================================== */
        #navbar-left h1 {
            font-weight: 600 !important;
        }
    </style>


    <style>
        /* =========================================================
           FILTER COLOR CONSISTENCY
           Use All Categories as the visual reference for the whole
           Seller Compliance filter row.
        ========================================================== */
        #sellerSearch,
        #sellerTypeFilter,
        #complianceFilter {
            border-color: #D9D6D4 !important;
            background-color: #FFFFFF !important;
            color: #76716E !important;
        }

        #sellerSearch::placeholder {
            color: #76716E !important;
            opacity: 1;
        }

        .compliance-search-icon {
            color: #76716E !important;
        }

        #complianceFilter {
            width: 168px !important;
            min-width: 168px !important;
        }

        #sellerSearch:focus,
        #sellerTypeFilter:focus,
        #complianceFilter:focus {
            border-color: #7B1B1B !important;
            box-shadow: 0 0 0 2px rgba(123, 27, 27, 0.10) !important;
        }
    </style>

</body>

</html>