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
            'sorting_centers' => 24,
            'delivery_hubs' => 58,
            'couriers' => 93,
            'registered_date' => 'May 26, 2026',
            'contact_person' => 'Maria Santos',
            'contact_phone' => '0917-456-3210',
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
            'registered_business_name' => 'Flash Express Philippines Inc.', 'company_type' => 'Corporation', 'years_in_operation' => '6',
            'sec_registration_number' => 'CS202012345', 'tin' => '234-567-890-000', 'service_areas' => ['Region IV-A', 'NCR'],
            'company_website' => 'https://www.flashexpress.ph', 'representative_position' => 'Operations Manager', 'representative_email' => 'andrea.cruz@example.com', 'representative_sex' => 'Female', 'representative_birthday' => '1994-03-12',
            'region' => 'Region IV-A (CALABARZON)', 'province' => 'Laguna', 'city' => 'Biñan', 'barangay' => 'San Francisco', 'postal_code' => '4024', 'street' => 'Southwoods Avenue', 'house_no' => '2', 'building' => 'Building 2', 'subdivision' => 'Southwoods Business Park',
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
            'registered_business_name' => 'Ninja Van Philippines Corp.', 'company_type' => 'Corporation', 'years_in_operation' => '8',
            'sec_registration_number' => 'CS201812345', 'tin' => '345-678-901-000', 'service_areas' => ['Region IV-A', 'NCR'],
            'representative_position' => 'Regional Coordinator', 'representative_email' => 'paolo.reyes@example.com', 'representative_sex' => 'Male', 'representative_birthday' => '1992-08-19',
            'region' => 'Region IV-A (CALABARZON)', 'province' => 'Laguna', 'city' => 'Santa Rosa', 'barangay' => 'Balibago', 'postal_code' => '4026', 'street' => 'Business District Road',
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
                            <th>Sorting Centers</th>
                            <th>Delivery Hubs</th>
                            <th>Couriers</th>
                            <th>Riders</th>
                            <th>Contact Person / Number</th>
                            <th>Date Registered</th>
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

<div id="pending-registration-full-details" class="ar-full-details" aria-label="Full registration details"></div>

        <div class="pending-review-columns" style="display:none">

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
                        <strong id="pending-review-logo-initials">L</strong>
                        <span>Profile photo not available</span>
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
                    <span>L</span>
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
                    Sorting Centers
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

                    </div>
                    <div class="ar-company-extra" id="lmCompanyRegistrationDetails" aria-label="Complete company registration information"></div>
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
                                        <th>Sorting Center Name</th>
                                        <th>Address</th>
                                        <th>Contact Person</th>
                                        <th>Phone Number</th>
                                        <th>Riders</th>
                                        <th>Delivery Hubs</th>
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
                            No sorting center information available yet.
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


<!-- Sorting Center details open independently instead of below the table. -->
<div id="lmCenterOverlay" class="lm-center-overlay" hidden role="dialog" aria-modal="true" aria-labelledby="lmCenterTitle">
  <section class="lm-center-dialog">
    <header class="lm-center-head"><div><h2 id="lmCenterTitle">Sorting Center Details</h2><p>Registered center information and assigned delivery hubs</p></div><button type="button" id="lmCenterClose" aria-label="Close sorting center details">&times;</button></header>
    <div id="lmCenterBody" class="lm-center-body"></div>
    <footer class="lm-center-footer"><button type="button" id="lmCenterDone">Close</button></footer>
  </section>
</div>

@php

    $registrationDetailDemos = [
        'Flash Express' => ['sec_registration_number'=>'CS202012345','tin'=>'234-567-890-000','service_areas'=>['Region IV-A','NCR'],'registered_business_name'=>'Flash Express Philippines Inc.','company_type'=>'Corporation','years_in_operation'=>'6','representative_position'=>'Operations Director','representative_email'=>'andrea.cruz@example.com','representative_sex'=>'female','representative_birthday'=>'1990-07-14','region'=>'Region IV-A (CALABARZON)','province'=>'Laguna','city'=>'Biñan','barangay'=>'San Francisco','postal_code'=>'4024','street'=>'Southwoods Avenue','house_no'=>'Lot 5','building'=>'Bldg. 2','subdivision'=>'Southwoods Business Park'],
        'Ninja Van' => ['sec_registration_number'=>'CS201912456','tin'=>'345-678-901-000','service_areas'=>['NCR','Region IV-A'],'registered_business_name'=>'Ninja Van Philippines Corporation','company_type'=>'Corporation','years_in_operation'=>'7','representative_position'=>'Regional Manager','representative_email'=>'paolo.reyes@example.com','representative_sex'=>'male','representative_birthday'=>'1988-08-09','region'=>'Region IV-A (CALABARZON)','province'=>'Laguna','city'=>'Santa Rosa','barangay'=>'Balibago','postal_code'=>'4026','street'=>'National Highway','house_no'=>'28','building'=>'Operations Center','subdivision'=>'Business District'],
    ];

    $logisticsManagementClientConfig = [
        'pendingRequests' => $pendingLogisticsRequests,
        'pendingTotalEntries' => $pendingLogisticsTotalEntries,
        'companies' => $logisticsCompanies,
        'rejectedArchive' => $rejectedLogisticsArchive,
        'totalEntries' => $logisticsTotalEntries,
        'stats' => $logisticsStats,
        'apiUrl' => url('/api/v1/admin/logistics-management'),
        'accountChangeRequestsUrl' => url('/api/v1/admin/logistics/account-change-requests'),
        'registrationPreview' => $registrationDetailDemos,
    ];
