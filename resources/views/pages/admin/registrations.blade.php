
<style>
    /* =========================================================
       DASHBOARD TYPOGRAPHY / FORMAT
       Uses the same Poppins family, sizing, weights, and
       visual hierarchy as the Dashboard.
    ========================================================== */

    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

    #admin-content,
    #admin-content * {
        font-family: 'Poppins', sans-serif;
    }

    #admin-content {
        color: #17120F;
    }

    #admin-content h2 {
        color: #17120F;
        font-size: 21px;
        line-height: 1.2;
        font-weight: 600;
    }

    #admin-content h3 {
        color: #17120F;
        font-weight: 600;
    }

    /* Dashboard-style registration stat cards */
    #admin-content .registration-stat-card {
        min-height: 108px;
        background: #FFFFFF;
        border: 1px solid #F0E9E6;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(42, 20, 15, 0.05);
        padding: 16px;
        box-sizing: border-box;
    }

    #admin-content .registration-stat-icon {
        width: 40px;
        height: 40px;
        flex: 0 0 40px;
        object-fit: contain;
        display: block;
    }

    #admin-content .registration-stat-number {
        font-size: 23px;
        line-height: 1;
        font-weight: 600;
        color: #17120F;
    }

    #admin-content .registration-stat-label {
        margin-top: 5px;
        font-size: 13px;
        line-height: 1.15;
        font-weight: 400;
        color: #8C8784;
    }

    #admin-content .registration-card-link {
        font-size: 12px;
        line-height: 1.2;
        font-weight: 500;
        color: #7B1B1B;
    }

    /* Match Dashboard's common text hierarchy across this page. */
    #admin-content .text-\[23px\] { font-size: 23px; }
    #admin-content .text-\[21px\] { font-size: 21px; }
    #admin-content .text-\[20px\] { font-size: 20px; }
    #admin-content .text-\[19px\] { font-size: 19px; }
    #admin-content .text-\[18px\] { font-size: 18px; }
    #admin-content .text-\[17px\] { font-size: 17px; }
    #admin-content .text-\[15px\] { font-size: 15px; }
    #admin-content .text-\[14px\] { font-size: 14px; }
    #admin-content .text-\[13px\] { font-size: 13px; }
    #admin-content .text-\[12px\] { font-size: 12px; }
    #admin-content .text-\[11px\] { font-size: 11px; }
    #admin-content .text-\[10px\] { font-size: 10px; }

    #admin-content .font-semibold { font-weight: 600; }
    #admin-content .font-medium { font-weight: 500; }
    #admin-content .font-normal { font-weight: 400; }

    /* Keep very small utility/status text readable while matching Dashboard scale. */
    #admin-content .registration-table-text {
        font-size: 14px;
        line-height: 1.25;
        font-weight: 400;
        color: #17120F;
    }

    #admin-content .registration-muted-text {
        font-size: 12px;
        line-height: 1.25;
        font-weight: 400;
        color: #8C8784;
    }

    /* Dashboard-like buttons */
    #admin-content button,
    #admin-content input,
    #admin-content select,
    #admin-content textarea {
        font-family: 'Poppins', sans-serif;
    }

    /* Hide the visual scrollbar inside registration detail modals while keeping them scrollable. */
    .registration-detail-modal {
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .registration-detail-modal > .registration-detail-body {
        min-height: 0;
        overflow-y: auto;
        scrollbar-width: auto;
        scrollbar-color: #c7c7c7 #f7f7f7;
    }

    .registration-detail-modal > .registration-detail-body::-webkit-scrollbar {
        width: 9px;
    }

    .registration-detail-modal > .registration-detail-body::-webkit-scrollbar-track {
        background: #f7f7f7;
    }

    .registration-detail-modal > .registration-detail-body::-webkit-scrollbar-thumb {
        background: #c7c7c7;
        border-radius: 8px;
    }

    /* Make the entire Approved/Rejected modal content scroll as one unit. */
    .registration-archive-modal {
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .registration-archive-modal::-webkit-scrollbar {
        display: none;
        width: 0;
        height: 0;
    }

    /* =========================================================
       CATEGORY PILLS
    ========================================================== */

    .registration-category-pill {
        display: inline-flex;
        align-items: center;
        width: fit-content;
        min-height: 28px;
        padding: 5px 11px;
        border-radius: 9999px;
        font-size: 12px;
        line-height: 1.2;
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


    /* =========================================================
       REGISTRATIONS TABLE — FINAL 12PX CONTENT
       Keep all visible table row content at exactly 12px.
       Table headers and controls can keep their own sizing.
    ========================================================== */
    #admin-content #registrations-table tbody,
    #admin-content #registrations-table tbody tr,
    #admin-content #registrations-table tbody td,
    #admin-content #registrations-table tbody td span,
    #admin-content #registrations-table tbody td div {
        font-size: 12px !important;
    }

    #admin-content #registrations-table tbody td .text-\[10px\] {
        font-size: 12px !important;
    }

    /* Keep the archive tables consistent too. */
    #admin-content #approved-users-body,
    #admin-content #approved-users-body tr,
    #admin-content #approved-users-body td,
    #admin-content #approved-users-body td span,
    #admin-content #approved-users-body td div,
    #admin-content #rejected-users-body,
    #admin-content #rejected-users-body tr,
    #admin-content #rejected-users-body td,
    #admin-content #rejected-users-body td span,
    #admin-content #rejected-users-body td div {
        font-size: 12px !important;
    }

    /* Search bar — match the compact Seller Compliance width. */
    #admin-content .registration-search-wrap {
        position: relative;
        width: 330px;
        flex: 0 0 330px;
    }

    #admin-content #registration-search {
        width: 100%;
        height: 36px;
        box-sizing: border-box;
        border-radius: 8px;
        padding: 0 38px 0 12px;
        font-size: 13px;
    }

    @media (max-width: 1279px) {
        #admin-content .registration-search-wrap {
            width: 100%;
            flex: 1 1 100%;
        }
    }

</style>

@extends('layouts.admin')

@section('page-title', 'Account Registrations')

@section('content')

<div
    id="admin-content"
    class="ml-60 pt-[110px] pl-5 pb-7 min-h-screen transition-all duration-300"
