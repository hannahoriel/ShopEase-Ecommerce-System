@extends('layouts.admin')

@section('page-title', 'Logistics Management')

@section('content')

@vite('resources/css/admin/logistics-management.css')

@php
    /*
    |--------------------------------------------------------------------------
    | DEMO / FALLBACK DATA
    |--------------------------------------------------------------------------
    | Replace this later with controller data.
    */
    $logisticsCompanies = $logisticsCompanies ?? [
        [
            'company' => 'J&T Express',
            'branches' => 24,
            'riders' => 154,

            'contact_number' => '123456789',
            'company_email' => 'j&texpress@gmail.com',
            'office_address' => 'kung saan ka masaya',
            'description' => 'J&T Express provides parcel pickup, sorting, transportation, and last-mile delivery services for businesses and customers.',
            'dti_permit' => '',
            'business_permit' => '',
            'partnership_since' => 2019,
            'account_created_date' => 'May 26, 2026',
            'account_created_time' => '6:45 PM',
            'logo' => '',

            'branches_data' => [
                [
                    'name' => 'Main Hub',
                    'address' => 'Purok 2, Masico, Pila, Laguna. Building 206',
                    'contact_person' => 'Maria Santos',
                    'phone' => '0945-789-1028',
                    'riders' => 25,
                ],
                [
                    'name' => 'Sorting Hub',
                    'address' => 'Purok 2, Masico, Pila, Laguna. Building 206',
                    'contact_person' => 'Maria Santos',
                    'phone' => '0945-789-1028',
                    'riders' => 25,
                ],
                [
                    'name' => 'Delivery Hub',
                    'address' => 'Purok 2, Masico, Pila, Laguna. Building 206',
                    'contact_person' => 'Maria Santos',
                    'phone' => '0945-789-1028',
                    'riders' => 25,
                ],
                [
                    'name' => 'Laguna Center',
                    'address' => 'Purok 2, Masico, Pila, Laguna. Building 206',
                    'contact_person' => 'Maria Santos',
                    'phone' => '0945-789-1028',
                    'riders' => 25,
                ],
            ],
        ],
    ];

    $pendingLogisticsRequests = $pendingLogisticsRequests ?? [
        [
            'company' => 'Flash Express',
            'email' => 'flash.express@gmail.com',
            'phone' => '0917-245-8812',
            'contact_person' => 'Andrea Cruz',
            'registered_date' => '2026-05-31',
            'office_address' => 'Bldg. 2, Southwoods Business Park, Biñan, Laguna',
            'description' => 'Flash Express provides nationwide parcel pickup, sorting, and last-mile delivery services for online sellers and customers.',
            'registered_display' => 'May 31, 2026 10:30 AM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'Ninja Van',
            'email' => 'ninjavan.ph@gmail.com',
            'phone' => '0918-662-1405',
            'contact_person' => 'Paolo Reyes',
            'registered_date' => '2026-05-30',
            'office_address' => 'Sta. Rosa Business District, Sta. Rosa, Laguna',
            'description' => 'Ninja Van offers technology-enabled parcel delivery, fulfillment, and logistics support for e-commerce businesses.',
            'registered_display' => 'May 30, 2026 3:45 PM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'GoGo Xpress',
            'email' => 'gogoxpress@gmail.com',
            'phone' => '0920-841-7701',
            'contact_person' => 'Camille Santos',
            'registered_date' => '2026-05-29',
            'office_address' => 'National Highway, Calamba, Laguna',
            'description' => 'GoGo Xpress provides parcel delivery, cash-on-delivery support, and logistics services for small businesses and online merchants.',
            'registered_display' => 'May 29, 2026 1:15 PM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'LBC Express',
            'email' => 'lbc.logistics@gmail.com',
            'phone' => '0919-321-5558',
            'contact_person' => 'Daniel Garcia',
            'registered_date' => '2026-05-28',
            'office_address' => 'Poblacion, Sta. Cruz, Laguna',
            'description' => 'LBC Express provides domestic courier, cargo, remittance, and logistics services through a nationwide branch network.',
            'registered_display' => 'May 28, 2026 11:05 AM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => '2GO Logistics',
            'email' => '2go.logistics@gmail.com',
            'phone' => '0921-776-2400',
            'contact_person' => 'Mika Flores',
            'registered_date' => '2026-05-27',
            'office_address' => 'Port Area Logistics Center, Manila',
            'description' => '2GO Logistics provides freight forwarding, distribution, warehousing, and transport solutions for businesses.',
            'registered_display' => 'May 27, 2026 9:40 AM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'Entrego',
            'email' => 'entrego.ph@gmail.com',
            'phone' => '0916-908-4473',
            'contact_person' => 'Joshua Lim',
            'registered_date' => '2026-05-26',
            'office_address' => 'Cabuyao Logistics Hub, Cabuyao, Laguna',
            'description' => 'Entrego provides parcel management, fulfillment, and last-mile delivery solutions for businesses and e-commerce platforms.',
            'registered_display' => 'May 26, 2026 5:20 PM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
        [
            'company' => 'XDE Logistics',
            'email' => 'xde.logistics@gmail.com',
            'phone' => '0922-667-9094',
            'contact_person' => 'Sofia Mendoza',
            'registered_date' => '2026-05-25',
            'office_address' => 'Industrial Park, Calamba, Laguna',
            'description' => 'XDE Logistics provides cargo handling, trucking, distribution, and end-to-end logistics support for commercial clients.',
            'registered_display' => 'May 25, 2026 2:10 PM',
            'logo' => '',
            'dti_permit' => '',
            'business_permit' => '',
        ],
    ];

    $rejectedLogisticsArchive = $rejectedLogisticsArchive ?? [
        [
            'company' => 'PrimeTrack Logistics',
            'email' => 'primetrack.logistics@gmail.com',
            'phone' => '0917-801-2431',
            'contact_person' => 'Carlo Mendoza',
            'rejected_display' => 'May 24, 2026',
            'rejection_reason' => 'Incomplete or missing business permits.',
            'rejection_details' => '',
        ],
        [
            'company' => 'CargoLink Express',
            'email' => 'cargolink.express@gmail.com',
            'phone' => '0918-445-9072',
            'contact_person' => 'Mia Torres',
            'rejected_display' => 'May 21, 2026',
            'rejection_reason' => 'Company information is inconsistent or cannot be verified.',
            'rejection_details' => '',
        ],
        [
            'company' => 'RapidMove Logistics',
            'email' => 'rapidmove.logistics@gmail.com',
            'phone' => '0920-338-6147',
            'contact_person' => 'Jerome Castillo',
            'rejected_display' => 'May 18, 2026',
            'rejection_reason' => 'Does not meet ShopEase logistics partnership requirements.',
            'rejection_details' => '',
        ],
    ];

    $logisticsStats = $logisticsStats ?? [
        'pending_requests' => count($pendingLogisticsRequests),
        'accepted_logistics' => 12,
        'rejected_logistics' => count($rejectedLogisticsArchive),
        'total_companies' => 12,
    ];

    $pendingLogisticsTotalEntries = $pendingLogisticsTotalEntries ?? count($pendingLogisticsRequests);
    $logisticsTotalEntries = $logisticsTotalEntries ?? 378;
@endphp



<div
    id="admin-content"
    class="ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300"
>
    <!-- =====================================================
         LOGISTICS STAT CARDS
         Matches Account Registrations card layout exactly.
    ====================================================== -->

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <!-- =====================================================
             PENDING REQUESTS
        ====================================================== -->

        <div
            class="logistics-stat-card
                   flex flex-col justify-between
                   transition-all duration-300
                   hover:-translate-y-1 hover:shadow-md"
        >

            <div class="flex items-center gap-2.5">

                <!-- Icon -->

                <div
                    class="shrink-0 flex items-center justify-center"
                >

                    <img
                        src="{{ asset('icons/admin/complaints-disputes/in-progress.png') }}"
                        class="logistics-stat-icon"
                        alt="Pending Requests"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p
                        id="pending-logistics-stat-count"
                        class="logistics-stat-number"
                    >
                        {{ number_format($logisticsStats['pending_requests']) }}
                    </p>

                    <p class="logistics-stat-label whitespace-nowrap">
                        Pending Requests
                    </p>

                </div>

            </div>

        </div>


        <!-- =====================================================
             ACCEPTED LOGISTICS
        ====================================================== -->

        <div
            class="logistics-stat-card
                   flex flex-col justify-between
                   transition-all duration-300
                   hover:-translate-y-1 hover:shadow-md"
        >

            <div class="flex items-center gap-2.5">

                <!-- Icon -->

                <div
                    class="shrink-0 flex items-center justify-center"
                >

                    <img
                        src="{{ asset('icons/admin/registrations/Approved Registrations.png') }}"
                        class="logistics-stat-icon"
                        alt="Accepted Logistics"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p
                        id="accepted-logistics-stat-count"
                        class="logistics-stat-number"
                    >
                        {{ number_format($logisticsStats['accepted_logistics']) }}
                    </p>

                    <p class="logistics-stat-label whitespace-nowrap">
                        Accepted Logistics
                    </p>

                </div>

            </div>

        </div>


        <!-- =====================================================
             REJECTED LOGISTICS ARCHIVE
        ====================================================== -->

        <div>

            <button
                type="button"
                id="rejected-logistics-button"
                class="logistics-stat-card w-full
                       relative text-left
                       flex flex-col justify-center
                       transition-all duration-300
                       hover:-translate-y-1 hover:shadow-md
                       hover:border-[#E9A3A3]
                       group"
                aria-label="View rejected logistics"
            >

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-2.5">

                        <!-- Icon -->

                        <div
                            class="shrink-0 flex items-center justify-center"
                        >

                            <img
                                src="{{ asset('icons/admin/registrations/Rejected Registrations.png') }}"
                                class="logistics-stat-icon"
                                alt="Rejected Logistics"
                            >

                        </div>


                        <!-- Content -->

                        <div class="min-w-0">

                            <p
                                id="rejected-logistics-stat-count"
                                class="logistics-stat-number"
                            >
                                {{ number_format($logisticsStats['rejected_logistics']) }}
                            </p>

                            <p class="logistics-stat-label whitespace-nowrap">
                                Rejected Logistics
                            </p>

                        </div>

                    </div>


                    <!-- Arrow -->

                    <svg
                        class="w-[18px] h-[18px] shrink-0 text-gray-400
                               group-hover:text-[#7B1B1B]
                               group-hover:translate-x-1
                               transition-all duration-300"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="m9 5 7 7-7 7"
                        />

                    </svg>

                </div>


                <!-- Bottom Text -->

                <div class="mt-2 ml-[52px]">

                    <span class="logistics-card-link">
                        View rejected users →
                    </span>

                </div>

            </button>

        </div>


        <!-- =====================================================
             TOTAL LOGISTICS
        ====================================================== -->

        <div
            class="logistics-stat-card relative
                   flex flex-col justify-start
                   transition-all duration-300
                   hover:-translate-y-1 hover:shadow-md"
        >

            <div class="flex items-center gap-2.5">

                <!-- Icon -->

                <div
                    class="shrink-0 flex items-center justify-center"
                >

                    <img
                        src="{{ asset('icons/admin/logistics/logistics.png') }}"
                        class="logistics-stat-icon"
                        alt="Total Logistics"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p
                        id="total-logistics-stat-count"
                        class="logistics-stat-number"
                    >
                        {{ number_format($logisticsStats['total_companies']) }}
                    </p>

                    <p class="logistics-stat-label whitespace-nowrap">
                        Total Logistics
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- =====================================================
         MAIN TABS
    ====================================================== -->
    <section class="logistics-main-tabs-card">
        <div
            class="logistics-main-tabs"
            role="tablist"
            aria-label="Logistics management tabs"
        >
            <button
                id="pending-requests-main-tab"
                type="button"
                class="logistics-main-tab active"
                role="tab"
                aria-selected="true"
                data-main-tab="pending"
            >
                Pending Requests
            </button>

            <button
                id="logistics-main-tab"
                type="button"
                class="logistics-main-tab"
                role="tab"
                aria-selected="false"
                data-main-tab="logistics"
            >
                Logistics
            </button>
        </div>
    </section>

    <!-- =====================================================
         PENDING REQUESTS TAB
    ====================================================== -->
    <section
        id="pending-requests-panel"
        class="logistics-main-panel active"
        aria-labelledby="pending-requests-main-tab"
    >
        <!-- Filters -->
        <div class="pending-logistics-filter-card">

            <div class="pending-logistics-search-wrap">
                <input
                    id="pending-logistics-search"
                    type="text"
                    class="pending-logistics-search"
                    placeholder="Search company, email, phone, contact person"
                    autocomplete="off"
                >

                <svg
                    class="pending-logistics-search-icon"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />
                </svg>
            </div>

            <div class="pending-date-wrap">
                <input
                    id="pending-logistics-date"
                    type="date"
                    class="pending-date-input"
                    aria-label="Filter pending requests by registration date"
                >
            </div>

            <button
                id="pending-logistics-reload"
                type="button"
                class="pending-reload"
                title="Reload"
                aria-label="Reset pending request filters"
            >
                <svg
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M20 11a8.1 8.1 0 0 0-15.5-2M4 5v4h4M4 13a8.1 8.1 0 0 0 15.5 2M20 19v-4h-4"
                    />
                </svg>
            </button>
        </div>

        <!-- Pending Table -->
        <section class="pending-logistics-table-card">
            <div class="pending-logistics-table-scroll">
                <table
                    id="pending-logistics-table"
                    class="pending-logistics-table"
                >
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                            <th>Contact Person</th>
                            <th>Date Registered</th>
                        </tr>
                    </thead>

                    <tbody id="pending-logistics-body"></tbody>
                </table>

                <div
                    id="pending-logistics-empty"
                    class="pending-table-empty"
                >
                    No pending logistics requests found.
                </div>
            </div>

            <div class="pending-table-footer">
                <p
                    id="pending-logistics-count"
                    class="pending-count"
                >
                    Showing 0 out of {{ number_format($pendingLogisticsTotalEntries) }} entries
                </p>

                <div class="pending-pagination">
                    <button
                        id="pending-logistics-prev"
                        type="button"
                        class="pending-page-button"
                        aria-label="Previous page"
                    >
                        ‹
                    </button>

                    <div
                        id="pending-logistics-pages"
                        class="flex items-center gap-2"
                    ></div>

                    <button
                        id="pending-logistics-next"
                        type="button"
                        class="pending-page-button"
                        aria-label="Next page"
                    >
                        ›
                    </button>

                    <select
                        id="pending-logistics-items"
                        class="pending-items"
                    >
                        <option value="10" selected>Items per page: 10</option>
                        <option value="20">Items per page: 20</option>
                        <option value="50">Items per page: 50</option>
                    </select>
                </div>
            </div>
        </section>
    </section>

    <!-- =====================================================
         LOGISTICS TAB — CURRENT TABLE RETAINED
    ====================================================== -->
    <section
        id="logistics-panel"
        class="logistics-main-panel"
        aria-labelledby="logistics-main-tab"
    >
        <section class="logistics-action-card">
            <div class="logistics-search-wrap">
                <input
                    id="logistics-search"
                    type="text"
                    class="logistics-search"
                    placeholder="Search company"
                    autocomplete="off"
                >

                <svg
                    class="logistics-search-icon"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />
                </svg>
            </div>
        </section>

        <section class="logistics-table-card">
            <div class="logistics-table-scroll">
                <table class="logistics-table">
                    <thead>
                        <tr>
                            <th>Company Name</th>
                            <th>Number of Branches</th>
                            <th>Number of Riders</th>
                        </tr>
                    </thead>

                    <tbody id="logistics-table-body"></tbody>
                </table>

                <div
                    id="logistics-empty"
                    class="logistics-empty"
                >
                    No logistics company matches your search.
                </div>
            </div>

            <div class="logistics-table-footer">
                <p
                    id="logistics-count"
                    class="logistics-count"
                >
                    Showing 1 out of {{ number_format($logisticsTotalEntries) }} entries
                </p>

                <div class="logistics-pagination">
                    <button
                        id="logistics-prev"
                        type="button"
                        class="logistics-page-button"
                        aria-label="Previous page"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m15 6-6 6 6 6"
                            />
                        </svg>
                    </button>

                    <div
                        id="logistics-pages"
                        class="flex items-center gap-2"
                    ></div>

                    <button
                        id="logistics-next"
                        type="button"
                        class="logistics-page-button"
                        aria-label="Next page"
                    >
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m9 6 6 6-6 6"
                            />
                        </svg>
                    </button>

                    <select
                        id="logistics-items"
                        class="logistics-items"
                    >
                        <option value="7" selected>Items per page: 7</option>
                        <option value="10">Items per page: 10</option>
                        <option value="20">Items per page: 20</option>
                    </select>
                </div>
            </div>
        </section>
    </section>
</div>


<!-- =========================================================
     REJECTED LOGISTICS ARCHIVE MODAL
     Styled to match the Rejected Users modal in Registrations.
========================================================== -->
<div
    id="rejected-logistics-modal"
    class="fixed inset-0 z-[180] hidden items-center justify-center
           bg-black/30 backdrop-blur-sm px-5"
    aria-hidden="true"
>
    <div
        class="logistics-archive-modal bg-white w-full max-w-5xl max-h-[88vh]
               overflow-y-auto rounded-2xl shadow-xl
               transform transition-all duration-300"
        role="dialog"
        aria-modal="true"
        aria-labelledby="rejected-logistics-modal-title"
    >
        <!-- Modal Header -->
        <div
            class="flex items-center justify-between
                   px-5 py-4 border-b border-gray-200"
        >
            <div>
                <h3
                    id="rejected-logistics-modal-title"
                    class="text-[20px] font-bold text-gray-900"
                >
                    Rejected Logistics
                </h3>

                <p class="text-[13px] text-gray-400 mt-1">
                    Archived rejected logistics requests
                </p>
            </div>

            <button
                type="button"
                id="close-rejected-logistics"
                class="w-8 h-8 flex items-center justify-center
                       rounded-full text-gray-500
                       hover:bg-gray-100 hover:text-gray-800
                       transition"
                aria-label="Close rejected logistics"
            >
                <svg
                    class="w-[18px] h-[18px]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M6 6l12 12M18 6 6 18"
                    />
                </svg>
            </button>
        </div>

        <!-- Rejected Logistics Search -->
        <div class="px-6 py-4 border-b border-gray-100 bg-[#FFFBF9]">
            <div class="relative max-w-md">
                <input
                    type="text"
                    id="rejected-logistics-search"
                    placeholder="Search rejected logistics..."
                    class="w-full h-[40px] rounded-lg border border-gray-300
                           bg-white pl-10 pr-4 text-[13px] text-gray-700
                           placeholder:text-gray-300 outline-none
                           focus:border-[#7B1B1B]
                           focus:ring-2 focus:ring-[#7B1B1B]/10
                           transition"
                >

                <svg
                    class="pointer-events-none absolute left-3 top-1/2
                           -translate-y-1/2 w-4 h-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />
                </svg>
            </div>
        </div>

        <!-- Rejected Logistics Table -->
        <div class="overflow-x-auto max-h-none">
            <table class="w-full min-w-[900px]">
                <thead class="sticky top-0 bg-white">
                    <tr class="border-b border-gray-200">
                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            Company
                        </th>

                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            Email
                        </th>

                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            Phone
                        </th>

                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            Contact Person
                        </th>

                        <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                            <button
                                type="button"
                                id="rejected-logistics-date-sort"
                                class="inline-flex items-center gap-1.5
                                       hover:text-[#7B1B1B] transition"
                                aria-label="Sort rejected logistics by date"
                            >
                                <span>Date Rejected</span>

                                <span
                                    id="rejected-logistics-sort-arrow"
                                    class="inline-flex items-center justify-center
                                           w-6 h-6 text-[17px] leading-none
                                           font-bold text-[#7B1B1B]
                                           rounded-md bg-[#FFF0EC]
                                           border border-[#F3C2B5]"
                                >
                                    ↓
                                </span>
                            </button>
                        </th>
                    </tr>
                </thead>

                <tbody
                    id="rejected-logistics-body"
                    class="divide-y divide-gray-200"
                ></tbody>
            </table>
        </div>

        <!-- Modal Footer -->
        <div
            class="px-6 py-4 border-t border-gray-200
                   flex items-center justify-between"
        >
            <p
                id="rejected-logistics-count"
                class="text-[12px] text-gray-400"
            ></p>

            <button
                type="button"
                id="close-rejected-logistics-bottom"
                class="px-5 py-2 rounded-lg
                       bg-[#7B1B1B] text-white
                       text-[13px] font-medium
                       hover:bg-[#641515]
                       transition"
            >
                Close
            </button>
        </div>
    </div>
</div>


<!-- =========================================================
     PENDING LOGISTICS REVIEW MODAL
========================================================== -->
<div
    id="pending-review-modal"
    class="pending-review-backdrop"
    aria-hidden="true"
>
    <div
        class="pending-review-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="pending-review-title"
    >
        <h2
            id="pending-review-title"
            class="pending-review-title"
        >
            Logistics Application Details
        </h2>

        <div class="pending-review-columns">

            <!-- LEFT: READ-ONLY COMPANY INFORMATION -->
            <section class="pending-review-panel">
                <h3 class="pending-review-panel-title">
                    <img
                        src="{{ asset('icons/admin/logistics/truck.png') }}"
                        alt=""
                        aria-hidden="true"
                    >
                    Company Information
                </h3>

                <div class="pending-review-info-list">
                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Company Name
                        </span>
                        <div
                            id="pending-review-company"
                            class="pending-review-info-value"
                        >
                            —
                        </div>
                    </div>

                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Contact Number
                        </span>
                        <div
                            id="pending-review-phone"
                            class="pending-review-info-value"
                        >
                            —
                        </div>
                    </div>

                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Email Address
                        </span>
                        <div
                            id="pending-review-email"
                            class="pending-review-info-value"
                        >
                            —
                        </div>
                    </div>

                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Contact Person
                        </span>
                        <div
                            id="pending-review-contact-person"
                            class="pending-review-info-value"
                        >
                            —
                        </div>
                    </div>

                    <div class="pending-review-info-item">
                        <span class="pending-review-info-label">
                            Office Address
                        </span>
                        <div
                            id="pending-review-address"
                            class="pending-review-info-value address"
                        >
                            —
                        </div>
                    </div>
                </div>

                <h4 class="pending-review-additional-title">
                    Additional Details
                </h4>

                <span class="pending-review-info-label">
                    Company Logo
                </span>

                <div
                    id="pending-review-logo-box"
                    class="pending-review-logo-box"
                >
                    <div class="pending-review-logo-placeholder">
                        <svg
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <rect
                                x="3"
                                y="4"
                                width="18"
                                height="16"
                                rx="2"
                                stroke-width="1.8"
                            />
                            <circle
                                cx="8.5"
                                cy="9"
                                r="1.5"
                                stroke-width="1.8"
                            />
                            <path
                                d="m5 17 4.2-4.2 3.2 3.2 2.1-2.1L19 18"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>

                        <span>No company logo uploaded</span>
                    </div>

                    <img
                        id="pending-review-logo"
                        src=""
                        alt="Company logo"
                    >
                </div>

                <h4 class="pending-review-permits-title">
                    Permits
                </h4>

                <div class="pending-review-permits-grid">
                    <div class="pending-review-permit-item">
                        <span class="pending-review-permit-label">
                            DTI Permit
                        </span>

                        <button
                            id="pending-review-dti-button"
                            type="button"
                            class="pending-review-permit-button"
                            aria-label="View DTI Permit"
                        >
                            <img
                                id="pending-review-dti-permit"
                                src=""
                                alt="DTI Permit preview"
                            >

                            <span class="pending-review-permit-zoom-hint">
                                Click to view
                            </span>
                        </button>
                    </div>

                    <div class="pending-review-permit-item">
                        <span class="pending-review-permit-label">
                            Business Permit
                        </span>

                        <button
                            id="pending-review-business-button"
                            type="button"
                            class="pending-review-permit-button"
                            aria-label="View Business Permit"
                        >
                            <img
                                id="pending-review-business-permit"
                                src=""
                                alt="Business Permit preview"
                            >

                            <span class="pending-review-permit-zoom-hint">
                                Click to view
                            </span>
                        </button>
                    </div>
                </div>
            </section>

            <!-- RIGHT: LOGISTICS DESCRIPTION -->
            <section class="pending-review-panel">
                <h3 class="pending-review-panel-title">
                    Logistics Description
                </h3>

                <div class="pending-review-description-note">
                    <svg
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                        aria-hidden="true"
                    >
                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke-width="2"
                        />
                        <path
                            d="M12 10v6M12 7h.01"
                            stroke-width="2"
                            stroke-linecap="round"
                        />
                    </svg>

                    <p>
                        Review the logistics company's submitted description
                        and company information before processing the request.
                    </p>
                </div>

                <span class="pending-review-info-label">
                    Description
                </span>

                <div
                    id="pending-review-description"
                    class="pending-review-description-box"
                >
                    —
                </div>

                <div class="pending-review-meta">
                    <div class="pending-review-meta-item">
                        <span class="pending-review-meta-label">
                            Date Registered
                        </span>
                        <span
                            id="pending-review-date"
                            class="pending-review-meta-value"
                        >
                            —
                        </span>
                    </div>

                    <div class="pending-review-meta-item pending-review-status-item">
                        <span class="pending-review-meta-label">
                            Request Status
                        </span>
                        <span class="pending-review-status-pill">
                            Pending
                        </span>
                    </div>
                </div>
            </section>

        </div>

        <div class="pending-review-actions">
            <button
                id="pending-review-close"
                type="button"
                class="pending-review-close"
            >
                Close
            </button>

            <button
                id="pending-review-reject"
                type="button"
                class="pending-review-reject"
            >
                Reject
            </button>

            <button
                id="pending-review-approve"
                type="button"
                class="pending-review-approve"
            >
                Approve
            </button>
        </div>
    </div>
</div>


<!-- =========================================================
     LOGISTICS DECISION FLASH MESSAGE
========================================================== -->
<div
    id="logistics-flash"
    class="logistics-decision-flash"
    role="status"
    aria-live="polite"
    aria-atomic="true"
>
    <div class="logistics-flash-content">

        <div
            id="logistics-flash-icon"
            class="logistics-flash-icon"
        ></div>

        <div class="logistics-flash-copy">
            <p
                id="logistics-flash-title"
                class="logistics-flash-title"
            ></p>

            <p
                id="logistics-flash-message"
                class="logistics-flash-message"
            ></p>
        </div>

        <button
            id="logistics-flash-close"
            type="button"
            class="logistics-flash-close"
            aria-label="Close notification"
        >
            <svg
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                aria-hidden="true"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 6l12 12M18 6 6 18"
                />
            </svg>
        </button>

    </div>
</div>


<!-- =========================================================
     REJECT LOGISTICS REQUEST MODAL
========================================================== -->
<div
    id="logistics-reject-modal"
    class="logistics-reject-backdrop"
    aria-hidden="true"
>
    <div
        class="logistics-reject-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="logistics-reject-title"
    >
        <div class="logistics-reject-body">
            <h3
                id="logistics-reject-title"
                class="logistics-reject-title"
            >
                Reject Logistics Request
            </h3>

            <p class="logistics-reject-subtitle">
                Please select the reason for rejecting this logistics company request.
            </p>

            <div class="logistics-reject-reason-section">
                <label class="logistics-reject-label">
                    Reason<span class="logistics-reject-required">*</span>
                </label>

                <div
                    id="logistics-reject-reason-options"
                    class="logistics-reject-options"
                >
                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Incomplete or missing business permits."
                        >
                        <span>
                            Incomplete or missing business permits.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="DTI or Business Permit is invalid, expired, or unverifiable."
                        >
                        <span>
                            DTI or Business Permit is invalid, expired, or unverifiable.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Company information is inconsistent or cannot be verified."
                        >
                        <span>
                            Company information is inconsistent or cannot be verified.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Does not meet ShopEase logistics partnership requirements."
                        >
                        <span>
                            Does not meet ShopEase logistics partnership requirements.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Insufficient delivery coverage or operational capacity."
                        >
                        <span>
                            Insufficient delivery coverage or operational capacity.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Duplicate or existing logistics partner account."
                        >
                        <span>
                            Duplicate or existing logistics partner account.
                        </span>
                    </label>

                    <label class="logistics-reject-option">
                        <input
                            type="radio"
                            name="logistics_reject_reason"
                            value="Other"
                        >
                        <span>
                            Other
                        </span>
                    </label>
                </div>

                <p
                    id="logistics-reject-reason-error"
                    class="logistics-reject-error"
                >
                    Please select a reason before rejecting the request.
                </p>
            </div>

            <div class="logistics-reject-details-section">
                <label
                    for="logistics-reject-additional-details"
                    class="logistics-reject-label"
                >
                    Additional Details
                    <span style="font-weight:400;">(Optional)</span>
                </label>

                <div class="logistics-reject-textarea-wrap">
                    <textarea
                        id="logistics-reject-additional-details"
                        class="logistics-reject-textarea"
                        maxlength="300"
                        placeholder="Write additional details here..."
                    ></textarea>

                    <span
                        id="logistics-reject-details-count"
                        class="logistics-reject-character-count"
                    >
                        0/300
                    </span>
                </div>
            </div>
        </div>

        <div class="logistics-reject-actions">
            <button
                id="cancel-logistics-reject"
                type="button"
                class="logistics-reject-cancel"
            >
                Cancel
            </button>

            <button
                id="confirm-logistics-reject"
                type="button"
                class="logistics-reject-confirm"
            >
                Reject
            </button>
        </div>
    </div>
</div>


<!-- =========================================================
     PERMIT IMAGE ZOOM MODAL
========================================================== -->
<div
    id="permit-zoom-modal"
    class="permit-zoom-backdrop"
    aria-hidden="true"
>
    <div
        class="permit-zoom-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="permit-zoom-title"
    >
        <div class="permit-zoom-header">
            <h3
                id="permit-zoom-title"
                class="permit-zoom-title"
            >
                Permit Preview
            </h3>

            <button
                id="permit-zoom-close"
                type="button"
                class="permit-zoom-close"
                aria-label="Close permit preview"
            >
                ×
            </button>
        </div>

        <div class="permit-zoom-image-wrap">
            <img
                id="permit-zoom-image"
                class="permit-zoom-image"
                src=""
                alt="Permit preview"
            >
        </div>
    </div>
</div>


<!-- =========================================================
     COMPANY DETAILS MODAL
========================================================== -->
<div
    id="logistics-details-modal"
    class="logistics-details-backdrop"
    aria-hidden="true"
>
    <div
        class="logistics-details-dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="logistics-details-title"
    >
        <header class="logistics-details-header">
            <h2
                id="logistics-details-title"
                class="logistics-details-title"
            >
                Company Details
            </h2>
        </header>

        <div class="logistics-details-body">

            <div class="logistics-details-company-head">
                <div
                    id="logistics-details-avatar"
                    class="logistics-details-avatar"
                >
                    <img
                        id="logistics-details-avatar-image"
                        src=""
                        alt="Company logo"
                    >
                </div>

                <div class="logistics-details-company-copy">
                    <h3
                        id="logistics-details-company-name"
                        class="logistics-details-company-name"
                    >
                        J&T Express
                    </h3>

                    <p
                        id="logistics-details-company-partnership"
                        class="logistics-details-company-partnership"
                    >
                        In partnership with ShopEase since 2019
                    </p>
                </div>
            </div>

            <div
                class="logistics-details-tabs"
                role="tablist"
                aria-label="Company details tabs"
            >
                <button
                    id="logistics-details-company-tab"
                    type="button"
                    class="logistics-details-tab active"
                    role="tab"
                    aria-selected="true"
                    aria-controls="logistics-company-details-panel"
                    data-details-tab="company"
                >
                    Company Details
                </button>

                <button
                    id="logistics-details-branches-tab"
                    type="button"
                    class="logistics-details-tab"
                    role="tab"
                    aria-selected="false"
                    aria-controls="logistics-branches-panel"
                    data-details-tab="branches"
                >
                    Branches
                </button>
            </div>

            <div class="logistics-details-content-shell">

                <!-- COMPANY DETAILS TAB -->
                <section
                    id="logistics-company-details-panel"
                    class="logistics-details-panel active"
                    role="tabpanel"
                    aria-labelledby="logistics-details-company-tab"
                >
                    <div class="logistics-company-info-grid">

                        <div class="logistics-company-info-left">

                            <h4 class="logistics-detail-section-title">
                                Company Information
                            </h4>

                            <div class="logistics-detail-list">
                                <span class="logistics-detail-label">
                                    Company Name
                                </span>
                                <span
                                    id="details-company-name"
                                    class="logistics-detail-value"
                                >
                                    J&T Express
                                </span>

                                <span class="logistics-detail-label">
                                    Contact Number
                                </span>
                                <span
                                    id="details-contact-number"
                                    class="logistics-detail-value"
                                >
                                    123456789
                                </span>

                                <span class="logistics-detail-label">
                                    Email Address
                                </span>
                                <span
                                    id="details-company-email"
                                    class="logistics-detail-value"
                                >
                                    j&texpress@gmail.com
                                </span>

                                <span class="logistics-detail-label">
                                    Office Address
                                </span>
                                <span
                                    id="details-office-address"
                                    class="logistics-detail-value"
                                >
                                    kung saan ka masaya
                                </span>
                            </div>

</div>

                        <div class="logistics-company-info-right">
                            <div class="logistics-created-block">
                                <svg
                                    class="logistics-created-icon"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M8 2v3m8-3v3M4 9h16M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 13h2v2H9z"
                                    />
                                </svg>

                                <div class="logistics-created-copy">
                                    <strong>Account Created</strong>

                                    <div class="logistics-created-date-line">
                                        <span id="details-created-date">
                                            May 26, 2026
                                        </span>

                                        <span id="details-created-time">
                                            6:45 PM
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="logistics-details-extra">

                        <div class="logistics-details-description">
                            <h4 class="logistics-detail-section-title">
                                Logistics Description
                            </h4>

                            <p
                                id="details-company-description"
                                class="logistics-details-description-text"
                            >
                                —
                            </p>
                        </div>

                        <div class="logistics-details-permits">
                            <h4 class="logistics-detail-section-title">
                                Permits
                            </h4>

                            <div class="logistics-details-permits-grid">

                                <div class="logistics-details-permit-item">
                                    <span class="logistics-details-permit-label">
                                        DTI Permit
                                    </span>

                                    <button
                                        id="details-dti-permit-button"
                                        type="button"
                                        class="logistics-details-permit-button"
                                        aria-label="View DTI Permit"
                                    >
                                        <img
                                            id="details-dti-permit"
                                            src=""
                                            alt="DTI Permit preview"
                                        >

                                        <span class="logistics-details-permit-zoom-hint">
                                            Click to view
                                        </span>
                                    </button>
                                </div>

                                <div class="logistics-details-permit-item">
                                    <span class="logistics-details-permit-label">
                                        Business Permit
                                    </span>

                                    <button
                                        id="details-business-permit-button"
                                        type="button"
                                        class="logistics-details-permit-button"
                                        aria-label="View Business Permit"
                                    >
                                        <img
                                            id="details-business-permit"
                                            src=""
                                            alt="Business Permit preview"
                                        >

                                        <span class="logistics-details-permit-zoom-hint">
                                            Click to view
                                        </span>
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>
                </section>

                <!-- BRANCHES TAB -->
                <section
                    id="logistics-branches-panel"
                    class="logistics-details-panel"
                    role="tabpanel"
                    aria-labelledby="logistics-details-branches-tab"
                >
                    <div class="logistics-branches-table-wrap">
                        <div style="overflow-x:auto;">
                            <table class="logistics-branches-table">
                                <thead>
                                    <tr>
                                        <th>Branch Name</th>
                                        <th>Address</th>
                                        <th>Contact Person</th>
                                        <th>Phone Number</th>
                                        <th>Riders</th>
                                    </tr>
                                </thead>

                                <tbody id="logistics-branches-body"></tbody>
                            </table>
                        </div>

                        <div
                            id="logistics-branches-empty"
                            class="logistics-branches-empty"
                            hidden
                        >
                            No branch information available yet.
                        </div>
                    </div>
                </section>

            </div>
        </div>

        <footer class="logistics-details-footer">
            <button
                id="close-logistics-details"
                type="button"
                class="logistics-details-close"
            >
                Close
            </button>
        </footer>
    </div>
</div>


@php
    $logisticsManagementClientConfig = [
        'pendingRequests' => $pendingLogisticsRequests,
        'pendingTotalEntries' => $pendingLogisticsTotalEntries,
        'companies' => $logisticsCompanies,
        'rejectedArchive' => $rejectedLogisticsArchive,
        'totalEntries' => $logisticsTotalEntries,
        'stats' => $logisticsStats,
    ];
@endphp

<script
    type="application/json"
    id="logisticsManagementConfig"
>{!! json_encode($logisticsManagementClientConfig, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>

@endsection

@push('scripts')

@vite('resources/js/admin/logistics-management.js')

@endpush