@endphp

<script
    type="application/json"
    id="logisticsManagementConfig"
>{!! json_encode($logisticsManagementClientConfig, JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>


<style id="logistics-management-corrections">
/* The full registration review is laid out like registration's review step. */
#pending-review-modal .pending-review-dialog{width:min(1080px,calc(100vw - 36px));max-width:1080px;max-height:92dvh;overflow-y:auto;padding:27px 32px;background:#fff;border-radius:17px;box-sizing:border-box}
#pending-review-modal .pending-review-title{font-size:21px;font-weight:650;margin:0 0 21px;line-height:1.4}
#pending-registration-full-details{display:grid;gap:18px;margin-bottom:20px}
#pending-registration-full-details .ar-section{border:1px solid #ece5e1;border-radius:12px;overflow:hidden;background:white}
#pending-registration-full-details .ar-section h3{margin:0;padding:15px 18px;color:#6e1821;font-size:14px;font-weight:600;background:#fcf8f6;border-bottom:1px solid #ece5e1}
#pending-registration-full-details .ar-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;padding:18px}
#pending-registration-full-details .ar-cell{min-width:0;display:flex;flex-direction:column;gap:6px}
#pending-registration-full-details .ar-cell small{display:block;font-size:10px;color:#89817f;letter-spacing:.15px;line-height:1.5}
#pending-registration-full-details .ar-cell strong{display:flex;align-items:center;min-height:39px;background:#fbfaf9;border:1px solid #e5e0dd;padding:9px 11px;border-radius:8px;color:#333033;font-weight:500;font-size:12px;line-height:1.5;overflow-wrap:anywhere;word-break:break-word}
#pending-registration-full-details .ar-docs{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:18px}
#pending-registration-full-details .ar-doc{border:1px dashed #e0d8d4;border-radius:9px;padding:15px;min-width:0;color:#8c8581;font-size:11px;line-height:1.7}
#pending-registration-full-details .ar-doc b{display:block;font-size:12px;color:#352f2d;margin-bottom:5px}
#pending-registration-full-details .ar-doc a{color:#7b1b1b;text-decoration:underline}
/* EIGHT-COLUMN LOGISTICS TABLE FIX.
   The original CSS assigns 46% / 27% / 27% to columns 1-3,
   leaving no room for newly added columns. Reset every column. */
#admin-content .logistics-table-scroll{width:100%;max-width:100%;overflow-x:auto;overflow-y:hidden;-webkit-overflow-scrolling:touch}
#admin-content .logistics-table{width:100%!important;min-width:1020px!important;table-layout:fixed!important;border-collapse:collapse}
#admin-content .logistics-table th:nth-child(1){width:24%!important}
#admin-content .logistics-table th:nth-child(2){width:12%!important}
#admin-content .logistics-table th:nth-child(3){width:12%!important}
#admin-content .logistics-table th:nth-child(4){width:10%!important}
#admin-content .logistics-table th:nth-child(5){width:10%!important}
#admin-content .logistics-table th:nth-child(6){width:20%!important}
#admin-content .logistics-table th:nth-child(7){width:12%!important}
#admin-content .logistics-table th,#admin-content .logistics-table td{padding:12px 10px!important;vertical-align:middle;box-sizing:border-box;overflow-wrap:anywhere;white-space:normal}
#admin-content .logistics-table thead th{font-size:11px!important;line-height:1.4}
#admin-content .logistics-table .logistics-company-name{overflow-wrap:anywhere;min-width:0}
#admin-content .logistics-table td:nth-child(6) small{display:block;line-height:1.6}
@media(max-width:1150px){#admin-content .logistics-table{min-width:1020px!important}}

.lm-change-badge{display:inline-flex;align-items:center;gap:5px;border:1px solid #e7cf99;background:#fff7e5;color:#8a601f;padding:5px 9px;border-radius:20px;margin-top:7px;font:600 10px Poppins,sans-serif;cursor:pointer}
.lm-change-badge:hover{background:#ffedc4}
.lm-change-strip[hidden]{display:none!important}.lm-change-strip{margin:12px 0 17px;padding:13px 15px;background:#fffbf3;border:1px solid #ecdbb6;border-radius:11px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;font-size:11px;color:#765b2d}
.lm-change-strip button{background:white;border:1px solid #e0c992;color:#73501a;padding:8px 11px;border-radius:8px;font:500 11px Poppins,sans-serif;cursor:pointer}
.lm-change-layer[hidden]{display:none!important}.lm-change-layer{position:fixed;inset:0;z-index:20000;background:#160c0daa;display:grid;place-items:center;padding:18px}
.lm-change-dialog{width:min(760px,100%);max-height:90dvh;overflow:auto;background:#fff;border-radius:17px;padding:25px;box-shadow:0 22px 70px #0003;font-family:Poppins,sans-serif}
.lm-change-dialog h2{margin:0 0 6px;font-size:19px;font-weight:600}.lm-change-dialog p{font-size:11px;color:#827973;line-height:1.7}
.lm-change-table{width:100%;border-collapse:collapse;margin-top:19px}.lm-change-table th,.lm-change-table td{padding:12px 10px;text-align:left;vertical-align:top;border-bottom:1px solid #eee8e5;font-size:11px;overflow-wrap:anywhere}.lm-change-table th{background:#fbf8f6;color:#766e69}
.lm-change-dialog-actions{margin-top:20px;display:flex;justify-content:flex-end;flex-wrap:wrap;gap:9px}.lm-change-dialog-actions button{border:1px solid #dcd1cb;border-radius:8px;background:#fff;color:#574b46;font:600 11px Poppins,sans-serif;padding:10px 16px;cursor:pointer}.lm-change-dialog-actions .approve{background:#277b49;color:#fff;border-color:#277b49}.lm-change-dialog-actions .reject{color:#a6373d;border-color:#ebc3c5}
@media(max-width:760px){#pending-review-modal .pending-review-dialog{padding:20px 16px}#pending-registration-full-details .ar-grid{grid-template-columns:repeat(2,minmax(0,1fr))}#pending-registration-full-details .ar-docs{grid-template-columns:1fr}}
@media(max-width:480px){#pending-registration-full-details .ar-grid{grid-template-columns:1fr}}
</style>
<div class="lm-change-layer" id="lmChangeLayer" hidden><section class="lm-change-dialog" role="dialog" aria-modal="true" aria-labelledby="lmChangeTitle"><h2 id="lmChangeTitle">Account Information Change Request</h2><p id="lmChangeDesc"></p><div id="lmChangeBody"></div><div class="lm-change-dialog-actions" id="lmChangeActions"></div></section></div>


<style id="lm-pending-profile-layout-v2">
/* Pending registration review: clean profile-like presentation; no boxed fields. */
#pending-review-modal .pending-review-dialog{width:min(1030px,calc(100vw - 28px));max-height:92dvh;overflow-y:auto;padding:29px 34px 24px;border-radius:19px;background:#fff}
#pending-review-modal .pending-review-title{font-size:21px;font-weight:650;color:#1f1b1b;margin:0 0 21px;padding:0 0 15px;border-bottom:1px solid #eae5e2}
#pending-registration-full-details{display:block!important;margin:0 0 16px}
#pending-registration-full-details .ar-section{display:block!important;background:transparent!important;border:0!important;border-radius:0!important;margin:0 0 23px!important;overflow:visible!important;box-shadow:none!important}
#pending-registration-full-details .ar-section h3{background:transparent!important;border:0!important;border-bottom:1px solid #ece5e2!important;color:#8a2928!important;padding:0 0 11px!important;margin:0 0 14px!important;font-size:14px!important;font-weight:650!important;letter-spacing:0!important}
#pending-registration-full-details .ar-grid{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:15px 33px!important;padding:0!important;margin:0!important}
#pending-registration-full-details .ar-cell{display:grid!important;grid-template-columns:minmax(125px,43%) minmax(0,1fr)!important;align-items:start!important;gap:10px!important;min-width:0!important;border:0!important;background:transparent!important;box-shadow:none!important;padding:0!important}
#pending-registration-full-details .ar-cell small{font-size:11px!important;font-weight:400!important;color:#96908f!important;line-height:1.65!important;margin:0!important;padding:0!important;text-transform:none!important;letter-spacing:0!important;overflow-wrap:anywhere}
#pending-registration-full-details .ar-cell strong{display:block!important;min-height:0!important;padding:0!important;margin:0!important;background:transparent!important;border:0!important;border-radius:0!important;box-shadow:none!important;color:#272424!important;font-size:11px!important;font-weight:550!important;line-height:1.7!important;white-space:normal!important;overflow-wrap:anywhere!important;word-break:normal!important}
/* Documents stay last, with subtle file tiles rather than textbox-like values. */
#pending-registration-full-details .ar-docs{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:12px!important;padding:0!important}
#pending-registration-full-details .ar-doc{border:1px solid #ece4e0!important;border-radius:10px!important;padding:13px 15px!important;background:#fff!important;min-width:0!important;line-height:1.6!important}
#pending-registration-full-details .ar-doc b{font-size:11px!important;color:#3b3432!important;margin-bottom:5px!important}
#pending-review-modal .pending-review-actions{border-top:1px solid #eee7e4;padding-top:17px;margin-top:13px}
@media(max-width:850px){#pending-registration-full-details .ar-grid{grid-template-columns:1fr!important}#pending-review-modal .pending-review-dialog{padding:22px 22px}}
@media(max-width:530px){#pending-registration-full-details .ar-cell{grid-template-columns:minmax(105px,42%) minmax(0,1fr)!important;gap:9px!important}#pending-registration-full-details .ar-docs{grid-template-columns:1fr!important}#pending-review-modal .pending-review-dialog{padding:19px 15px}}
</style>


<!-- Dedicated image/PDF preview shared by both registration and company details -->
<div id="lmDocumentPreviewLayer" class="lm-document-layer" hidden role="dialog" aria-modal="true" aria-labelledby="lmDocumentPreviewHeading">
  <section class="lm-document-dialog">
    <header class="lm-document-bar"><h2 id="lmDocumentPreviewHeading">Document Preview</h2><button type="button" data-close-lm-document aria-label="Close document preview">&times;</button></header>
    <div id="lmDocumentPreviewBody" class="lm-document-body"></div>
  </section>
</div>
<style>
.lm-document-layer[hidden]{display:none!important}.lm-document-layer{position:fixed;inset:0;z-index:99999;background:rgba(15,11,13,.72);display:flex;align-items:center;justify-content:center;padding:18px}
.lm-document-dialog{width:min(980px,100%);height:min(88vh,830px);background:#fff;border-radius:15px;display:flex;flex-direction:column;box-shadow:0 25px 70px #0005;overflow:hidden}
.lm-document-bar{height:65px;flex:0 0 65px;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #eee5e1}
.lm-document-bar h2{font-size:16px;font-weight:600;margin:0}.lm-document-bar button{border:0;border-radius:50%;width:35px;height:35px;background:#f6f1ef;font-size:25px;color:#6c5553;cursor:pointer}
.lm-document-body{flex:1;min-height:0;display:flex;align-items:center;justify-content:center;overflow:auto;padding:15px;background:#f6f4f3;color:#7a6f6c}
.lm-document-body img{display:block;max-width:100%;max-height:100%;object-fit:contain;border-radius:5px}.lm-document-body iframe{width:100%;height:100%;border:0;background:#fff}
.ar-view-document{border:1px solid #ead5cf;border-radius:7px;padding:7px 12px;background:#fff8f6;color:#7b1b1b;font:500 11px Poppins,sans-serif;cursor:pointer;text-align:left}
.ar-view-document:hover{background:#ffede7}.lm-document-preview-open{overflow:hidden}
</style>

@endsection

@push('scripts')

@vite('resources/js/admin/logistics-management.js')

@endpush
<style>
#pending-review-modal .pending-review-dialog{width:min(1060px,calc(100vw - 34px));max-height:92dvh;overflow-y:auto;border-radius:17px;background:#fff;padding:25px 29px}
#pending-review-modal .ar-section{border:1px solid #eee5e0;border-radius:12px;overflow:hidden;margin-bottom:16px}
#pending-review-modal .ar-section h3{font-size:14px;background:#fcf9f7;color:#651b1b;padding:14px 18px;border-bottom:1px solid #eee5e0;margin:0}
#pending-review-modal .ar-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px 22px;padding:18px 21px}
#pending-review-modal .ar-cell{display:flex;flex-direction:column;gap:5px;min-width:0}
#pending-review-modal .ar-cell small{color:#938a86;font-size:11px}
#pending-review-modal .ar-cell strong{font-size:12px;font-weight:550;background:none;border:0;padding:0;min-height:unset;color:#241d1b}
#lmCompanyRegistrationDetails{padding:18px 5px 6px}.ar-company-extra .ar-section{border:1px solid #eee6e2;border-radius:10px;overflow:hidden;margin:14px 0}.ar-company-extra .ar-section h3{padding:13px 16px;background:#fcf9f8;margin:0;color:#7b1b1b;font-size:13px}.ar-company-extra .ar-grid{padding:16px;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:17px}.ar-company-extra .ar-cell{display:grid;gap:5px}.ar-company-extra .ar-cell small{font-size:10px;color:#887d79}.ar-company-extra .ar-cell strong{font-size:12px;font-weight:500;overflow-wrap:anywhere}.ar-company-extra .ar-docs{padding:16px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:11px}.ar-company-extra .ar-doc{padding:12px;border:1px solid #ece4df;border-radius:8px;font-size:11px}.ar-company-extra .ar-doc b{display:block}.ar-company-extra .ar-doc a{text-decoration:underline;color:#7b1b1b}
.lm-facility-detail{padding:12px 15px;border:1px solid #eee5df;border-radius:10px;margin:10px 0}.lm-facility-detail summary{cursor:pointer;font-weight:600;font-size:12px;color:#641b22}.lm-facility-detail p{font-size:11px;line-height:1.8;color:#756a65}
@media(max-width:700px){.ar-company-extra .ar-grid{grid-template-columns:1fr 1fr}#pending-review-modal .ar-grid{grid-template-columns:1fr}}
</style>
<style>
/* Admin logistics: stand-alone sorting center modal */
.lm-center-overlay[hidden]{display:none!important}
.lm-center-overlay{position:fixed;inset:0;background:rgba(20,9,10,.62);z-index:100005;display:flex;align-items:center;justify-content:center;padding:20px;font-family:Poppins,sans-serif}
.lm-center-dialog{width:min(950px,100%);max-height:90dvh;display:flex;flex-direction:column;background:#fff;border-radius:17px;box-shadow:0 22px 75px #20070740;overflow:hidden}
.lm-center-head{display:flex;align-items:flex-start;justify-content:space-between;gap:15px;padding:19px 25px;border-bottom:1px solid #ece4e0}
.lm-center-head h2{font-size:19px;font-weight:650;margin:0;color:#2b2020}.lm-center-head p{margin:4px 0 0;font-size:11px;color:#978a85}
.lm-center-head button{width:34px;height:34px;flex:none;border:0;border-radius:100%;background:#f6efec;font-size:24px;cursor:pointer;color:#765b55}
.lm-center-body{min-height:0;overflow-y:auto;padding:14px 25px 24px}.lm-center-footer{display:flex;justify-content:flex-end;padding:13px 25px;border-top:1px solid #ece4e0}
.lm-center-footer button{border:0;border-radius:8px;background:#7b1b1b;color:#fff;padding:10px 21px;font-size:12px;cursor:pointer}
.lm-center-body .ar-section{border:1px solid #eee5e0;border-radius:10px;overflow:hidden;margin:12px 0}
.lm-center-body .ar-section h3{background:#fcf8f6;color:#7b1b1b;font-size:13px;padding:12px 16px;margin:0;font-weight:650}
.lm-center-body .ar-grid{padding:17px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:17px}
.lm-center-body .ar-cell{display:flex;flex-direction:column;gap:5px;min-width:0}.lm-center-body .ar-cell small{font-size:11px;color:#938780}.lm-center-body .ar-cell strong{font-size:12px;color:#342b29;font-weight:550;overflow-wrap:anywhere}
.lm-center-body .lm-facility-detail{margin:9px 14px}.lm-center-body .lm-facility-detail[open] summary{margin-bottom:11px}
.lm-center-body .lm-hub-info{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:7px 0}
.lm-center-body .lm-hub-info div{display:grid;gap:3px}.lm-center-body .lm-hub-info small{color:#988a84;font-size:10px}.lm-center-body .lm-hub-info strong{font-size:12px;font-weight:500;overflow-wrap:anywhere}
@media(max-width:650px){.lm-center-overlay{padding:10px}.lm-center-body{padding:10px 13px}.lm-center-body .ar-grid,.lm-center-body .lm-hub-info{grid-template-columns:1fr}.lm-center-head{padding:15px}}
</style>