>
    <!-- =========================================================
         REGISTRATION STAT CARDS
    ========================================================== -->

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">

        <!-- =====================================================
             PENDING
        ====================================================== -->

        <div
            class="registration-stat-card
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
                        src="{{ asset('icons/admin/registrations/Pending Registrations.png') }}"
                        class="registration-stat-icon"
                        alt="Pending Registrations"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p id="pending-count-card" class="registration-stat-number">
                        {{ $counts['pending'] }}
                    </p>

                    <p class="registration-stat-label whitespace-nowrap">
                        Pending Requests
                    </p>

                </div>

            </div>


            <!-- Bottom Text -->

        </div>


        <!-- =====================================================
             REJECTED USERS ARCHIVE
        ====================================================== -->

        <div>

            <button
                type="button"
                id="rejected-users-button"
                class="registration-stat-card w-full
                       relative text-left
                       flex flex-col justify-center
                       transition-all duration-300
                       hover:-translate-y-1 hover:shadow-md
                       hover:border-[#E9A3A3]
                       group"
            >

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-2.5">

                        <!-- Icon -->

                        <div
                            class="shrink-0 flex items-center justify-center"
                        >

                            <img
                                src="{{ asset('icons/admin/registrations/Rejected Registrations.png') }}"
                                class="registration-stat-icon"
                                alt="Rejected Registrations"
                            >

                        </div>


                        <!-- Content -->

                        <div class="min-w-0">

                            <p id="rejected-count-card" class="registration-stat-number">
                                {{ $counts['rejected'] }}
                            </p>

                            <p class="registration-stat-label whitespace-nowrap">
                                Rejected Users
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

                    <span class="registration-card-link">
                        View rejected users →
                    </span>

                </div>

            </button>

        </div>


        <!-- =====================================================
             APPROVED USERS ARCHIVE
        ====================================================== -->

        <div>

            <button
                type="button"
                id="approved-users-button"
                class="registration-stat-card w-full
                       relative text-left
                       flex flex-col justify-center
                       transition-all duration-300
                       hover:-translate-y-1 hover:shadow-md
                       hover:border-[#E9A3A3]
                       group"
            >

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-2.5">

                        <!-- Icon -->

                        <div
                            class="shrink-0 flex items-center justify-center"
                        >

                            <img
                                src="{{ asset('icons/admin/registrations/Approved Registrations.png') }}"
                                class="registration-stat-icon"
                                alt="Approved Registrations"
                            >

                        </div>


                        <!-- Content -->

                        <div class="min-w-0">

                            <p id="approved-count-card" class="registration-stat-number">
                                {{ $counts['approved'] }}
                            </p>

                            <p class="registration-stat-label whitespace-nowrap">
                                Approved Users
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

                    <span class="registration-card-link">
                        View approved users →
                    </span>

                </div>

            </button>

        </div>


        <!-- =====================================================
             TOTAL REGISTRATIONS
        ====================================================== -->

        <div
            class="registration-stat-card relative
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
                        src="{{ asset('icons/admin/registrations/Total registrations.png') }}"
                        class="registration-stat-icon"
                        alt="Total Registrations"
                    >

                </div>


                <!-- Content -->

                <div class="min-w-0">

                    <p id="total-count-card" class="registration-stat-number">
                        {{ $counts['total'] }}
                    </p>

                    <p class="registration-stat-label whitespace-nowrap">
                        Total Registration
                    </p>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         APPROVED USERS ARCHIVE MODAL
    ========================================================== -->

    <div
        id="approved-users-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center
               bg-black/30 backdrop-blur-sm px-5"
    >

        <div
            class="registration-archive-modal bg-white w-full max-w-5xl max-h-[88vh]
                   overflow-y-auto rounded-2xl shadow-xl
                   transform transition-all duration-300"
        >

            <!-- Modal Header -->

            <div
                class="flex items-center justify-between
                       px-5 py-4 border-b border-gray-200"
            >

                <div>

                    <h3 class="text-[20px] font-bold text-gray-900">
                        Approved Users
                    </h3>

                    <p class="text-[13px] text-gray-400 mt-1">
                        Archived approved registrations
                    </p>

                </div>


                <button
                    type="button"
                    id="close-approved-users"
                    class="w-8 h-8 flex items-center justify-center
                           rounded-full text-gray-500
                           hover:bg-gray-100 hover:text-gray-800
                           transition"
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
                            d="M6 6l12 12M18 6L6 18"
                        />

                    </svg>

                </button>

            </div>


            <!-- Approved Users Search -->
            <div class="px-6 py-4 border-b border-gray-100 bg-[#FFFBF9]">
                <div class="relative max-w-md">
                    <input
                        type="text"
                        id="approved-users-search"
                        placeholder="Search approved users..."
                        class="w-full h-[40px] rounded-lg border border-gray-300 bg-white pl-10 pr-4 text-[13px] text-gray-700 placeholder:text-gray-300 outline-none focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10 transition"
                    >
                    <svg
                        class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                    </svg>
                </div>
            </div>


            <!-- Approved Users Table -->

            <div class="overflow-x-auto max-h-none">

                <table class="w-full min-w-[800px]">

                    <thead class="sticky top-0 bg-white">

                        <tr class="border-b border-gray-200">

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Applicant
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                User Type
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Email
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Phone
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                <button
                                    type="button"
                                    class="archive-date-sort inline-flex items-center gap-1.5 hover:text-[#7B1B1B] transition"
                                    data-target="approved-users-body"
                                    aria-label="Sort approved users by date"
                                >
                                    <span>Date Approved</span>
                                    <span class="archive-sort-arrow inline-flex items-center justify-center w-6 h-6 text-[17px] leading-none font-bold text-[#7B1B1B] rounded-md bg-[#FFF0EC] border border-[#F3C2B5]">↓</span>
                                </button>
                            </th>

                        </tr>

                    </thead>


                    <tbody id="approved-users-body" class="divide-y divide-gray-200">
                    </tbody>

                </table>

            </div>


            <!-- Modal Footer -->

            <div
                class="px-6 py-4 border-t border-gray-200
                       flex items-center justify-between"
            >

                <p id="approved-users-count" class="text-[12px] text-gray-400">
                </p>

                <button
                    type="button"
                    id="close-approved-users-bottom"
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
         REJECTED USERS ARCHIVE MODAL
    ========================================================== -->

    <div
        id="rejected-users-modal"
        class="fixed inset-0 z-[100] hidden items-center justify-center
               bg-black/30 backdrop-blur-sm px-5"
    >

        <div
            class="registration-archive-modal bg-white w-full max-w-5xl max-h-[88vh]
                   overflow-y-auto rounded-2xl shadow-xl
                   transform transition-all duration-300"
        >

            <!-- Modal Header -->

            <div
                class="flex items-center justify-between
                       px-5 py-4 border-b border-gray-200"
            >

                <div>

                    <h3 class="text-[20px] font-bold text-gray-900">
                        Rejected Users
                    </h3>

                    <p class="text-[13px] text-gray-400 mt-1">
                        Archived rejected registrations
                    </p>

                </div>


                <button
                    type="button"
                    id="close-rejected-users"
                    class="w-8 h-8 flex items-center justify-center
                           rounded-full text-gray-500
                           hover:bg-gray-100 hover:text-gray-800
                           transition"
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
                            d="M6 6l12 12M18 6L6 18"
                        />

                    </svg>

                </button>

            </div>


            <!-- Rejected Users Search -->
            <div class="px-6 py-4 border-b border-gray-100 bg-[#FFFBF9]">
                <div class="relative max-w-md">
                    <input
                        type="text"
                        id="rejected-users-search"
                        placeholder="Search rejected users..."
                        class="w-full h-[40px] rounded-lg border border-gray-300 bg-white pl-10 pr-4 text-[13px] text-gray-700 placeholder:text-gray-300 outline-none focus:border-[#7B1B1B] focus:ring-2 focus:ring-[#7B1B1B]/10 transition"
                    >
                    <svg
                        class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                    </svg>
                </div>
            </div>


            <!-- Rejected Users Table -->

            <div class="overflow-x-auto max-h-none">

                <table class="w-full min-w-[800px]">

                    <thead class="sticky top-0 bg-white">

                        <tr class="border-b border-gray-200">

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Applicant
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                User Type
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Email
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                Phone
                            </th>

                            <th class="text-left px-6 py-4 text-[13px] font-medium text-gray-400">
                                <button
                                    type="button"
                                    class="archive-date-sort inline-flex items-center gap-1.5 hover:text-[#7B1B1B] transition"
                                    data-target="rejected-users-body"
                                    aria-label="Sort rejected users by date"
                                >
                                    <span>Date Rejected</span>
                                    <span class="archive-sort-arrow inline-flex items-center justify-center w-6 h-6 text-[17px] leading-none font-bold text-[#7B1B1B] rounded-md bg-[#FFF0EC] border border-[#F3C2B5]">↓</span>
                                </button>
                            </th>

                        </tr>

                    </thead>


                    <tbody id="rejected-users-body" class="divide-y divide-gray-200">
                    </tbody>

                </table>

            </div>


            <!-- Modal Footer -->

            <div
                class="px-6 py-4 border-t border-gray-200
                       flex items-center justify-between"
            >

                <p id="rejected-users-count" class="text-[12px] text-gray-400">
                </p>

                <button
                    type="button"
                    id="close-rejected-users-bottom"
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
         SELLER DETAILS MODAL
    ========================================================== -->

    <div
        id="seller-details-modal"
        class="fixed inset-0 z-[120] hidden items-center justify-center
               bg-black/35 backdrop-blur-[2px] px-5 py-6"
        aria-hidden="true"
    >

        <div
            class="relative registration-detail-modal bg-white w-full max-w-3xl max-h-[92vh]
                   overflow-hidden rounded-[24px] shadow-2xl
                   border border-white"
            role="dialog"
            aria-modal="true"
            aria-labelledby="seller-details-title"
        >

            <div class="shrink-0 px-7 pt-6 pb-3">

                <div class="flex items-center justify-between pb-4 border-b border-gray-200">

                    <h3
                        id="seller-details-title"
                        class="text-[23px] font-semibold text-gray-900"
                    >
                        Seller Details
                    </h3>

                    <button
                        type="button"
                        class="registration-modal-close w-9 h-9 flex items-center justify-center
                               rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition"
                        data-modal="seller-details-modal"
                        aria-label="Close seller details"
                    >
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>

                </div>

            </div>

            <div class="registration-detail-body flex-1 px-7 pb-5">

                <div class="flex items-center justify-center min-h-[220px] text-[13px] text-gray-400">
                    Loading registration details...
                </div>

            </div>

            <div class="shrink-0 px-7 py-5 border-t border-gray-200 flex items-center justify-end gap-4 bg-white">
                <button
                    type="button"
                    class="registration-reject px-9 py-2.5 rounded-lg border border-[#C92D32] bg-[#FFE6E6] text-[#A61B1B] text-[14px] font-semibold hover:bg-[#FFDADA] transition"
                >
                    Reject
                </button>
                <button
                    type="button"
                    class="registration-approve px-9 py-2.5 rounded-lg border border-[#4E9B46] bg-[#E7F4E3] text-[#28721B] text-[14px] font-semibold hover:bg-[#DCEFD7] transition"
                >
                    Approve
                </button>
            </div>

        </div>

    </div>


    <!-- =========================================================
         BUYER DETAILS MODAL
    ========================================================== -->

    <div
        id="buyer-details-modal"
        class="fixed inset-0 z-[120] hidden items-center justify-center
               bg-black/35 backdrop-blur-[2px] px-5 py-6"
        aria-hidden="true"
    >

        <div
            class="relative registration-detail-modal bg-white w-full max-w-3xl max-h-[92vh]
                   overflow-hidden rounded-[24px] shadow-2xl
                   border border-white"
            role="dialog"
            aria-modal="true"
            aria-labelledby="buyer-details-title"
        >

            <div class="shrink-0 px-7 pt-6 pb-3">
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 id="buyer-details-title" class="text-[23px] font-semibold text-gray-900">Buyer Details</h3>
                    <button
                        type="button"
                        class="registration-modal-close w-9 h-9 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition"
                        data-modal="buyer-details-modal"
                        aria-label="Close buyer details"
                    >
                        <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="registration-detail-body flex-1 px-7 pb-5">

                <div class="flex items-center justify-center min-h-[220px] text-[13px] text-gray-400">
                    Loading registration details...
                </div>

            </div>

            <div class="shrink-0 px-7 py-5 border-t border-gray-200 flex items-center justify-end gap-4 bg-white">
                <button type="button" class="registration-reject px-9 py-2.5 rounded-lg border border-[#C92D32] bg-[#FFE6E6] text-[#A61B1B] text-[14px] font-semibold hover:bg-[#FFDADA] transition">Reject</button>
                <button type="button" class="registration-approve px-9 py-2.5 rounded-lg border border-[#4E9B46] bg-[#E7F4E3] text-[#28721B] text-[14px] font-semibold hover:bg-[#DCEFD7] transition">Approve</button>
            </div>

        </div>

    </div>



    <!-- =========================================================
         REJECT REGISTRATION MODAL
    ========================================================== -->
    <div
        id="reject-registration-modal"
        class="fixed inset-0 z-[140] hidden items-center justify-center
               bg-black/35 backdrop-blur-[2px] px-5 py-6"
        aria-hidden="true"
    >
        <div
            class="relative bg-white w-full max-w-[610px] rounded-[28px] shadow-2xl
                   border border-white"
            role="dialog"
            aria-modal="true"
            aria-labelledby="reject-registration-title"
        >
            <div class="px-8 pt-7 pb-7">

                <h3
                    id="reject-registration-title"
                    class="text-[23px] font-medium text-gray-900"
                >
                    Reject Registration
                </h3>

                <p class="mt-2 text-[17px] leading-6 text-gray-400">
                    Please provide a reason for rejecting the registration.
                </p>

                <div class="mt-10">
                    <label class="block text-[17px] font-medium text-gray-900 mb-2">
                        Reason<span class="text-[#D62F2F]">*</span>
                    </label>

                    <div id="reject-reason-options" class="space-y-2">
                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Incomplete or missing required documents." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Incomplete or missing required documents.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Invalid or unverifiable information." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Invalid or unverifiable information.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Does not meet platform requirements." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Does not meet platform requirements.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Prohibited or restricted business/category." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Prohibited or restricted business/category.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg border border-gray-200 bg-[#FCFCFC] cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Duplicate account." class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Duplicate account.</span>
                        </label>

                        <label class="flex items-center gap-3 min-h-[40px] px-3 rounded-lg cursor-pointer hover:bg-[#FFF9F7] transition">
                            <input type="radio" name="reject_reason" value="Other (please specify)" class="w-[18px] h-[18px] accent-[#8F211F]">
                            <span class="text-[15px] text-gray-900">Other (please specify)</span>
                        </label>
                    </div>

                    <p
                        id="reject-reason-error"
                        class="hidden mt-2 text-[12px] text-[#B3262E]"
                    >
                        Please select a reason before rejecting the registration.
                    </p>
                </div>

                <div class="mt-5">
                    <label class="block text-[17px] font-medium text-gray-900 mb-2">
                        Additional Details <span class="font-normal">(Optional)</span>
                    </label>

                    <div class="relative">
                        <textarea
                            id="reject-additional-details"
                            maxlength="300"
                            rows="3"
                            placeholder="Write additional details here..."
                            class="w-full h-[59px] resize-none rounded-lg border border-gray-200 bg-white
                                   px-3 py-2.5 pr-12 text-[15px] text-gray-700
                                   placeholder:text-gray-400 outline-none
                                   focus:border-[#8F211F] focus:ring-2 focus:ring-[#8F211F]/10 transition"
                        ></textarea>

                        <span
                            id="reject-details-count"
                            class="absolute right-3 bottom-2 text-[11px] text-gray-500"
                        >
                            0/300
                        </span>
                    </div>
                </div>
            </div>

            <div class="px-8 pb-7 flex items-center justify-end gap-3">
                <button
                    type="button"
                    id="cancel-reject-registration"
                    class="px-6 py-2.5 rounded-lg border border-gray-300 bg-white
                           text-[#8F211F] text-[14px] font-semibold
                           hover:bg-gray-50 transition"
                >
                    Cancel
                </button>

                <button
                    type="button"
                    id="confirm-reject-registration"
                    class="px-7 py-2.5 rounded-lg border border-[#C92D32] bg-[#FFE6E6] text-[#A61B1B]
                           text-[14px] font-semibold hover:bg-[#FFDADA] transition"
                >
                    Reject
                </button>
            </div>
        </div>
    </div>

    <!-- =========================================================
         REGISTRATION FLASH MESSAGE
    ========================================================== -->
    <div
        id="registration-flash"
        class="registration-decision-flash"
        role="status"
        aria-live="polite"
        aria-atomic="true"
    >
        <div class="flex items-start gap-3">
            <div
                id="registration-flash-icon"
                class="w-9 h-9 rounded-full flex items-center justify-center shrink-0"
            ></div>

            <div class="min-w-0">
                <p id="registration-flash-title" class="text-[14px] font-semibold text-gray-900"></p>
                <p id="registration-flash-message" class="mt-1 text-[12px] leading-5 text-gray-500"></p>
            </div>

            <button
                type="button"
                id="registration-flash-close"
                class="ml-auto w-7 h-7 rounded-full flex items-center justify-center
                       text-gray-400 hover:bg-gray-100 transition shrink-0"
                aria-label="Close notification"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- =========================================================
         FILTER BAR
    ========================================================== -->

    <div
        class="bg-white rounded-xl shadow-sm border border-gray-100
               p-3.5 mb-4"
    >

        <div class="flex flex-col xl:flex-row items-stretch xl:items-center gap-2.5">

            <!-- Search -->

            <div class="registration-search-wrap">

                <input
                    id="registration-search"
                    type="text"
                    placeholder="Search name, email, and phone"
                    class="w-full h-[36px] rounded-lg border border-gray-300
                           bg-white pl-4 pr-11 text-[13px] text-gray-700
                           placeholder:text-gray-300
                           outline-none
                           focus:border-maroon-700
                           focus:ring-2 focus:ring-maroon-700/10
                           transition"
                >

                <svg
                    class="absolute right-3 top-1/2 -translate-y-1/2
                           w-5 h-5 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z"
                    />

                </svg>

            </div>


            <!-- User Type -->

            <div class="relative">

                <select
                    id="user-type-filter"
                    class="appearance-none w-full xl:w-[132px]
                           h-[36px] rounded-lg border border-gray-300
                           bg-white px-3 pr-9 text-[13px] text-gray-700
                           outline-none cursor-pointer
                           focus:border-maroon-700
                           focus:ring-2 focus:ring-maroon-700/10"
                >

                    <option value="all">
                        All User Types
                    </option>

                    <option value="seller">
                        Seller
                    </option>

                    <option value="buyer">
                        Buyer
                    </option>

                    <option value="logistics">
                        Logistics
                    </option>

                    <option value="rider">
                        Rider
                    </option>

                </select>

                <svg
                    class="pointer-events-none absolute right-3 top-1/2
                           -translate-y-1/2 w-4 h-4 text-gray-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >

                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="m6 9 6 6 6-6"
                    />

                </svg>

            </div>


            <!-- Date -->

            <div class="relative">

                <input
                    id="date-filter"
                    type="date"
                    class="w-full xl:w-[195px]
                           h-[36px] rounded-lg border border-gray-300
                           bg-white px-3 pr-10 text-[13px] text-gray-700
                           outline-none
                           focus:border-maroon-700
                           focus:ring-2 focus:ring-maroon-700/10
                           [&::-webkit-calendar-picker-indicator]:opacity-0
                           [&::-webkit-calendar-picker-indicator]:absolute
                           [&::-webkit-calendar-picker-indicator]:right-0
                           [&::-webkit-calendar-picker-indicator]:w-10
                           [&::-webkit-calendar-picker-indicator]:h-full
                           [&::-webkit-calendar-picker-indicator]:cursor-pointer"
                >

                <button
                    type="button"
                    id="date-calendar-button"
                    class="absolute right-0 top-0
                           w-9 h-[36px]
                           flex items-center justify-center
                           text-gray-700
                           hover:text-[#7B1B1B]
                           cursor-pointer"
                    aria-label="Open calendar"
                >

                    <svg
                        class="w-4 h-4"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                        />

                    </svg>

                </button>

            </div>


            <!-- Reload -->

            <button
                id="registration-reload"
                type="button"
                class="w-[40px] h-[38px] shrink-0
                       flex items-center justify-center
                       rounded-lg text-gray-900
                       hover:bg-[#FFF0EC]
                       active:scale-95
                       transition-all duration-200"
                title="Reload"
            >

                <svg
                    id="registration-reload-icon"
                    class="w-[18px] h-[18px]"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
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

    </div>


    <!-- =========================================================
         REGISTRATIONS TABLE
    ========================================================== -->

    <div
        class="bg-white rounded-xl shadow-sm border border-gray-100
               overflow-hidden"
    >

        <div class="overflow-x-auto">

            <table
                id="registrations-table"
                class="w-full min-w-[780px]"
            >

                <thead>

                    <tr class="border-b border-gray-200">

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Applicant
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            User Type
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Email
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Phone
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Date Registered
                        </th>

                        <th class="text-left px-4 py-3 text-[12px] font-medium text-gray-400">
                            Status
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse($registrations as $registration)
                        <tr
                            class="registration-row cursor-pointer hover:bg-[#FFF9F7] transition"
                            data-id="{{ $registration->id }}"
                            data-name="{{ $registration->full_name }}"
                            data-email="{{ $registration->email }}"
                            data-phone="{{ $registration->phone }}"
                            data-type="{{ $registration->user_type }}"
                            data-status="{{ $registration->status }}"
                            data-date="{{ $registration->created_at->toDateString() }}"
                            role="button"
                            tabindex="0"
                        >
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-[#F6D8D2] flex items-center justify-center text-[10px] font-semibold text-[#7B1B1B] shrink-0">
                                        {{ collect(explode(' ', $registration->full_name))->filter()->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->join('') }}
                                    </div>
                                    <span class="text-[12px] font-medium text-gray-800">{{ $registration->full_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5">
                                <div class="flex items-center gap-1.5">
                                    @if(strtolower($registration->user_type) === 'seller')
                                        <img src="{{ asset('icons/admin/dashboard/body/seller.png') }}" class="w-[18px] h-[18px] object-contain" alt="Seller">
                                    @elseif(strtolower($registration->user_type) === 'buyer')
                                        <img src="{{ asset('icons/admin/dashboard/body/buyer.png') }}" class="w-[18px] h-[18px] object-contain" alt="Buyer">
                                    @elseif(strtolower($registration->user_type) === 'logistics')
                                        <img src="{{ asset('icons/admin/dashboard/body/logistics.png') }}" class="w-[18px] h-[18px] object-contain" alt="Logistics">
                                    @elseif(strtolower($registration->user_type) === 'rider')
                                        <img src="{{ asset('icons/admin/dashboard/body/rider.png') }}" class="w-[18px] h-[18px] object-contain" alt="Rider">
                                    @endif
                                    <span class="text-[12px] text-gray-800">{{ ucfirst($registration->user_type) }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-2.5 text-[12px] text-gray-400">{{ $registration->email }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-gray-800">{{ $registration->phone }}</td>
                            <td class="px-4 py-2.5 text-[12px] text-gray-800">{{ $registration->created_at->format('F j, Y g:i A') }}</td>
                            <td class="px-4 py-2.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-[#FFE5D0] text-[#E87D22] text-[10px] font-medium border border-[#FFD1B8]">
                                    {{ ucfirst($registration->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-[13px] text-gray-400">
                                No registrations found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>


        <!-- =====================================================
             TABLE FOOTER / PAGINATION
        ====================================================== -->

        <div
            class="flex flex-col md:flex-row items-center justify-between
                   gap-3 px-4 py-3 border-t border-gray-200"
        >

            <p
                id="registration-count"
                class="text-[12px] text-gray-400"
            >
                Showing 0–0 of 0 entries
            </p>


            <div class="flex items-center gap-1.5">

                <button
                    type="button"
                    id="registration-prev"
                    class="w-6 h-6 flex items-center justify-center
                           text-gray-700 hover:bg-gray-100
                           rounded transition disabled:opacity-30 disabled:cursor-not-allowed"
                    aria-label="Previous page"
                >
                    ‹
                </button>

                <div id="registration-pages" class="flex items-center gap-2"></div>

                <button
                    type="button"
                    id="registration-next"
                    class="w-6 h-6 flex items-center justify-center
                           text-gray-700 hover:bg-gray-100
                           rounded transition disabled:opacity-30 disabled:cursor-not-allowed"
                    aria-label="Next page"
                >
                    ›
                </button>

                <select
                    id="items-per-page"
                    class="ml-2 h-7 rounded-md
                           border border-[#F0B9AC]
                           bg-[#FFF5F1]
                           px-2 text-[12px] text-gray-700
                           outline-none cursor-pointer"
                >
                    <option value="10" selected>Items per page: 10</option>
                    <option value="20">Items per page: 20</option>
                    <option value="50">Items per page: 50</option>
                </select>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     IMAGE PREVIEW MODAL
========================================================== -->

<div
    id="registration-image-preview-modal"
    class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/75 backdrop-blur-[2px] px-5 py-6"
    aria-hidden="true"
>
    <div
        class="relative w-full max-w-6xl max-h-[92vh] rounded-2xl bg-white shadow-2xl overflow-hidden"
        role="dialog"
        aria-modal="true"
        aria-labelledby="registration-image-preview-title"
    >
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 bg-white">
            <h3 id="registration-image-preview-title" class="text-[18px] font-semibold text-gray-900">Image Preview</h3>

            <button
                type="button"
                id="registration-image-preview-close"
                class="w-8 h-8 flex items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition"
                aria-label="Close image preview"
            >
                <svg class="w-[18px] h-[18px]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        <div class="bg-[#F5F1EF] p-5 flex items-center justify-center max-h-[calc(92vh-74px)] overflow-auto">
            <img
                id="registration-image-preview"
                src=""
                alt="Image preview"
                class="max-w-full max-h-[calc(92vh-120px)] w-auto h-auto object-contain rounded-lg bg-white shadow-sm"
            >
        </div>
    </div>
</div>



<style>
    /* =========================================================
       REGISTRATION ACTION FLASH
       Same quick lower-right style used across the admin modals.
    ========================================================== */
    .registration-decision-flash {
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
        transform: translateY(10px);
        transition: opacity .08s ease, transform .08s ease, visibility .08s ease;
    }

    .registration-decision-flash.show {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transform: translateY(0);
    }

    .registration-decision-flash.approved {
        border-color: #BFDDB8;
    }

    .registration-decision-flash.rejected,
    .registration-decision-flash.error {
        border-color: #E7A8AB;
    }

    @media (max-width: 640px) {
        .registration-decision-flash {
            right: 16px;
            bottom: 16px;
        }
    }
</style>


<style>
    /* =========================================================
       REGISTRATION FILTER COLOR CONSISTENCY
       Matches the Seller Compliance filter/search styling.
    ========================================================== */

    /* Search, user-type filter, and date field use the same
       neutral border/background/text colors as Seller Compliance. */
    #registration-search,
    #user-type-filter,
    #date-filter {
        border-color: #D9D6D4 !important;
        background-color: #FFFFFF !important;
        color: #76716E !important;
    }

    /* Match the Seller Compliance search placeholder tone. */
    #registration-search::placeholder {
        color: #76716E !important;
        opacity: 1;
    }

    /* Search icon + select arrow + calendar icon. */
    #registration-search + svg,
    #user-type-filter + svg,
    #date-calendar-button {
        color: #76716E !important;
    }

    /* Keep native date text consistent across Chromium browsers. */
    #date-filter::-webkit-datetime-edit,
    #date-filter::-webkit-datetime-edit-fields-wrapper,
    #date-filter::-webkit-datetime-edit-text,
    #date-filter::-webkit-datetime-edit-month-field,
    #date-filter::-webkit-datetime-edit-day-field,
    #date-filter::-webkit-datetime-edit-year-field {
        color: #76716E !important;
    }

    /* Same maroon active/focus indication used in Seller Compliance. */
    #registration-search:focus,
    #user-type-filter:focus,
    #date-filter:focus {
        border-color: #7B1B1B !important;
        box-shadow: 0 0 0 2px rgba(123, 27, 27, 0.10) !important;
    }
</style>

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /* =========================================================
       ELEMENTS
    ========================================================== */

    const searchInput =
        document.getElementById('registration-search');

    const userTypeFilter =
        document.getElementById('user-type-filter');

    const dateFilter =
        document.getElementById('date-filter');

    const rows =
        document.querySelectorAll('.registration-row');

    const count =
        document.getElementById('registration-count');

    const reloadButton =
        document.getElementById('registration-reload');

    const reloadIcon =
        document.getElementById('registration-reload-icon');

    const itemsPerPageSelect =
        document.getElementById('items-per-page');


    /* =========================================================
       PAGINATION + FILTERING + LIVE REGISTRATION ARCHIVE
    ========================================================== */

    let allRows = Array.from(document.querySelectorAll('.registration-row'));
    const serverCounts = @json($counts);

    // Remove legacy demo rows before any filtering or empty-state rendering.
    allRows = allRows.filter(row => row.dataset.id);
    document.querySelectorAll('.registration-row:not([data-id])').forEach(row => row.remove());

    const approvedUsersBody = document.getElementById('approved-users-body');
    const rejectedUsersBody = document.getElementById('rejected-users-body');
    const pendingCountCard = document.getElementById('pending-count-card');
    const approvedCountCard = document.getElementById('approved-count-card');
    const rejectedCountCard = document.getElementById('rejected-count-card');
    const totalCountCard = document.getElementById('total-count-card');
    const approvedUsersCount = document.getElementById('approved-users-count');
    const rejectedUsersCount = document.getElementById('rejected-users-count');
    const approvedUsersSearch = document.getElementById('approved-users-search');
    const rejectedUsersSearch = document.getElementById('rejected-users-search');

    // Archive rows are loaded from the database when their modal opens.
    if (approvedUsersBody) {
        approvedUsersBody.innerHTML = '';
    }

    if (rejectedUsersBody) {
        rejectedUsersBody.innerHTML = '';
    }

    const totalRegistrations = serverCounts.total;

    let approvedCount = serverCounts.approved;
    let rejectedCount = serverCounts.rejected;

    let currentPage = 1;
    let itemsPerPage = 10;
    let filteredRows = [...allRows];
    let activeRegistrationRow = null;

    function formatCurrentDate() {
        return new Date().toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function formatCategoryDisplay(category) {
        return String(category ?? '—')
            .replace(/Electronics\s+and\s+Gadgets/gi, 'Electronics & Gadgets')
            .replace(/Women\'s\s+Apparel/gi, "Women's Apparel")
            .replace(/Men\'s\s+Apparel/gi, "Men's Apparel")
            .replace(/Kids\s+and\s+Baby/gi, 'Kids & Baby')
            .replace(/Home\s+and\s+Garden/gi, 'Home & Garden')
            .replace(/Sports\s+and\s+Outdoors/gi, 'Sports & Outdoors')
            .replace(/Health\s+and\s+Beauty/gi, 'Health & Beauty')
            .replace(/Books\s+and\s+Media/gi, 'Books & Media')
            .replace(/Food\s+and\s+Gourmet/gi, 'Food & Gourmet')
            .replace(/Furniture\s+and\s+Office\s+Equipment/gi, 'Furniture & Office Equipment')
            .replace(/Office\s+and\s+School\s+Supplies/gi, 'Office & School Supplies');
    }

    function categoryBadgeClass(category) {
        const normalized = formatCategoryDisplay(category);
        const map = {
            'Pet Supplies': 'category-pet-supplies',
            'Electronics & Gadgets': 'category-electronics-and-gadgets',
            "Women's Apparel": 'category-womens-apparel',
            "Men's Apparel": 'category-mens-apparel',
            'Kids & Baby': 'category-kids-and-baby',
            'Home & Garden': 'category-home-and-garden',
            'Sports & Outdoors': 'category-sports-and-outdoors',
            'Health & Beauty': 'category-health-and-beauty',
            'Books & Media': 'category-books-and-media',
            'Food & Gourmet': 'category-food-and-gourmet',
            'Automotive & Motorcycle': 'category-automotive-motorcycle',
            'Furniture & Office Equipment': 'category-furniture-and-office-equipment',
            'Jewelry & Watches': 'category-jewelry-and-watches',
            'Office & School Supplies': 'category-office-and-school-supplies'
        };
        return map[normalized] || 'category-default';
    }

    function renderCategoryPills(categories) {
        let values = Array.isArray(categories) ? categories : [categories];
        values = values.filter(value => String(value ?? '').trim() !== '');

        if (!values.length) {
            return '<span class="registration-category-pill category-default">—</span>';
        }

        return values.map(value => {
            const label = formatCategoryDisplay(value);
            return `<span class="registration-category-pill ${categoryBadgeClass(label)}">${escapeHtml(label)}</span>`;
        }).join(' ');
    }

    function initials(name) {
        return (name || '?')
            .split(' ')
            .filter(Boolean)
            .slice(0, 2)
            .map(part => part.charAt(0).toUpperCase())
            .join('');
    }

    function createArchiveRow(row, action) {
        const name = row.dataset.name || 'Unknown User';
        const email = row.dataset.email || '—';
        const phone = row.dataset.phone || '—';
        const type = row.dataset.type === 'seller' ? 'Seller' : 'Buyer';
        const dateLabel = action === 'approved' ? 'Date Approved' : 'Date Rejected';
        const icon = type === 'Seller'
            ? "{{ asset('icons/admin/dashboard/body/seller.png') }}"
            : "{{ asset('icons/admin/dashboard/body/buyer.png') }}";

        const tr = document.createElement('tr');
        tr.className = 'hover:bg-[#FFF9F7] transition';
        tr.dataset.sourceName = name;
        tr.dataset.action = action;
        tr.dataset.archiveTimestamp = String(Date.now());

        tr.innerHTML = `
            <td class="px-6 py-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-full bg-[#F6D8D2] flex items-center justify-center text-[10px] font-semibold text-[#7B1B1B] shrink-0">
                        ${initials(name)}
                    </div>
                    <span class="text-[12px] font-medium text-gray-800">${name}</span>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="flex items-center gap-1.5">
                    <img src="${icon}" class="w-[18px] h-[18px] object-contain" alt="${type}">
                    <span class="text-[13px]">${type}</span>
                </div>
            </td>
            <td class="px-6 py-4 text-[13px] text-gray-400">${email}</td>
            <td class="px-6 py-4 text-[13px]">${phone}</td>
            <td class="px-6 py-4 text-[13px]">${formatCurrentDate()}</td>
        `;

        return tr;
    }

    function updateRegistrationStats() {
        if (pendingCountCard) {
            pendingCountCard.textContent = allRows.length;
        }

        if (approvedCountCard) {
            approvedCountCard.textContent = approvedCount;
        }

        if (rejectedCountCard) {
            rejectedCountCard.textContent = rejectedCount;
        }

        if (totalCountCard) {
            totalCountCard.textContent = totalRegistrations;
        }

        if (approvedUsersCount) {
            approvedUsersCount.textContent = `${approvedCount} approved users`;
        }

        if (rejectedUsersCount) {
            rejectedUsersCount.textContent = `${rejectedCount} rejected users`;
        }
    }

    function filterArchiveUsers(tbody, searchInput) {
        if (!tbody || !searchInput) {
            return;
        }

        const query = searchInput.value.toLowerCase().trim();
        const archiveRows = Array.from(tbody.querySelectorAll('tr'));

        archiveRows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const matches = query === '' || rowText.includes(query);
            row.classList.toggle('hidden', !matches);
        });
    }

    function resetArchiveSearch(searchInput, tbody) {
        if (searchInput) {
            searchInput.value = '';
        }

        if (tbody) {
            tbody.querySelectorAll('tr').forEach(row => row.classList.remove('hidden'));
        }
    }

    async function loadArchiveUsers(endpoint, tbody, action) {
        if (!tbody) {
            return;
        }

        const response = await fetch(endpoint, {
            headers: { 'Accept': 'application/json' }
        });

        if (!response.ok) {
            throw new Error('Unable to load archived registrations.');
        }

        const result = await response.json();
        const iconByType = {
            seller: "{{ asset('icons/admin/dashboard/body/seller.png') }}",
            buyer: "{{ asset('icons/admin/dashboard/body/buyer.png') }}",
            rider: "{{ asset('icons/admin/dashboard/body/rider.png') }}",
            logistics: "{{ asset('icons/admin/dashboard/body/logistics.png') }}"
        };

        tbody.innerHTML = result.data.map(registration => {
            const name = registration.full_name || `${registration.first_name} ${registration.last_name}`;
            const initials = name.split(' ').filter(Boolean).slice(0, 2).map(part => part[0].toUpperCase()).join('');
            const type = registration.user_type || '';
            const reviewedDate = registration.reviewed_at
                ? new Date(registration.reviewed_at).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })
                : '—';

            return `<tr class="hover:bg-[#FFF9F7] transition">
                <td class="px-6 py-4"><div class="flex items-center gap-2.5"><div class="w-7 h-7 rounded-full bg-[#F6D8D2] flex items-center justify-center text-[10px] font-semibold text-[#7B1B1B] shrink-0">${escapeHtml(initials)}</div><span class="text-[12px] font-medium text-gray-800">${escapeHtml(name)}</span></div></td>
                <td class="px-6 py-4"><div class="flex items-center gap-1.5"><img src="${iconByType[type] || iconByType.buyer}" class="w-[18px] h-[18px] object-contain" alt="${escapeHtml(type)}"><span class="text-[13px]">${escapeHtml(type.charAt(0).toUpperCase() + type.slice(1))}</span></div></td>
                <td class="px-6 py-4 text-[13px] text-gray-400">${escapeHtml(registration.email)}</td>
                <td class="px-6 py-4 text-[13px]">${escapeHtml(registration.phone)}</td>
                <td class="px-6 py-4 text-[13px]">${reviewedDate}</td>
            </tr>`;
        }).join('') || `<tr><td colspan="5" class="px-6 py-10 text-center text-sm text-gray-400">No ${action} users found.</td></tr>`;

        if (action === 'approved') {
            approvedCount = result.count;
        } else {
            rejectedCount = result.count;
        }
        updateRegistrationStats();
    }

    /* =========================================================
       APPROVED / REJECTED DATE SORTING
    ========================================================== */

    const archiveSortState = {
        'approved-users-body': 'desc',
        'rejected-users-body': 'desc'
    };

    function getArchiveRowDate(row) {
        const dateCell = row.cells[4];

        if (!dateCell) {
            return 0;
        }

        const timestamp = Date.parse(dateCell.textContent.trim());

        return Number.isNaN(timestamp) ? 0 : timestamp;
    }

    function sortArchiveRows(tbodyId, direction) {
        const tbody = document.getElementById(tbodyId);

        if (!tbody) {
            return;
        }

        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const dateA = getArchiveRowDate(a);
            const dateB = getArchiveRowDate(b);

            return direction === 'asc'
                ? dateA - dateB
                : dateB - dateA;
        });

        rows.forEach(row => tbody.appendChild(row));
    }

    function updateArchiveSortArrow(button, direction) {
        const arrow = button.querySelector('.archive-sort-arrow');

        if (arrow) {
            arrow.textContent = direction === 'asc' ? '↑' : '↓';
        }
    }

    document.querySelectorAll('.archive-date-sort').forEach(button => {
        const targetId = button.dataset.target;

        // Default: newest date first.
        sortArchiveRows(targetId, archiveSortState[targetId]);

        button.addEventListener('click', function () {
            const currentDirection = archiveSortState[targetId] || 'desc';
            const nextDirection = currentDirection === 'desc' ? 'asc' : 'desc';

            archiveSortState[targetId] = nextDirection;
            sortArchiveRows(targetId, nextDirection);
            updateArchiveSortArrow(this, nextDirection);
        });
    });

    if (approvedUsersSearch) {
        approvedUsersSearch.addEventListener('input', function () {
            filterArchiveUsers(approvedUsersBody, approvedUsersSearch);
        });
    }

    if (rejectedUsersSearch) {
        rejectedUsersSearch.addEventListener('input', function () {
            filterArchiveUsers(rejectedUsersBody, rejectedUsersSearch);
        });
    }

    function getFilters() {
        return {
            search: searchInput ? searchInput.value.toLowerCase().trim() : '',
            userType: userTypeFilter ? userTypeFilter.value : 'all',
            date: dateFilter ? dateFilter.value : ''
        };
    }

    function matchesFilters(row, filters) {
        const name = (row.dataset.name || '').toLowerCase();
        const email = (row.dataset.email || '').toLowerCase();
        const phone = (row.dataset.phone || '').toLowerCase();
        const type = row.dataset.type || '';
        const rowDate = row.dataset.date || '';

        const matchesSearch =
            filters.search === '' ||
            name.includes(filters.search) ||
            email.includes(filters.search) ||
            phone.includes(filters.search);

        const matchesType =
            filters.userType === 'all' ||
            type === filters.userType;

        const matchesDate =
            filters.date === '' ||
            rowDate === filters.date;

        return matchesSearch && matchesType && matchesDate;
    }

    function renderPagination() {
        const pagesContainer = document.getElementById('registration-pages');
        const prevButton = document.getElementById('registration-prev');
        const nextButton = document.getElementById('registration-next');

        const totalPages = Math.max(1, Math.ceil(filteredRows.length / itemsPerPage));

        currentPage = Math.min(currentPage, totalPages);
        currentPage = Math.max(currentPage, 1);

        if (pagesContainer) {
            pagesContainer.innerHTML = '';

            for (let page = 1; page <= totalPages; page++) {
                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = page;
                button.dataset.page = page;
                button.className =
                    'registration-page w-7 h-7 flex items-center justify-center rounded-md text-[12px] transition ' +
                    (page === currentPage
                        ? 'bg-[#FFD1C2] text-[#7B1B1B] font-medium'
                        : 'hover:bg-gray-100 text-gray-700');

                button.addEventListener('click', function () {
                    currentPage = Number(this.dataset.page);
                    renderTable();
                });

                pagesContainer.appendChild(button);
            }
        }

        if (prevButton) {
            prevButton.disabled = currentPage <= 1;
            prevButton.classList.toggle('opacity-40', currentPage <= 1);
            prevButton.classList.toggle('cursor-not-allowed', currentPage <= 1);
        }

        if (nextButton) {
            nextButton.disabled = currentPage >= totalPages;
            nextButton.classList.toggle('opacity-40', currentPage >= totalPages);
            nextButton.classList.toggle('cursor-not-allowed', currentPage >= totalPages);
        }
    }

    function renderTable() {
        allRows.forEach(row => row.classList.add('hidden'));

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const pageRows = filteredRows.slice(startIndex, endIndex);

        pageRows.forEach(row => row.classList.remove('hidden'));

        const visibleStart = filteredRows.length === 0 ? 0 : startIndex + 1;
        const visibleEnd = Math.min(endIndex, filteredRows.length);

        if (count) {
            count.textContent =
                `Showing ${visibleStart}–${visibleEnd} of ${filteredRows.length} entries`;
        }

        renderPagination();
        updateRegistrationStats();
    }

    function filterRegistrations(resetPage = true) {
        const filters = getFilters();
        filteredRows = allRows.filter(row => matchesFilters(row, filters));

        if (resetPage) {
            currentPage = 1;
        }

        renderTable();
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => filterRegistrations(true));
    }

    if (userTypeFilter) {
        userTypeFilter.addEventListener('change', () => filterRegistrations(true));
    }

    if (dateFilter) {
        dateFilter.addEventListener('change', () => filterRegistrations(true));
    }

    const dateCalendarButton = document.getElementById('date-calendar-button');

    if (dateCalendarButton && dateFilter) {
        dateCalendarButton.addEventListener('click', function () {
            if (typeof dateFilter.showPicker === 'function') {
                dateFilter.showPicker();
            } else {
                dateFilter.focus();
                dateFilter.click();
            }
        });
    }

    const prevButton = document.getElementById('registration-prev');
    const nextButton = document.getElementById('registration-next');

    if (prevButton) {
        prevButton.addEventListener('click', function () {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function () {
            const totalPages = Math.max(1, Math.ceil(filteredRows.length / itemsPerPage));

            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
    }

    if (itemsPerPageSelect) {
        itemsPerPageSelect.addEventListener('change', function () {
            itemsPerPage = Number(this.value) || 10;
            currentPage = 1;
            renderTable();
        });
    }

    /* =========================================================
       RELOAD UX
    ========================================================== */

    if (reloadButton) {
        reloadButton.addEventListener('click', function () {
            if (reloadIcon) {
                reloadIcon.classList.add('animate-spin');
            }

            reloadButton.disabled = true;

            setTimeout(() => {
                if (searchInput) searchInput.value = '';
                if (userTypeFilter) userTypeFilter.value = 'all';
                if (dateFilter) dateFilter.value = '';
                if (itemsPerPageSelect) itemsPerPageSelect.value = '10';

                itemsPerPage = 10;
                currentPage = 1;
                filterRegistrations(true);

                if (reloadIcon) {
                    reloadIcon.classList.remove('animate-spin');
                }

                reloadButton.disabled = false;
            }, 500);
        });
    }

    /* =========================================================
       REGISTRATION DETAILS MODALS
    ========================================================== */

    const sellerDetailsModal = document.getElementById('seller-details-modal');
    const buyerDetailsModal = document.getElementById('buyer-details-modal');

    const registrationImagePreviewModal =
        document.getElementById('registration-image-preview-modal');

    const registrationImagePreview =
        document.getElementById('registration-image-preview');

    const registrationImagePreviewTitle =
        document.getElementById('registration-image-preview-title');

    const registrationImagePreviewClose =
        document.getElementById('registration-image-preview-close');

    function openRegistrationModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

    }


    function openRegistrationImagePreview(imageUrl, imageTitle) {
        if (!registrationImagePreviewModal || !registrationImagePreview) {
            return;
        }

        registrationImagePreview.src = imageUrl || '';
        registrationImagePreview.alt = imageTitle || 'Image preview';

        if (registrationImagePreviewTitle) {
            registrationImagePreviewTitle.textContent =
                imageTitle || 'Image Preview';
        }

        registrationImagePreviewModal.classList.remove('hidden');
        registrationImagePreviewModal.classList.add('flex');
        registrationImagePreviewModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }


    function closeRegistrationImagePreview() {
        if (!registrationImagePreviewModal) {
            return;
        }

        registrationImagePreviewModal.classList.add('hidden');
        registrationImagePreviewModal.classList.remove('flex');
        registrationImagePreviewModal.setAttribute('aria-hidden', 'true');

        if (registrationImagePreview) {
            registrationImagePreview.removeAttribute('src');
        }

        const anyRegistrationModalOpen =
            (sellerDetailsModal && !sellerDetailsModal.classList.contains('hidden')) ||
            (buyerDetailsModal && !buyerDetailsModal.classList.contains('hidden'));

        if (!anyRegistrationModalOpen) {
            document.body.classList.remove('overflow-hidden');
        }
    }


    function closeRegistrationModal(modal) {

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.classList.remove('flex');
        modal.setAttribute('aria-hidden', 'true');

        const anyRegistrationModalOpen =
            (sellerDetailsModal && !sellerDetailsModal.classList.contains('hidden')) ||
            (buyerDetailsModal && !buyerDetailsModal.classList.contains('hidden'));

        if (!anyRegistrationModalOpen) {
            document.body.classList.remove('overflow-hidden');
        }

    }

    function escapeHtml(value) {
        return String(value ?? '—').replace(/[&<>'"]/g, character => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            "'": '&#039;',
            '"': '&quot;'
        }[character]));
    }


    function categoryBadgeClass(category) {
        const normalizedCategory = String(category ?? '')
            .replace(/\band\b/gi, '&')
            .replace(/\s{2,}/g, ' ')
            .trim();

        const map = {
            'Pet Supplies': 'category-pet-supplies',
            'Electronics & Gadgets': 'category-electronics-and-gadgets',
            "Women's Apparel": 'category-womens-apparel',
            "Women’s Apparel": 'category-womens-apparel',
            "Men's Apparel": 'category-mens-apparel',
            "Men’s Apparel": 'category-mens-apparel',
            'Kids & Baby': 'category-kids-and-baby',
            'Home & Garden': 'category-home-and-garden',
            'Sports & Outdoors': 'category-sports-and-outdoors',
            'Health & Beauty': 'category-health-and-beauty',
            'Books & Media': 'category-books-and-media',
            'Food & Gourmet': 'category-food-and-gourmet',
            'Automotive & Motorcycle': 'category-automotive-motorcycle',
            'Furniture & Office Equipment': 'category-furniture-and-office-equipment',
            'Jewelry & Watches': 'category-jewelry-and-watches',
            'Office & School Supplies': 'category-office-and-school-supplies'
        };

        return map[normalizedCategory] || 'category-default';
    }


    function renderCategoryPills(categoryValue) {
        let categories = categoryValue;

        if (typeof categories === 'string') {
            const trimmed = categories.trim();

            if (!trimmed) {
                categories = [];
            } else if (trimmed.startsWith('[')) {
                try {
                    const decoded = JSON.parse(trimmed);
                    categories = Array.isArray(decoded) ? decoded : [trimmed];
                } catch (error) {
                    categories = [trimmed];
                }
            } else {
                categories = trimmed.split(',').map(item => item.trim()).filter(Boolean);
            }
        }

        if (!Array.isArray(categories)) {
            categories = categories ? [String(categories)] : [];
        }

        if (!categories.length) {
            return '<span class="text-[15px] text-gray-400">—</span>';
        }

        return `<div class="flex flex-wrap items-center gap-2">${categories.map(category => {
            const safeCategory = String(category).trim();
            const displayCategory = safeCategory.replace(/\band\b/gi, '&');
            return `<span class="registration-category-pill ${categoryBadgeClass(safeCategory)}">${escapeHtml(displayCategory)}</span>`;
        }).join('')}</div>`;
    }

    function renderRegistrationDetails(modal, registration) {
        if (!modal) {
            return;
        }

        const body = modal.querySelector('.registration-detail-modal > .px-7.pb-5');
        const birthday = registration.birthdate
            ? new Date(`${registration.birthdate}T00:00:00`).toLocaleDateString('en-US', {
                year: 'numeric', month: 'long', day: 'numeric'
            })
            : '—';
        const age = registration.birthdate
            ? Math.floor((Date.now() - new Date(registration.birthdate).getTime()) / 31557600000)
            : '—';
        const validId = registration.valid_id_url
            ? `<button type="button" class="registration-image-trigger group block w-full text-left" data-image-url="${escapeHtml(registration.valid_id_url)}" data-image-title="Uploaded Valid ID" aria-label="View uploaded valid ID">
                    <div class="w-full h-[360px] rounded-xl border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center transition group-hover:border-[#A52A2A] group-hover:shadow-md">
                        <img src="${escapeHtml(registration.valid_id_url)}" alt="Uploaded valid ID" class="w-full h-full object-contain bg-white">
                    </div>
                    <p class="mt-2 text-[12px] text-gray-400">Click the image to preview</p>
                </button>`
            : '<div class="h-40 flex items-center justify-center rounded-lg border border-dashed border-gray-300 text-sm text-gray-400">No valid ID uploaded</div>';
        const businessPermit = registration.business_permit_url
            ? `<button type="button" class="registration-image-trigger group block w-full text-left" data-image-url="${escapeHtml(registration.business_permit_url)}" data-image-title="Business Permit" aria-label="View business permit">
                    <div class="w-full h-[360px] rounded-xl border border-gray-200 bg-gray-50 overflow-hidden flex items-center justify-center transition group-hover:border-[#A52A2A] group-hover:shadow-md">
                        <img src="${escapeHtml(registration.business_permit_url)}" alt="Business Permit" class="w-full h-full object-contain bg-white">
                    </div>
                    <p class="mt-2 text-[12px] text-gray-400">Click the image to preview</p>
                </button>`
            : '<span class="text-gray-400">Business permit not provided</span>';

        if (body) {
            body.innerHTML = `
                <section>
                    <h4 class="text-[20px] font-semibold text-[#A52A2A] mb-6">Personal Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-[1fr_300px] gap-8">
                        <div class="space-y-4 text-[15px]">
                            <div><span class="text-gray-400">Last Name</span><p class="font-medium">${escapeHtml(registration.last_name)}</p></div>
                            <div><span class="text-gray-400">First Name</span><p class="font-medium">${escapeHtml(registration.first_name)}</p></div>
                            <div><span class="text-gray-400">Middle Name</span><p class="font-medium">${escapeHtml(registration.middle_name)}</p></div>
                            <div><span class="text-gray-400">Sex</span><p class="font-medium">${escapeHtml(registration.sex)}</p></div>
                            <div><span class="text-gray-400">Birthday</span><p class="font-medium">${birthday}</p></div>
                            <div><span class="text-gray-400">Age</span><p class="font-medium">${age}</p></div>
                            <div><span class="text-gray-400">Email</span><p class="font-medium break-all">${escapeHtml(registration.email)}</p></div>
                            <div><span class="text-gray-400">Contact No.</span><p class="font-medium">${escapeHtml(registration.phone)}</p></div>
                        </div>
                        <div><p class="text-[15px] text-gray-400 mb-2">Uploaded Valid ID</p>${validId}</div>
                    </div>
                </section>
                <div class="border-t border-gray-200 my-7"></div>
                <section>
                    <h4 class="text-[20px] font-semibold text-[#A52A2A] mb-6">Address</h4>
                    <div class="space-y-4 text-[15px]">
                        <div><span class="text-gray-400">Province</span><p class="font-medium">${escapeHtml(registration.province)}</p></div>
                        <div><span class="text-gray-400">Municipality</span><p class="font-medium">${escapeHtml(registration.municipality)}</p></div>
                        <div><span class="text-gray-400">Barangay</span><p class="font-medium">${escapeHtml(registration.barangay)}</p></div>
                        <div><span class="text-gray-400">Street</span><p class="font-medium">${escapeHtml(registration.street)}</p></div>
                        <div><span class="text-gray-400">House No.</span><p class="font-medium">${escapeHtml(registration.house_no)}</p></div>
                        <div><span class="text-gray-400">Zip Code</span><p class="font-medium">${escapeHtml(registration.zip_code)}</p></div>
                    </div>
                </section>
                ${registration.user_type === 'seller' ? `
                    <div class="border-t border-gray-200 my-7"></div>
                    <section>
                        <h4 class="text-[20px] font-semibold text-[#A52A2A] mb-6">Business Information</h4>
                        <div class="space-y-4 text-[15px]">
                            <div><span class="text-gray-400">Business Name</span><p class="font-medium">${escapeHtml(registration.business_name)}</p></div>
                            <div><span class="text-gray-400">Category</span><div class="mt-1">${renderCategoryPills(registration.business_categories ?? registration.business_category)}</div></div>
                            <div><span class="text-gray-400">Business Permit</span><div class="mt-1">${businessPermit}</div></div>
                        </div>
                    </section>` : ''}
            `;
        }

        const title = modal.querySelector('h3');
        if (title) {
            title.textContent = `${registration.user_type === 'seller' ? 'Seller' : 'Buyer'} Details`;
        }
    }

    async function loadRegistrationDetails(row, modal) {
        if (!row.dataset.id) {
            return false;
        }

        try {
            const response = await fetch(`/admin/registrations/${row.dataset.id}`, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) {
                throw new Error('Unable to load registration details.');
            }

            renderRegistrationDetails(modal, await response.json());
            return true;
        } catch (error) {
            console.error(error);
            return false;
        }
    }


    document.addEventListener('click', function (event) {
        const imageTrigger = event.target.closest('.registration-image-trigger');

        if (!imageTrigger) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        openRegistrationImagePreview(
            imageTrigger.dataset.imageUrl,
            imageTrigger.dataset.imageTitle
        );
    });


    if (registrationImagePreviewClose) {
        registrationImagePreviewClose.addEventListener('click', function () {
            closeRegistrationImagePreview();
        });
    }


    if (registrationImagePreviewModal) {
        registrationImagePreviewModal.addEventListener('click', function (event) {
            if (event.target === registrationImagePreviewModal) {
                closeRegistrationImagePreview();
            }
        });
    }


    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeRegistrationImagePreview();
        }
    });


    document.querySelectorAll('.registration-row').forEach(row => {

        row.addEventListener('click', async function () {

            activeRegistrationRow = this;
            const type = (this.dataset.type || '').toLowerCase();

            if (type === 'seller') {
                if (await loadRegistrationDetails(this, sellerDetailsModal)) {
                    openRegistrationModal(sellerDetailsModal);
                }
                return;
            }

            if (type === 'buyer') {
                if (await loadRegistrationDetails(this, buyerDetailsModal)) {
                    openRegistrationModal(buyerDetailsModal);
                }
            }

        });

        row.setAttribute('role', 'button');
        row.setAttribute('tabindex', '0');

        row.addEventListener('keydown', function (event) {

            if (event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();
            this.click();

        });

    });


    document.querySelectorAll('.registration-modal-close').forEach(button => {

        button.addEventListener('click', function () {

            const modalId = this.dataset.modal;
            closeRegistrationModal(document.getElementById(modalId));

        });

    });


    [sellerDetailsModal, buyerDetailsModal].forEach(modal => {

        if (!modal) {
            return;
        }

        modal.addEventListener('click', function (event) {

            if (event.target === modal) {
                closeRegistrationModal(modal);
            }

        });

    });


    function moveRegistrationToArchive(row, action) {
        if (!row || !row.parentNode) {
            return;
        }

        const archiveBody = action === 'approved'
            ? approvedUsersBody
            : rejectedUsersBody;

        if (!archiveBody) {
            return;
        }

        const archiveRow = createArchiveRow(row, action);
        archiveBody.insertBefore(archiveRow, archiveBody.firstChild);

        if (action === 'approved') {
            sortArchiveRows('approved-users-body', archiveSortState['approved-users-body']);
            if (approvedUsersSearch) {
                filterArchiveUsers(approvedUsersBody, approvedUsersSearch);
            }
            approvedCount++;
        } else {
            sortArchiveRows('rejected-users-body', archiveSortState['rejected-users-body']);
            rejectedCount++;
            if (rejectedUsersSearch) {
                filterArchiveUsers(rejectedUsersBody, rejectedUsersSearch);
            }
        }

        allRows = allRows.filter(item => item !== row);
        row.remove();
        activeRegistrationRow = null;

        filterRegistrations(true);
        updateRegistrationStats();
    }


    /* =========================================================
       APPROVE / REJECT REGISTRATION ACTIONS
    ========================================================== */

    const rejectRegistrationModal =
        document.getElementById('reject-registration-modal');

    const cancelRejectRegistration =
        document.getElementById('cancel-reject-registration');

    const confirmRejectRegistration =
        document.getElementById('confirm-reject-registration');

    const rejectAdditionalDetails =
        document.getElementById('reject-additional-details');

    const rejectDetailsCount =
        document.getElementById('reject-details-count');

    const rejectReasonError =
        document.getElementById('reject-reason-error');

    const registrationFlash =
        document.getElementById('registration-flash');

    const registrationFlashIcon =
        document.getElementById('registration-flash-icon');

    const registrationFlashTitle =
        document.getElementById('registration-flash-title');

    const registrationFlashMessage =
        document.getElementById('registration-flash-message');

    const registrationFlashClose =
        document.getElementById('registration-flash-close');

    let registrationFlashTimer = null;

    function getSelectedRejectReason() {
        const checked =
            document.querySelector('input[name="reject_reason"]:checked');

        return checked ? checked.value : '';
    }

    function resetRejectRegistrationForm() {
        document
            .querySelectorAll('input[name="reject_reason"]')
            .forEach(input => {
                input.checked = false;
            });

        if (rejectAdditionalDetails) {
            rejectAdditionalDetails.value = '';
        }

        if (rejectDetailsCount) {
            rejectDetailsCount.textContent = '0/300';
        }

        if (rejectReasonError) {
            rejectReasonError.classList.add('hidden');
        }
    }

    function openRejectRegistrationModal() {
        if (!rejectRegistrationModal) {
            return;
        }

        resetRejectRegistrationForm();

        rejectRegistrationModal.classList.remove('hidden');
        rejectRegistrationModal.classList.add('flex');
        rejectRegistrationModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    }

    function closeRejectRegistrationModal() {
        if (!rejectRegistrationModal) {
            return;
        }

        rejectRegistrationModal.classList.add('hidden');
        rejectRegistrationModal.classList.remove('flex');
        rejectRegistrationModal.setAttribute('aria-hidden', 'true');

        const anyRegistrationModalOpen =
            (sellerDetailsModal && !sellerDetailsModal.classList.contains('hidden')) ||
            (buyerDetailsModal && !buyerDetailsModal.classList.contains('hidden'));

        if (!anyRegistrationModalOpen) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function showRegistrationFlash(type, name) {
        if (!registrationFlash) {
            return;
        }

        const approved = type === 'approved';
        const rejected = type === 'rejected' || type === 'reject';
        const isError = type === 'error';

        registrationFlash.classList.remove('approved', 'rejected', 'error');
        registrationFlash.classList.add(
            approved ? 'approved' : (isError ? 'error' : 'rejected')
        );

        registrationFlashIcon.className =
            'w-9 h-9 rounded-full flex items-center justify-center shrink-0 ' +
            (approved ? 'bg-[#DDF0D6]' : 'bg-[#FFE3E5]');

        registrationFlashIcon.innerHTML = approved
            ? `<svg class="w-5 h-5 text-[#28721B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6"/>
               </svg>`
            : `<svg class="w-5 h-5 text-[#B3262E]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6 6 18"/>
               </svg>`;

        if (isError) {
            registrationFlashTitle.textContent = 'Action Failed';
            registrationFlashMessage.textContent = name || 'Unable to update this registration.';
        } else {
            registrationFlashTitle.textContent =
                approved ? 'Registration Approved' : 'Registration Rejected';

            registrationFlashMessage.textContent =
                approved
                    ? `${name}'s registration has been approved. The user will be notified via email.`
                    : `${name}'s registration has been rejected. The user will be notified via email.`;
        }

        clearTimeout(registrationFlashTimer);

        // Show immediately on the same action click.
        registrationFlash.classList.add('show');

        registrationFlashTimer = setTimeout(() => {
            registrationFlash.classList.remove('show');
        }, 3800);
    }

    async function submitRegistrationReview(row, action, payload = {}) {
        if (!row.dataset.id) {
            moveRegistrationToArchive(row, action);
            return true;
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        const response = await fetch(`/admin/registrations/${row.dataset.id}/${action}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify(payload)
        });

        if (!response.ok) {
            const error = await response.json().catch(() => ({}));
            throw new Error(error.message || `Unable to ${action} registration.`);
        }

        moveRegistrationToArchive(row, action);
        return true;
    }

    if (registrationFlashClose) {
        registrationFlashClose.addEventListener('click', () => {
            clearTimeout(registrationFlashTimer);

            if (registrationFlash) {
                registrationFlash.classList.remove('show');
            }
        });
    }

    if (rejectAdditionalDetails && rejectDetailsCount) {
        rejectAdditionalDetails.addEventListener('input', () => {
            rejectDetailsCount.textContent =
                `${rejectAdditionalDetails.value.length}/300`;
        });
    }

    if (cancelRejectRegistration) {
        cancelRejectRegistration.addEventListener(
            'click',
            closeRejectRegistrationModal
        );
    }

    if (rejectRegistrationModal) {
        rejectRegistrationModal.addEventListener('click', function (event) {

            if (event.target === rejectRegistrationModal) {
                closeRejectRegistrationModal();
            }

        });
    }

    if (confirmRejectRegistration) {
        confirmRejectRegistration.addEventListener('click', async function () {

            if (!activeRegistrationRow) {
                closeRejectRegistrationModal();
                return;
            }

            const reason = getSelectedRejectReason();

            if (!reason) {
                if (rejectReasonError) {
                    rejectReasonError.classList.remove('hidden');
                }

                return;
            }

            const name =
                activeRegistrationRow.dataset.name ||
                'The applicant';

            // Flash immediately when the Reject action is confirmed.
            showRegistrationFlash('rejected', name);

            confirmRejectRegistration.disabled = true;

            try {
                await submitRegistrationReview(activeRegistrationRow, 'reject', {
                    reason,
                    details: rejectAdditionalDetails?.value || ''
                });
                closeRegistrationModal(sellerDetailsModal);
                closeRegistrationModal(buyerDetailsModal);
                closeRejectRegistrationModal();
            } catch (error) {
                showRegistrationFlash('error', error.message);
            } finally {
                confirmRejectRegistration.disabled = false;
            }

        });
    }

    document.querySelectorAll('.registration-reject').forEach(button => {

        button.addEventListener('click', async function () {

            if (!activeRegistrationRow) {
                return;
            }

            openRejectRegistrationModal();

        });

    });

    document.querySelectorAll('.registration-approve').forEach(button => {

        button.addEventListener('click', async function () {

            if (!activeRegistrationRow) {
                return;
            }

            const name =
                activeRegistrationRow.dataset.name ||
                'The applicant';

            const confirmed = window.confirm(
                `Are you sure you want to approve ${name}'s registration?`
            );

            if (!confirmed) {
                return;
            }

            // Flash immediately after the admin confirms approval.
            showRegistrationFlash('approved', name);

            button.disabled = true;

            try {
                await submitRegistrationReview(activeRegistrationRow, 'approve');
                closeRegistrationModal(sellerDetailsModal);
                closeRegistrationModal(buyerDetailsModal);
            } catch (error) {
                showRegistrationFlash('error', error.message);
            } finally {
                button.disabled = false;
            }

        });

    });



    /* =========================================================
       APPROVED USERS ARCHIVE MODAL
    ========================================================== */

    const approvedUsersButton =
        document.getElementById(
            'approved-users-button'
        );

    const approvedUsersModal =
        document.getElementById(
            'approved-users-modal'
        );

    const closeApprovedUsers =
        document.getElementById(
            'close-approved-users'
        );

    const closeApprovedUsersBottom =
        document.getElementById(
            'close-approved-users-bottom'
        );


    if (
        approvedUsersButton &&
        approvedUsersModal
    ) {

        approvedUsersButton.addEventListener(
            'click',
            async function () {

                approvedUsersModal.classList.remove(
                    'hidden'
                );

                approvedUsersModal.classList.add(
                    'flex'
                );

                resetArchiveSearch(approvedUsersSearch, approvedUsersBody);

                try {
                    await loadArchiveUsers('/admin/registrations/approved/list', approvedUsersBody, 'approved');
                } catch (error) {
                    console.error(error);
                }

                document.body.classList.add(
                    'overflow-hidden'
                );

            }
        );

    }


    function closeApprovedModal() {

        if (!approvedUsersModal) {
            return;
        }

        approvedUsersModal.classList.add(
            'hidden'
        );

        approvedUsersModal.classList.remove(
            'flex'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );

    }


    if (closeApprovedUsers) {

        closeApprovedUsers.addEventListener(
            'click',
            closeApprovedModal
        );

    }


    if (closeApprovedUsersBottom) {

        closeApprovedUsersBottom.addEventListener(
            'click',
            closeApprovedModal
        );

    }


    if (approvedUsersModal) {

        approvedUsersModal.addEventListener(
            'click',
            function (event) {

                if (
                    event.target ===
                    approvedUsersModal
                ) {

                    closeApprovedModal();

                }

            }
        );

    }


    /* =========================================================
       REJECTED USERS ARCHIVE MODAL
    ========================================================== */

    const rejectedUsersButton =
        document.getElementById(
            'rejected-users-button'
        );

    const rejectedUsersModal =
        document.getElementById(
            'rejected-users-modal'
        );

    const closeRejectedUsers =
        document.getElementById(
            'close-rejected-users'
        );

    const closeRejectedUsersBottom =
        document.getElementById(
            'close-rejected-users-bottom'
        );


    if (
        rejectedUsersButton &&
        rejectedUsersModal
    ) {

        rejectedUsersButton.addEventListener(
            'click',
            async function () {

                rejectedUsersModal.classList.remove(
                    'hidden'
                );

                rejectedUsersModal.classList.add(
                    'flex'
                );

                resetArchiveSearch(rejectedUsersSearch, rejectedUsersBody);

                try {
                    await loadArchiveUsers('/admin/registrations/rejected/list', rejectedUsersBody, 'rejected');
                } catch (error) {
                    console.error(error);
                }

                document.body.classList.add(
                    'overflow-hidden'
                );

            }
        );

    }


    function closeRejectedModal() {

        if (!rejectedUsersModal) {
            return;
        }

        rejectedUsersModal.classList.add(
            'hidden'
        );

        rejectedUsersModal.classList.remove(
            'flex'
        );

        document.body.classList.remove(
            'overflow-hidden'
        );

    }


    if (closeRejectedUsers) {

        closeRejectedUsers.addEventListener(
            'click',
            closeRejectedModal
        );

    }


    if (closeRejectedUsersBottom) {

        closeRejectedUsersBottom.addEventListener(
            'click',
            closeRejectedModal
        );

    }


    if (rejectedUsersModal) {

        rejectedUsersModal.addEventListener(
            'click',
            function (event) {

                if (
                    event.target ===
                    rejectedUsersModal
                ) {

                    closeRejectedModal();

                }

            }
        );

    }


    /* =========================================================
       ESC TO CLOSE MODALS
    ========================================================== */

    document.addEventListener('keydown', function (event) {

        if (event.key !== 'Escape') {
            return;
        }

        if (
            sellerDetailsModal &&
            !sellerDetailsModal.classList.contains('hidden')
        ) {
            closeRegistrationModal(sellerDetailsModal);
        }

        if (
            buyerDetailsModal &&
            !buyerDetailsModal.classList.contains('hidden')
        ) {
            closeRegistrationModal(buyerDetailsModal);
        }

        if (
            approvedUsersModal &&
            !approvedUsersModal.classList.contains('hidden')
        ) {
            closeApprovedModal();
        }

        if (
            rejectedUsersModal &&
            !rejectedUsersModal.classList.contains('hidden')
        ) {
            closeRejectedModal();
        }

        if (
            rejectRegistrationModal &&
            !rejectRegistrationModal.classList.contains('hidden')
        ) {
            closeRejectRegistrationModal();
        }

    });

    /* =========================================================
       PAGE LOAD UX
    ========================================================== */

    const pageContent =
        document.getElementById('admin-content');


    if (pageContent) {

        pageContent.classList.add(
            'opacity-0',
            'translate-y-2'
        );

        requestAnimationFrame(() => {

            setTimeout(() => {

                pageContent.classList.remove(
                    'opacity-0',
                    'translate-y-2'
                );

            }, 80);

        });

    }


    /* =========================================================
       INITIAL RENDER
    ========================================================== */

    renderTable();

});

</script>

@endpush

@endsection
